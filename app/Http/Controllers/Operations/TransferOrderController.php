<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\TransferOrder;
use App\Models\TransferOrderItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransferOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransferOrder::with([
            'fromWarehouse',
            'toWarehouse',
            'vehicle',
            'driver',
            'items'
        ]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transfer_number', 'like', "%{$search}%")
                  ->orWhereHas('fromWarehouse', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('toWarehouse', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Transfer Type Filter
        if ($request->filled('transfer_type')) {
            $query->where('transfer_type', $request->transfer_type);
        }

        // From Warehouse Filter
        if ($request->filled('from_warehouse_id')) {
            $query->where('from_warehouse_id', $request->from_warehouse_id);
        }

        // To Warehouse Filter
        if ($request->filled('to_warehouse_id')) {
            $query->where('to_warehouse_id', $request->to_warehouse_id);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('transfer_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transfer_date', '<=', $request->date_to);
        }

        $transferOrders = $query->latest()->paginate(15)->withQueryString();

        // Data for filters
        $statuses = ['draft', 'approved', 'in_transit', 'received', 'completed', 'cancelled'];
        $transferTypes = ['inter_warehouse', 'internal_bin', 'consolidation'];
        $warehouses = Warehouse::orderBy('name')->get();

        return view('operations.transfer-orders.index', compact(
            'transferOrders',
            'statuses',
            'transferTypes',
            'warehouses'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        // Get vehicles without status filter (or adjust based on your Vehicle model)
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        
        // Get all users for driver selection (no role filter)
        $drivers = User::orderBy('name')->get();
        
        $transferNumber = TransferOrder::generateTransferNumber();

        return view('operations.transfer-orders.create', compact(
            'warehouses',
            'products',
            'vehicles',
            'drivers',
            'transferNumber'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_type' => 'required|in:inter_warehouse,internal_bin,consolidation',
            'transfer_date' => 'required|date',
            'expected_arrival_date' => 'nullable|date|after_or_equal:transfer_date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.from_storage_bin_id' => 'nullable|exists:storage_bins,id',
            'items.*.to_storage_bin_id' => 'nullable|exists:storage_bins,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.serial_number' => 'nullable|string',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'items.*.unit_of_measure' => 'required|string',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Create Transfer Order
            $transferOrder = TransferOrder::create([
                'transfer_number' => TransferOrder::generateTransferNumber(),
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'transfer_type' => $validated['transfer_type'],
                'transfer_date' => $validated['transfer_date'],
                'expected_arrival_date' => $validated['expected_arrival_date'] ?? null,
                'vehicle_id' => $validated['vehicle_id'] ?? null,
                'driver_id' => $validated['driver_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
                'created_by' => Auth::check() ? Auth::id() : null,
            ]);

            // Create Transfer Order Items
            $totalItems = 0;
            $totalQuantity = 0;

            foreach ($validated['items'] as $item) {
                TransferOrderItem::create([
                    'transfer_order_id' => $transferOrder->id,
                    'product_id' => $item['product_id'],
                    'from_storage_bin_id' => $item['from_storage_bin_id'] ?? null,
                    'to_storage_bin_id' => $item['to_storage_bin_id'] ?? null,
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'quantity_requested' => $item['quantity_requested'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'notes' => $item['notes'] ?? null,
                    'status' => 'pending',
                ]);

                $totalItems++;
                $totalQuantity += $item['quantity_requested'];
            }

            // Update totals
            $transferOrder->update([
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
            ]);

            DB::commit();

            return redirect()
                ->route('operations.transfer-orders.show', $transferOrder)
                ->with('success', 'Transfer Order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create Transfer Order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TransferOrder $transferOrder)
    {
        $transferOrder->load([
            'fromWarehouse',
            'toWarehouse',
            'vehicle',
            'driver',
            'approvedBy',
            'createdBy',
            'updatedBy',
            'items.product',
            'items.fromStorageBin',
            'items.toStorageBin'
        ]);

        return view('operations.transfer-orders.show', compact('transferOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransferOrder $transferOrder)
    {
        if (!in_array($transferOrder->status, ['draft', 'approved'])) {
            return back()->with('error', 'Only draft or approved transfer orders can be edited.');
        }

        $transferOrder->load('items');
        $warehouses = Warehouse::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        // Get all vehicles (no status filter)
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        
        // Get all users for driver selection (no role filter)
        $drivers = User::orderBy('name')->get();

        return view('operations.transfer-orders.edit', compact(
            'transferOrder',
            'warehouses',
            'products',
            'vehicles',
            'drivers'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransferOrder $transferOrder)
    {
        if (!in_array($transferOrder->status, ['draft', 'approved'])) {
            return back()->with('error', 'Only draft or approved transfer orders can be updated.');
        }

        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_type' => 'required|in:inter_warehouse,internal_bin,consolidation',
            'transfer_date' => 'required|date',
            'expected_arrival_date' => 'nullable|date|after_or_equal:transfer_date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.from_storage_bin_id' => 'nullable|exists:storage_bins,id',
            'items.*.to_storage_bin_id' => 'nullable|exists:storage_bins,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.serial_number' => 'nullable|string',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'items.*.unit_of_measure' => 'required|string',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update Transfer Order
            $transferOrder->update([
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'transfer_type' => $validated['transfer_type'],
                'transfer_date' => $validated['transfer_date'],
                'expected_arrival_date' => $validated['expected_arrival_date'] ?? null,
                'vehicle_id' => $validated['vehicle_id'] ?? null,
                'driver_id' => $validated['driver_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => Auth::check() ? Auth::id() : null,
            ]);

            // Delete existing items
            $transferOrder->items()->delete();

            // Create new items
            $totalItems = 0;
            $totalQuantity = 0;

            foreach ($validated['items'] as $item) {
                TransferOrderItem::create([
                    'transfer_order_id' => $transferOrder->id,
                    'product_id' => $item['product_id'],
                    'from_storage_bin_id' => $item['from_storage_bin_id'] ?? null,
                    'to_storage_bin_id' => $item['to_storage_bin_id'] ?? null,
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'quantity_requested' => $item['quantity_requested'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'notes' => $item['notes'] ?? null,
                    'status' => 'pending',
                ]);

                $totalItems++;
                $totalQuantity += $item['quantity_requested'];
            }

            // Update totals
            $transferOrder->update([
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
            ]);

            DB::commit();

            return redirect()
                ->route('operations.transfer-orders.show', $transferOrder)
                ->with('success', 'Transfer Order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update Transfer Order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransferOrder $transferOrder)
    {
        if ($transferOrder->status !== 'draft') {
            return back()->with('error', 'Only draft transfer orders can be deleted.');
        }

        try {
            $transferOrder->delete();
            return redirect()
                ->route('operations.transfer-orders.index')
                ->with('success', 'Transfer Order deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete Transfer Order: ' . $e->getMessage());
        }
    }

    /**
     * Approve transfer order
     */
    public function approve(TransferOrder $transferOrder)
    {
        if ($transferOrder->status !== 'draft') {
            return back()->with('error', 'Only draft transfer orders can be approved.');
        }

        $transferOrder->update([
            'status' => 'approved',
            'approved_by' => Auth::check() ? Auth::id() : null,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Transfer Order approved successfully!');
    }

    /**
     * Ship transfer order
     */
    public function ship(TransferOrder $transferOrder)
    {
        if ($transferOrder->status !== 'approved') {
            return back()->with('error', 'Only approved transfer orders can be shipped.');
        }

        try {
            DB::beginTransaction();

            $transferOrder->update([
                'status' => 'in_transit',
                'shipped_at' => now(),
            ]);

            // Update all items to shipped
            $transferOrder->items()->update([
                'status' => 'shipped',
                'quantity_shipped' => DB::raw('quantity_requested'),
            ]);

            DB::commit();

            return back()->with('success', 'Transfer Order shipped successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to ship Transfer Order: ' . $e->getMessage());
        }
    }

    /**
     * Receive transfer order
     */
    public function receive(TransferOrder $transferOrder)
    {
        if ($transferOrder->status !== 'in_transit') {
            return back()->with('error', 'Only in-transit transfer orders can be received.');
        }

        try {
            DB::beginTransaction();

            $transferOrder->update([
                'status' => 'received',
                'received_at' => now(),
            ]);

            // Update all items to received
            $transferOrder->items()->update([
                'status' => 'received',
                'quantity_received' => DB::raw('quantity_shipped'),
            ]);

            DB::commit();

            return back()->with('success', 'Transfer Order received successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to receive Transfer Order: ' . $e->getMessage());
        }
    }

    /**
     * Complete transfer order
     */
    public function complete(TransferOrder $transferOrder)
    {
        if ($transferOrder->status !== 'received') {
            return back()->with('error', 'Only received transfer orders can be completed.');
        }

        $transferOrder->update([
            'status' => 'completed',
        ]);

        return back()->with('success', 'Transfer Order completed successfully!');
    }

    /**
     * Cancel transfer order
     */
    public function cancel(TransferOrder $transferOrder)
    {
        if (in_array($transferOrder->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Completed or cancelled transfer orders cannot be cancelled again.');
        }

        $transferOrder->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Transfer Order cancelled successfully!');
    }
}