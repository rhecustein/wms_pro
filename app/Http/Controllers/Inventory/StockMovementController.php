<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockMovementsExport;

class StockMovementController extends Controller
{
    /**
     * Display a listing of stock movements
     */
    public function index(Request $request)
    {
        $query = StockMovement::with(['warehouse', 'product', 'fromBin', 'toBin', 'performedBy'])
            ->orderBy('movement_date', 'desc');

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('batch_number', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        // Movement Type Filter
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        // Warehouse Filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Reference Type Filter
        if ($request->filled('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        $movements = $query->paginate(15)->withQueryString();

        // Get filter options
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $movementTypes = [
            'inbound' => 'Inbound',
            'outbound' => 'Outbound',
            'transfer' => 'Transfer',
            'adjustment' => 'Adjustment',
            'putaway' => 'Putaway',
            'picking' => 'Picking',
            'replenishment' => 'Replenishment'
        ];
        $referenceTypes = [
            'purchase_order' => 'Purchase Order',
            'sales_order' => 'Sales Order',
            'transfer' => 'Transfer',
            'adjustment' => 'Adjustment'
        ];

        return view('inventory.movements.index', compact(
            'movements',
            'warehouses',
            'movementTypes',
            'referenceTypes'
        ));
    }

    /**
     * Display the specified stock movement
     */
    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['warehouse', 'product', 'fromBin', 'toBin', 'performedBy']);

        return view('inventory.movements.show', compact('stockMovement'));
    }

    /**
     * Display print page for stock movements
     */
    public function print(Request $request)
    {
        $query = StockMovement::with(['warehouse', 'product', 'fromBin', 'toBin', 'performedBy'])
            ->orderBy('movement_date', 'desc');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('batch_number', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        // Get all results for printing (no pagination)
        $movements = $query->get();

        return view('inventory.movements.print', compact('movements'));
    }

    /**
     * Display stock movements by product
     */
    public function byProduct(Request $request, Product $product)
    {
        $query = StockMovement::with(['warehouse', 'fromBin', 'toBin', 'performedBy'])
            ->where('product_id', $product->id)
            ->orderBy('movement_date', 'desc');

        // Movement Type Filter
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        // Warehouse Filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        $movements = $query->paginate(15)->withQueryString();

        // Get filter options
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $movementTypes = [
            'inbound' => 'Inbound',
            'outbound' => 'Outbound',
            'transfer' => 'Transfer',
            'adjustment' => 'Adjustment',
            'putaway' => 'Putaway',
            'picking' => 'Picking',
            'replenishment' => 'Replenishment'
        ];

        return view('inventory.movements.by-product', compact(
            'product',
            'movements',
            'warehouses',
            'movementTypes'
        ));
    }

    /**
     * Display stock movements by warehouse
     */
    public function byWarehouse(Request $request, Warehouse $warehouse)
    {
        $query = StockMovement::with(['product', 'fromBin', 'toBin', 'performedBy'])
            ->where('warehouse_id', $warehouse->id)
            ->orderBy('movement_date', 'desc');

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        // Movement Type Filter
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        $movements = $query->paginate(15)->withQueryString();

        // Get filter options
        $movementTypes = [
            'inbound' => 'Inbound',
            'outbound' => 'Outbound',
            'transfer' => 'Transfer',
            'adjustment' => 'Adjustment',
            'putaway' => 'Putaway',
            'picking' => 'Picking',
            'replenishment' => 'Replenishment'
        ];

        return view('inventory.movements.by-warehouse', compact(
            'warehouse',
            'movements',
            'movementTypes'
        ));
    }

    /**
     * Export stock movements to Excel
     */
    public function export(Request $request)
    {
        $query = StockMovement::with(['warehouse', 'product', 'fromBin', 'toBin', 'performedBy'])
            ->orderBy('movement_date', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('batch_number', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        $movements = $query->get();

        $fileName = 'stock-movements-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(new StockMovementsExport($movements), $fileName);
    }
}