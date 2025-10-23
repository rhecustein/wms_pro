<?php

namespace App\Http\Controllers\Inbound;

use App\Http\Controllers\Controller;
use App\Models\PutawayTask;
use App\Models\GoodReceiving;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PutawayTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PutawayTask::with(['goodReceiving', 'warehouse', 'product', 'storageBin', 'assignedUser', 'pallet']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('task_number', 'like', "%{$search}%")
                    ->orWhere('batch_number', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $putawayTasks = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $statuses = ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'];
        $priorities = ['high', 'medium', 'low'];

        return view('inbound.putaway-tasks.index', compact(
            'putawayTasks',
            'warehouses',
            'users',
            'statuses',
            'priorities'
        ));
    }

    /**
     * Display pending tasks only
     */
    public function pending(Request $request)
    {
        $query = PutawayTask::with(['goodReceiving', 'warehouse', 'product', 'storageBin'])
            ->whereIn('status', ['pending', 'assigned']);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $putawayTasks = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        $warehouses = Warehouse::orderBy('name')->get();

        return view('inbound.putaway-tasks.pending', compact('putawayTasks', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $goodReceivings = GoodReceiving::where('status', 'completed')
            ->with('vendor')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $warehouses = Warehouse::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $storageBins = StorageBin::where('status', 'active')
            ->orderBy('code')
            ->get();
        $users = User::orderBy('name')->get();

        return view('inbound.putaway-tasks.create', compact(
            'goodReceivings',
            'warehouses',
            'products',
            'storageBins',
            'users'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'good_receiving_id' => 'required|exists:good_receivings,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'batch_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_of_measure' => 'required|string|max:50',
            'from_location' => 'required|string|max:255',
            'to_storage_bin_id' => 'nullable|exists:storage_bins,id',
            'pallet_id' => 'nullable|exists:pallets,id',
            'priority' => 'required|in:high,medium,low',
            'packaging_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Generate task number
            $lastTask = PutawayTask::latest('id')->first();
            $nextNumber = $lastTask ? intval(substr($lastTask->task_number, 4)) + 1 : 1;
            $validated['task_number'] = 'PUT-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $validated['status'] = $request->filled('assigned_to') ? 'assigned' : 'pending';
            $validated['assigned_at'] = $request->filled('assigned_to') ? now() : null;
            $validated['suggested_by_system'] = false;
            $validated['created_by'] = Auth::id();

            $putawayTask = PutawayTask::create($validated);

            DB::commit();

            return redirect()->route('inbound.putaway-tasks.show', $putawayTask)
                ->with('success', 'Putaway task created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create putaway task: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PutawayTask $putawayTask)
    {
        $putawayTask->load([
            'goodReceiving.vendor',
            'goodReceiving.purchaseOrder',
            'warehouse',
            'product',
            'storageBin',
            'pallet',
            'assignedUser',
            'creator',
            'updater'
        ]);

        return view('inbound.putaway-tasks.show', compact('putawayTask'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PutawayTask $putawayTask)
    {
        if (!in_array($putawayTask->status, ['pending', 'assigned'])) {
            return back()->with('error', 'Cannot edit task in ' . $putawayTask->status . ' status.');
        }

        $goodReceivings = GoodReceiving::where('status', 'completed')
            ->with('vendor')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $warehouses = Warehouse::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $storageBins = StorageBin::where('status', 'active')
            ->orderBy('code')
            ->get();
        $users = User::orderBy('name')->get();

        return view('inbound.putaway-tasks.edit', compact(
            'putawayTask',
            'goodReceivings',
            'warehouses',
            'products',
            'storageBins',
            'users'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PutawayTask $putawayTask)
    {
        if (!in_array($putawayTask->status, ['pending', 'assigned'])) {
            return back()->with('error', 'Cannot edit task in ' . $putawayTask->status . ' status.');
        }

        $validated = $request->validate([
            'good_receiving_id' => 'required|exists:good_receivings,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'batch_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_of_measure' => 'required|string|max:50',
            'from_location' => 'required|string|max:255',
            'to_storage_bin_id' => 'nullable|exists:storage_bins,id',
            'pallet_id' => 'nullable|exists:pallets,id',
            'priority' => 'required|in:high,medium,low',
            'packaging_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $validated['status'] = $request->filled('assigned_to') ? 'assigned' : 'pending';
            $validated['assigned_at'] = $request->filled('assigned_to') && !$putawayTask->assigned_at ? now() : $putawayTask->assigned_at;
            $validated['updated_by'] = Auth::id();

            $putawayTask->update($validated);

            DB::commit();

            return redirect()->route('inbound.putaway-tasks.show', $putawayTask)
                ->with('success', 'Putaway task updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update putaway task: ' . $e->getMessage());
        }
    }

    /**
     * Assign task to user
     */
    public function assign(Request $request, PutawayTask $putawayTask)
    {
        if ($putawayTask->status !== 'pending') {
            return back()->with('error', 'Only pending tasks can be assigned.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        try {
            $putawayTask->update([
                'assigned_to' => $validated['assigned_to'],
                'assigned_at' => now(),
                'status' => 'assigned',
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Task assigned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign task: ' . $e->getMessage());
        }
    }

    /**
     * Start the putaway task
     */
    public function start(PutawayTask $putawayTask)
    {
        if (!in_array($putawayTask->status, ['pending', 'assigned'])) {
            return back()->with('error', 'Task cannot be started from ' . $putawayTask->status . ' status.');
        }

        try {
            $putawayTask->update([
                'status' => 'in_progress',
                'started_at' => now(),
                'assigned_to' => $putawayTask->assigned_to ?? Auth::id(),
                'assigned_at' => $putawayTask->assigned_at ?? now(),
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Task started successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start task: ' . $e->getMessage());
        }
    }

    /**
     * Complete the putaway task
     */
    public function complete(Request $request, PutawayTask $putawayTask)
    {
        if ($putawayTask->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress tasks can be completed.');
        }

        $validated = $request->validate([
            'to_storage_bin_id' => 'required|exists:storage_bins,id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $putawayTask->update([
                'status' => 'completed',
                'completed_at' => now(),
                'to_storage_bin_id' => $validated['to_storage_bin_id'],
                'notes' => $validated['notes'] ?? $putawayTask->notes,
                'updated_by' => Auth::id(),
            ]);

            // Update storage bin quantity (assuming you have inventory management)
            // $storageBin = StorageBin::find($validated['to_storage_bin_id']);
            // $storageBin->addInventory($putawayTask->product_id, $putawayTask->quantity);

            DB::commit();

            return redirect()->route('inbound.putaway-tasks.show', $putawayTask)
                ->with('success', 'Task completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete task: ' . $e->getMessage());
        }
    }

    /**
     * Cancel the putaway task
     */
    public function cancel(Request $request, PutawayTask $putawayTask)
    {
        if (in_array($putawayTask->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel task in ' . $putawayTask->status . ' status.');
        }

        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        try {
            $putawayTask->update([
                'status' => 'cancelled',
                'notes' => $validated['notes'],
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Task cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel task: ' . $e->getMessage());
        }
    }

    /**
     * Execute putaway task (mobile/operator view)
     */
    public function execute(PutawayTask $putawayTask)
    {
        if (!in_array($putawayTask->status, ['assigned', 'in_progress'])) {
            return redirect()->route('inbound.putaway-tasks.show', $putawayTask)
                ->with('error', 'Task cannot be executed in ' . $putawayTask->status . ' status.');
        }

        $putawayTask->load([
            'goodReceiving',
            'warehouse',
            'product',
            'storageBin',
            'pallet'
        ]);

        $availableBins = StorageBin::where('warehouse_id', $putawayTask->warehouse_id)
            ->where('status', 'active')
            ->orderBy('code')
            ->get();

        return view('inbound.putaway-tasks.execute', compact('putawayTask', 'availableBins'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PutawayTask $putawayTask)
    {
        if ($putawayTask->status !== 'pending') {
            return back()->with('error', 'Only pending tasks can be deleted.');
        }

        try {
            $putawayTask->delete();
            return redirect()->route('inbound.putaway-tasks.index')
                ->with('success', 'Putaway task deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete putaway task: ' . $e->getMessage());
        }
    }
}