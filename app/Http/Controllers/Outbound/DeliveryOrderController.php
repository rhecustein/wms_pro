<?php

namespace App\Http\Controllers\Outbound;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use App\Models\PackingOrder;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DeliveryOrder::with(['salesOrder', 'warehouse', 'customer', 'vehicle', 'driver']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('do_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhere('recipient_name', 'like', "%{$search}%");
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

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('delivery_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('delivery_date', '<=', $request->date_to);
        }

        $deliveryOrders = $query->latest()->paginate(15);

        // Define statuses and other filter options
        $statuses = ['prepared', 'loaded', 'in_transit', 'delivered', 'returned', 'cancelled'];
        $warehouses = Warehouse::all();
        $customers = Customer::all();

        return view('outbound.delivery-orders.index', compact('deliveryOrders', 'statuses', 'warehouses', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doNumber = DeliveryOrder::generateDoNumber();
        
        // Get all necessary data for dropdowns
        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'ready_to_pack'])->get();
        $packingOrders = PackingOrder::where('status', 'completed')->get();
        $warehouses = Warehouse::all();
        $customers = Customer::all();
        $vehicles = Vehicle::where('status', 'available')->get();
        
        // Get users with driver role - adjust based on your role implementation
        // If you don't have roles yet, just get all users
        $drivers = User::all(); // Or use: User::whereHas('roles', function($q) { $q->where('name', 'driver'); })->get();

        return view('outbound.delivery-orders.create', compact(
            'doNumber',
            'salesOrders',
            'packingOrders',
            'warehouses',
            'customers',
            'vehicles',
            'drivers'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'do_number' => 'required|string|unique:delivery_orders,do_number',
            'sales_order_id' => 'required|exists:sales_orders,id',
            'packing_order_id' => 'nullable|exists:packing_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'required|exists:customers,id',
            'delivery_date' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
            'total_boxes' => 'required|integer|min:0',
            'total_weight_kg' => 'required|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'prepared';
        $validated['created_by'] = auth()->id() ?? 1; // Default to 1 if no auth

        $deliveryOrder = DeliveryOrder::create($validated);

        return redirect()->route('outbound.delivery-orders.show', $deliveryOrder)
            ->with('success', 'Delivery Order created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['salesOrder', 'packingOrder', 'warehouse', 'customer', 'vehicle', 'driver', 'createdBy', 'updatedBy']);

        return view('outbound.delivery-orders.show', compact('deliveryOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryOrder $deliveryOrder)
    {
        if (!in_array($deliveryOrder->status, ['prepared', 'loaded'])) {
            return redirect()->route('outbound.delivery-orders.show', $deliveryOrder)
                ->with('error', 'Cannot edit delivery order in current status.');
        }

        $salesOrders = SalesOrder::whereIn('status', ['confirmed', 'ready_to_pack'])->get();
        $packingOrders = PackingOrder::where('status', 'completed')->get();
        $warehouses = Warehouse::all();
        $customers = Customer::all();
        $vehicles = Vehicle::where('status', 'available')->get();
        $drivers = User::all();

        return view('outbound.delivery-orders.edit', compact(
            'deliveryOrder',
            'salesOrders',
            'packingOrders',
            'warehouses',
            'customers',
            'vehicles',
            'drivers'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'packing_order_id' => 'nullable|exists:packing_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'required|exists:customers,id',
            'delivery_date' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
            'total_boxes' => 'required|integer|min:0',
            'total_weight_kg' => 'required|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id() ?? 1;

        $deliveryOrder->update($validated);

        return redirect()->route('outbound.delivery-orders.show', $deliveryOrder)
            ->with('success', 'Delivery Order updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryOrder $deliveryOrder)
    {
        if (!in_array($deliveryOrder->status, ['prepared', 'cancelled'])) {
            return redirect()->route('outbound.delivery-orders.index')
                ->with('error', 'Cannot delete delivery order in current status.');
        }

        $deliveryOrder->delete();

        return redirect()->route('outbound.delivery-orders.index')
            ->with('success', 'Delivery Order deleted successfully!');
    }

    /**
     * Load the delivery order
     */
    public function load(DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status !== 'prepared') {
            return back()->with('error', 'Delivery Order must be in prepared status to load.');
        }

        $deliveryOrder->update([
            'status' => 'loaded',
            'loaded_at' => now(),
            'updated_by' => auth()->id() ?? 1,
        ]);

        return back()->with('success', 'Delivery Order loaded successfully!');
    }

    /**
     * Dispatch the delivery order
     */
    public function dispatch(DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status !== 'loaded') {
            return back()->with('error', 'Delivery Order must be in loaded status to dispatch.');
        }

        if (!$deliveryOrder->vehicle_id || !$deliveryOrder->driver_id) {
            return back()->with('error', 'Vehicle and driver must be assigned before dispatch.');
        }

        $deliveryOrder->update([
            'status' => 'in_transit',
            'departed_at' => now(),
            'updated_by' => auth()->id() ?? 1,
        ]);

        return back()->with('success', 'Delivery Order dispatched successfully!');
    }

    /**
     * Mark as in transit
     */
    public function inTransit(DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status !== 'loaded') {
            return back()->with('error', 'Invalid status transition.');
        }

        $deliveryOrder->update([
            'status' => 'in_transit',
            'departed_at' => now(),
            'updated_by' => auth()->id() ?? 1,
        ]);

        return back()->with('success', 'Status updated to In Transit!');
    }

    /**
     * Deliver the order
     */
    public function deliver(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status !== 'in_transit') {
            return back()->with('error', 'Delivery Order must be in transit to deliver.');
        }

        $validated = $request->validate([
            'received_by_name' => 'required|string|max:255',
            'received_by_signature' => 'nullable|string',
            'delivery_proof_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('delivery_proof_image')) {
            $path = $request->file('delivery_proof_image')->store('delivery-proofs', 'public');
            $validated['delivery_proof_image'] = $path;
        }

        $validated['status'] = 'delivered';
        $validated['delivered_at'] = now();
        $validated['updated_by'] = auth()->id() ?? 1;

        $deliveryOrder->update($validated);

        return back()->with('success', 'Delivery Order marked as delivered!');
    }

    /**
     * Cancel the order
     */
    public function cancel(Request $request, DeliveryOrder $deliveryOrder)
    {
        if (in_array($deliveryOrder->status, ['delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel this delivery order.');
        }

        $deliveryOrder->update([
            'status' => 'cancelled',
            'notes' => $request->cancel_reason,
            'updated_by' => auth()->id() ?? 1,
        ]);

        return back()->with('success', 'Delivery Order cancelled!');
    }

    /**
     * Show proof page
     */
    public function proof(DeliveryOrder $deliveryOrder)
    {
        return view('outbound.delivery-orders.proof', compact('deliveryOrder'));
    }

    /**
     * Upload delivery proof
     */
    public function uploadProof(Request $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validate([
            'delivery_proof_image' => 'required|image|max:2048',
            'received_by_name' => 'required|string|max:255',
        ]);

        if ($request->hasFile('delivery_proof_image')) {
            // Delete old proof if exists
            if ($deliveryOrder->delivery_proof_image) {
                Storage::disk('public')->delete($deliveryOrder->delivery_proof_image);
            }

            $path = $request->file('delivery_proof_image')->store('delivery-proofs', 'public');
            
            $deliveryOrder->update([
                'delivery_proof_image' => $path,
                'received_by_name' => $validated['received_by_name'],
                'updated_by' => auth()->id() ?? 1,
            ]);
        }

        return back()->with('success', 'Delivery proof uploaded successfully!');
    }

    /**
     * Print delivery order
     */
    public function print(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['salesOrder', 'packingOrder', 'warehouse', 'customer', 'vehicle', 'driver', 'createdBy']);
        
        return view('outbound.delivery-orders.print', compact('deliveryOrder'));
    }

    /**
     * Track delivery order
     */
    public function tracking(DeliveryOrder $deliveryOrder)
    {
        $timeline = [
            [
                'status' => 'prepared',
                'label' => 'Prepared',
                'icon' => 'fa-box',
                'time' => $deliveryOrder->created_at,
                'active' => true,
            ],
            [
                'status' => 'loaded',
                'label' => 'Loaded',
                'icon' => 'fa-truck-loading',
                'time' => $deliveryOrder->loaded_at,
                'active' => $deliveryOrder->loaded_at !== null,
            ],
            [
                'status' => 'in_transit',
                'label' => 'In Transit',
                'icon' => 'fa-shipping-fast',
                'time' => $deliveryOrder->departed_at,
                'active' => $deliveryOrder->departed_at !== null,
            ],
            [
                'status' => 'delivered',
                'label' => 'Delivered',
                'icon' => 'fa-check-circle',
                'time' => $deliveryOrder->delivered_at,
                'active' => $deliveryOrder->delivered_at !== null,
            ],
        ];

        return view('outbound.delivery-orders.tracking', compact('deliveryOrder', 'timeline'));
    }
}