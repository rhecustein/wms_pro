<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Warehouse;
use App\Models\StorageArea;
use App\Models\Product;
use App\Models\StorageBin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\StockOpnameExport;
use Maatwebsite\Excel\Facades\Excel;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockOpname::with(['warehouse', 'storageArea', 'scheduledBy', 'completedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('opname_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('warehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Type
        if ($request->filled('opname_type')) {
            $query->where('opname_type', $request->opname_type);
        }

        // Filter by Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('opname_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('opname_date', '<=', $request->date_to);
        }

        $opnames = $query->latest('opname_date')->paginate(15)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->get();
        
        $statuses = ['planned', 'in_progress', 'completed', 'cancelled'];
        $types = ['full', 'cycle', 'spot'];

        return view('inventory.opnames.index', compact('opnames', 'warehouses', 'statuses', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        
        // Generate opname number
        $lastOpname = StockOpname::latest('id')->first();
        $nextNumber = $lastOpname ? intval(substr($lastOpname->opname_number, 4)) + 1 : 1;
        $opnameNumber = 'OPN-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return view('inventory.opnames.create', compact('warehouses', 'products', 'opnameNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'storage_area_id' => 'nullable|exists:storage_areas,id',
            'opname_date' => 'required|date',
            'opname_type' => 'required|in:full,cycle,spot',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.storage_bin_id' => 'required|exists:storage_bins,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.serial_number' => 'nullable|string',
            'items.*.system_quantity' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Generate opname number
            $lastOpname = StockOpname::latest('id')->lockForUpdate()->first();
            $nextNumber = $lastOpname ? intval(substr($lastOpname->opname_number, 4)) + 1 : 1;
            $opnameNumber = 'OPN-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Create opname
            $opname = StockOpname::create([
                'opname_number' => $opnameNumber,
                'warehouse_id' => $validated['warehouse_id'],
                'storage_area_id' => $validated['storage_area_id'],
                'opname_date' => $validated['opname_date'],
                'opname_type' => $validated['opname_type'],
                'status' => 'planned',
                'total_items_planned' => count($validated['items']),
                'scheduled_by' => Auth::id(),
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create opname items
            foreach ($validated['items'] as $item) {
                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $item['product_id'],
                    'storage_bin_id' => $item['storage_bin_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'system_quantity' => $item['system_quantity'],
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.opnames.show', $opname)
                ->with('success', 'Stock opname created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create stock opname: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockOpname $opname)
    {
        $opname->load([
            'warehouse', 
            'storageArea', 
            'items.product', 
            'items.storageBin', 
            'items.countedBy',
            'scheduledBy', 
            'completedBy', 
            'createdBy', 
            'updatedBy'
        ]);
        
        return view('inventory.opnames.show', compact('opname'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockOpname $opname)
    {
        if (!in_array($opname->status, ['planned'])) {
            return back()->with('error', 'Only planned opnames can be edited.');
        }

        $opname->load('items');
        $warehouses = Warehouse::where('is_active', true)->get();
        $storageAreas = StorageArea::where('warehouse_id', $opname->warehouse_id)->get();
        $products = Product::where('is_active', true)->get();

        return view('inventory.opnames.edit', compact('opname', 'warehouses', 'storageAreas', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockOpname $opname)
    {
        if (!in_array($opname->status, ['planned'])) {
            return back()->with('error', 'Only planned opnames can be updated.');
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'storage_area_id' => 'nullable|exists:storage_areas,id',
            'opname_date' => 'required|date',
            'opname_type' => 'required|in:full,cycle,spot',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.storage_bin_id' => 'required|exists:storage_bins,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.serial_number' => 'nullable|string',
            'items.*.system_quantity' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update opname
            $opname->update([
                'warehouse_id' => $validated['warehouse_id'],
                'storage_area_id' => $validated['storage_area_id'],
                'opname_date' => $validated['opname_date'],
                'opname_type' => $validated['opname_type'],
                'total_items_planned' => count($validated['items']),
                'notes' => $validated['notes'],
                'updated_by' => Auth::id(),
            ]);

            // Delete old items
            $opname->items()->delete();

            // Create new items
            foreach ($validated['items'] as $item) {
                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $item['product_id'],
                    'storage_bin_id' => $item['storage_bin_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'system_quantity' => $item['system_quantity'],
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.opnames.show', $opname)
                ->with('success', 'Stock opname updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update stock opname: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockOpname $opname)
    {
        if (!in_array($opname->status, ['planned'])) {
            return back()->with('error', 'Only planned opnames can be deleted.');
        }

        try {
            $opname->delete();
            return redirect()->route('inventory.opnames.index')
                ->with('success', 'Stock opname deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete stock opname: ' . $e->getMessage());
        }
    }

    /**
     * Start stock opname
     */
    public function start(StockOpname $opname)
    {
        if ($opname->status !== 'planned') {
            return back()->with('error', 'Only planned opnames can be started.');
        }

        try {
            $opname->update([
                'status' => 'in_progress',
                'started_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Stock opname started successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start stock opname: ' . $e->getMessage());
        }
    }

    /**
     * Complete stock opname
     */
    public function complete(StockOpname $opname)
    {
        if ($opname->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress opnames can be completed.');
        }

        // Check if all items are counted
        $pendingItems = $opname->items()->where('status', 'pending')->count();
        if ($pendingItems > 0) {
            return back()->with('error', "There are still {$pendingItems} items not counted. Please count all items before completing.");
        }

        DB::beginTransaction();
        try {
            // Calculate statistics
            $totalCounted = $opname->items()->whereNotNull('physical_quantity')->count();
            $varianceCount = $opname->items()->whereRaw('physical_quantity != system_quantity')->count();
            $accuracyPercentage = $totalCounted > 0 ? (($totalCounted - $varianceCount) / $totalCounted) * 100 : 0;

            $opname->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completed_by' => Auth::id(),
                'total_items_counted' => $totalCounted,
                'variance_count' => $varianceCount,
                'accuracy_percentage' => round($accuracyPercentage, 2),
                'updated_by' => Auth::id(),
            ]);

            // Update all counted items to adjusted status
            $opname->items()->where('status', 'counted')->update(['status' => 'adjusted']);

            DB::commit();

            return back()->with('success', 'Stock opname completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete stock opname: ' . $e->getMessage());
        }
    }

    /**
     * Cancel stock opname
     */
    public function cancel(StockOpname $opname)
    {
        if ($opname->status === 'completed') {
            return back()->with('error', 'Completed opnames cannot be cancelled.');
        }

        if ($opname->status === 'cancelled') {
            return back()->with('error', 'Opname is already cancelled.');
        }

        try {
            $opname->update([
                'status' => 'cancelled',
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Stock opname cancelled successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel stock opname: ' . $e->getMessage());
        }
    }

    /**
     * Show count form
     */
    public function count(StockOpname $opname)
    {
        if ($opname->status !== 'in_progress') {
            return redirect()->route('inventory.opnames.show', $opname)
                ->with('error', 'Only in-progress opnames can be counted.');
        }

        $opname->load(['warehouse', 'items.product', 'items.storageBin', 'items.countedBy']);
        
        return view('inventory.opnames.count', compact('opname'));
    }

    /**
     * Update item count
     */
    public function updateCount(Request $request, StockOpname $opname, StockOpnameItem $item)
    {
        if ($opname->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress opnames can be counted.');
        }

        $validated = $request->validate([
            'physical_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $variance = $validated['physical_quantity'] - $item->system_quantity;
            
            $item->update([
                'physical_quantity' => $validated['physical_quantity'],
                'variance' => $variance,
                'status' => 'counted',
                'counted_by' => Auth::id(),
                'counted_at' => now(),
                'notes' => $validated['notes'],
            ]);

            return back()->with('success', 'Item count updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update item count: ' . $e->getMessage());
        }
    }

    /**
     * Print stock opname
     */
    public function print(StockOpname $opname)
    {
        $opname->load([
            'warehouse', 
            'storageArea', 
            'items.product', 
            'items.storageBin', 
            'items.countedBy',
            'scheduledBy', 
            'completedBy'
        ]);
        
        return view('inventory.opnames.print', compact('opname'));
    }

    /**
     * Export stock opname to Excel
     */
    public function export(StockOpname $opname)
    {
        $opname->load([
            'warehouse', 
            'storageArea', 
            'items.product', 
            'items.storageBin', 
            'items.countedBy'
        ]);

        $fileName = 'stock-opname-' . $opname->opname_number . '-' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new StockOpnameExport($opname), $fileName);
    }

    /**
     * Get storage areas by warehouse (AJAX)
     */
    public function getStorageAreas($warehouseId)
    {
        $storageAreas = StorageArea::where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->get(['id', 'area_code', 'area_name']);

        return response()->json($storageAreas);
    }

    /**
     * Get storage bins by warehouse (AJAX)
     */
    public function getStorageBins($warehouseId)
    {
        $storageBins = StorageBin::where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->get(['id', 'bin_code', 'bin_name']);

        return response()->json($storageBins);
    }
}