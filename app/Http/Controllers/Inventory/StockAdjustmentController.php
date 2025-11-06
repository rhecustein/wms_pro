<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StorageBin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockAdjustmentsExport;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['warehouse', 'createdBy', 'approvedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('adjustment_number', 'like', "%{$search}%")
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
        if ($request->filled('adjustment_type')) {
            $query->where('adjustment_type', $request->adjustment_type);
        }

        // Filter by Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('adjustment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('adjustment_date', '<=', $request->date_to);
        }

        $adjustments = $query->latest('adjustment_date')->paginate(15)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->get();
        
        $statuses = ['draft', 'approved', 'posted', 'cancelled'];
        $types = ['addition', 'reduction', 'correction'];

        return view('inventory.adjustments.index', compact('adjustments', 'warehouses', 'statuses', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        
        // Generate adjustment number
        $lastAdjustment = StockAdjustment::latest('id')->first();
        $nextNumber = $lastAdjustment ? intval(substr($lastAdjustment->adjustment_number, 4)) + 1 : 1;
        $adjustmentNumber = 'ADJ-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return view('inventory.adjustments.create', compact('warehouses', 'adjustmentNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_date' => 'required|date',
            'adjustment_type' => 'required|in:addition,reduction,correction',
            'reason' => 'required|in:damaged,expired,lost,found,count_correction',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.storage_bin_id' => 'required|exists:storage_bins,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.serial_number' => 'nullable|string',
            'items.*.current_quantity' => 'required|integer|min:0',
            'items.*.adjusted_quantity' => 'required|integer|min:0',
            'items.*.unit_of_measure' => 'required|string',
            'items.*.reason' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate adjustment number
            $lastAdjustment = StockAdjustment::latest('id')->lockForUpdate()->first();
            $nextNumber = $lastAdjustment ? intval(substr($lastAdjustment->adjustment_number, 4)) + 1 : 1;
            $adjustmentNumber = 'ADJ-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Create adjustment
            $adjustment = StockAdjustment::create([
                'adjustment_number' => $adjustmentNumber,
                'warehouse_id' => $validated['warehouse_id'],
                'adjustment_date' => $validated['adjustment_date'],
                'adjustment_type' => $validated['adjustment_type'],
                'reason' => $validated['reason'],
                'status' => 'draft',
                'total_items' => count($validated['items']),
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create adjustment items
            foreach ($validated['items'] as $item) {
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'storage_bin_id' => $item['storage_bin_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'current_quantity' => $item['current_quantity'],
                    'adjusted_quantity' => $item['adjusted_quantity'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'reason' => $item['reason'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.adjustments.show', $adjustment)
                ->with('success', 'Stock adjustment created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create stock adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockAdjustment $adjustment)
    {
        $adjustment->load(['warehouse', 'items.product', 'items.storageBin', 'createdBy', 'approvedBy', 'updatedBy']);
        
        return view('inventory.adjustments.show', compact('adjustment'));
    }

    /**
     * Display print page for specific adjustment
     */
    public function print(StockAdjustment $adjustment)
    {
        $adjustment->load(['warehouse', 'items.product', 'items.storageBin', 'createdBy', 'approvedBy', 'updatedBy']);
        
        return view('inventory.adjustments.print', compact('adjustment'));
    }

    /**
     * Display print page for adjustments list
     */
    public function printList(Request $request)
    {
        $query = StockAdjustment::with(['warehouse', 'createdBy', 'approvedBy']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('adjustment_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('warehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('adjustment_type')) {
            $query->where('adjustment_type', $request->adjustment_type);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('adjustment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('adjustment_date', '<=', $request->date_to);
        }

        $adjustments = $query->latest('adjustment_date')->get();

        return view('inventory.adjustments.print-list', compact('adjustments'));
    }

    /**
     * Export adjustments to Excel
     */
    public function export(Request $request)
    {
        $query = StockAdjustment::with(['warehouse', 'createdBy', 'approvedBy']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('adjustment_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('warehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('adjustment_type')) {
            $query->where('adjustment_type', $request->adjustment_type);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('adjustment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('adjustment_date', '<=', $request->date_to);
        }

        $adjustments = $query->latest('adjustment_date')->get();

        $fileName = 'stock-adjustments-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(new StockAdjustmentsExport($adjustments), $fileName);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockAdjustment $adjustment)
    {
        if ($adjustment->status !== 'draft') {
            return back()->with('error', 'Only draft adjustments can be edited.');
        }

        $adjustment->load('items.product', 'items.storageBin');
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('inventory.adjustments.edit', compact('adjustment', 'warehouses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockAdjustment $adjustment)
    {
        if ($adjustment->status !== 'draft') {
            return back()->with('error', 'Only draft adjustments can be updated.');
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_date' => 'required|date',
            'adjustment_type' => 'required|in:addition,reduction,correction',
            'reason' => 'required|in:damaged,expired,lost,found,count_correction',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.storage_bin_id' => 'required|exists:storage_bins,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.serial_number' => 'nullable|string',
            'items.*.current_quantity' => 'required|integer|min:0',
            'items.*.adjusted_quantity' => 'required|integer|min:0',
            'items.*.unit_of_measure' => 'required|string',
            'items.*.reason' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update adjustment
            $adjustment->update([
                'warehouse_id' => $validated['warehouse_id'],
                'adjustment_date' => $validated['adjustment_date'],
                'adjustment_type' => $validated['adjustment_type'],
                'reason' => $validated['reason'],
                'total_items' => count($validated['items']),
                'notes' => $validated['notes'],
                'updated_by' => Auth::id(),
            ]);

            // Delete old items
            $adjustment->items()->delete();

            // Create new items
            foreach ($validated['items'] as $item) {
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'storage_bin_id' => $item['storage_bin_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'current_quantity' => $item['current_quantity'],
                    'adjusted_quantity' => $item['adjusted_quantity'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'reason' => $item['reason'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.adjustments.show', $adjustment)
                ->with('success', 'Stock adjustment updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update stock adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockAdjustment $adjustment)
    {
        if ($adjustment->status !== 'draft') {
            return back()->with('error', 'Only draft adjustments can be deleted.');
        }

        try {
            $adjustment->delete();
            return redirect()->route('inventory.adjustments.index')
                ->with('success', 'Stock adjustment deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete stock adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Approve stock adjustment
     */
    public function approve(StockAdjustment $adjustment)
    {
        if ($adjustment->status !== 'draft') {
            return back()->with('error', 'Only draft adjustments can be approved.');
        }

        try {
            $adjustment->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Stock adjustment approved successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve stock adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Post stock adjustment (update inventory)
     */
    public function post(StockAdjustment $adjustment)
    {
        if ($adjustment->status !== 'approved') {
            return back()->with('error', 'Only approved adjustments can be posted.');
        }

        DB::beginTransaction();
        try {
            // TODO: Update inventory stock levels here
            // This would involve updating stock_movements and product stock quantities
            
            $adjustment->update([
                'status' => 'posted',
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return back()->with('success', 'Stock adjustment posted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to post stock adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Cancel stock adjustment
     */
    public function cancel(StockAdjustment $adjustment)
    {
        if ($adjustment->status === 'posted') {
            return back()->with('error', 'Posted adjustments cannot be cancelled.');
        }

        if ($adjustment->status === 'cancelled') {
            return back()->with('error', 'Adjustment is already cancelled.');
        }

        try {
            $adjustment->update([
                'status' => 'cancelled',
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Stock adjustment cancelled successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel stock adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Search products (AJAX) - For Select2
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');
        
        $products = Product::where('is_active', true)
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'sku', 'barcode', 'current_stock')
            ->limit(50)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->name . ' (' . $product->sku . ')',
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'current_stock' => $product->current_stock
                ];
            });

        return response()->json([
            'results' => $products,
            'pagination' => ['more' => false]
        ]);
    }

    /**
     * Get storage bins by warehouse (AJAX) - With search support
     */
    public function getStorageBins($warehouseId)
    {
        $search = request()->get('q', '');
        
        $query = StorageBin::where('warehouse_id', $warehouseId)
            ->where('is_active', true);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('aisle', 'like', "%{$search}%")
                  ->orWhere('row', 'like', "%{$search}%");
            });
        }
        
        $storageBins = $query->orderBy('code')
            ->limit(100)
            ->get(['id', 'code', 'aisle', 'row', 'column', 'level', 'status'])
            ->map(function($bin) {
                return [
                    'id' => $bin->id,
                    'text' => $bin->code . ' (' . ucfirst($bin->status) . ')',
                    'code' => $bin->code,
                    'aisle' => $bin->aisle,
                    'row' => $bin->row,
                    'column' => $bin->column,
                    'level' => $bin->level,
                    'status' => $bin->status
                ];
            });

        return response()->json([
            'results' => $storageBins,
            'pagination' => ['more' => false]
        ]);
    }
}