<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\CrossDockingOrder;
use App\Models\Warehouse;
use App\Models\InboundShipment;
use App\Models\SalesOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrossDockingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CrossDockingOrder::with(['warehouse', 'product', 'inboundShipment', 'outboundOrder']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cross_dock_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        $crossDockings = $query->latest('scheduled_date')->paginate(15)->withQueryString();

        $statuses = ['scheduled', 'receiving', 'sorting', 'loading', 'completed', 'cancelled'];
        $warehouses = Warehouse::all();

        return view('operations.cross-docking.index', compact('crossDockings', 'statuses', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::all();
        $inboundShipments = InboundShipment::whereIn('status', ['in_transit', 'arrived'])->get();
        $outboundOrders = SalesOrder::whereIn('status', ['confirmed', 'processing'])->get();
        $products = Product::where('is_active', 'active')->get();

        return view('operations.cross-docking.create', compact('warehouses', 'inboundShipments', 'outboundOrders', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'inbound_shipment_id' => 'nullable|exists:inbound_shipments,id',
            'outbound_order_id' => 'nullable|exists:sales_orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_of_measure' => 'required|string|max:255',
            'scheduled_date' => 'required|date',
            'dock_in' => 'nullable|string|max:255',
            'dock_out' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate cross dock number
            $lastOrder = CrossDockingOrder::whereDate('created_at', today())->latest()->first();
            $number = $lastOrder ? intval(substr($lastOrder->cross_dock_number, -5)) + 1 : 1;
            $validated['cross_dock_number'] = 'CD-' . str_pad($number, 5, '0', STR_PAD_LEFT);

            $validated['created_by'] = auth()->id();
            $validated['status'] = 'scheduled';

            $crossDocking = CrossDockingOrder::create($validated);

            DB::commit();

            return redirect()->route('cross-docking.index')
                ->with('success', 'Cross docking order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create cross docking order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CrossDockingOrder $crossDocking)
    {
        $crossDocking->load(['warehouse', 'product', 'inboundShipment', 'outboundOrder', 'creator', 'updater']);

        return view('operations.cross-docking.show', compact('crossDocking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CrossDockingOrder $crossDocking)
    {
        if (!in_array($crossDocking->status, ['scheduled'])) {
            return back()->with('error', 'Only scheduled cross docking orders can be edited.');
        }

        $warehouses = Warehouse::all();
        $inboundShipments = InboundShipment::whereIn('status', ['in_transit', 'arrived'])->get();
        $outboundOrders = SalesOrder::whereIn('status', ['confirmed', 'processing'])->get();
        $products = Product::where('is_active', 'active')->get();

        return view('operations.cross-docking.edit', compact('crossDocking', 'warehouses', 'inboundShipments', 'outboundOrders', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CrossDockingOrder $crossDocking)
    {
        if (!in_array($crossDocking->status, ['scheduled'])) {
            return back()->with('error', 'Only scheduled cross docking orders can be updated.');
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'inbound_shipment_id' => 'nullable|exists:inbound_shipments,id',
            'outbound_order_id' => 'nullable|exists:sales_orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_of_measure' => 'required|string|max:255',
            'scheduled_date' => 'required|date',
            'dock_in' => 'nullable|string|max:255',
            'dock_out' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['updated_by'] = auth()->id();
            $crossDocking->update($validated);

            DB::commit();

            return redirect()->route('cross-docking.show', $crossDocking)
                ->with('success', 'Cross docking order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update cross docking order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CrossDockingOrder $crossDocking)
    {
        if ($crossDocking->status !== 'scheduled') {
            return back()->with('error', 'Only scheduled cross docking orders can be deleted.');
        }

        try {
            $crossDocking->delete();
            return redirect()->route('cross-docking.index')
                ->with('success', 'Cross docking order deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete cross docking order: ' . $e->getMessage());
        }
    }

    /**
     * Start receiving process
     */
    public function startReceiving(CrossDockingOrder $crossDocking)
    {
        if (!$crossDocking->canStartReceiving()) {
            return back()->with('error', 'Cannot start receiving for this cross docking order.');
        }

        DB::beginTransaction();
        try {
            $crossDocking->update([
                'status' => 'receiving',
                'started_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Receiving process started successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to start receiving: ' . $e->getMessage());
        }
    }

    /**
     * Start sorting process
     */
    public function startSorting(CrossDockingOrder $crossDocking)
    {
        if (!$crossDocking->canStartSorting()) {
            return back()->with('error', 'Cannot start sorting for this cross docking order.');
        }

        DB::beginTransaction();
        try {
            $crossDocking->update([
                'status' => 'sorting',
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Sorting process started successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to start sorting: ' . $e->getMessage());
        }
    }

    /**
     * Start loading process
     */
    public function startLoading(CrossDockingOrder $crossDocking)
    {
        if (!$crossDocking->canStartLoading()) {
            return back()->with('error', 'Cannot start loading for this cross docking order.');
        }

        DB::beginTransaction();
        try {
            $crossDocking->update([
                'status' => 'loading',
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Loading process started successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to start loading: ' . $e->getMessage());
        }
    }

    /**
     * Complete cross docking order
     */
    public function complete(CrossDockingOrder $crossDocking)
    {
        if (!$crossDocking->canComplete()) {
            return back()->with('error', 'Cannot complete this cross docking order.');
        }

        DB::beginTransaction();
        try {
            $crossDocking->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('cross-docking.show', $crossDocking)
                ->with('success', 'Cross docking order completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete cross docking order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel cross docking order
     */
    public function cancel(CrossDockingOrder $crossDocking)
    {
        if (!$crossDocking->canCancel()) {
            return back()->with('error', 'Cannot cancel this cross docking order.');
        }

        DB::beginTransaction();
        try {
            $crossDocking->update([
                'status' => 'cancelled',
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Cross docking order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel cross docking order: ' . $e->getMessage());
        }
    }
}