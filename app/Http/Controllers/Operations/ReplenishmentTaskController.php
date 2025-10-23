<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\ReplenishmentTask;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReplenishmentTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ReplenishmentTask::with([
            'warehouse',
            'product',
            'fromStorageBin',
            'toStorageBin',
            'assignedUser'
        ]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('task_number', 'like', "%{$search}%")
                    ->orWhere('batch_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('trigger_type')) {
            $query->where('trigger_type', $request->trigger_type);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $replenishmentTasks = $query->latest()->paginate(20);

        // Get filter options
        $statuses = ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'];
        $priorities = ['urgent', 'high', 'medium', 'low'];
        $triggerTypes = ['min_level', 'empty_pick_face', 'manual'];
        $warehouses = Warehouse::all();
        $users = User::all();

        return view('operations.replenishments.index', compact(
            'replenishmentTasks',
            'statuses',
            'priorities',
            'triggerTypes',
            'warehouses',
            'users'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::all();
        $products = Product::all();
        $users = User::all();
        
        // Get storage bins grouped by warehouse
        $storageBins = StorageBin::with('warehouse')
            ->orderBy('warehouse_id')
            ->orderBy('code')
            ->get()
            ->groupBy('warehouse_id');

        return view('operations.replenishments.create', compact(
            'warehouses',
            'products',
            'users',
            'storageBins'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'from_storage_bin_id' => 'required|exists:storage_bins,id',
            'to_storage_bin_id' => 'required|exists:storage_bins,id',
            'batch_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'quantity_suggested' => 'required|integer|min:1',
            'unit_of_measure' => 'required|string|max:50',
            'priority' => 'required|in:urgent,high,medium,low',
            'trigger_type' => 'required|in:min_level,empty_pick_face,manual',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['task_number'] = ReplenishmentTask::generateTaskNumber();
        $validated['created_by'] = auth()->id();

        if ($request->filled('assigned_to')) {
            $validated['assigned_at'] = now();
            $validated['status'] = 'assigned';
        }

        $replenishmentTask = ReplenishmentTask::create($validated);

        return redirect()
            ->route('operations.replenishments.show', $replenishmentTask)
            ->with('success', 'Replenishment task created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReplenishmentTask $replenishment)
    {
        $replenishment->load([
            'warehouse',
            'product',
            'fromStorageBin',
            'toStorageBin',
            'assignedUser',
            'createdBy',
            'updatedBy'
        ]);

        return view('operations.replenishments.show', compact('replenishment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReplenishmentTask $replenishment)
    {
        if (!in_array($replenishment->status, ['pending', 'assigned'])) {
            return redirect()
                ->route('operations.replenishments.show', $replenishment)
                ->with('error', 'Only pending or assigned tasks can be edited.');
        }

        $warehouses = Warehouse::all();
        $products = Product::all();
        $users = User::all();
        $storageBins = StorageBin::where('warehouse_id', $replenishment->warehouse_id)->get();

        return view('operations.replenishments.edit', compact(
            'replenishment',
            'warehouses',
            'products',
            'users',
            'storageBins'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReplenishmentTask $replenishment)
    {
        if (!in_array($replenishment->status, ['pending', 'assigned'])) {
            return redirect()
                ->route('operations.replenishments.show', $replenishment)
                ->with('error', 'Only pending or assigned tasks can be updated.');
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'from_storage_bin_id' => 'required|exists:storage_bins,id',
            'to_storage_bin_id' => 'required|exists:storage_bins,id',
            'batch_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'quantity_suggested' => 'required|integer|min:1',
            'unit_of_measure' => 'required|string|max:50',
            'priority' => 'required|in:urgent,high,medium,low',
            'trigger_type' => 'required|in:min_level,empty_pick_face,manual',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        if ($request->filled('assigned_to') && !$replenishment->assigned_at) {
            $validated['assigned_at'] = now();
            $validated['status'] = 'assigned';
        }

        $replenishment->update($validated);

        return redirect()
            ->route('operations.replenishments.show', $replenishment)
            ->with('success', 'Replenishment task updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReplenishmentTask $replenishment)
    {
        if ($replenishment->status !== 'pending') {
            return redirect()
                ->route('operations.replenishments.index')
                ->with('error', 'Only pending tasks can be deleted.');
        }

        $replenishment->delete();

        return redirect()
            ->route('operations.replenishments.index')
            ->with('success', 'Replenishment task deleted successfully!');
    }

    /**
     * Show suggestions page
     */
    public function suggestions()
    {
        try {
            // Get products that need replenishment based on min level
            $suggestions = DB::table('storage_bins as sb')
                ->join('warehouses as w', 'sb.warehouse_id', '=', 'w.id')
                ->leftJoin('storage_bins as source_bins', function ($join) {
                    $join->on('source_bins.warehouse_id', '=', 'sb.warehouse_id')
                        ->where('source_bins.bin_type', '=', 'high_rack');
                })
                ->where('sb.bin_type', 'pick_face')
                ->whereNotNull('source_bins.id')
                ->whereRaw('sb.current_quantity < sb.min_quantity')
                ->whereRaw('source_bins.current_quantity > 0')
                ->select(
                    'sb.id as to_storage_bin_id',
                    'source_bins.id as from_storage_bin_id',
                    'w.id as warehouse_id',
                    'w.name as warehouse_name',
                    'sb.bin_code as pick_face_bin',
                    'source_bins.bin_code as source_bin',
                    'sb.current_quantity',
                    'sb.min_quantity',
                    'sb.max_quantity',
                    'source_bins.current_quantity as source_quantity',
                    DB::raw('LEAST(sb.max_quantity - sb.current_quantity, source_bins.current_quantity) as suggested_quantity')
                )
                ->get();

            return view('operations.replenishments.suggestions', compact('suggestions'));
            
        } catch (\Exception $e) {
            // If query fails, return empty collection
            $suggestions = collect([]);
            return view('operations.replenishments.suggestions', compact('suggestions'))
                ->with('error', 'Failed to load suggestions: ' . $e->getMessage());
        }
    }

    /**
     * Generate replenishment suggestions
     */
    public function generateSuggestions(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'priority' => 'required|in:urgent,high,medium,low',
        ]);

        try {
            $query = DB::table('storage_bins as sb')
                ->leftJoin('storage_bins as source_bins', function ($join) {
                    $join->on('source_bins.warehouse_id', '=', 'sb.warehouse_id')
                        ->where('source_bins.bin_type', '=', 'high_rack');
                })
                ->where('sb.bin_type', 'pick_face')
                ->whereNotNull('source_bins.id')
                ->whereRaw('sb.current_quantity < sb.min_quantity')
                ->whereRaw('source_bins.current_quantity > 0');

            if ($request->filled('warehouse_id')) {
                $query->where('sb.warehouse_id', $request->warehouse_id);
            }

            $suggestions = $query->select(
                'sb.warehouse_id',
                'source_bins.id as from_storage_bin_id',
                'sb.id as to_storage_bin_id',
                DB::raw('LEAST(sb.max_quantity - sb.current_quantity, source_bins.current_quantity) as quantity_suggested')
            )->get();

            if ($suggestions->isEmpty()) {
                return redirect()
                    ->route('operations.replenishments.index')
                    ->with('info', 'No replenishment suggestions found. All pick face locations are adequately stocked.');
            }

            $created = 0;
            foreach ($suggestions as $suggestion) {
                // Get product info from storage bin if available
                $fromBin = StorageBin::find($suggestion->from_storage_bin_id);
                $toBin = StorageBin::find($suggestion->to_storage_bin_id);
                
                // Skip if bins not found or no product associated
                if (!$fromBin || !$toBin) {
                    continue;
                }

                // Get a product from warehouse for this replenishment
                // You may need to adjust this logic based on your business rules
                $product = Product::first(); // TODO: Implement proper product selection logic
                
                if (!$product) {
                    continue;
                }

                ReplenishmentTask::create([
                    'task_number' => ReplenishmentTask::generateTaskNumber(),
                    'warehouse_id' => $suggestion->warehouse_id,
                    'product_id' => $product->id,
                    'from_storage_bin_id' => $suggestion->from_storage_bin_id,
                    'to_storage_bin_id' => $suggestion->to_storage_bin_id,
                    'quantity_suggested' => $suggestion->quantity_suggested,
                    'unit_of_measure' => $product->unit_of_measure ?? 'PCS',
                    'priority' => $validated['priority'],
                    'status' => 'pending',
                    'trigger_type' => 'min_level',
                    'created_by' => auth()->id(),
                ]);
                $created++;
            }

            if ($created > 0) {
                return redirect()
                    ->route('operations.replenishments.index')
                    ->with('success', "{$created} replenishment task(s) generated successfully!");
            } else {
                return redirect()
                    ->route('operations.replenishments.index')
                    ->with('warning', 'No valid replenishment tasks could be generated.');
            }
            
        } catch (\Exception $e) {
            return redirect()
                ->route('operations.replenishments.index')
                ->with('error', 'Failed to generate suggestions: ' . $e->getMessage());
        }
    }

    /**
     * Assign task to user
     */
    public function assign(Request $request, ReplenishmentTask $replenishmentTask)
    {
        if ($replenishmentTask->status !== 'pending') {
            return back()->with('error', 'Only pending tasks can be assigned.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $replenishmentTask->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_at' => now(),
            'status' => 'assigned',
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Task assigned successfully!');
    }

    /**
     * Start task execution
     */
    public function start(ReplenishmentTask $replenishmentTask)
    {
        if ($replenishmentTask->status !== 'assigned') {
            return back()->with('error', 'Only assigned tasks can be started.');
        }

        $replenishmentTask->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Task started successfully!');
    }

    /**
     * Execute task page
     */
    public function execute(ReplenishmentTask $replenishmentTask)
    {
        if (!in_array($replenishmentTask->status, ['assigned', 'in_progress'])) {
            return redirect()
                ->route('operations.replenishments.show', $replenishmentTask)
                ->with('error', 'Only assigned or in-progress tasks can be executed.');
        }

        $replenishmentTask->load([
            'warehouse',
            'product',
            'fromStorageBin',
            'toStorageBin',
            'assignedUser'
        ]);

        return view('operations.replenishments.execute', compact('replenishmentTask'));
    }

    /**
     * Complete task
     */
    public function complete(Request $request, ReplenishmentTask $replenishmentTask)
    {
        if (!in_array($replenishmentTask->status, ['assigned', 'in_progress'])) {
            return back()->with('error', 'Only assigned or in-progress tasks can be completed.');
        }

        $validated = $request->validate([
            'quantity_moved' => 'required|integer|min:1|max:' . $replenishmentTask->quantity_suggested,
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update task
            $replenishmentTask->update([
                'quantity_moved' => $validated['quantity_moved'],
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $validated['notes'] ?? $replenishmentTask->notes,
                'updated_by' => auth()->id(),
            ]);

            // Update storage bins inventory
            // Deduct from source bin
            $replenishmentTask->fromStorageBin->decrement('current_quantity', $validated['quantity_moved']);

            // Add to destination bin
            $replenishmentTask->toStorageBin->increment('current_quantity', $validated['quantity_moved']);

            DB::commit();

            return redirect()
                ->route('operations.replenishments.show', $replenishmentTask)
                ->with('success', 'Task completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete task: ' . $e->getMessage());
        }
    }

    /**
     * Cancel task
     */
    public function cancel(Request $request, ReplenishmentTask $replenishmentTask)
    {
        if (in_array($replenishmentTask->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel completed or already cancelled tasks.');
        }

        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $replenishmentTask->update([
            'status' => 'cancelled',
            'notes' => $validated['notes'],
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Task cancelled successfully!');
    }
}