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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
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
        
        // Validate sort fields untuk keamanan
        $allowedSortFields = ['po_number', 'po_date', 'total_amount', 'status', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        
        $query->orderBy($sortBy, $sortOrder);

        $perPage = min($request->get('per_page', 15), 100); // Max 100 items per page
        $purchaseOrders = $query->paginate($perPage);

        // Get filter options
        $statuses = $this->getStatusOptions();
        $paymentStatuses = $this->getPaymentStatusOptions();

        $warehouses = Warehouse::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);
            
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'company_name']);

        // Statistics
        $stats = $this->getPurchaseOrderStats();

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
        $warehouses = Warehouse::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'address']);
            
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'company_name', 'address', 'phone', 'email', 'payment_term_days']);
            
        $products = Product::where('is_active', true)
            ->with(['unit:id,name', 'category:id,name'])
            ->orderBy('name')
            ->get(['id', 'sku', 'name', 'unit_id', 'category_id', 'purchase_price', 'tax_rate']);
            
        $units = Unit::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        
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
        $validated = $this->validatePurchaseOrder($request);

        DB::beginTransaction();
        try {
            // Create Purchase Order
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => PurchaseOrder::generatePoNumber(),
                'warehouse_id' => $validated['warehouse_id'],
                'supplier_id' => $validated['supplier_id'],
                'po_date' => $validated['po_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'payment_due_days' => $validated['payment_due_days'] ?? 30,
                'subtotal' => $validated['subtotal'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'discount_rate' => $validated['discount_rate'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'shipping_cost' => $validated['shipping_cost'] ?? 0,
                'other_cost' => $validated['other_cost'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'currency' => $validated['currency'] ?? 'IDR',
                'shipping_address' => $validated['shipping_address'] ?? null,
                'shipping_method' => $validated['shipping_method'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'created_by' => auth()->id(),
            ]);

            // Create Purchase Order Items
            $this->createPurchaseOrderItems($purchaseOrder, $validated['items']);

            DB::commit();

            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('PO Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create Purchase Order. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'warehouse:id,name,code,address',
            'supplier:id,name,code,company_name,address,phone,email',
            'creator:id,name,email',
            'updater:id,name,email',
            'approver:id,name,email',
            'items.product:id,sku,name',
            'items.product.unit:id,name',
            'items.unit:id,name',
            'inboundShipments'
        ]);

        return view('inbound.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        // Check if editable
        if (!$purchaseOrder->isEditable()) {
            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot edit Purchase Order with status: ' . $purchaseOrder->status);
        }

        $warehouses = Warehouse::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'address']);
            
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'company_name', 'address', 'phone', 'email']);
            
        $products = Product::where('is_active', true)
            ->with(['unit:id,name', 'category:id,name'])
            ->orderBy('name')
            ->get(['id', 'sku', 'name', 'unit_id', 'category_id', 'purchase_price']);
            
        $units = Unit::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

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
        // Check if editable
        if (!$purchaseOrder->isEditable()) {
            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot update Purchase Order with status: ' . $purchaseOrder->status);
        }

        $validated = $this->validatePurchaseOrder($request, $purchaseOrder->id);

        DB::beginTransaction();
        try {
            // Update Purchase Order
            $purchaseOrder->update([
                'warehouse_id' => $validated['warehouse_id'],
                'supplier_id' => $validated['supplier_id'],
                'po_date' => $validated['po_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'payment_due_days' => $validated['payment_due_days'] ?? 30,
                'subtotal' => $validated['subtotal'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'discount_rate' => $validated['discount_rate'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'shipping_cost' => $validated['shipping_cost'] ?? 0,
                'other_cost' => $validated['other_cost'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'currency' => $validated['currency'] ?? 'IDR',
                'shipping_address' => $validated['shipping_address'] ?? null,
                'shipping_method' => $validated['shipping_method'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            // Update Items
            $this->updatePurchaseOrderItems($purchaseOrder, $validated['items']);

            DB::commit();

            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('PO Update Failed', [
                'po_id' => $purchaseOrder->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update Purchase Order. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->isDeletable()) {
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
            
            Log::error('PO Delete Failed', [
                'po_id' => $purchaseOrder->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()
                ->route('inbound.purchase-orders.index')
                ->with('error', 'Failed to delete Purchase Order.');
        }
    }

    /**
     * Update status of purchase order
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys($this->getStatusOptions()))],
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $purchaseOrder->status;

            $purchaseOrder->update([
                'status' => $validated['status'],
                'updated_by' => auth()->id(),
            ]);

            // Handle status-specific logic
            if ($validated['status'] === 'approved' && $oldStatus !== 'approved') {
                $purchaseOrder->update([
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            }

            // Validate completion
            if ($validated['status'] === 'completed') {
                if (!$purchaseOrder->canBeCompleted()) {
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
            
            Log::error('PO Status Update Failed', [
                'po_id' => $purchaseOrder->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Failed to update status.');
        }
    }

    /**
     * Approve purchase order
     */
    public function approve(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeApproved()) {
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
        if (!$purchaseOrder->canBeCancelled()) {
            return redirect()
                ->route('inbound.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot cancel Purchase Order with status: ' . $purchaseOrder->status);
        }

        $validated = $request->validate([
            'notes' => 'required|string|min:10|max:1000',
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
            
            Log::error('PO Duplicate Failed', [
                'po_id' => $purchaseOrder->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()
                ->route('inbound.purchase-orders.index')
                ->with('error', 'Failed to duplicate Purchase Order.');
        }
    }

    // ========== PRIVATE HELPER METHODS ==========

    /**
     * Validate purchase order request
     */
    private function validatePurchaseOrder(Request $request, $poId = null)
    {
        return $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:po_date',
            'payment_terms' => 'nullable|string|max:255',
            'payment_due_days' => 'nullable|integer|min:0|max:365',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'shipping_address' => 'nullable|string|max:500',
            'shipping_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'terms_conditions' => 'nullable|string|max:5000',
            
            // Items
            'items' => 'required|array|min:1|max:100',
            'items.*.id' => 'nullable|exists:purchase_order_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|numeric|min:0.01|max:999999',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.unit_price' => 'required|numeric|min:0|max:999999999',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string|max:500',
        ]);
    }

    /**
     * Create purchase order items
     */
    private function createPurchaseOrderItems(PurchaseOrder $purchaseOrder, array $items)
    {
        foreach ($items as $index => $itemData) {
            $product = Product::findOrFail($itemData['product_id']);
            
            $subtotal = $itemData['quantity_ordered'] * $itemData['unit_price'];
            $taxAmount = $subtotal * ($itemData['tax_rate'] ?? 0) / 100;
            $discountAmount = $subtotal * ($itemData['discount_rate'] ?? 0) / 100;
            $lineTotal = $subtotal + $taxAmount - $discountAmount;

            $purchaseOrder->items()->create([
                'product_id' => $itemData['product_id'],
                'product_sku' => $product->sku ?? '',
                'product_name' => $product->name,
                'quantity_ordered' => $itemData['quantity_ordered'],
                'quantity_received' => 0,
                'quantity_rejected' => 0,
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
    }

    /**
     * Update purchase order items
     */
    private function updatePurchaseOrderItems(PurchaseOrder $purchaseOrder, array $items)
    {
        $existingItemIds = $purchaseOrder->items()->pluck('id')->toArray();
        $updatedItemIds = [];

        foreach ($items as $index => $itemData) {
            $product = Product::findOrFail($itemData['product_id']);
            
            $subtotal = $itemData['quantity_ordered'] * $itemData['unit_price'];
            $taxAmount = $subtotal * ($itemData['tax_rate'] ?? 0) / 100;
            $discountAmount = $subtotal * ($itemData['discount_rate'] ?? 0) / 100;
            $lineTotal = $subtotal + $taxAmount - $discountAmount;

            $itemPayload = [
                'product_id' => $itemData['product_id'],
                'product_sku' => $product->sku ?? '',
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
                $itemPayload['quantity_received'] = 0;
                $itemPayload['quantity_rejected'] = 0;
                $newItem = $purchaseOrder->items()->create($itemPayload);
                $updatedItemIds[] = $newItem->id;
            }
        }

        // Delete removed items
        $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
        if (!empty($itemsToDelete)) {
            $purchaseOrder->items()->whereIn('id', $itemsToDelete)->delete();
        }
    }

    /**
     * Get status options
     */
    private function getStatusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'confirmed' => 'Confirmed',
            'partial_received' => 'Partial Received',
            'received' => 'Received',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];
    }

    /**
     * Get payment status options
     */
    private function getPaymentStatusOptions(): array
    {
        return [
            'unpaid' => 'Unpaid',
            'partial' => 'Partial',
            'paid' => 'Paid'
        ];
    }

    /**
     * Get purchase order statistics
     */
    private function getPurchaseOrderStats(): array
    {
        return [
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
    }
}