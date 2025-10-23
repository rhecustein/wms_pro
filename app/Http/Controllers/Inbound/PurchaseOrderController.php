<?php

namespace App\Http\Controllers\Inbound;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['warehouse', 'vendor', 'creator']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Warehouse Filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Vendor Filter
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('po_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('po_date', '<=', $request->date_to);
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $statuses = ['draft', 'submitted', 'confirmed', 'partial', 'completed', 'cancelled'];
        $warehouses = Warehouse::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('inbound.purchase-orders.index', compact(
            'purchaseOrders',
            'statuses',
            'warehouses',
            'vendors'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        $poNumber = PurchaseOrder::generatePoNumber();

        return view('inbound.purchase-orders.create', compact(
            'warehouses',
            'vendors',
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
            'vendor_id' => 'required|exists:vendors,id',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:po_date',
            'payment_terms' => 'nullable|string|max:255',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'notes' => 'nullable|string',
        ]);

        $validated['po_number'] = PurchaseOrder::generatePoNumber();
        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->id();

        $purchaseOrder = PurchaseOrder::create($validated);

        return redirect()
            ->route('inbound.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['warehouse', 'vendor', 'creator', 'updater']);

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

        $warehouses = Warehouse::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('inbound.purchase-orders.edit', compact(
            'purchaseOrder',
            'warehouses',
            'vendors'
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
            'vendor_id' => 'required|exists:vendors,id',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:po_date',
            'payment_terms' => 'nullable|string|max:255',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $purchaseOrder->update($validated);

        return redirect()
            ->route('inbound.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order updated successfully!');
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

        $purchaseOrder->delete();

        return redirect()
            ->route('inbound.purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully!');
    }

    /**
     * Update status of purchase order
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,submitted,confirmed,partial,completed,cancelled',
        ]);

        $purchaseOrder->update([
            'status' => $validated['status'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('inbound.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order status updated successfully!');
    }
}