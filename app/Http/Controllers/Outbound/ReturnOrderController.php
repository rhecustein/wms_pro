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
use Carbon\Carbon;

class ReturnOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ReturnOrder::with(['warehouse', 'customer', 'deliveryOrder', 'inspectedBy'])
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Return Type Filter
        if ($request->filled('return_type')) {
            $query->where('return_type', $request->return_type);
        }

        // Warehouse Filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }

        $returnOrders = $query->paginate(15)->withQueryString();

        $statuses = ['pending', 'received', 'inspected', 'restocked', 'disposed', 'cancelled'];
        $returnTypes = ['customer_return', 'damaged', 'expired', 'wrong_item'];
        $warehouses = Warehouse::all();

        return view('outbound.returns.index', compact('returnOrders', 'statuses', 'returnTypes', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $deliveryOrders = DeliveryOrder::where('status', 'delivered')->get();
        $warehouses = Warehouse::all();
        $customers = Customer::all();
        $products = Product::all();
        
        return view('outbound.returns.create', compact('deliveryOrders', 'warehouses', 'customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_order_id' => 'nullable|exists:delivery_orders,id',
            'sales_order_id' => 'nullable|exists:sales_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'required|exists:customers,id',
            'return_date' => 'required|date',
            'return_type' => 'required|in:customer_return,damaged,expired,wrong_item',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_returned' => 'required|integer|min:1',
            'items.*.batch_number' => 'nullable|string',
            'items.*.serial_number' => 'nullable|string',
            'items.*.return_reason' => 'nullable|string',
            'items.*.condition' => 'required|in:good,damaged,expired',
        ]);

        DB::beginTransaction();
        try {
            // Generate Return Number
            $returnNumber = $this->generateReturnNumber();

            // Create Return Order
            $returnOrder = ReturnOrder::create([
                'return_number' => $returnNumber,
                'delivery_order_id' => $validated['delivery_order_id'],
                'sales_order_id' => $validated['sales_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'customer_id' => $validated['customer_id'],
                'return_date' => $validated['return_date'],
                'return_type' => $validated['return_type'],
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Create Return Order Items
            $totalItems = 0;
            $totalQuantity = 0;

            foreach ($validated['items'] as $item) {
                ReturnOrderItem::create([
                    'return_order_id' => $returnOrder->id,
                    'product_id' => $item['product_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'quantity_returned' => $item['quantity_returned'],
                    'return_reason' => $item['return_reason'] ?? null,
                    'condition' => $item['condition'],
                ]);

                $totalItems++;
                $totalQuantity += $item['quantity_returned'];
            }

            // Update totals
            $returnOrder->update([
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
            ]);

            DB::commit();
            return redirect()->route('outbound.returns.show', $returnOrder)
                ->with('success', 'Return Order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create return order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ReturnOrder $return)
    {
        $return->load(['warehouse', 'customer', 'deliveryOrder', 'salesOrder', 'items.product', 'items.restockedToBin', 'inspectedBy', 'createdBy']);
        
        return view('outbound.returns.show', compact('return'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReturnOrder $return)
    {
        if (!in_array($return->status, ['pending'])) {
            return back()->with('error', 'Only pending returns can be edited.');
        }

        $return->load('items.product');
        $deliveryOrders = DeliveryOrder::where('status', 'delivered')->get();
        $warehouses = Warehouse::all();
        $customers = Customer::all();
        $products = Product::all();
        
        return view('outbound.returns.edit', compact('return', 'deliveryOrders', 'warehouses', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReturnOrder $return)
    {
        if (!in_array($return->status, ['pending'])) {
            return back()->with('error', 'Only pending returns can be updated.');
        }

        $validated = $request->validate([
            'return_date' => 'required|date',
            'return_type' => 'required|in:customer_return,damaged,expired,wrong_item',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_returned' => 'required|integer|min:1',
            'items.*.batch_number' => 'nullable|string',
            'items.*.return_reason' => 'nullable|string',
            'items.*.condition' => 'required|in:good,damaged,expired',
        ]);

        DB::beginTransaction();
        try {
            // Update Return Order
            $return->update([
                'return_date' => $validated['return_date'],
                'return_type' => $validated['return_type'],
                'notes' => $validated['notes'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            // Delete old items and create new ones
            $return->items()->delete();

            $totalItems = 0;
            $totalQuantity = 0;

            foreach ($validated['items'] as $item) {
                ReturnOrderItem::create([
                    'return_order_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'quantity_returned' => $item['quantity_returned'],
                    'return_reason' => $item['return_reason'] ?? null,
                    'condition' => $item['condition'],
                ]);

                $totalItems++;
                $totalQuantity += $item['quantity_returned'];
            }

            // Update totals
            $return->update([
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
            ]);

            DB::commit();
            return redirect()->route('outbound.returns.show', $return)
                ->with('success', 'Return Order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update return order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReturnOrder $return)
    {
        if ($return->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be deleted.');
        }

        try {
            $return->delete();
            return redirect()->route('outbound.returns.index')
                ->with('success', 'Return Order deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete return order: ' . $e->getMessage());
        }
    }

    /**
     * Mark return as received
     */
    public function receive(ReturnOrder $return)
    {
        if ($return->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be received.');
        }

        try {
            $return->update([
                'status' => 'received',
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Return Order marked as received!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to receive return: ' . $e->getMessage());
        }
    }

    /**
     * Inspect return items
     */
    public function inspect(Request $request, ReturnOrder $return)
    {
        if ($return->status !== 'received') {
            return back()->with('error', 'Only received returns can be inspected.');
        }

        $validated = $request->validate([
            'disposition' => 'required|in:restock,quarantine,dispose,rework',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:return_order_items,id',
            'items.*.disposition' => 'required|in:restock,quarantine,dispose,rework',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $itemData) {
                $item = ReturnOrderItem::find($itemData['item_id']);
                $item->update([
                    'disposition' => $itemData['disposition'],
                ]);
            }

            $return->update([
                'status' => 'inspected',
                'disposition' => $validated['disposition'],
                'inspected_by' => Auth::id(),
                'inspected_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return back()->with('success', 'Return Order inspected successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to inspect return: ' . $e->getMessage());
        }
    }

    /**
     * Restock return items
     */
    public function restock(Request $request, ReturnOrder $return)
    {
        if ($return->status !== 'inspected') {
            return back()->with('error', 'Only inspected returns can be restocked.');
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:return_order_items,id',
            'items.*.storage_bin_id' => 'required|exists:storage_bins,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $itemData) {
                $item = ReturnOrderItem::find($itemData['item_id']);
                
                $item->update([
                    'quantity_restocked' => $itemData['quantity'],
                    'restocked_to_bin_id' => $itemData['storage_bin_id'],
                ]);

                // Update inventory (you need to implement this based on your inventory system)
                // InventoryTransaction::create([...]);
            }

            $return->update([
                'status' => 'restocked',
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return back()->with('success', 'Return items restocked successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to restock items: ' . $e->getMessage());
        }
    }

    /**
     * Cancel return order
     */
    public function cancel(Request $request, ReturnOrder $return)
    {
        if (!in_array($return->status, ['pending', 'received'])) {
            return back()->with('error', 'Cannot cancel this return order.');
        }

        try {
            $return->update([
                'status' => 'cancelled',
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('outbound.returns.index')
                ->with('success', 'Return Order cancelled successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel return: ' . $e->getMessage());
        }
    }

    /**
     * Print return order
     */
    public function print(ReturnOrder $return)
    {
        $return->load(['warehouse', 'customer', 'items.product']);
        
        return view('outbound.returns.print', compact('return'));
    }

    /**
     * Generate unique return number
     */
    private function generateReturnNumber()
    {
        $prefix = 'RET-';
        $date = date('Ymd');
        $lastReturn = ReturnOrder::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReturn) {
            $lastNumber = intval(substr($lastReturn->return_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}