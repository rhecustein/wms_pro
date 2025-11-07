<?php

namespace App\Http\Controllers\Outbound;

use App\Http\Controllers\Controller;
use App\Models\ReturnOrder;
use App\Models\ReturnOrderItem;
use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StorageBin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Exception;

class ReturnOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = ReturnOrder::with(['warehouse', 'customer', 'deliveryOrder', 'salesOrder', 'inspectedBy', 'receivedBy'])
                ->orderBy('created_at', 'desc');

            // Search
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            // Status Filter
            if ($request->filled('status')) {
                $query->status($request->status);
            }

            // Return Type Filter
            if ($request->filled('return_type')) {
                $query->returnType($request->return_type);
            }

            // Warehouse Filter
            if ($request->filled('warehouse_id')) {
                $query->warehouse($request->warehouse_id);
            }

            // Date Range Filter
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->dateRange($request->date_from, $request->date_to);
            } elseif ($request->filled('date_from')) {
                $query->whereDate('return_date', '>=', $request->date_from);
            } elseif ($request->filled('date_to')) {
                $query->whereDate('return_date', '<=', $request->date_to);
            }

            $returnOrders = $query->paginate(15)->withQueryString();

            $statuses = ['pending', 'received', 'inspected', 'restocked', 'disposed', 'cancelled'];
            $returnTypes = ['customer_return', 'damaged', 'expired', 'wrong_item'];
            $warehouses = Warehouse::all();

            return view('outbound.returns.index', compact('returnOrders', 'statuses', 'returnTypes', 'warehouses'));
            
        } catch (Exception $e) {
            Log::error('Error fetching return orders: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('outbound.returns.index', [
                'returnOrders' => collect([]),
                'statuses' => ['pending', 'received', 'inspected', 'restocked', 'disposed', 'cancelled'],
                'returnTypes' => ['customer_return', 'damaged', 'expired', 'wrong_item'],
                'warehouses' => collect([])
            ])->with('error', 'Failed to load return orders. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $deliveryOrders = DeliveryOrder::where('status', 'delivered')
                ->with(['customer', 'warehouse'])
                ->orderBy('created_at', 'desc')
                ->get();
            $warehouses = Warehouse::orderBy('name')->get();
            $customers = Customer::orderBy('name')->get();
            $products = Product::where('status', 'active')->orderBy('name')->get();
            
            return view('outbound.returns.create', compact('deliveryOrders', 'warehouses', 'customers', 'products'));
            
        } catch (Exception $e) {
            Log::error('Error loading create return order form: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('outbound.returns.index')
                ->with('error', 'Failed to load form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'delivery_order_id' => 'nullable|exists:delivery_orders,id',
                'sales_order_id' => 'nullable|exists:sales_orders,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'customer_id' => 'required|exists:customers,id',
                'return_date' => 'required|date',
                'return_type' => 'required|in:customer_return,damaged,expired,wrong_item',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity_returned' => 'required|integer|min:1',
                'items.*.batch_number' => 'nullable|string|max:100',
                'items.*.serial_number' => 'nullable|string|max:100',
                'items.*.return_reason' => 'nullable|string|max:500',
                'items.*.condition' => 'required|in:good,damaged,expired,defective',
                'items.*.unit_price' => 'nullable|numeric|min:0',
            ], [
                'warehouse_id.required' => 'Please select a warehouse.',
                'warehouse_id.exists' => 'The selected warehouse is invalid.',
                'customer_id.required' => 'Please select a customer.',
                'customer_id.exists' => 'The selected customer is invalid.',
                'return_date.required' => 'Return date is required.',
                'return_date.date' => 'Please provide a valid date.',
                'return_type.required' => 'Please select a return type.',
                'return_type.in' => 'Invalid return type selected.',
                'items.required' => 'At least one item is required.',
                'items.min' => 'At least one item is required.',
                'items.*.product_id.required' => 'Product is required for all items.',
                'items.*.product_id.exists' => 'One or more selected products are invalid.',
                'items.*.quantity_returned.required' => 'Quantity is required for all items.',
                'items.*.quantity_returned.min' => 'Quantity must be at least 1.',
                'items.*.condition.required' => 'Condition is required for all items.',
                'items.*.condition.in' => 'Invalid condition selected.',
            ]);

            DB::beginTransaction();

            // Verify warehouse exists and is active
            $warehouse = Warehouse::find($validated['warehouse_id']);
            if (!$warehouse) {
                throw new Exception('Warehouse not found.');
            }

            // Verify customer exists and is active
            $customer = Customer::find($validated['customer_id']);
            if (!$customer) {
                throw new Exception('Customer not found.');
            }

            // Create Return Order (return_number will be auto-generated by model)
            $returnOrder = ReturnOrder::create([
                'delivery_order_id' => $validated['delivery_order_id'] ?? null,
                'sales_order_id' => $validated['sales_order_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'],
                'customer_id' => $validated['customer_id'],
                'return_date' => $validated['return_date'],
                'return_type' => $validated['return_type'],
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            if (!$returnOrder) {
                throw new Exception('Failed to create return order.');
            }

            // Create Return Order Items
            foreach ($validated['items'] as $item) {
                // Verify product exists
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new Exception("Product with ID {$item['product_id']} not found.");
                }

                $returnItem = ReturnOrderItem::create([
                    'return_order_id' => $returnOrder->id,
                    'product_id' => $item['product_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'quantity_returned' => $item['quantity_returned'],
                    'return_reason' => $item['return_reason'] ?? null,
                    'condition' => $item['condition'],
                    'unit_price' => $item['unit_price'] ?? $product->price ?? 0,
                ]);

                if (!$returnItem) {
                    throw new Exception('Failed to create return order item.');
                }
            }

            // Totals will be auto-calculated by model events
            $returnOrder->calculateTotals();

            DB::commit();

            Log::info('Return order created successfully', [
                'return_id' => $returnOrder->id,
                'return_number' => $returnOrder->return_number,
                'user_id' => Auth::id(),
                'customer_id' => $validated['customer_id'],
                'total_items' => $returnOrder->total_items
            ]);

            return redirect()->route('outbound.returns.show', $returnOrder)
                ->with('success', 'Return Order created successfully! Return Number: ' . $returnOrder->return_number);

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating return order: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token']),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Failed to create return order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ReturnOrder $return)
    {
        try {
            $return->load([
                'warehouse', 
                'customer', 
                'deliveryOrder.customer', 
                'salesOrder', 
                'items.product', 
                'items.restockedToBin', 
                'items.quarantineBin',
                'inspectedBy', 
                'receivedBy',
                'createdBy',
                'updatedBy'
            ]);
            
            return view('outbound.returns.show', compact('return'));
            
        } catch (Exception $e) {
            Log::error('Error displaying return order: ' . $e->getMessage(), [
                'return_id' => $return->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('outbound.returns.index')
                ->with('error', 'Failed to load return order details.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReturnOrder $return)
    {
        try {
            if (!$return->can_edit) {
                return redirect()->back()
                    ->with('error', 'Only pending returns can be edited.');
            }

            $return->load('items.product');
            $deliveryOrders = DeliveryOrder::where('status', 'delivered')
                ->with(['customer', 'warehouse'])
                ->orderBy('created_at', 'desc')
                ->get();
            $warehouses = Warehouse::orderBy('name')->get();
            $customers = Customer::orderBy('name')->get();
            $products = Product::where('status', 'active')->orderBy('name')->get();
            
            return view('outbound.returns.edit', compact('return', 'deliveryOrders', 'warehouses', 'customers', 'products'));
            
        } catch (Exception $e) {
            Log::error('Error loading edit return order form: ' . $e->getMessage(), [
                'return_id' => $return->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('outbound.returns.index')
                ->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReturnOrder $return)
    {
        try {
            if (!$return->can_edit) {
                return back()->with('error', 'Only pending returns can be updated.');
            }

            $validated = $request->validate([
                'return_date' => 'required|date',
                'return_type' => 'required|in:customer_return,damaged,expired,wrong_item',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity_returned' => 'required|integer|min:1',
                'items.*.batch_number' => 'nullable|string|max:100',
                'items.*.serial_number' => 'nullable|string|max:100',
                'items.*.return_reason' => 'nullable|string|max:500',
                'items.*.condition' => 'required|in:good,damaged,expired,defective',
                'items.*.unit_price' => 'nullable|numeric|min:0',
            ], [
                'return_date.required' => 'Return date is required.',
                'return_type.required' => 'Please select a return type.',
                'items.required' => 'At least one item is required.',
                'items.min' => 'At least one item is required.',
            ]);

            DB::beginTransaction();

            // Update Return Order
            $return->update([
                'return_date' => $validated['return_date'],
                'return_type' => $validated['return_type'],
                'notes' => $validated['notes'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            // Delete old items
            $return->items()->delete();

            // Create new items
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new Exception("Product with ID {$item['product_id']} not found.");
                }

                ReturnOrderItem::create([
                    'return_order_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'quantity_returned' => $item['quantity_returned'],
                    'return_reason' => $item['return_reason'] ?? null,
                    'condition' => $item['condition'],
                    'unit_price' => $item['unit_price'] ?? $product->price ?? 0,
                ]);
            }

            // Recalculate totals
            $return->calculateTotals();

            DB::commit();

            Log::info('Return order updated successfully', [
                'return_id' => $return->id,
                'return_number' => $return->return_number,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('outbound.returns.show', $return)
                ->with('success', 'Return Order updated successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating return order: ' . $e->getMessage(), [
                'return_id' => $return->id ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Failed to update return order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReturnOrder $return)
    {
        try {
            if (!$return->can_delete) {
                return back()->with('error', 'Only pending returns can be deleted.');
            }

            DB::beginTransaction();

            $returnNumber = $return->return_number;
            
            // Delete related items first (will be cascaded by FK, but explicit is better)
            $return->items()->delete();
            
            // Soft delete the return order
            $return->delete();

            DB::commit();

            Log::info('Return order deleted successfully', [
                'return_number' => $returnNumber,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('outbound.returns.index')
                ->with('success', 'Return Order deleted successfully!');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting return order: ' . $e->getMessage(), [
                'return_id' => $return->id ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to delete return order. Please try again.');
        }
    }

    /**
     * Mark return as received
     */
    public function receive(Request $request, ReturnOrder $return)
    {
        try {
            // Allow receiving from both 'pending' and 'received' status (prevent double-click)
            if (!in_array($return->status, ['pending', 'received'])) {
                return back()->with('error', 'Only pending returns can be received.');
            }

            // If already received, just show info message
            if ($return->status === 'received') {
                return back()->with('info', 'Return Order is already marked as received!');
            }

            DB::beginTransaction();

            // Use model method
            $success = $return->markAsReceived(Auth::id());

            if (!$success) {
                throw new Exception('Failed to mark return as received.');
            }

            DB::commit();

            Log::info('Return order marked as received', [
                'return_id' => $return->id,
                'return_number' => $return->return_number,
                'user_id' => Auth::id()
            ]);

            return back()->with('success', 'Return Order marked as received successfully!');
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error receiving return order: ' . $e->getMessage(), [
                'return_id' => $return->id ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to receive return. Please try again.');
        }
    }

    /**
     * Inspect return items
     */
    public function inspect(Request $request, ReturnOrder $return)
    {
        try {
            if (!$return->can_inspect) {
                return back()->with('error', 'Only received returns can be inspected.');
            }

            $validated = $request->validate([
                'disposition' => 'required|in:restock,quarantine,dispose,rework',
                'items' => 'required|array',
                'items.*.item_id' => 'required|exists:return_order_items,id',
                'items.*.disposition' => 'required|in:restock,quarantine,dispose,rework,return_to_supplier',
                'items.*.inspection_notes' => 'nullable|string|max:500',
            ], [
                'disposition.required' => 'Please select a disposition.',
                'items.required' => 'Items data is required.',
                'items.*.item_id.exists' => 'One or more items are invalid.',
                'items.*.disposition.required' => 'Disposition is required for all items.',
            ]);

            DB::beginTransaction();

            foreach ($validated['items'] as $itemData) {
                $item = ReturnOrderItem::where('id', $itemData['item_id'])
                    ->where('return_order_id', $return->id)
                    ->first();
                
                if (!$item) {
                    throw new Exception("Item with ID {$itemData['item_id']} not found in this return order.");
                }

                // Use model method
                $item->markAsInspected(
                    $itemData['disposition'], 
                    $itemData['inspection_notes'] ?? null
                );
            }

            // Use model method
            $success = $return->markAsInspected(Auth::id(), $validated['disposition']);

            if (!$success) {
                throw new Exception('Failed to mark return as inspected.');
            }

            DB::commit();

            Log::info('Return order inspected successfully', [
                'return_id' => $return->id,
                'return_number' => $return->return_number,
                'disposition' => $validated['disposition'],
                'user_id' => Auth::id()
            ]);

            return back()->with('success', 'Return Order inspected successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error inspecting return order: ' . $e->getMessage(), [
                'return_id' => $return->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to inspect return: ' . $e->getMessage());
        }
    }

    /**
     * Restock return items
     */
    public function restock(Request $request, ReturnOrder $return)
    {
        try {
            if (!$return->can_restock) {
                return back()->with('error', 'Only inspected returns can be restocked.');
            }

            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.item_id' => 'required|exists:return_order_items,id',
                'items.*.storage_bin_id' => 'required|exists:storage_bins,id',
                'items.*.quantity' => 'required|integer|min:1',
            ], [
                'items.required' => 'Items data is required.',
                'items.*.storage_bin_id.required' => 'Storage bin is required for all items.',
                'items.*.storage_bin_id.exists' => 'One or more storage bins are invalid.',
                'items.*.quantity.required' => 'Quantity is required for all items.',
                'items.*.quantity.min' => 'Quantity must be at least 1.',
            ]);

            DB::beginTransaction();

            foreach ($validated['items'] as $itemData) {
                $item = ReturnOrderItem::where('id', $itemData['item_id'])
                    ->where('return_order_id', $return->id)
                    ->first();
                
                if (!$item) {
                    throw new Exception("Item with ID {$itemData['item_id']} not found in this return order.");
                }

                // Verify quantity
                if ($itemData['quantity'] > $item->quantity_pending) {
                    throw new Exception("Restock quantity cannot exceed pending quantity for item ID {$itemData['item_id']}.");
                }

                // Verify storage bin
                $storageBin = StorageBin::find($itemData['storage_bin_id']);
                if (!$storageBin) {
                    throw new Exception("Storage bin with ID {$itemData['storage_bin_id']} not found.");
                }

                // Use model method
                $success = $item->restockTo($itemData['storage_bin_id'], $itemData['quantity']);
                
                if (!$success) {
                    throw new Exception("Failed to restock item ID {$itemData['item_id']}.");
                }

                // TODO: Create inventory transaction
                // InventoryTransaction::create([...]);
            }

            // Use model method
            $success = $return->markAsRestocked(Auth::id());

            if (!$success) {
                throw new Exception('Failed to mark return as restocked.');
            }

            DB::commit();

            Log::info('Return items restocked successfully', [
                'return_id' => $return->id,
                'return_number' => $return->return_number,
                'user_id' => Auth::id()
            ]);

            return back()->with('success', 'Return items restocked successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error restocking return items: ' . $e->getMessage(), [
                'return_id' => $return->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to restock items: ' . $e->getMessage());
        }
    }

    /**
     * Cancel return order
     */
    public function cancel(Request $request, ReturnOrder $return)
    {
        try {
            if (!$return->can_cancel) {
                return back()->with('error', 'Cannot cancel this return order.');
            }

            DB::beginTransaction();

            // Use model method
            $success = $return->cancel(Auth::id());

            if (!$success) {
                throw new Exception('Failed to cancel return order.');
            }

            DB::commit();

            Log::info('Return order cancelled', [
                'return_id' => $return->id,
                'return_number' => $return->return_number,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('outbound.returns.index')
                ->with('success', 'Return Order cancelled successfully!');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error cancelling return order: ' . $e->getMessage(), [
                'return_id' => $return->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to cancel return. Please try again.');
        }
    }

    /**
     * Print return order
     */
    public function print(ReturnOrder $return)
    {
        try {
            $return->load([
                'warehouse', 
                'customer', 
                'deliveryOrder',
                'salesOrder',
                'items.product',
                'items.restockedToBin',
                'items.quarantineBin',
                'inspectedBy',
                'receivedBy',
                'createdBy'
            ]);
            
            return view('outbound.returns.print', compact('return'));
            
        } catch (Exception $e) {
            Log::error('Error loading print view: ' . $e->getMessage(), [
                'return_id' => $return->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('outbound.returns.show', $return)
                ->with('error', 'Failed to load print view.');
        }
    }
}