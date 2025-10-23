<?php

namespace App\Http\Controllers\Inbound;

use App\Http\Controllers\Controller;
use App\Models\GoodReceiving;
use App\Models\GoodReceivingItem;
use App\Models\InboundShipment;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GoodReceivingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GoodReceiving::with(['warehouse', 'vendor', 'receivedBy', 'purchaseOrder', 'inboundShipment']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('gr_number', 'like', "%{$search}%")
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

        // Quality Status Filter
        if ($request->filled('quality_status')) {
            $query->where('quality_status', $request->quality_status);
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
            $query->whereDate('receiving_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('receiving_date', '<=', $request->date_to);
        }

        $goodReceivings = $query->latest('receiving_date')->paginate(15)->withQueryString();

        $statuses = ['draft', 'in_progress', 'quality_check', 'completed', 'partial', 'cancelled'];
        $qualityStatuses = ['pending', 'passed', 'failed', 'partial'];
        $warehouses = Warehouse::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('inbound.good-receivings.index', compact(
            'goodReceivings',
            'statuses',
            'qualityStatuses',
            'warehouses',
            'vendors'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        $purchaseOrders = PurchaseOrder::where('status', 'approved')
            ->whereDoesntHave('goodReceivings', function($query) {
                $query->whereIn('status', ['completed']);
            })
            ->with('vendor')
            ->orderBy('po_date', 'desc')
            ->get();
        
        $inboundShipments = InboundShipment::where('status', 'in_transit')
            ->with('vendor')
            ->orderBy('shipment_date', 'desc')
            ->get();

        $selectedPO = null;
        $selectedShipment = null;

        if ($request->filled('purchase_order_id')) {
            $selectedPO = PurchaseOrder::with(['items.product', 'vendor', 'warehouse'])
                ->findOrFail($request->purchase_order_id);
        }

        if ($request->filled('inbound_shipment_id')) {
            $selectedShipment = InboundShipment::with(['items.product', 'vendor', 'warehouse'])
                ->findOrFail($request->inbound_shipment_id);
        }

        return view('inbound.good-receivings.create', compact(
            'warehouses',
            'vendors',
            'purchaseOrders',
            'inboundShipments',
            'selectedPO',
            'selectedShipment'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'inbound_shipment_id' => 'nullable|exists:inbound_shipments,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'vendor_id' => 'required|exists:vendors,id',
            'receiving_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_expected' => 'required|integer|min:1',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.pallets' => 'nullable|integer|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate GR Number
            $lastGR = GoodReceiving::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->latest('id')
                ->first();
            
            $nextNumber = $lastGR ? (int)substr($lastGR->gr_number, -5) + 1 : 1;
            $grNumber = 'GR-' . date('Ym') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Calculate totals
            $totalItems = count($validated['items']);
            $totalQuantity = array_sum(array_column($validated['items'], 'quantity_received'));
            $totalPallets = array_sum(array_column($validated['items'], 'pallets'));

            // Create Good Receiving
            $goodReceiving = GoodReceiving::create([
                'gr_number' => $grNumber,
                'inbound_shipment_id' => $validated['inbound_shipment_id'] ?? null,
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'],
                'vendor_id' => $validated['vendor_id'],
                'receiving_date' => $validated['receiving_date'],
                'status' => 'draft',
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
                'total_pallets' => $totalPallets,
                'quality_status' => 'pending',
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create Good Receiving Items
            foreach ($validated['items'] as $item) {
                GoodReceivingItem::create([
                    'good_receiving_id' => $goodReceiving->id,
                    'product_id' => $item['product_id'],
                    'quantity_expected' => $item['quantity_expected'],
                    'quantity_received' => $item['quantity_received'],
                    'pallets' => $item['pallets'] ?? 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('inbound.good-receivings.show', $goodReceiving)
                ->with('success', 'Good Receiving created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create Good Receiving: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GoodReceiving $goodReceiving)
    {
        $goodReceiving->load([
            'warehouse',
            'vendor',
            'receivedBy',
            'qualityCheckedBy',
            'purchaseOrder',
            'inboundShipment',
            'items.product',
            'createdBy',
            'updatedBy'
        ]);

        return view('inbound.good-receivings.show', compact('goodReceiving'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GoodReceiving $goodReceiving)
    {
        if (!in_array($goodReceiving->status, ['draft', 'in_progress'])) {
            return back()->with('error', 'Cannot edit Good Receiving with status: ' . $goodReceiving->status);
        }

        $goodReceiving->load(['items.product']);
        $warehouses = Warehouse::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();

        return view('inbound.good-receivings.edit', compact(
            'goodReceiving',
            'warehouses',
            'vendors',
            'products'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GoodReceiving $goodReceiving)
    {
        if (!in_array($goodReceiving->status, ['draft', 'in_progress'])) {
            return back()->with('error', 'Cannot update Good Receiving with status: ' . $goodReceiving->status);
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'vendor_id' => 'required|exists:vendors,id',
            'receiving_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_expected' => 'required|integer|min:1',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.pallets' => 'nullable|integer|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $totalItems = count($validated['items']);
            $totalQuantity = array_sum(array_column($validated['items'], 'quantity_received'));
            $totalPallets = array_sum(array_column($validated['items'], 'pallets'));

            // Update Good Receiving
            $goodReceiving->update([
                'warehouse_id' => $validated['warehouse_id'],
                'vendor_id' => $validated['vendor_id'],
                'receiving_date' => $validated['receiving_date'],
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
                'total_pallets' => $totalPallets,
                'notes' => $validated['notes'],
                'updated_by' => Auth::id(),
            ]);

            // Delete old items and create new ones
            $goodReceiving->items()->delete();

            foreach ($validated['items'] as $item) {
                GoodReceivingItem::create([
                    'good_receiving_id' => $goodReceiving->id,
                    'product_id' => $item['product_id'],
                    'quantity_expected' => $item['quantity_expected'],
                    'quantity_received' => $item['quantity_received'],
                    'pallets' => $item['pallets'] ?? 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('inbound.good-receivings.show', $goodReceiving)
                ->with('success', 'Good Receiving updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update Good Receiving: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodReceiving $goodReceiving)
    {
        if ($goodReceiving->status !== 'draft') {
            return back()->with('error', 'Only draft Good Receivings can be deleted');
        }

        DB::beginTransaction();
        try {
            $goodReceiving->items()->delete();
            $goodReceiving->delete();

            DB::commit();

            return redirect()->route('inbound.good-receivings.index')
                ->with('success', 'Good Receiving deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete Good Receiving: ' . $e->getMessage());
        }
    }

    /**
     * Start receiving process
     */
    public function start(GoodReceiving $goodReceiving)
    {
        if ($goodReceiving->status !== 'draft') {
            return back()->with('error', 'Can only start draft Good Receivings');
        }

        $goodReceiving->update([
            'status' => 'in_progress',
            'received_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Good Receiving started successfully!');
    }

    /**
     * Perform quality check
     */
    public function qualityCheck(Request $request, GoodReceiving $goodReceiving)
    {
        if (!in_array($goodReceiving->status, ['in_progress', 'quality_check'])) {
            return back()->with('error', 'Cannot perform quality check on this Good Receiving');
        }

        $validated = $request->validate([
            'quality_status' => 'required|in:passed,failed,partial',
            'quality_notes' => 'nullable|string',
        ]);

        $goodReceiving->update([
            'status' => 'quality_check',
            'quality_status' => $validated['quality_status'],
            'quality_checked_by' => Auth::id(),
            'quality_checked_at' => now(),
            'notes' => $goodReceiving->notes . "\n\nQuality Check Notes: " . ($validated['quality_notes'] ?? ''),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Quality check completed successfully!');
    }

    /**
     * Complete receiving process
     */
    public function complete(GoodReceiving $goodReceiving)
    {
        if (!in_array($goodReceiving->status, ['in_progress', 'quality_check'])) {
            return back()->with('error', 'Cannot complete this Good Receiving');
        }

        DB::beginTransaction();
        try {
            // Check if all items received match expected
            $allComplete = true;
            foreach ($goodReceiving->items as $item) {
                if ($item->quantity_received < $item->quantity_expected) {
                    $allComplete = false;
                    break;
                }
            }

            $status = $allComplete ? 'completed' : 'partial';

            $goodReceiving->update([
                'status' => $status,
                'updated_by' => Auth::id(),
            ]);

            // Update PO status if linked
            if ($goodReceiving->purchase_order_id) {
                $po = $goodReceiving->purchaseOrder;
                $po->update(['status' => 'received']);
            }

            // Update Inbound Shipment status if linked
            if ($goodReceiving->inbound_shipment_id) {
                $shipment = $goodReceiving->inboundShipment;
                $shipment->update(['status' => 'received']);
            }

            DB::commit();

            return back()->with('success', 'Good Receiving completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete Good Receiving: ' . $e->getMessage());
        }
    }

    /**
     * Cancel receiving process
     */
    public function cancel(Request $request, GoodReceiving $goodReceiving)
    {
        if (in_array($goodReceiving->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel this Good Receiving');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        $goodReceiving->update([
            'status' => 'cancelled',
            'notes' => $goodReceiving->notes . "\n\nCancellation Reason: " . $validated['cancellation_reason'],
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Good Receiving cancelled successfully!');
    }

    /**
     * Print Good Receiving document
     */
    public function print(GoodReceiving $goodReceiving)
    {
        $goodReceiving->load([
            'warehouse',
            'vendor',
            'receivedBy',
            'qualityCheckedBy',
            'items.product'
        ]);

        return view('inbound.good-receivings.print', compact('goodReceiving'));
    }
}