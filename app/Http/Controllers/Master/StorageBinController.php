<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\StorageBin;
use App\Models\Warehouse;
use App\Models\StorageArea;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Unit;
use App\Models\InventoryStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorageBinController extends Controller
{
    // No middleware - without role

    /**
     * Display a listing of storage bins
     */
    public function index(Request $request)
    {
        $query = StorageBin::with(['warehouse', 'storageArea', 'customer']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('aisle', 'like', "%{$search}%")
                    ->orWhere('row', 'like', "%{$search}%")
                    ->orWhere('column', 'like', "%{$search}%")
                    ->orWhere('level', 'like', "%{$search}%");
            });
        }

        // Warehouse filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Storage Area filter
        if ($request->filled('storage_area_id')) {
            $query->where('storage_area_id', $request->storage_area_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Bin Type filter
        if ($request->filled('bin_type')) {
            $query->where('bin_type', $request->bin_type);
        }

        // Active filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Hazmat filter
        if ($request->filled('is_hazmat')) {
            $query->where('is_hazmat', $request->is_hazmat);
        }

        $storageBins = $query->latest()->paginate(20)->withQueryString();
        
        // Get filter options
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $storageAreas = StorageArea::where('is_active', true)->orderBy('name')->get();
        $statuses = ['available', 'occupied', 'reserved', 'blocked', 'maintenance'];
        $binTypes = ['pick_face', 'high_rack', 'staging', 'quarantine'];

        return view('master.storage-bins.index', compact(
            'storageBins',
            'warehouses',
            'storageAreas',
            'statuses',
            'binTypes'
        ));
    }

    /**
     * Show the form for creating a new storage bin
     */
    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $storageAreas = StorageArea::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        
        return view('master.storage-bins.create', compact('warehouses', 'storageAreas', 'customers'));
    }

    /**
     * Store a newly created storage bin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'storage_area_id' => 'nullable|exists:storage_areas,id',
            'aisle' => 'required|string|max:10',
            'row' => 'required|string|max:10',
            'column' => 'required|string|max:10',
            'level' => 'required|string|max:10',
            'status' => 'required|in:available,occupied,reserved,blocked,maintenance',
            'bin_type' => 'required|in:pick_face,high_rack,staging,quarantine',
            'max_weight_kg' => 'nullable|numeric|min:0',
            'max_volume_cbm' => 'nullable|numeric|min:0',
            'packaging_restriction' => 'nullable|in:none,drum,carton,pallet',
            'customer_id' => 'nullable|exists:customers,id',
            'is_hazmat' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Generate code
        $validated['code'] = strtoupper(
            $validated['aisle'] . 
            $validated['row'] . 
            $validated['column'] . 
            $validated['level']
        );

        // Check if code already exists
        if (StorageBin::where('code', $validated['code'])->exists()) {
            return back()->withInput()->withErrors(['code' => 'Storage bin code already exists.']);
        }

        $storageBin = StorageBin::create($validated);

        return redirect()
            ->route('master.storage-bins.show', $storageBin)
            ->with('success', 'Storage bin created successfully.');
    }

    /**
     * Display the specified storage bin
     */
    public function show(StorageBin $storageBin)
    {
        $storageBin->load(['warehouse', 'storageArea', 'customer']);
        
        return view('master.storage-bins.show', compact('storageBin'));
    }

    /**
     * Show the form for editing the specified storage bin
     */
    public function edit(StorageBin $storageBin)
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $storageAreas = StorageArea::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        
        return view('master.storage-bins.edit', compact('storageBin', 'warehouses', 'storageAreas', 'customers'));
    }

    /**
     * Update the specified storage bin
     */
    public function update(Request $request, StorageBin $storageBin)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'storage_area_id' => 'nullable|exists:storage_areas,id',
            'aisle' => 'required|string|max:10',
            'row' => 'required|string|max:10',
            'column' => 'required|string|max:10',
            'level' => 'required|string|max:10',
            'status' => 'required|in:available,occupied,reserved,blocked,maintenance',
            'bin_type' => 'required|in:pick_face,high_rack,staging,quarantine',
            'max_weight_kg' => 'nullable|numeric|min:0',
            'max_volume_cbm' => 'nullable|numeric|min:0',
            'packaging_restriction' => 'nullable|in:none,drum,carton,pallet',
            'customer_id' => 'nullable|exists:customers,id',
            'is_hazmat' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Generate code
        $newCode = strtoupper(
            $validated['aisle'] . 
            $validated['row'] . 
            $validated['column'] . 
            $validated['level']
        );

        // Check if code already exists (except current bin)
        if (StorageBin::where('code', $newCode)->where('id', '!=', $storageBin->id)->exists()) {
            return back()->withInput()->withErrors(['code' => 'Storage bin code already exists.']);
        }

        $validated['code'] = $newCode;
        $storageBin->update($validated);

        return redirect()
            ->route('master.storage-bins.show', $storageBin)
            ->with('success', 'Storage bin updated successfully.');
    }

    /**
     * Remove the specified storage bin
     */
    public function destroy(StorageBin $storageBin)
    {
        // Check if bin has current stock
        if ($storageBin->current_quantity > 0) {
            return back()->with('error', 'Cannot delete storage bin with existing stock.');
        }

        $storageBin->delete();

        return redirect()
            ->route('master.storage-bins.index')
            ->with('success', 'Storage bin deleted successfully.');
    }

    /**
     * Get bins by storage area
     */
    public function byStorageArea(StorageArea $storageArea)
    {
        $bins = $storageArea->storageBins()
            ->with(['warehouse'])
            ->orderBy('code')
            ->get();

        return view('master.storage-bins.by-area', compact('storageArea', 'bins'));
    }

    /**
     * Generate multiple storage bins
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'storage_area_id' => 'nullable|exists:storage_areas,id',
            'aisle_start' => 'required|string|max:10',
            'aisle_end' => 'required|string|max:10',
            'row_start' => 'required|integer|min:1',
            'row_end' => 'required|integer|min:1|gte:row_start',
            'column_start' => 'required|integer|min:1',
            'column_end' => 'required|integer|min:1|gte:column_start',
            'level_start' => 'required|string|max:10',
            'level_end' => 'required|string|max:10',
            'bin_type' => 'required|in:pick_face,high_rack,staging,quarantine',
            'max_weight_kg' => 'nullable|numeric|min:0',
            'max_volume_cbm' => 'nullable|numeric|min:0',
        ]);

        $generated = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            // Generate aisles range
            $aisles = $this->generateRange($validated['aisle_start'], $validated['aisle_end']);
            $levels = $this->generateRange($validated['level_start'], $validated['level_end']);

            foreach ($aisles as $aisle) {
                for ($row = $validated['row_start']; $row <= $validated['row_end']; $row++) {
                    for ($column = $validated['column_start']; $column <= $validated['column_end']; $column++) {
                        foreach ($levels as $level) {
                            $code = strtoupper($aisle . str_pad($row, 2, '0', STR_PAD_LEFT) . str_pad($column, 2, '0', STR_PAD_LEFT) . $level);

                            // Skip if exists
                            if (StorageBin::where('code', $code)->exists()) {
                                $skipped++;
                                continue;
                            }

                            StorageBin::create([
                                'warehouse_id' => $validated['warehouse_id'],
                                'storage_area_id' => $validated['storage_area_id'],
                                'code' => $code,
                                'aisle' => $aisle,
                                'row' => str_pad($row, 2, '0', STR_PAD_LEFT),
                                'column' => str_pad($column, 2, '0', STR_PAD_LEFT),
                                'level' => $level,
                                'status' => 'available',
                                'bin_type' => $validated['bin_type'],
                                'max_weight_kg' => $validated['max_weight_kg'],
                                'max_volume_cbm' => $validated['max_volume_cbm'],
                                'is_active' => true,
                            ]);

                            $generated++;
                        }
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('master.storage-bins.index')
                ->with('success', "Generated {$generated} storage bins successfully. Skipped {$skipped} existing bins.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error generating storage bins: ' . $e->getMessage());
        }
    }

    /**
     * Activate storage bin
     */
    public function activate(StorageBin $storageBin)
    {
        $storageBin->update(['is_active' => true]);

        return back()->with('success', 'Storage bin activated successfully.');
    }

    /**
     * Deactivate storage bin
     */
    public function deactivate(StorageBin $storageBin)
    {
        $storageBin->update(['is_active' => false]);

        return back()->with('success', 'Storage bin deactivated successfully.');
    }

    /**
     * Get current stock in bin
     */
    public function currentStock(StorageBin $storageBin)
    {
        $storageBin->load(['warehouse', 'storageArea']);
        
        // You can add relationship to get actual stock items if you have inventory table
        return view('master.storage-bins.current-stock', compact('storageBin'));
    }

    // ============================================
    // QUICK ACTIONS METHODS
    // ============================================

    /**
     * Show add stock form for storage bin
     */
    public function addStockForm(StorageBin $storageBin)
    {
        $storageBin->load(['warehouse', 'storageArea']);
        
        // Get active products
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get active units
        $units = Unit::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('master.storage-bins.add-stock', compact('storageBin', 'products', 'units'));
    }

    /**
     * Store stock to storage bin
     */
    public function addStockStore(Request $request, StorageBin $storageBin)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_of_measure' => 'required|string|max:50',
            'batch_number' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date|after:today',
            'manufacturing_date' => 'nullable|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if bin is available
        if (!in_array($storageBin->status, ['available', 'occupied'])) {
            return back()->withErrors(['error' => 'Storage bin is not available for stock addition.']);
        }

        DB::beginTransaction();
        try {
            // Create or update inventory stock
            $inventoryStock = InventoryStock::updateOrCreate(
                [
                    'storage_bin_id' => $storageBin->id,
                    'warehouse_id' => $storageBin->warehouse_id,
                    'product_id' => $validated['product_id'],
                    'batch_number' => $validated['batch_number'] ?? null,
                ],
                [
                    'storage_area_id' => $storageBin->storage_area_id,
                    'unit_of_measure' => $validated['unit_of_measure'],
                    'quantity' => DB::raw('quantity + ' . $validated['quantity']),
                    'serial_number' => $validated['serial_number'] ?? null,
                    'expiry_date' => $validated['expiry_date'] ?? null,
                    'manufacturing_date' => $validated['manufacturing_date'] ?? null,
                ]
            );

            // Create stock movement record
            StockMovement::create([
                'warehouse_id' => $storageBin->warehouse_id,
                'product_id' => $validated['product_id'],
                'from_bin_id' => null,
                'to_bin_id' => $storageBin->id,
                'batch_number' => $validated['batch_number'] ?? null,
                'serial_number' => $validated['serial_number'] ?? null,
                'quantity' => $validated['quantity'],
                'unit_of_measure' => $validated['unit_of_measure'],
                'movement_type' => 'inbound',
                'reference_type' => 'adjustment',
                'reference_number' => 'ADD-' . time(),
                'movement_date' => now(),
                'performed_by' => Auth::id(),
                'notes' => $validated['notes'] ?? 'Manual stock addition to bin ' . $storageBin->code,
            ]);

            // Update bin status to occupied if it was available
            if ($storageBin->status === 'available') {
                $storageBin->update(['status' => 'occupied']);
            }

            DB::commit();

            return redirect()
                ->route('master.storage-bins.current-stock', $storageBin)
                ->with('success', 'Stock added successfully to bin ' . $storageBin->code);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to add stock: ' . $e->getMessage()]);
        }
    }

    /**
     * Show transfer stock form
     */
    public function transferStockForm(StorageBin $storageBin)
    {
        $storageBin->load(['warehouse', 'storageArea']);
        
        // Get stock items in this bin
        $stockItems = InventoryStock::where('storage_bin_id', $storageBin->id)
            ->with(['product'])
            ->where('quantity', '>', 0)
            ->get();
        
        // Get available destination bins (exclude current bin)
        $destinationBins = StorageBin::where('warehouse_id', $storageBin->warehouse_id)
            ->where('id', '!=', $storageBin->id)
            ->whereIn('status', ['available', 'occupied'])
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
        
        return view('master.storage-bins.transfer-stock', compact('storageBin', 'stockItems', 'destinationBins'));
    }

    /**
     * Process transfer stock
     */
    public function transferStockStore(Request $request, StorageBin $storageBin)
    {
        $validated = $request->validate([
            'inventory_stock_id' => 'required|exists:inventory_stocks,id',
            'destination_bin_id' => 'required|exists:storage_bins,id',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|string|in:relocation,replenishment,consolidation,optimization,other',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get inventory stock
        $inventoryStock = InventoryStock::findOrFail($validated['inventory_stock_id']);

        // Validate quantity
        if ($validated['quantity'] > $inventoryStock->quantity) {
            return back()->withErrors(['quantity' => 'Transfer quantity cannot exceed available quantity.']);
        }

        // Get destination bin
        $destinationBin = StorageBin::findOrFail($validated['destination_bin_id']);

        // Check destination bin is available
        if (!in_array($destinationBin->status, ['available', 'occupied'])) {
            return back()->withErrors(['destination_bin_id' => 'Destination bin is not available.']);
        }

        DB::beginTransaction();
        try {
            // Reduce quantity from source bin
            $inventoryStock->decrement('quantity', $validated['quantity']);

            // Add to destination bin
            InventoryStock::updateOrCreate(
                [
                    'storage_bin_id' => $destinationBin->id,
                    'warehouse_id' => $destinationBin->warehouse_id,
                    'product_id' => $inventoryStock->product_id,
                    'batch_number' => $inventoryStock->batch_number,
                ],
                [
                    'storage_area_id' => $destinationBin->storage_area_id,
                    'unit_of_measure' => $inventoryStock->unit_of_measure,
                    'quantity' => DB::raw('quantity + ' . $validated['quantity']),
                    'serial_number' => $inventoryStock->serial_number,
                    'expiry_date' => $inventoryStock->expiry_date,
                    'manufacturing_date' => $inventoryStock->manufacturing_date,
                ]
            );

            // Create stock movement record
            $referenceNumber = 'TRF-' . time();
            
            StockMovement::create([
                'warehouse_id' => $storageBin->warehouse_id,
                'product_id' => $inventoryStock->product_id,
                'from_bin_id' => $storageBin->id,
                'to_bin_id' => $destinationBin->id,
                'batch_number' => $inventoryStock->batch_number,
                'serial_number' => $inventoryStock->serial_number,
                'quantity' => $validated['quantity'],
                'unit_of_measure' => $inventoryStock->unit_of_measure,
                'movement_type' => 'transfer',
                'reference_type' => 'transfer',
                'reference_number' => $referenceNumber,
                'movement_date' => now(),
                'performed_by' => Auth::id(),
                'notes' => 'Transfer from ' . $storageBin->code . ' to ' . $destinationBin->code . 
                          ' - Reason: ' . $validated['reason'] . 
                          ($validated['notes'] ? ' - ' . $validated['notes'] : ''),
            ]);

            // Update source bin status if empty
            if ($inventoryStock->fresh()->quantity == 0) {
                $totalStock = InventoryStock::where('storage_bin_id', $storageBin->id)
                    ->sum('quantity');
                if ($totalStock == 0) {
                    $storageBin->update(['status' => 'available']);
                }
            }

            // Update destination bin status
            if ($destinationBin->status === 'available') {
                $destinationBin->update(['status' => 'occupied']);
            }

            DB::commit();

            return redirect()
                ->route('master.storage-bins.current-stock', $storageBin)
                ->with('success', 'Stock transferred successfully from ' . $storageBin->code . ' to ' . $destinationBin->code);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to transfer stock: ' . $e->getMessage()]);
        }
    }

    /**
     * Show adjust inventory form
     */
    public function adjustInventoryForm(StorageBin $storageBin)
    {
        $storageBin->load(['warehouse', 'storageArea']);
        
        // Get stock items in this bin
        $stockItems = InventoryStock::where('storage_bin_id', $storageBin->id)
            ->with(['product'])
            ->where('quantity', '>', 0)
            ->get();
        
        return view('master.storage-bins.adjust-inventory', compact('storageBin', 'stockItems'));
    }

    /**
     * Process adjust inventory
     */
    public function adjustInventoryStore(Request $request, StorageBin $storageBin)
    {
        $validated = $request->validate([
            'inventory_stock_id' => 'required|exists:inventory_stocks,id',
            'adjustment_type' => 'required|in:add,reduce,set',
            'quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|in:damaged,expired,lost,found,count_correction,system_error,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $inventoryStock = InventoryStock::findOrFail($validated['inventory_stock_id']);

        DB::beginTransaction();
        try {
            $oldQuantity = $inventoryStock->quantity;
            $newQuantity = 0;
            $adjustmentQty = 0;
            $movementType = 'adjustment';

            // Calculate new quantity based on adjustment type
            switch ($validated['adjustment_type']) {
                case 'add':
                    $newQuantity = $oldQuantity + $validated['quantity'];
                    $adjustmentQty = $validated['quantity'];
                    break;
                case 'reduce':
                    $newQuantity = max(0, $oldQuantity - $validated['quantity']);
                    $adjustmentQty = $validated['quantity'];
                    break;
                case 'set':
                    $newQuantity = $validated['quantity'];
                    $adjustmentQty = abs($newQuantity - $oldQuantity);
                    break;
            }

            // Update inventory stock
            $inventoryStock->update(['quantity' => $newQuantity]);

            // Create stock movement record
            StockMovement::create([
                'warehouse_id' => $storageBin->warehouse_id,
                'product_id' => $inventoryStock->product_id,
                'from_bin_id' => $validated['adjustment_type'] === 'reduce' ? $storageBin->id : null,
                'to_bin_id' => $validated['adjustment_type'] === 'add' ? $storageBin->id : null,
                'batch_number' => $inventoryStock->batch_number,
                'serial_number' => $inventoryStock->serial_number,
                'quantity' => $adjustmentQty,
                'unit_of_measure' => $inventoryStock->unit_of_measure,
                'movement_type' => 'adjustment',
                'reference_type' => 'adjustment',
                'reference_number' => 'ADJ-' . time(),
                'movement_date' => now(),
                'performed_by' => Auth::id(),
                'notes' => 'Adjustment (' . $validated['adjustment_type'] . ') in bin ' . $storageBin->code . 
                          ' - Reason: ' . $validated['reason'] . 
                          ' - Old Qty: ' . $oldQuantity . ', New Qty: ' . $newQuantity . 
                          ($validated['notes'] ? ' - ' . $validated['notes'] : ''),
            ]);

            // Update bin status if needed
            if ($newQuantity == 0) {
                $totalStock = InventoryStock::where('storage_bin_id', $storageBin->id)
                    ->sum('quantity');
                if ($totalStock == 0) {
                    $storageBin->update(['status' => 'available']);
                }
            }

            DB::commit();

            return redirect()
                ->route('master.storage-bins.current-stock', $storageBin)
                ->with('success', 'Inventory adjusted successfully. Old quantity: ' . $oldQuantity . ', New quantity: ' . $newQuantity);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to adjust inventory: ' . $e->getMessage()]);
        }
    }

    /**
     * View stock movement history
     */
    public function viewHistory(StorageBin $storageBin)
    {
        $storageBin->load(['warehouse', 'storageArea']);
        
        // Get stock movements for this bin (from or to)
        $movements = StockMovement::where(function($query) use ($storageBin) {
                $query->where('from_bin_id', $storageBin->id)
                      ->orWhere('to_bin_id', $storageBin->id);
            })
            ->with(['product', 'performedBy', 'fromBin', 'toBin'])
            ->latest('movement_date')
            ->paginate(20);
        
        return view('master.storage-bins.history', compact('storageBin', 'movements'));
    }

    /**
     * Helper: Generate range for aisles/levels
     */
    private function generateRange($start, $end)
    {
        $range = [];
        
        if (is_numeric($start) && is_numeric($end)) {
            for ($i = $start; $i <= $end; $i++) {
                $range[] = $i;
            }
        } else {
            // For letters (A-Z)
            $current = $start;
            while ($current <= $end) {
                $range[] = $current;
                $current++;
            }
        }
        
        return $range;
    }
}