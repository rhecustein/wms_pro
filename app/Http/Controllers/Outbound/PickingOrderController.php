<?php

namespace App\Http\Controllers\Outbound;

use App\Http\Controllers\Controller;
use App\Models\PickingOrder;
use App\Models\PickingOrderItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PickingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
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

            $pickingOrders = $query->latest()->paginate(15)->withQueryString();

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
        } catch (\Exception $e) {
            Log::error('Error loading picking orders: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Failed to load picking orders. Please try again.');
        }
    }

    /**
     * Display pending picking orders.
     */
    public function pending()
    {
        try {
            $pickingOrders = PickingOrder::with(['salesOrder', 'warehouse', 'assignedUser'])
                ->where('status', 'pending')
                ->latest()
                ->paginate(15);

            $warehouses = Warehouse::orderBy('name')->get();
            $users = User::where('is_active', true)->orderBy('name')->get();

            return view('outbound.picking-orders.pending', compact('pickingOrders', 'warehouses', 'users'));
        } catch (\Exception $e) {
            Log::error('Error loading pending picking orders: ' . $e->getMessage());
            
            return redirect()->route('picking-orders.index')
                ->with('error', 'Failed to load pending orders. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
            
            $salesOrders = SalesOrder::where('status', 'confirmed')
                ->whereDoesntHave('pickingOrders', function ($q) {
                    $q->whereIn('status', ['pending', 'assigned', 'in_progress']);
                })
                ->orderBy('so_number', 'desc')
                ->get();
            
            $users = User::where('is_active', true)->orderBy('name')->get();

            if ($warehouses->isEmpty()) {
                return redirect()->route('picking-orders.index')
                    ->with('warning', 'No active warehouses available. Please create a warehouse first.');
            }

            if ($salesOrders->isEmpty()) {
                return redirect()->route('picking-orders.index')
                    ->with('warning', 'No confirmed sales orders available for picking.');
            }

            return view('outbound.picking-orders.create', compact('warehouses', 'salesOrders', 'users'));
        } catch (\Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            
            return redirect()->route('picking-orders.index')
                ->with('error', 'Failed to load form. Please try again.');
        }
    }

    /**
     * Get sales order items for picking (AJAX endpoint)
     */
    public function getSalesOrderItems(Request $request, $salesOrderId)
    {
        try {
            Log::info('Getting sales order items', [
                'sales_order_id' => $salesOrderId,
                'warehouse_id' => $request->warehouse_id
            ]);

            // Validate warehouse
            if (!$request->filled('warehouse_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warehouse ID is required'
                ], 400);
            }

            // Get Sales Order with items
            $salesOrder = SalesOrder::with(['items.product'])->findOrFail($salesOrderId);

            Log::info('Sales order found', [
                'so_number' => $salesOrder->so_number,
                'items_count' => $salesOrder->items->count()
            ]);

            // Map items with inventory data
            $items = $salesOrder->items->map(function ($item) use ($request) {
                $product = $item->product;
                
                if (!$product) {
                    Log::warning('Product not found for sales order item', [
                        'sales_order_item_id' => $item->id
                    ]);
                    return null;
                }

                // Get inventories for this product in the selected warehouse
                $inventories = Inventory::with('storageBin')
                    ->where('product_id', $product->id)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->where('quantity', '>', 0)
                    ->get();

                Log::info('Inventories found', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'warehouse_id' => $request->warehouse_id,
                    'inventories_count' => $inventories->count()
                ]);

                return [
                    'id' => $item->id,
                    'product_id' => $product->id,
                    'product_code' => $product->code ?? $product->sku ?? 'N/A',
                    'product_name' => $product->name,
                    'quantity_ordered' => $item->quantity,
                    'unit_of_measure' => $item->unit_of_measure ?? 'PCS',
                    'inventories' => $inventories->map(function ($inv) {
                        return [
                            'storage_bin_id' => $inv->storage_bin_id,
                            'storage_bin_name' => $inv->storageBin->name ?? $inv->storageBin->bin_code ?? 'Unknown',
                            'batch_number' => $inv->batch_number,
                            'serial_number' => $inv->serial_number,
                            'expiry_date' => $inv->expiry_date,
                            'quantity_available' => $inv->quantity,
                        ];
                    })->values()->all()
                ];
            })->filter()->values(); // Remove null items

            Log::info('Items processed', [
                'total_items' => $items->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $items
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Sales order not found', [
                'sales_order_id' => $salesOrderId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sales order not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error getting sales order items', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'sales_order_id' => $salesOrderId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load sales order items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'sales_order_id' => 'required|exists:sales_orders,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'picking_date' => 'required|date',
                'picking_type' => 'required|in:single_order,batch,wave,zone',
                'priority' => 'required|in:urgent,high,medium,low',
                'assigned_to' => 'nullable|exists:users,id',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.sales_order_item_id' => 'required|exists:sales_order_items,id',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.storage_bin_id' => 'required|exists:storage_bins,id',
                'items.*.quantity_requested' => 'required|integer|min:1',
                'items.*.batch_number' => 'nullable|string|max:100',
                'items.*.serial_number' => 'nullable|string|max:100',
                'items.*.expiry_date' => 'nullable|date',
                'items.*.unit_of_measure' => 'required|string|max:50',
            ], [
                'sales_order_id.required' => 'Please select a sales order',
                'warehouse_id.required' => 'Please select a warehouse',
                'picking_date.required' => 'Please select a picking date',
                'items.required' => 'Please add at least one item to pick',
                'items.min' => 'Please add at least one item to pick',
            ]);

            // Validate sales order status
            $salesOrder = SalesOrder::find($validated['sales_order_id']);
            if ($salesOrder->status !== 'confirmed') {
                throw ValidationException::withMessages([
                    'sales_order_id' => 'Selected sales order must be in confirmed status.'
                ]);
            }

            DB::beginTransaction();

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
                // Validate inventory availability
                $inventory = Inventory::where('product_id', $item['product_id'])
                    ->where('storage_bin_id', $item['storage_bin_id'])
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                if (!$inventory || $inventory->quantity < $item['quantity_requested']) {
                    throw new \Exception("Insufficient inventory for product ID {$item['product_id']} in selected storage bin.");
                }

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

            Log::info('Picking order created successfully', [
                'picking_order_id' => $pickingOrder->id,
                'picking_number' => $pickingOrder->picking_number,
                'created_by' => Auth::id()
            ]);

            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('success', 'Picking order created successfully!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create picking order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return back()->with('error', 'Failed to create picking order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PickingOrder $pickingOrder)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error loading picking order details: ' . $e->getMessage());
            
            return redirect()->route('picking-orders.index')
                ->with('error', 'Failed to load picking order details.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PickingOrder $pickingOrder)
    {
        try {
            if (!in_array($pickingOrder->status, ['pending', 'assigned'])) {
                return redirect()->route('picking-orders.show', $pickingOrder)
                    ->with('error', 'Only pending or assigned picking orders can be edited.');
            }

            $pickingOrder->load('items.product', 'items.storageBin');
            $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
            $salesOrders = SalesOrder::where('status', 'confirmed')->orderBy('so_number')->get();
            $users = User::where('is_active', true)->orderBy('name')->get();

            return view('outbound.picking-orders.edit', compact('pickingOrder', 'warehouses', 'salesOrders', 'users'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            
            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PickingOrder $pickingOrder)
    {
        try {
            if (!in_array($pickingOrder->status, ['pending', 'assigned'])) {
                return back()->with('error', 'Only pending or assigned picking orders can be updated.');
            }

            $validated = $request->validate([
                'picking_date' => 'required|date',
                'picking_type' => 'required|in:single_order,batch,wave,zone',
                'priority' => 'required|in:urgent,high,medium,low',
                'assigned_to' => 'nullable|exists:users,id',
                'notes' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

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

            Log::info('Picking order updated', [
                'picking_order_id' => $pickingOrder->id,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('success', 'Picking order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update picking order', [
                'picking_order_id' => $pickingOrder->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to update picking order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PickingOrder $pickingOrder)
    {
        try {
            if ($pickingOrder->status !== 'pending') {
                return back()->with('error', 'Only pending picking orders can be deleted.');
            }

            $pickingNumber = $pickingOrder->picking_number;
            $pickingOrder->delete();

            Log::info('Picking order deleted', [
                'picking_number' => $pickingNumber,
                'deleted_by' => Auth::id()
            ]);

            return redirect()->route('picking-orders.index')
                ->with('success', 'Picking order deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete picking order', [
                'picking_order_id' => $pickingOrder->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to delete picking order: ' . $e->getMessage());
        }
    }

    /**
     * Assign picking order to a user.
     */
    public function assign(Request $request, PickingOrder $pickingOrder)
    {
        try {
            $validated = $request->validate([
                'assigned_to' => 'required|exists:users,id',
            ]);

            if ($pickingOrder->status !== 'pending') {
                return back()->with('error', 'Only pending picking orders can be assigned.');
            }

            $pickingOrder->update([
                'assigned_to' => $validated['assigned_to'],
                'assigned_at' => now(),
                'status' => 'assigned',
                'updated_by' => Auth::id(),
            ]);

            Log::info('Picking order assigned', [
                'picking_order_id' => $pickingOrder->id,
                'assigned_to' => $validated['assigned_to'],
                'assigned_by' => Auth::id()
            ]);

            return back()->with('success', 'Picking order assigned successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to assign picking order', [
                'picking_order_id' => $pickingOrder->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to assign picking order: ' . $e->getMessage());
        }
    }

    /**
     * Start picking process.
     */
    public function start(PickingOrder $pickingOrder)
    {
        try {
            if (!in_array($pickingOrder->status, ['pending', 'assigned'])) {
                return back()->with('error', 'Only pending or assigned picking orders can be started.');
            }

            $pickingOrder->update([
                'status' => 'in_progress',
                'started_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            Log::info('Picking process started', [
                'picking_order_id' => $pickingOrder->id,
                'started_by' => Auth::id()
            ]);

            return redirect()->route('picking-orders.execute', $pickingOrder)
                ->with('success', 'Picking process started!');

        } catch (\Exception $e) {
            Log::error('Failed to start picking', [
                'picking_order_id' => $pickingOrder->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to start picking: ' . $e->getMessage());
        }
    }

    /**
     * Execute picking process.
     */
    public function execute(PickingOrder $pickingOrder)
    {
        try {
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

        } catch (\Exception $e) {
            Log::error('Error loading execute view: ' . $e->getMessage());
            
            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('error', 'Failed to load picking execution page.');
        }
    }

    /**
     * Complete picking order.
     */
    public function complete(PickingOrder $pickingOrder)
    {
        try {
            if ($pickingOrder->status !== 'in_progress') {
                return back()->with('error', 'Only in-progress picking orders can be completed.');
            }

            // Check if all items are picked
            $pendingItems = $pickingOrder->items()->where('status', 'pending')->count();
            if ($pendingItems > 0) {
                return back()->with('error', "Cannot complete picking order. {$pendingItems} items are still pending.");
            }

            $pickingOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            Log::info('Picking order completed', [
                'picking_order_id' => $pickingOrder->id,
                'completed_by' => Auth::id()
            ]);

            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('success', 'Picking order completed successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to complete picking order', [
                'picking_order_id' => $pickingOrder->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to complete picking order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel picking order.
     */
    public function cancel(PickingOrder $pickingOrder)
    {
        try {
            if ($pickingOrder->status === 'completed') {
                return back()->with('error', 'Completed picking orders cannot be cancelled.');
            }

            DB::beginTransaction();

            $pickingOrder->update([
                'status' => 'cancelled',
                'updated_by' => Auth::id(),
            ]);

            // Cancel all pending items
            $pickingOrder->items()->where('status', 'pending')->update(['status' => 'cancelled']);

            DB::commit();

            Log::info('Picking order cancelled', [
                'picking_order_id' => $pickingOrder->id,
                'cancelled_by' => Auth::id()
            ]);

            return back()->with('success', 'Picking order cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to cancel picking order', [
                'picking_order_id' => $pickingOrder->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to cancel picking order: ' . $e->getMessage());
        }
    }

    /**
     * Show wave picking interface.
     */
    public function wave()
    {
        try {
            $pendingOrders = PickingOrder::with(['salesOrder.customer', 'warehouse'])
                ->where('status', 'pending')
                ->orderBy('priority')
                ->orderBy('picking_date')
                ->get();

            $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
            $users = User::where('is_active', true)->orderBy('name')->get();

            return view('outbound.picking-orders.wave', compact('pendingOrders', 'warehouses', 'users'));

        } catch (\Exception $e) {
            Log::error('Error loading wave picking page: ' . $e->getMessage());
            
            return redirect()->route('picking-orders.index')
                ->with('error', 'Failed to load wave picking page.');
        }
    }

    /**
     * Generate batch picking orders (Wave Creation).
     */
    public function batchGenerate(Request $request)
    {
        try {
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

            $updatedCount = 0;
            $skippedCount = 0;
            $skippedOrders = [];
            
            foreach ($validated['picking_order_ids'] as $pickingOrderId) {
                $pickingOrder = PickingOrder::find($pickingOrderId);
                
                // Skip if order not found or not in pending status
                if (!$pickingOrder || $pickingOrder->status !== 'pending') {
                    $skippedCount++;
                    if ($pickingOrder) {
                        $skippedOrders[] = $pickingOrder->picking_number . ' (' . $pickingOrder->status . ')';
                    }
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

            Log::info('Wave created successfully', [
                'updated_count' => $updatedCount,
                'skipped_count' => $skippedCount,
                'created_by' => Auth::id()
            ]);

            $message = "Wave created successfully! {$updatedCount} picking order(s) updated.";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} order(s) skipped";
                if (!empty($skippedOrders)) {
                    $message .= ": " . implode(', ', $skippedOrders);
                }
                $message .= ")";
            }

            return redirect()->route('picking-orders.wave')
                ->with('success', $message);
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Wave Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
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
        try {
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

        } catch (\Exception $e) {
            Log::error('Error loading print view: ' . $e->getMessage());
            
            return redirect()->route('picking-orders.show', $pickingOrder)
                ->with('error', 'Failed to load print view.');
        }
    }
}