<?php
// app/Http/Controllers/Inbound/InboundShipmentController.php

namespace App\Http\Controllers\Inbound;

use App\Http\Controllers\Controller;
use App\Models\InboundShipment;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InboundShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = InboundShipment::with(['purchaseOrder', 'warehouse', 'vendor'])
            ->latest('arrival_date');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                  ->orWhere('vehicle_number', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhere('seal_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
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
            $query->whereDate('arrival_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('arrival_date', '<=', $request->date_to);
        }

        $shipments = $query->paginate(15)->withQueryString();

        $statuses = ['scheduled', 'arrived', 'unloading', 'received', 'completed'];
        $warehouses = Warehouse::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('inbound.shipments.index', compact('shipments', 'statuses', 'warehouses', 'vendors'));
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrder::whereIn('status', ['approved', 'partial'])
            ->with('vendor', 'warehouse')
            ->orderBy('po_number')
            ->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('inbound.shipments.create', compact('purchaseOrders', 'warehouses', 'vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'vendor_id' => 'required|exists:vendors,id',
            'arrival_date' => 'required|date',
            'expected_pallets' => 'nullable|integer|min:0',
            'vehicle_number' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:255',
            'seal_number' => 'nullable|string|max:255',
            'dock_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate shipment number
            $lastShipment = InboundShipment::latest('id')->first();
            $nextNumber = $lastShipment ? ((int) substr($lastShipment->shipment_number, 4)) + 1 : 1;
            $validated['shipment_number'] = 'ISH-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $validated['created_by'] = auth()->id();
            $validated['status'] = 'scheduled';

            $shipment = InboundShipment::create($validated);

            DB::commit();

            return redirect()
                ->route('inbound.shipments.show', $shipment)
                ->with('success', 'Inbound shipment created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create inbound shipment: ' . $e->getMessage());
        }
    }

    public function show(InboundShipment $shipment)
    {
        $shipment->load(['purchaseOrder', 'warehouse', 'vendor', 'createdBy', 'updatedBy']);

        return view('inbound.shipments.show', compact('shipment'));
    }

    public function edit(InboundShipment $shipment)
    {
        if (!in_array($shipment->status, ['scheduled', 'arrived'])) {
            return back()->with('error', 'Cannot edit shipment in current status!');
        }

        $purchaseOrders = PurchaseOrder::whereIn('status', ['approved', 'partial'])
            ->with('vendor', 'warehouse')
            ->orderBy('po_number')
            ->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('inbound.shipments.edit', compact('shipment', 'purchaseOrders', 'warehouses', 'vendors'));
    }

    public function update(Request $request, InboundShipment $shipment)
    {
        if (!in_array($shipment->status, ['scheduled', 'arrived'])) {
            return back()->with('error', 'Cannot update shipment in current status!');
        }

        $validated = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'vendor_id' => 'required|exists:vendors,id',
            'arrival_date' => 'required|date',
            'expected_pallets' => 'nullable|integer|min:0',
            'vehicle_number' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:255',
            'seal_number' => 'nullable|string|max:255',
            'dock_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $shipment->update($validated);

        return redirect()
            ->route('inbound.shipments.show', $shipment)
            ->with('success', 'Inbound shipment updated successfully!');
    }

    public function destroy(InboundShipment $shipment)
    {
        if ($shipment->status !== 'scheduled') {
            return back()->with('error', 'Cannot delete shipment that has been processed!');
        }

        $shipment->delete();

        return redirect()
            ->route('inbound.shipments.index')
            ->with('success', 'Inbound shipment deleted successfully!');
    }

    public function markArrived(Request $request, InboundShipment $shipment)
    {
        if ($shipment->status !== 'scheduled') {
            return back()->with('error', 'Invalid status transition!');
        }

        $validated = $request->validate([
            'received_pallets' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $shipment->update([
            'status' => 'arrived',
            'received_pallets' => $validated['received_pallets'] ?? $shipment->received_pallets,
            'notes' => $validated['notes'] ?? $shipment->notes,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Shipment marked as arrived!');
    }

    public function startUnloading(InboundShipment $shipment)
    {
        if ($shipment->status !== 'arrived') {
            return back()->with('error', 'Invalid status transition!');
        }

        $shipment->update([
            'status' => 'unloading',
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Unloading process started!');
    }

    public function complete(Request $request, InboundShipment $shipment)
    {
        if (!in_array($shipment->status, ['unloading', 'received'])) {
            return back()->with('error', 'Invalid status transition!');
        }

        $validated = $request->validate([
            'received_pallets' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $shipment->update([
            'status' => 'completed',
            'received_pallets' => $validated['received_pallets'],
            'notes' => $validated['notes'] ?? $shipment->notes,
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('inbound.shipments.show', $shipment)
            ->with('success', 'Shipment completed successfully!');
    }
}