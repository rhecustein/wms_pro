<?php

namespace App\Http\Controllers\Outbound;

use App\Http\Controllers\Controller;
use App\Models\PackingOrder;
use App\Models\PackingOrderItem;
use App\Models\PickingOrder;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PackingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PackingOrder::with(['pickingOrder', 'salesOrder', 'warehouse', 'assignedUser', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('packing_number', 'like', "%{$search}%")
                  ->orWhereHas('salesOrder', function($q) use ($search) {
                      $q->where('order_number', 'like', "%{$search}%");
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

        // Assigned To Filter
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('packing_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('packing_date', '<=', $request->date_to);
        }

        $packingOrders = $query->paginate(20)->withQueryString();

        // Data for filters
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        $warehouses = Warehouse::all();
        $users = User::all();

        return view('outbound.packing-orders.index', compact(
            'packingOrders',
            'statuses',
            'warehouses',
            'users'
        ));
    }

    /**
     * Display pending packing orders
     */
    public function pending()
    {
        $packingOrders = PackingOrder::with(['pickingOrder', 'salesOrder', 'warehouse', 'assignedUser'])
            ->where('status', 'pending')
            ->orderBy('packing_date', 'asc')
            ->paginate(20);

        return view('outbound.packing-orders.pending', compact('packingOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pickingOrders = PickingOrder::where('status', 'completed')
            ->whereDoesntHave('packingOrders', function($query) {
                $query->whereIn('status', ['pending', 'in_progress', 'completed']);
            })
            ->with(['salesOrder', 'warehouse'])
            ->get();
        
        $warehouses = Warehouse::all();
        $users = User::all();

        return view('outbound.packing-orders.create', compact('pickingOrders', 'warehouses', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'picking_order_id' => 'required|exists:picking_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'packing_date' => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $pickingOrder = PickingOrder::findOrFail($validated['picking_order_id']);

            // Generate packing number
            $lastPacking = PackingOrder::whereDate('created_at', today())->latest()->first();
            $number = $lastPacking ? intval(substr($lastPacking->packing_number, -5)) + 1 : 1;
            $packingNumber = 'PACK-' . str_pad($number, 5, '0', STR_PAD_LEFT);

            $packingOrder = PackingOrder::create([
                'packing_number' => $packingNumber,
                'picking_order_id' => $validated['picking_order_id'],
                'sales_order_id' => $pickingOrder->sales_order_id,
                'warehouse_id' => $validated['warehouse_id'],
                'packing_date' => $validated['packing_date'],
                'assigned_to' => $validated['assigned_to'],
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('outbound.packing-orders.show', $packingOrder)
                ->with('success', 'Packing order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create packing order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PackingOrder $packingOrder)
    {
        $packingOrder->load([
            'pickingOrder.items.product',
            'salesOrder.customer',
            'warehouse',
            'assignedUser',
            'items.product',
            'items.packedBy',
            'createdBy',
            'updatedBy'
        ]);

        return view('outbound.packing-orders.show', compact('packingOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PackingOrder $packingOrder)
    {
        if (!in_array($packingOrder->status, ['pending', 'in_progress'])) {
            return back()->with('error', 'Cannot edit packing order with status: ' . $packingOrder->status);
        }

        $warehouses = Warehouse::all();
        $users = User::all();

        return view('outbound.packing-orders.edit', compact('packingOrder', 'warehouses', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PackingOrder $packingOrder)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'packing_date' => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        try {
            $packingOrder->update([
                ...$validated,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('outbound.packing-orders.show', $packingOrder)
                ->with('success', 'Packing order updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update packing order: ' . $e->getMessage());
        }
    }

    /**
     * Assign packing order to user
     */
    public function assign(Request $request, PackingOrder $packingOrder)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        try {
            $packingOrder->update([
                'assigned_to' => $validated['assigned_to'],
                'status' => 'in_progress',
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Packing order assigned successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign packing order: ' . $e->getMessage());
        }
    }

    /**
     * Start packing process
     */
    public function start(PackingOrder $packingOrder)
    {
        if ($packingOrder->status !== 'pending') {
            return back()->with('error', 'Can only start pending packing orders');
        }

        try {
            $packingOrder->update([
                'status' => 'in_progress',
                'started_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('outbound.packing-orders.execute', $packingOrder)
                ->with('success', 'Packing process started!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start packing: ' . $e->getMessage());
        }
    }

    /**
     * Execute packing (scan and pack items)
     */
    public function execute(PackingOrder $packingOrder)
    {
        $packingOrder->load([
            'pickingOrder.items.product',
            'items.product',
            'warehouse',
            'salesOrder.customer'
        ]);

        $pickingItems = $packingOrder->pickingOrder->items;

        return view('outbound.packing-orders.execute', compact('packingOrder', 'pickingItems'));
    }

    /**
     * Complete packing order
     */
    public function complete(PackingOrder $packingOrder)
    {
        if ($packingOrder->status !== 'in_progress') {
            return back()->with('error', 'Can only complete in-progress packing orders');
        }

        try {
            DB::beginTransaction();

            // Calculate totals
            $totalBoxes = $packingOrder->items()->distinct('box_number')->count('box_number');
            $totalWeight = $packingOrder->items()->sum('box_weight_kg');

            $packingOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
                'total_boxes' => $totalBoxes,
                'total_weight_kg' => $totalWeight,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('outbound.packing-orders.show', $packingOrder)
                ->with('success', 'Packing order completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete packing order: ' . $e->getMessage());
        }
    }

    /**
     * Print packing label
     */
    public function printLabel(PackingOrder $packingOrder)
    {
        $packingOrder->load([
            'salesOrder.customer',
            'warehouse',
            'items.product'
        ]);

        return view('outbound.packing-orders.print-label', compact('packingOrder'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PackingOrder $packingOrder)
    {
        if ($packingOrder->status !== 'pending') {
            return back()->with('error', 'Can only delete pending packing orders');
        }

        try {
            $packingOrder->delete();
            return redirect()->route('outbound.packing-orders.index')
                ->with('success', 'Packing order deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete packing order: ' . $e->getMessage());
        }
    }
}