<?php
// app/Http/Controllers/Inbound/InboundShipmentController.php

namespace App\Http\Controllers\Inbound;

use App\Http\Controllers\Controller;
use App\Models\InboundShipment;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InboundShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = InboundShipment::with([
            'purchaseOrder:id,po_number,status',
            'warehouse:id,name,code',
            'supplier:id,name,code',
            'createdBy:id,name',
            'receivedBy:id,name'
        ])->latest('arrival_date');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                  ->orWhere('vehicle_number', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhere('seal_number', 'like', "%{$search}%")
                  ->orWhere('container_number', 'like', "%{$search}%")
                  ->orWhere('bill_of_lading', 'like', "%{$search}%")
                  ->orWhereHas('purchaseOrder', function($q) use ($search) {
                      $q->where('po_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function($q) use ($search) {
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

        // Supplier Filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('arrival_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('arrival_date', '<=', $request->date_to);
        }

        $shipments = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => InboundShipment::count(),
            'scheduled' => InboundShipment::where('status', 'scheduled')->count(),
            'in_transit' => InboundShipment::where('status', 'in_transit')->count(),
            'arrived' => InboundShipment::where('status', 'arrived')->count(),
            'completed' => InboundShipment::where('status', 'completed')->count(),
            'total_pallets' => InboundShipment::whereIn('status', ['completed'])->sum('received_pallets'),
        ];

        $statuses = [
            'scheduled' => 'Scheduled',
            'in_transit' => 'In Transit',
            'arrived' => 'Arrived',
            'unloading' => 'Unloading',
            'inspection' => 'Inspection',
            'received' => 'Received',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];
        
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('inbound.shipments.index', compact(
            'shipments',
            'statuses',
            'warehouses',
            'suppliers',
            'stats'
        ));
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrder::whereIn('status', ['approved', 'confirmed'])
            ->with(['supplier:id,name,code', 'warehouse:id,name,code'])
            ->orderBy('po_number')
            ->get();
            
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $vehicles = Vehicle::where('status', 'available')
            ->whereIn('vehicle_type', ['truck', 'van'])
            ->orderBy('vehicle_number')
            ->get();

        return view('inbound.shipments.create', compact(
            'purchaseOrders',
            'warehouses',
            'suppliers',
            'vehicles'
        ));
    }

    public function store(Request $request)
    {
        // Debug logging
        Log::info('=== INBOUND SHIPMENT STORE START ===');
        Log::info('Request All Data:', $request->all());

        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            
            // Dates
            'scheduled_date' => 'nullable|date',
            'shipment_date' => 'nullable|date',
            'arrival_date' => 'required|date',
            
            // Shipment Details
            'expected_pallets' => 'nullable|integer|min:0',
            'expected_boxes' => 'nullable|integer|min:0',
            'expected_weight' => 'nullable|numeric|min:0',
            
            // Vehicle & Driver
            'vehicle_type' => 'nullable|string|max:255',
            'vehicle_number' => 'nullable|string|max:255',
            'container_number' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:255',
            'driver_id_number' => 'nullable|string|max:255',
            'seal_number' => 'nullable|string|max:255',
            
            // Warehouse
            'dock_number' => 'nullable|string|max:255',
            
            // Documents
            'bill_of_lading' => 'nullable|string|max:255',
            'packing_list' => 'nullable|string|max:255',
            
            'notes' => 'nullable|string',
        ]);

        Log::info('After Validation:', $validated);

        // Check if supplier_id is in validated data
        if (!isset($validated['supplier_id']) || empty($validated['supplier_id'])) {
            Log::error('supplier_id is missing or empty!');
            return back()
                ->withInput()
                ->with('error', 'Supplier ID is required but was not found in the request.');
        }

        DB::beginTransaction();
        try {
            // Generate shipment number
            $lastShipment = InboundShipment::latest('id')->first();
            $nextNumber = $lastShipment ? ((int) substr($lastShipment->shipment_number, 4)) + 1 : 1;
            $shipmentNumber = 'ISH-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Prepare data for insert
            $dataToInsert = [
                'shipment_number' => $shipmentNumber,
                'purchase_order_id' => $validated['purchase_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'supplier_id' => $validated['supplier_id'],
                'arrival_date' => $validated['arrival_date'],
                'scheduled_date' => $validated['scheduled_date'] ?? null,
                'shipment_date' => $validated['shipment_date'] ?? null,
                'expected_pallets' => $validated['expected_pallets'] ?? null,
                'expected_boxes' => $validated['expected_boxes'] ?? null,
                'expected_weight' => $validated['expected_weight'] ?? null,
                'vehicle_type' => $validated['vehicle_type'] ?? null,
                'vehicle_number' => $validated['vehicle_number'] ?? null,
                'container_number' => $validated['container_number'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_phone' => $validated['driver_phone'] ?? null,
                'driver_id_number' => $validated['driver_id_number'] ?? null,
                'seal_number' => $validated['seal_number'] ?? null,
                'dock_number' => $validated['dock_number'] ?? null,
                'bill_of_lading' => $validated['bill_of_lading'] ?? null,
                'packing_list' => $validated['packing_list'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'scheduled',
                'created_by' => auth()->id(),
            ];

            Log::info('Data to Insert:', $dataToInsert);

            $shipment = InboundShipment::create($dataToInsert);

            Log::info('Shipment Created:', ['id' => $shipment->id, 'number' => $shipment->shipment_number]);

            // If vehicle is selected from dropdown, update its status
            if ($request->filled('vehicle_id') && $request->vehicle_id) {
                $vehicle = Vehicle::find($request->vehicle_id);
                if ($vehicle) {
                    $vehicle->update([
                        'status' => 'in_use',
                        'updated_by' => auth()->id()
                    ]);
                    Log::info('Vehicle Status Updated:', ['vehicle_id' => $vehicle->id, 'status' => 'in_use']);
                }
            }

            // Update PO status
            $po = PurchaseOrder::find($request->purchase_order_id);
            if ($po && $po->status === 'approved') {
                $po->update(['status' => 'confirmed']);
                Log::info('PO Status Updated:', ['po_id' => $po->id, 'status' => 'confirmed']);
            }

            DB::commit();

            Log::info('=== INBOUND SHIPMENT STORE SUCCESS ===');

            return redirect()
                ->route('inbound.shipments.show', $shipment)
                ->with('success', 'Inbound shipment created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('=== INBOUND SHIPMENT STORE FAILED ===');
            Log::error('Error Message:', ['message' => $e->getMessage()]);
            Log::error('Error File:', ['file' => $e->getFile()]);
            Log::error('Error Line:', ['line' => $e->getLine()]);
            Log::error('Error Trace:', ['trace' => $e->getTraceAsString()]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create inbound shipment: ' . $e->getMessage());
        }
    }

    public function show(InboundShipment $shipment)
    {
        $shipment->load([
            'purchaseOrder.items.product',
            'warehouse',
            'supplier',
            'createdBy',
            'updatedBy',
            'receivedBy',
            'inspectedBy'
        ]);

        return view('inbound.shipments.show', compact('shipment'));
    }

    public function edit(InboundShipment $shipment)
    {
        if (!in_array($shipment->status, ['scheduled', 'in_transit', 'arrived'])) {
            return back()->with('error', 'Cannot edit shipment in current status!');
        }

        $purchaseOrders = PurchaseOrder::whereIn('status', ['approved', 'confirmed'])
            ->with(['supplier:id,name,code', 'warehouse:id,name,code'])
            ->orderBy('po_number')
            ->get();
            
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $vehicles = Vehicle::whereIn('status', ['available', 'in_use'])
            ->whereIn('vehicle_type', ['truck', 'van'])
            ->orderBy('vehicle_number')
            ->get();

        return view('inbound.shipments.edit', compact(
            'shipment',
            'purchaseOrders',
            'warehouses',
            'suppliers',
            'vehicles'
        ));
    }

    public function update(Request $request, InboundShipment $shipment)
    {
        if (!in_array($shipment->status, ['scheduled', 'in_transit', 'arrived'])) {
            return back()->with('error', 'Cannot update shipment in current status!');
        }

        Log::info('=== INBOUND SHIPMENT UPDATE START ===');
        Log::info('Shipment ID:', ['id' => $shipment->id]);
        Log::info('Request Data:', $request->all());

        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            
            // Dates
            'scheduled_date' => 'nullable|date',
            'shipment_date' => 'nullable|date',
            'arrival_date' => 'required|date',
            
            // Shipment Details
            'expected_pallets' => 'nullable|integer|min:0',
            'expected_boxes' => 'nullable|integer|min:0',
            'expected_weight' => 'nullable|numeric|min:0',
            
            // Vehicle & Driver
            'vehicle_type' => 'nullable|string|max:255',
            'vehicle_number' => 'nullable|string|max:255',
            'container_number' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:255',
            'driver_id_number' => 'nullable|string|max:255',
            'seal_number' => 'nullable|string|max:255',
            
            // Warehouse
            'dock_number' => 'nullable|string|max:255',
            
            // Documents
            'bill_of_lading' => 'nullable|string|max:255',
            'packing_list' => 'nullable|string|max:255',
            
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['updated_by'] = auth()->id();

            $shipment->update($validated);

            Log::info('Shipment Updated Successfully');

            DB::commit();

            return redirect()
                ->route('inbound.shipments.show', $shipment)
                ->with('success', 'Inbound shipment updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('=== INBOUND SHIPMENT UPDATE FAILED ===');
            Log::error('Error:', ['message' => $e->getMessage()]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update inbound shipment: ' . $e->getMessage());
        }
    }

    public function destroy(InboundShipment $shipment)
    {
        if (!in_array($shipment->status, ['scheduled', 'cancelled'])) {
            return back()->with('error', 'Cannot delete shipment that has been processed!');
        }

        DB::beginTransaction();
        try {
            $shipment->delete();

            DB::commit();

            return redirect()
                ->route('inbound.shipments.index')
                ->with('success', 'Inbound shipment deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete shipment: ' . $e->getMessage());
        }
    }

    public function markInTransit(InboundShipment $shipment)
    {
        if ($shipment->status !== 'scheduled') {
            return back()->with('error', 'Invalid status transition!');
        }

        $shipment->update([
            'status' => 'in_transit',
            'shipment_date' => now(),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Shipment marked as in transit!');
    }

    public function markArrived(Request $request, InboundShipment $shipment)
    {
        if (!in_array($shipment->status, ['scheduled', 'in_transit'])) {
            return back()->with('error', 'Invalid status transition!');
        }

        $validated = $request->validate([
            'received_pallets' => 'nullable|integer|min:0',
            'received_boxes' => 'nullable|integer|min:0',
            'actual_weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $shipment->update([
            'status' => 'arrived',
            'arrival_date' => now(),
            'received_pallets' => $validated['received_pallets'] ?? $shipment->received_pallets,
            'received_boxes' => $validated['received_boxes'] ?? $shipment->received_boxes,
            'actual_weight' => $validated['actual_weight'] ?? $shipment->actual_weight,
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
            'unloading_start' => now(),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Unloading process started!');
    }

    public function startInspection(InboundShipment $shipment)
    {
        if ($shipment->status !== 'unloading') {
            return back()->with('error', 'Invalid status transition!');
        }

        $shipment->update([
            'status' => 'inspection',
            'unloading_end' => now(),
            'inspected_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Inspection process started!');
    }

    public function completeInspection(Request $request, InboundShipment $shipment)
    {
        if ($shipment->status !== 'inspection') {
            return back()->with('error', 'Invalid status transition!');
        }

        $validated = $request->validate([
            'inspection_result' => 'required|in:passed,failed,partial',
            'inspection_notes' => 'nullable|string',
            'has_damages' => 'boolean',
            'damage_description' => 'required_if:has_damages,true|nullable|string',
        ]);

        $validated['received_by'] = auth()->id();
        $validated['status'] = 'received';
        $validated['updated_by'] = auth()->id();

        $shipment->update($validated);

        return back()->with('success', 'Inspection completed!');
    }

    public function complete(Request $request, InboundShipment $shipment)
    {
        if (!in_array($shipment->status, ['inspection', 'received'])) {
            return back()->with('error', 'Invalid status transition!');
        }

        $validated = $request->validate([
            'received_pallets' => 'required|integer|min:0',
            'received_boxes' => 'nullable|integer|min:0',
            'actual_weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $shipment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'received_pallets' => $validated['received_pallets'],
                'received_boxes' => $validated['received_boxes'] ?? $shipment->received_boxes,
                'actual_weight' => $validated['actual_weight'] ?? $shipment->actual_weight,
                'notes' => $validated['notes'] ?? $shipment->notes,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('inbound.shipments.show', $shipment)
                ->with('success', 'Shipment completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete shipment: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, InboundShipment $shipment)
    {
        if (in_array($shipment->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel shipment in current status!');
        }

        $validated = $request->validate([
            'notes' => 'required|string|min:10',
        ]);

        DB::beginTransaction();
        try {
            $shipment->update([
                'status' => 'cancelled',
                'notes' => $shipment->notes . "\n\nCancellation Reason: " . $validated['notes'],
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('inbound.shipments.show', $shipment)
                ->with('success', 'Shipment cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel shipment: ' . $e->getMessage());
        }
    }
}