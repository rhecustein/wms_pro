<?php

namespace App\Http\Controllers\Outbound;

use App\Http\Controllers\Controller;
use App\Models\PickingOrder;
use App\Models\PickingOrderItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Warehouse;
use App\Models\WarehouseLocation; // FIXED: Ganti StorageBin dengan WarehouseLocation
use App\Models\User;
use App\Models\Product;
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
            $query = PickingOrder::with(['salesOrder.customer', 'warehouse', 'assignedUser', 'createdBy']);

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

            if ($request->filled('status')) $query->where('status', $request->status);
            if ($request->filled('priority')) $query->where('priority', $request->priority);
            if ($request->filled('picking_type')) $query->where('picking_type', $request->picking_type);
            if ($request->filled('warehouse_id')) $query->where('warehouse_id', $request->warehouse_id);
            if ($request->filled('date_from')) $query->whereDate('picking_date', '>=', $request->date_from);
            if ($request->filled('date_to')) $query->whereDate('picking_date', '<=', $request->date_to);

            $pickingOrders = $query->latest()->paginate(15)->withQueryString();

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
            Log::error('Error loading picking orders: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load picking orders. Please try again.');
        }
    }

    public function pending()
    {
        try {
            $pickingOrders = PickingOrder::with(['salesOrder.customer', 'warehouse', 'assignedUser'])
                ->where('status', 'pending')
                ->latest()
                ->paginate(15);

            $warehouses = Warehouse::orderBy('name')->get();
            $users = User::where('is_active', true)->orderBy('name')->get();

            return view('outbound.picking-orders.pending', compact('pickingOrders', 'warehouses', 'users'));
        } catch (\Exception $e) {
            Log::error('Error loading pending picking orders: ' . $e->getMessage());
            return redirect()->route('outbound.picking-orders.index')
                ->with('error', 'Failed to load pending orders. Please try again.');
        }
    }

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
                return redirect()->route('outbound.picking-orders.index')
                    ->with('warning', 'No active warehouses available. Please create a warehouse first.');
            }

            if ($salesOrders->isEmpty()) {
                return redirect()->route('outbound.picking-orders.index')
                    ->with('warning', 'No confirmed sales orders available for picking.');
            }

            return view('outbound.picking-orders.create', compact('warehouses', 'salesOrders', 'users'));
        } catch (\Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            return redirect()->route('outbound.picking-orders.index')
                ->with('error', 'Failed to load form. Please try again.');
        }
    }

    /**
     * Get sales order items for picking (AJAX endpoint)
     * OPTIMIZED & FIXED
     */
    public function getSalesOrderItems(Request $request, $salesOrderId)
    {
        try {
            if (!$request->filled('warehouse_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warehouse ID is required'
                ], 400);
            }

            $warehouseId = $request->warehouse_id;

            // Get Sales Order with items
            $salesOrder = SalesOrder::with([
                'items' => function($q) {
                    $q->select('id', 'sales_order_id', 'product_id', 'quantity_ordered', 'quantity_picked', 'unit_of_measure');
                }, 
                'items.product' => function($q) {
                    $q->select('id', 'name', 'sku', 'barcode');
                }
            ])->findOrFail($salesOrderId);

            // Get product IDs for bulk query
            $productIds = $salesOrder->items->pluck('product_id')->toArray();

            if (empty($productIds)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // OPTIMIZED: Get all inventories in ONE query with warehouse locations
            $inventories = Inventory::select([
                    'id', 'product_id', 'location_id', 'quantity_available', 
                    'batch_number', 'lot_number', 'serial_number', 'expiry_date'
                ])
                ->whereIn('product_id', $productIds)
                ->where('warehouse_id', $warehouseId)
                ->where('quantity_available', '>', 0)
                ->where('stock_status', 'in_stock')
                ->where('quality_status', 'good')
                ->with(['location' => function($q) {
                    $q->select('id', 'name', 'code', 'warehouse_id');
                }])
                ->orderBy('expiry_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get()
                ->groupBy('product_id');

            // Map items with inventory data
            $items = $salesOrder->items->map(function ($item) use ($inventories) {
                $product = $item->product;
                
                if (!$product) return null;

                // Get inventories for this specific product
                $productInventories = $inventories->get($product->id, collect());

                // Hitung remaining quantity yang belum dipick
                $remainingQuantity = $item->quantity_ordered - $item->quantity_picked;

                return [
                    'id' => $item->id,
                    'product_id' => $product->id,
                    'product_code' => $product->sku ?? $product->barcode ?? 'N/A',
                    'product_name' => $product->name,
                    'quantity_ordered' => (int) $item->quantity_ordered,
                    'quantity_picked' => (int) $item->quantity_picked,
                    'remaining_quantity' => (int) $remainingQuantity,
                    'unit_of_measure' => $item->unit_of_measure ?? 'PCS',
                    'inventories' => $productInventories->map(function ($inv) {
                        $locationName = 'Unknown';
                        $locationCode = '';
                        
                        if ($inv->location) {
                            $locationName = $inv->location->name ?? 'Unknown';
                            $locationCode = $inv->location->code ?? '';
                        }

                        return [
                            'location_id' => $inv->location_id,
                            'storage_bin_name' => $locationCode ? "{$locationCode} - {$locationName}" : $locationName,
                            'batch_number' => $inv->batch_number ?? '',
                            'lot_number' => $inv->lot_number ?? '',
                            'serial_number' => $inv->serial_number ?? '',
                            'expiry_date' => $inv->expiry_date ? $inv->expiry_date->format('Y-m-d') : '',
                            'quantity_available' => (float) $inv->quantity_available,
                        ];
                    })->values()->all()
                ];
            })->filter()->values();

            return response()->json([
                'success' => true,
                'data' => $items,
                'debug' => [
                    'total_items' => $items->count(),
                    'warehouse_id' => $warehouseId,
                    'product_ids' => $productIds
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sales order not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error getting sales order items', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'sales_order_id' => $salesOrderId,
                'warehouse_id' => $request->warehouse_id ?? null,
                'trace' => $e->getTraceAsString()
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
                'items.*.location_id' => 'required|exists:warehouse_locations,id',
                'items.*.quantity_requested' => 'required|numeric|min:0.01',
                'items.*.batch_number' => 'nullable|string|max:100',
                'items.*.lot_number' => 'nullable|string|max:100',
                'items.*.serial_number' => 'nullable|string|max:100',
                'items.*.expiry_date' => 'nullable|date',
                'items.*.unit_of_measure' => 'required|string|max:50',
            ], [
                'sales_order_id.required' => 'Please select a sales order',
                'warehouse_id.required' => 'Please select a warehouse',
                'picking_date.required' => 'Please select a picking date',
                'items.required' => 'Please add at least one item to pick',
                'items.min' => 'Please add at least one item to pick',
                'items.*.location_id.required' => 'Please select a location for all items',
                'items.*.location_id.exists' => 'Selected location is invalid',
            ]);

            // Validate sales order status
            $salesOrder = SalesOrder::findOrFail($validated['sales_order_id']);
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
                    ->where('location_id', $item['location_id'])
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->where('stock_status', 'in_stock')
                    ->where('quality_status', 'good')
                    ->first();

                if (!$inventory || $inventory->quantity_available < $item['quantity_requested']) {
                    throw new \Exception("Insufficient inventory for product ID {$item['product_id']} in selected location. Available: " . ($inventory->quantity_available ?? 0));
                }

                // Validate quantity_requested tidak melebihi remaining quantity
                $salesOrderItem = SalesOrderItem::find($item['sales_order_item_id']);
                $remainingQty = $salesOrderItem->quantity_ordered - $salesOrderItem->quantity_picked;
                
                if ($item['quantity_requested'] > $remainingQty) {
                    $product = Product::find($item['product_id']);
                    throw new \Exception("Requested quantity ({$item['quantity_requested']}) exceeds remaining quantity ({$remainingQty}) for product: " . ($product->name ?? 'Unknown'));
                }

                PickingOrderItem::create([
                    'picking_order_id' => $pickingOrder->id,
                    'sales_order_item_id' => $item['sales_order_item_id'],
                    'product_id' => $item['product_id'],
                    'storage_bin_id' => $item['location_id'], // FIXED: location_id disimpan di storage_bin_id
                    'batch_number' => $item['batch_number'] ?? null,
                    'lot_number' => $item['lot_number'] ?? null,
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

            return redirect()->route('outbound.picking-orders.show', $pickingOrder)
                ->with('success', 'Picking order created successfully!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create picking order', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user_id' => Auth::id()
            ]);
            
            return back()->with('error', 'Failed to create picking order: ' . $e->getMessage())
                ->withInput();
        }
    }

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
                'items.storageBin', // Ini akan load WarehouseLocation via relasi
                'items.pickedBy'
            ]);

            return view('outbound.picking-orders.show', compact('pickingOrder'));
        } catch (\Exception $e) {
            Log::error('Error loading picking order details: ' . $e->getMessage());
            return redirect()->route('outbound.picking-orders.index')
                ->with('error', 'Failed to load picking order details.');
        }
    }

    public function edit(PickingOrder $pickingOrder)
    {
        try {
            if (!in_array($pickingOrder->status, ['pending', 'assigned'])) {
                return redirect()->route('outbound.picking-orders.show', $pickingOrder)
                    ->with('error', 'Only pending or assigned picking orders can be edited.');
            }

            $pickingOrder->load('items.product', 'items.storageBin');
            $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
            $salesOrders = SalesOrder::where('status', 'confirmed')->orderBy('so_number')->get();
            $users = User::where('is_active', true)->orderBy('name')->get();

            return view('outbound.picking-orders.edit', compact('pickingOrder', 'warehouses', 'salesOrders', 'users'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->route('outbound.picking-orders.show', $pickingOrder)
                ->with('error', 'Failed to load edit form.');
        }
    }

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

            return redirect()->route('outbound.picking-orders.show', $pickingOrder)
                ->with('success', 'Picking order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update picking order: ' . $e->getMessage());
            return back()->with('error', 'Failed to update picking order: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(PickingOrder $pickingOrder)
    {
        try {
            if ($pickingOrder->status !== 'pending') {
                return back()->with('error', 'Only pending picking orders can be deleted.');
            }

            $pickingNumber = $pickingOrder->picking_number;
            $pickingOrder->delete();

            return redirect()->route('outbound.picking-orders.index')
                ->with('success', "Picking order {$pickingNumber} deleted successfully!");

        } catch (\Exception $e) {
            Log::error('Failed to delete picking order: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete picking order: ' . $e->getMessage());
        }
    }

    // ... methods lainnya tetap sama ...

    public function assign(Request $request, PickingOrder $pickingOrder)
    {
        try {
            $validated = $request->validate(['assigned_to' => 'required|exists:users,id']);

            if ($pickingOrder->status !== 'pending') {
                return back()->with('error', 'Only pending picking orders can be assigned.');
            }

            $pickingOrder->update([
                'assigned_to' => $validated['assigned_to'],
                'assigned_at' => now(),
                'status' => 'assigned',
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Picking order assigned successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to assign picking order: ' . $e->getMessage());
            return back()->with('error', 'Failed to assign picking order: ' . $e->getMessage());
        }
    }

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

            return redirect()->route('outbound.picking-orders.execute', $pickingOrder)
                ->with('success', 'Picking process started!');

        } catch (\Exception $e) {
            Log::error('Failed to start picking: ' . $e->getMessage());
            return back()->with('error', 'Failed to start picking: ' . $e->getMessage());
        }
    }

    public function execute(PickingOrder $pickingOrder)
    {
        try {
            if ($pickingOrder->status !== 'in_progress') {
                return redirect()->route('outbound.picking-orders.show', $pickingOrder)
                    ->with('error', 'Only in-progress picking orders can be executed.');
            }

            $pickingOrder->load([
                'items' => function ($q) { $q->orderBy('pick_sequence'); },
                'items.product',
                'items.storageBin',
                'salesOrder',
                'warehouse'
            ]);

            return view('outbound.picking-orders.execute', compact('pickingOrder'));

        } catch (\Exception $e) {
            Log::error('Error loading execute view: ' . $e->getMessage());
            return redirect()->route('outbound.picking-orders.show', $pickingOrder)
                ->with('error', 'Failed to load picking execution page.');
        }
    }

    public function complete(PickingOrder $pickingOrder)
    {
        try {
            if ($pickingOrder->status !== 'in_progress') {
                return back()->with('error', 'Only in-progress picking orders can be completed.');
            }

            $pendingItems = $pickingOrder->items()->where('status', 'pending')->count();
            if ($pendingItems > 0) {
                return back()->with('error', "Cannot complete picking order. {$pendingItems} items are still pending.");
            }

            $pickingOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('outbound.picking-orders.show', $pickingOrder)
                ->with('success', 'Picking order completed successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to complete picking order: ' . $e->getMessage());
            return back()->with('error', 'Failed to complete picking order: ' . $e->getMessage());
        }
    }

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

            $pickingOrder->items()->where('status', 'pending')->update(['status' => 'cancelled']);

            DB::commit();

            return back()->with('success', 'Picking order cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel picking order: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel picking order: ' . $e->getMessage());
        }
    }

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
            return redirect()->route('outbound.picking-orders.index')
                ->with('error', 'Failed to load wave picking page.');
        }
    }

    public function batchGenerate(Request $request)
    {
        try {
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
                
                if (!$pickingOrder || $pickingOrder->status !== 'pending') {
                    $skippedCount++;
                    if ($pickingOrder) {
                        $skippedOrders[] = $pickingOrder->picking_number . ' (' . $pickingOrder->status . ')';
                    }
                    continue;
                }

                $notes = '';
                if ($request->filled('wave_name')) {
                    $notes = 'Wave: ' . $validated['wave_name'];
                    if ($pickingOrder->notes) {
                        $notes .= ' | ' . $pickingOrder->notes;
                    }
                } else {
                    $notes = $pickingOrder->notes;
                }

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

            $message = "Wave created successfully! {$updatedCount} picking order(s) updated.";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} order(s) skipped";
                if (!empty($skippedOrders)) {
                    $message .= ": " . implode(', ', $skippedOrders);
                }
                $message .= ")";
            }

            return redirect()->route('outbound.picking-orders.wave')
                ->with('success', $message);
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wave Creation Failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create wave: ' . $e->getMessage())->withInput();
        }
    }

    public function print(PickingOrder $pickingOrder)
    {
        try {
            $pickingOrder->load([
                'items' => function ($q) { $q->orderBy('pick_sequence'); },
                'items.product',
                'items.storageBin',
                'salesOrder.customer',
                'warehouse',
                'assignedUser'
            ]);

            return view('outbound.picking-orders.print', compact('pickingOrder'));

        } catch (\Exception $e) {
            Log::error('Error loading print view: ' . $e->getMessage());
            return redirect()->route('outbound.picking-orders.show', $pickingOrder)
                ->with('error', 'Failed to load print view.');
        }
    }
}