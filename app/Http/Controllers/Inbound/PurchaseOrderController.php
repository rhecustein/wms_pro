<?php

namespace App\Http\Controllers\Inbound;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['warehouse', 'supplier', 'creator', 'items']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('supplier_invoice_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment Status Filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Warehouse Filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Supplier Filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('po_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('po_date', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $purchaseOrders = $query->paginate($request->get('per_page', 15));

        // Get filter options
        $statuses = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'confirmed' => 'Confirmed',
            'partial_received' => 'Partial Received',
            'received' => 'Received',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        $paymentStatuses = [
            'unpaid' => 'Unpaid',
            'partial' => 'Partial',
            'paid' => 'Paid'
        ];

        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => PurchaseOrder::count(),
            'draft' => PurchaseOrder::where('status', 'draft')->count(),
            'pending' => PurchaseOrder::whereIn('status', ['submitted', 'approved', 'confirmed'])->count(),
            'partial' => PurchaseOrder::where('status', 'partial_received')->count(),
            'completed' => PurchaseOrder::where('status', 'completed')->count(),
            'total_amount' => PurchaseOrder::whereNotIn('status', ['cancelled'])->sum('total_amount'),
            'unpaid_amount' => PurchaseOrder::where('payment_status', '!=', 'paid')
                ->whereNotIn('status', ['cancelled'])
                ->sum('total_amount'),
        ];

        return view('inbound.purchase-orders.index', compact(
            'purchaseOrders',
            'statuses',
            'paymentStatuses',
            'warehouses',
            'suppliers',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->with(['unit', 'category'])
            ->orderBy('name')
            ->get();
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        
        $poNumber = PurchaseOrder::generatePoNumber();

        return view('inbound.purchase-orders.create', compact(
            'warehouses',
            'suppliers',
            'products',
            'units',
            'poNumber'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:po_date',
            'payment_terms' => 'nullable|string|max:255',
            'payment_due_days' => 'nullable|integer|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'shipping_address' => 'nullable|string',
            'shipping_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            
            // Items
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create Purchase Order
            $poData = $validated;
            unset($poData['items']);
            
            $poData['po_number'] = PurchaseOrder::generatePoNumber();
            $poData['status'] = 'draft';
            $poData['payment_status'] = 'unpaid';
            $poData['created_by'] = auth()->id();

            $purchaseOrder = PurchaseOrder::create($poData);

            // Create Purchase Order Items
            foreach ($validated['items'] as $index => $itemData) {
                $product = Product::find($itemData['product_id']);
                
                $subtotal = $itemData['quantity_ordered'] * $itemData['unit_price'];
                $taxAmount = $subtotal * ($itemData['tax_rate'] ?? 0) / 100;
                $discountAmount = $subtotal * ($itemData['discount_rate'] ?? 0) / 100;
                $lineTotal = $subtotal + $taxAmount - $discountAmount;

                $purchaseOrder->items()->create([
                    'product_id' => $itemData['product_id'],
                    'product_sku' => $product->sku,
                    'product_name' => $product->name,
                    'quantity_ordered' => $itemData['quantity_ordered'],
                    'unit_id' => $itemData['unit_id'],
                    'unit_price' => $itemData['unit_price'],
                    'tax_rate' => $itemData['tax_rate'] ?? 0,
                    'tax_amount' => $taxAmount,
                    'discount_rate' => $itemData['discount_rate'] ?? 0,
                    'discount_amount' => $discountAmount,
                    'subtotal' => $subtotal,
                    'line_total' => $lineTotal,
                    'notes' => $itemData['notes'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'warehouse',
            'supplier',
            'creator',
            'updater',
            'approver',
            'items.product.unit',
            'items.unit',
            'inboundShipments'
        ]);

        return view('inbound.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'submitted'])) {
            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot edit Purchase Order with status: ' . $purchaseOrder->status);
        }

        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->with(['unit', 'category'])
            ->orderBy('name')
            ->get();
        $units = Unit::where('is_active', true)->orderBy('name')->get();

        $purchaseOrder->load(['items.product', 'items.unit']);

        return view('inbound.purchase-orders.edit', compact(
            'purchaseOrder',
            'warehouses',
            'suppliers',
            'products',
            'units'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'submitted'])) {
            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot update Purchase Order with status: ' . $purchaseOrder->status);
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:po_date',
            'payment_terms' => 'nullable|string|max:255',
            'payment_due_days' => 'nullable|integer|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'shipping_address' => 'nullable|string',
            'shipping_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            
            // Items
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:purchase_order_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update Purchase Order
            $poData = $validated;
            unset($poData['items']);
            $poData['updated_by'] = auth()->id();

            $purchaseOrder->update($poData);

            // Get existing item IDs
            $existingItemIds = $purchaseOrder->items()->pluck('id')->toArray();
            $updatedItemIds = [];

            // Update or Create Items
            foreach ($validated['items'] as $index => $itemData) {
                $product = Product::find($itemData['product_id']);
                
                $subtotal = $itemData['quantity_ordered'] * $itemData['unit_price'];
                $taxAmount = $subtotal * ($itemData['tax_rate'] ?? 0) / 100;
                $discountAmount = $subtotal * ($itemData['discount_rate'] ?? 0) / 100;
                $lineTotal = $subtotal + $taxAmount - $discountAmount;

                $itemPayload = [
                    'product_id' => $itemData['product_id'],
                    'product_sku' => $product->sku,
                    'product_name' => $product->name,
                    'quantity_ordered' => $itemData['quantity_ordered'],
                    'unit_id' => $itemData['unit_id'],
                    'unit_price' => $itemData['unit_price'],
                    'tax_rate' => $itemData['tax_rate'] ?? 0,
                    'tax_amount' => $taxAmount,
                    'discount_rate' => $itemData['discount_rate'] ?? 0,
                    'discount_amount' => $discountAmount,
                    'subtotal' => $subtotal,
                    'line_total' => $lineTotal,
                    'notes' => $itemData['notes'] ?? null,
                    'sort_order' => $index + 1,
                ];

                if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                    // Update existing item
                    $purchaseOrder->items()->where('id', $itemData['id'])->update($itemPayload);
                    $updatedItemIds[] = $itemData['id'];
                } else {
                    // Create new item
                    $newItem = $purchaseOrder->items()->create($itemPayload);
                    $updatedItemIds[] = $newItem->id;
                }
            }

            // Delete removed items
            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($itemsToDelete)) {
                $purchaseOrder->items()->whereIn('id', $itemsToDelete)->delete();
            }

            DB::commit();

            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()
                ->route('inbound.purchase-orders.index')
                ->with('error', 'Only draft Purchase Orders can be deleted!');
        }

        DB::beginTransaction();
        try {
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();

            DB::commit();

            return redirect()
                ->route('inbound.purchase-orders.index')
                ->with('success', 'Purchase Order deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('inbound.purchase-orders.index')
                ->with('error', 'Failed to delete Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Update status of purchase order
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,submitted,approved,confirmed,partial_received,received,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $purchaseOrder->status;

            $purchaseOrder->update([
                'status' => $validated['status'],
                'updated_by' => auth()->id(),
            ]);

            // If approved, set approved_by and approved_at
            if ($validated['status'] === 'approved' && $oldStatus !== 'approved') {
                $purchaseOrder->update([
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            }

            // If completed, check if all items received
            if ($validated['status'] === 'completed') {
                $allItemsReceived = $purchaseOrder->items()
                    ->whereColumn('quantity_received', '<', 'quantity_ordered')
                    ->count() === 0;

                if (!$allItemsReceived) {
                    DB::rollBack();
                    return redirect()
                        ->route('inbound.purchase-orders.show', $purchaseOrder)
                        ->with('error', 'Cannot complete PO. Not all items have been fully received.');
                }
            }

            DB::commit();

            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('success', "Purchase Order status updated from {$oldStatus} to {$validated['status']}!");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Approve purchase order
     */
    public function approve(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'submitted') {
            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only submitted Purchase Orders can be approved!');
        }

        $purchaseOrder->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('inbound.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order approved successfully!');
    }

    /**
     * Cancel purchase order
     */
    public function cancel(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (in_array($purchaseOrder->status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot cancel Purchase Order with status: ' . $purchaseOrder->status);
        }

        $validated = $request->validate([
            'notes' => 'required|string|min:10',
        ]);

        $purchaseOrder->update([
            'status' => 'cancelled',
            'notes' => $purchaseOrder->notes . "\n\nCancellation Reason: " . $validated['notes'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('inbound.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order cancelled successfully!');
    }

    /**
     * Print/Export purchase order
     */
    public function print(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'warehouse',
            'supplier',
            'creator',
            'approver',
            'items.product',
            'items.unit'
        ]);

        return view('inbound.purchase-orders.print', compact('purchaseOrder'));
    }

    /**
     * Duplicate purchase order
     */
    public function duplicate(PurchaseOrder $purchaseOrder)
    {
        DB::beginTransaction();
        try {
            $newPO = $purchaseOrder->replicate();
            $newPO->po_number = PurchaseOrder::generatePoNumber();
            $newPO->status = 'draft';
            $newPO->payment_status = 'unpaid';
            $newPO->approved_by = null;
            $newPO->approved_at = null;
            $newPO->created_by = auth()->id();
            $newPO->save();

            // Duplicate items
            foreach ($purchaseOrder->items as $item) {
                $newItem = $item->replicate();
                $newItem->purchase_order_id = $newPO->id;
                $newItem->quantity_received = 0;
                $newItem->quantity_rejected = 0;
                $newItem->save();
            }

            DB::commit();

            return redirect()
                ->route('inbound.purchase-orders.edit', $newPO)
                ->with('success', 'Purchase Order duplicated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('inbound.purchase-orders.index')
                ->with('error', 'Failed to duplicate Purchase Order: ' . $e->getMessage());
        }
    }
}