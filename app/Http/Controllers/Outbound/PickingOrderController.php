<?php

namespace App\Http\Controllers\Outbound;

use App\Http\Controllers\Controller;
use App\Models\PickingOrder;
use App\Models\PickingOrderItem;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Product;
use App\Models\StorageBin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PickingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PickingOrder::with(['salesOrder', 'warehouse', 'assignedUser', 'createdBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('picking_number', 'like', "%{$search}%")
                  ->orWhereHas('salesOrder', function ($q) use ($search) {
                      $q->where('so_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('assignedUser', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Priority Filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Picking Type Filter
        if ($request->filled('picking_type')) {
            $query->where('picking_type', $request->picking_type);
        }

        // Warehouse Filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('picking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('picking_date', '<=', $request->date_to);
        }

        $pickingOrders = $query->latest()->paginate(15);

        // Data for filters
        $statuses = ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'];
        $priorities = ['urgent', 'high', 'medium', 'low'];
        $pickingTypes = ['single_order', 'batch', 'wave', 'zone'];
        $warehouses = Warehouse::orderBy('name')->get();

        return view('outbound.picking-orders.index', compact(
            'pickingOrders',
            'statuses',
            'priorities',
            'pickingTypes',
            'warehouses'
        ));
    }

    /**
     * Display pending picking orders.
     */
    public function pending()
    {
        $pickingOrders = PickingOrder::with(['salesOrder', 'warehouse', 'assignedUser'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        $warehouses = Warehouse::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('outbound.picking-orders.pending', compact('pickingOrders', 'warehouses', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $salesOrders = SalesOrder::where('status', 'confirmed')
            ->whereDoesntHave('pickingOrders', function ($q) {
                $q->whereIn('status', ['pending', 'assigned', 'in_progress']);
            })
            ->orderBy('so_number')
            ->get();
        $users = User::orderBy('name')->get();

        return view('outbound.picking-orders.create', compact('warehouses', 'salesOrders', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'picking_date' => 'required|date',
            'picking_type' => 'required|in:single_order,batch,wave,zone',
            'priority' => 'required|in:urgent,high,medium,low',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sales_order_item_id' => 'required|exists:sales_order_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.storage_bin_id' => 'required|exists:storage_bins,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'items.*.batch_number' => 'nullable|string',
            'items.*.serial_number' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.unit_of_measure' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Create Picking Order
            $pickingOrder = PickingOrder::create([
                'picking_number' => PickingOrder::generatePickingNumber(),
                'sales_order_id' => $validated['sales_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'picking_date' => $validated['picking_date'],
                'picking_type' => $validated['picking_type'],
                'priority' => $validated['priority'],
                'status' => $request->filled('assigned_to') ? 'assigned' : 'pending',
                'assigned_to' => $validated['assigned_to'] ?? null,
                'assigned_at' => $request->filled('assigned_to') ? now() : null,
                'total_items' => count($validated['items']),
                'total_quantity' => collect($validated['items'])->sum('quantity_requested'),
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Create Picking Order Items
            foreach ($validated['items'] as $index => $item) {
                PickingOrderItem::create([
                    'picking_order_id' => $pickingOrder->id,
                    'sales_order_item_id' => $item['sales_order_item_id'],
                    'product_id' => $item['product_id'],
                    'storage_bin_id' => $item['storage_bin_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'quantity_requested' => $item['quantity_requested'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'pick_sequence' => $index + 1,
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('success', 'Picking order created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create picking order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PickingOrder $pickingOrder)
    {
        $pickingOrder->load([
            'salesOrder.customer',
            'warehouse',
            'assignedUser',
            'createdBy',
            'updatedBy',
            'items.product',
            'items.storageBin',
            'items.pickedBy'
        ]);

        return view('outbound.picking-orders.show', compact('pickingOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PickingOrder $pickingOrder)
    {
        if (!in_array($pickingOrder->status, ['pending', 'assigned'])) {
            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('error', 'Only pending or assigned picking orders can be edited.');
        }

        $pickingOrder->load('items.product', 'items.storageBin');
        $warehouses = Warehouse::orderBy('name')->get();
        $salesOrders = SalesOrder::where('status', 'confirmed')->orderBy('so_number')->get();
        $users = User::orderBy('name')->get();

        return view('outbound.picking-orders.edit', compact('pickingOrder', 'warehouses', 'salesOrders', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PickingOrder $pickingOrder)
    {
        if (!in_array($pickingOrder->status, ['pending', 'assigned'])) {
            return back()->with('error', 'Only pending or assigned picking orders can be updated.');
        }

        $validated = $request->validate([
            'picking_date' => 'required|date',
            'picking_type' => 'required|in:single_order,batch,wave,zone',
            'priority' => 'required|in:urgent,high,medium,low',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $pickingOrder->update([
                'picking_date' => $validated['picking_date'],
                'picking_type' => $validated['picking_type'],
                'priority' => $validated['priority'],
                'assigned_to' => $validated['assigned_to'] ?? null,
                'assigned_at' => $request->filled('assigned_to') && !$pickingOrder->assigned_at ? now() : $pickingOrder->assigned_at,
                'status' => $request->filled('assigned_to') ? 'assigned' : 'pending',
                'notes' => $validated['notes'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('success', 'Picking order updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update picking order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PickingOrder $pickingOrder)
    {
        if ($pickingOrder->status !== 'pending') {
            return back()->with('error', 'Only pending picking orders can be deleted.');
        }

        try {
            $pickingOrder->delete();
            return redirect()->route('picking-orders.index')
                ->with('success', 'Picking order deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete picking order: ' . $e->getMessage());
        }
    }

    /**
     * Assign picking order to a user.
     */
    public function assign(Request $request, PickingOrder $pickingOrder)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($pickingOrder->status !== 'pending') {
            return back()->with('error', 'Only pending picking orders can be assigned.');
        }

        try {
            $pickingOrder->update([
                'assigned_to' => $validated['assigned_to'],
                'assigned_at' => now(),
                'status' => 'assigned',
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Picking order assigned successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign picking order: ' . $e->getMessage());
        }
    }

    /**
     * Start picking process.
     */
    public function start(PickingOrder $pickingOrder)
    {
        if (!in_array($pickingOrder->status, ['pending', 'assigned'])) {
            return back()->with('error', 'Only pending or assigned picking orders can be started.');
        }

        try {
            $pickingOrder->update([
                'status' => 'in_progress',
                'started_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('picking-orders.execute', $pickingOrder)
                ->with('success', 'Picking process started!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start picking: ' . $e->getMessage());
        }
    }

    /**
     * Execute picking process.
     */
    public function execute(PickingOrder $pickingOrder)
    {
        if ($pickingOrder->status !== 'in_progress') {
            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('error', 'Only in-progress picking orders can be executed.');
        }

        $pickingOrder->load([
            'items' => function ($q) {
                $q->orderBy('pick_sequence');
            },
            'items.product',
            'items.storageBin',
            'salesOrder',
            'warehouse'
        ]);

        return view('outbound.picking-orders.execute', compact('pickingOrder'));
    }

    /**
     * Complete picking order.
     */
    public function complete(PickingOrder $pickingOrder)
    {
        if ($pickingOrder->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress picking orders can be completed.');
        }

        // Check if all items are picked
        $pendingItems = $pickingOrder->items()->where('status', 'pending')->count();
        if ($pendingItems > 0) {
            return back()->with('error', "Cannot complete picking order. {$pendingItems} items are still pending.");
        }

        try {
            $pickingOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('success', 'Picking order completed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to complete picking order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel picking order.
     */
    public function cancel(PickingOrder $pickingOrder)
    {
        if ($pickingOrder->status === 'completed') {
            return back()->with('error', 'Completed picking orders cannot be cancelled.');
        }

        try {
            $pickingOrder->update([
                'status' => 'cancelled',
                'updated_by' => Auth::id(),
            ]);

            // Cancel all pending items
            $pickingOrder->items()->where('status', 'pending')->update(['status' => 'cancelled']);

            return back()->with('success', 'Picking order cancelled successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel picking order: ' . $e->getMessage());
        }
    }

    /**
     * Show wave picking interface.
     */
    public function wave()
    {
        $pendingOrders = PickingOrder::with(['salesOrder.customer', 'warehouse'])
            ->where('status', 'pending')
            ->orderBy('priority')
            ->orderBy('picking_date')
            ->get();

        $warehouses = Warehouse::orderBy('name')->get();

        return view('outbound.picking-orders.wave', compact('pendingOrders', 'warehouses'));
    }

    /**
     * Generate batch picking orders (Wave Creation).
     */
    public function batchGenerate(Request $request)
    {
        // Log untuk debugging
        Log::info('Wave Creation Request', [
            'all_data' => $request->all(),
            'picking_order_ids' => $request->input('picking_order_ids'),
        ]);

        $validated = $request->validate([
            'picking_order_ids' => 'required|array|min:1',
            'picking_order_ids.*' => 'exists:picking_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'picking_date' => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
            'wave_name' => 'nullable|string|max:255',
        ], [
            'picking_order_ids.required' => 'Please select at least one picking order',
            'picking_order_ids.min' => 'Please select at least one picking order',
            'warehouse_id.required' => 'Please select a warehouse',
            'picking_date.required' => 'Please select a picking date',
        ]);

        DB::beginTransaction();
        try {
            $updatedCount = 0;
            $skippedCount = 0;
            
            foreach ($validated['picking_order_ids'] as $pickingOrderId) {
                $pickingOrder = PickingOrder::find($pickingOrderId);
                
                // Skip if order not found or not in pending status
                if (!$pickingOrder || $pickingOrder->status !== 'pending') {
                    $skippedCount++;
                    continue;
                }

                // Build notes with wave name
                $notes = '';
                if ($request->filled('wave_name')) {
                    $notes = 'Wave: ' . $validated['wave_name'];
                    if ($pickingOrder->notes) {
                        $notes .= ' | ' . $pickingOrder->notes;
                    }
                } else {
                    $notes = $pickingOrder->notes;
                }

                // Update existing picking order to wave type
                $pickingOrder->update([
                    'picking_type' => 'wave',
                    'warehouse_id' => $validated['warehouse_id'],
                    'picking_date' => $validated['picking_date'],
                    'assigned_to' => $validated['assigned_to'] ?? null,
                    'assigned_at' => $request->filled('assigned_to') ? now() : null,
                    'status' => $request->filled('assigned_to') ? 'assigned' : 'pending',
                    'notes' => $notes,
                    'updated_by' => Auth::id(),
                ]);

                $updatedCount++;
            }

            DB::commit();

            $message = "Wave created successfully! {$updatedCount} picking orders updated.";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} orders skipped - already processed)";
            }

            return redirect()->route('outbound.picking-orders.wave')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Wave Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to create wave: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Print picking order.
     */
    public function print(PickingOrder $pickingOrder)
    {
        $pickingOrder->load([
            'items' => function ($q) {
                $q->orderBy('pick_sequence');
            },
            'items.product',
            'items.storageBin',
            'salesOrder.customer',
            'warehouse',
            'assignedUser'
        ]);

        return view('outbound.picking-orders.print', compact('pickingOrder'));
    }
}