<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KpiReportController extends Controller
{
    public function dashboard(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $warehouseId = $request->input('warehouse_id');

        // Get warehouses for filter
        $warehouses = Warehouse::orderBy('name')->get();

        // Overall Accuracy (from opnames)
        $accuracyQuery = StockOpname::whereBetween('opname_date', [$dateFrom, $dateTo])
            ->where('status', 'completed');
        
        if ($warehouseId) {
            $accuracyQuery->where('warehouse_id', $warehouseId);
        }

        $overallAccuracy = $accuracyQuery->avg('accuracy_percentage') ?? 0;

        // Order Fulfillment Rate - berdasarkan movement_type 'outbound' atau 'picking'
        $totalOrders = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereIn('movement_type', ['outbound', 'picking'])
            ->where('reference_type', 'sales_order')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->distinct('reference_number')
            ->count('reference_number');

        $fulfilledOrders = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereIn('movement_type', ['outbound', 'picking'])
            ->where('reference_type', 'sales_order')
            ->whereNotNull('reference_number')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->distinct('reference_number')
            ->count('reference_number');

        $fulfillmentRate = $totalOrders > 0 ? ($fulfilledOrders / $totalOrders) * 100 : 0;

        // Inventory Turnover
        $totalMovements = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereIn('movement_type', ['outbound', 'picking'])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->sum('quantity');

        // Average inventory dari inventory_stocks
        $avgInventoryQuery = InventoryStock::where('status', 'available');
        if ($warehouseId) {
            $avgInventoryQuery->where('warehouse_id', $warehouseId);
        }
        $avgInventory = $avgInventoryQuery->sum('quantity') ?: 1;
        
        $inventoryTurnover = $avgInventory > 0 ? $totalMovements / $avgInventory : 0;

        // Efficiency Score - berdasarkan volume movements per hari
        $daysCount = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;
        $totalMovementsCount = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->count();

        $avgMovementsPerDay = $daysCount > 0 ? $totalMovementsCount / $daysCount : 0;
        $efficiencyScore = min(100, $avgMovementsPerDay * 2); // Skor berdasarkan volume

        // Recent Opnames
        $recentOpnames = StockOpname::with('warehouse')
            ->latest('opname_date')
            ->take(5)
            ->get();

        // Chart data - Monthly accuracy trend
        $monthlyAccuracy = StockOpname::whereBetween('opname_date', [now()->subMonths(6), now()])
            ->where('status', 'completed')
            ->selectRaw('DATE_FORMAT(opname_date, "%Y-%m") as month, AVG(accuracy_percentage) as accuracy')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('reports.kpi.dashboard', compact(
            'warehouses',
            'overallAccuracy',
            'fulfillmentRate',
            'inventoryTurnover',
            'efficiencyScore',
            'recentOpnames',
            'monthlyAccuracy',
            'dateFrom',
            'dateTo',
            'warehouseId'
        ));
    }

    public function accuracy(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $warehouseId = $request->input('warehouse_id');

        $warehouses = Warehouse::orderBy('name')->get();

        // Accuracy by warehouse
        $accuracyByWarehouse = StockOpname::with('warehouse')
            ->whereBetween('opname_date', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->selectRaw('warehouse_id, AVG(accuracy_percentage) as avg_accuracy, COUNT(*) as total_opnames')
            ->groupBy('warehouse_id')
            ->get();

        // Accuracy trend over time
        $accuracyTrend = StockOpname::whereBetween('opname_date', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->selectRaw('DATE(opname_date) as date, AVG(accuracy_percentage) as accuracy')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top performing products
        $topProducts = DB::table('stock_opname_items')
            ->join('stock_opnames', 'stock_opname_items.stock_opname_id', '=', 'stock_opnames.id')
            ->join('products', 'stock_opname_items.product_id', '=', 'products.id')
            ->whereBetween('stock_opnames.opname_date', [$dateFrom, $dateTo])
            ->where('stock_opnames.status', 'completed')
            ->selectRaw('products.name, products.sku, COUNT(*) as count_checks, AVG(CASE WHEN stock_opname_items.system_quantity = stock_opname_items.actual_quantity THEN 100 ELSE 0 END) as accuracy')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('accuracy')
            ->limit(10)
            ->get();

        return view('reports.kpi.accuracy', compact(
            'warehouses',
            'accuracyByWarehouse',
            'accuracyTrend',
            'topProducts',
            'dateFrom',
            'dateTo',
            'warehouseId'
        ));
    }

    public function efficiency(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $warehouseId = $request->input('warehouse_id');

        $warehouses = Warehouse::orderBy('name')->get();

        // Movement metrics by type
        $processingMetrics = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->selectRaw('
                movement_type,
                COUNT(*) as total_movements,
                SUM(quantity) as total_quantity,
                AVG(quantity) as avg_quantity
            ')
            ->groupBy('movement_type')
            ->get();

        // Daily efficiency trend - volume per day
        $dailyEfficiency = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->selectRaw('DATE(movement_date) as date, COUNT(*) as total, SUM(quantity) as total_quantity')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Warehouse efficiency comparison
        $warehouseEfficiency = StockMovement::with('warehouse')
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->selectRaw('warehouse_id, COUNT(*) as total_movements, SUM(quantity) as total_quantity')
            ->groupBy('warehouse_id')
            ->get();

        return view('reports.kpi.efficiency', compact(
            'warehouses',
            'processingMetrics',
            'dailyEfficiency',
            'warehouseEfficiency',
            'dateFrom',
            'dateTo',
            'warehouseId'
        ));
    }

    public function orderFulfillment(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $warehouseId = $request->input('warehouse_id');

        $warehouses = Warehouse::orderBy('name')->get();

        // Overall fulfillment metrics - berdasarkan sales_order
        $totalOrders = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereIn('movement_type', ['outbound', 'picking'])
            ->where('reference_type', 'sales_order')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->distinct('reference_number')
            ->count('reference_number');

        $completedOrders = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereIn('movement_type', ['outbound', 'picking'])
            ->where('reference_type', 'sales_order')
            ->whereNotNull('reference_number')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->distinct('reference_number')
            ->count('reference_number');

        $fulfillmentMetrics = (object)[
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => 0,
            'cancelled_orders' => 0
        ];

        // Daily fulfillment trend
        $dailyFulfillment = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereIn('movement_type', ['outbound', 'picking'])
            ->where('reference_type', 'sales_order')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->selectRaw('
                DATE(movement_date) as date,
                COUNT(DISTINCT reference_number) as total,
                COUNT(DISTINCT reference_number) as completed
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fulfillment by warehouse
        $warehouseFulfillment = StockMovement::with('warehouse')
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereIn('movement_type', ['outbound', 'picking'])
            ->where('reference_type', 'sales_order')
            ->selectRaw('
                warehouse_id,
                COUNT(DISTINCT reference_number) as total_orders,
                COUNT(DISTINCT reference_number) as completed_orders
            ')
            ->groupBy('warehouse_id')
            ->get();

        return view('reports.kpi.order-fulfillment', compact(
            'warehouses',
            'fulfillmentMetrics',
            'dailyFulfillment',
            'warehouseFulfillment',
            'dateFrom',
            'dateTo',
            'warehouseId'
        ));
    }

    public function inventoryTurnover(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $warehouseId = $request->input('warehouse_id');

        $warehouses = Warehouse::orderBy('name')->get();

        // Turnover by product category
        $turnoverByCategory = StockMovement::join('products', 'stock_movements.product_id', '=', 'products.id')
            ->leftJoin('inventory_stocks', function($join) use ($warehouseId) {
                $join->on('products.id', '=', 'inventory_stocks.product_id')
                     ->where('inventory_stocks.status', '=', 'available');
                if ($warehouseId) {
                    $join->where('inventory_stocks.warehouse_id', '=', $warehouseId);
                }
            })
            ->whereBetween('stock_movements.movement_date', [$dateFrom, $dateTo])
            ->whereIn('stock_movements.movement_type', ['outbound', 'picking'])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('stock_movements.warehouse_id', $warehouseId);
            })
            ->selectRaw('
                products.category,
                SUM(stock_movements.quantity) as total_out,
                AVG(inventory_stocks.quantity) as avg_stock
            ')
            ->groupBy('products.category')
            ->get()
            ->map(function($item) {
                $item->turnover_ratio = $item->avg_stock > 0 ? $item->total_out / $item->avg_stock : 0;
                return $item;
            });

        // Top moving products
        $topMovingProducts = StockMovement::join('products', 'stock_movements.product_id', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT product_id, SUM(quantity) as total_stock 
                               FROM inventory_stocks 
                               WHERE status = "available"' . 
                               ($warehouseId ? ' AND warehouse_id = ' . $warehouseId : '') . '
                               GROUP BY product_id) as inv_stock'), 
                      'products.id', '=', 'inv_stock.product_id')
            ->whereBetween('stock_movements.movement_date', [$dateFrom, $dateTo])
            ->whereIn('stock_movements.movement_type', ['outbound', 'picking'])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('stock_movements.warehouse_id', $warehouseId);
            })
            ->selectRaw('
                products.id,
                products.name,
                products.sku,
                COALESCE(inv_stock.total_stock, 0) as stock,
                SUM(stock_movements.quantity) as total_moved
            ')
            ->groupBy('products.id', 'products.name', 'products.sku', 'inv_stock.total_stock')
            ->orderByDesc('total_moved')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->turnover_ratio = $item->stock > 0 ? $item->total_moved / $item->stock : 0;
                return $item;
            });

        // Slow moving products
        $slowMovingProducts = StockMovement::join('products', 'stock_movements.product_id', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT product_id, SUM(quantity) as total_stock 
                               FROM inventory_stocks 
                               WHERE status = "available"' . 
                               ($warehouseId ? ' AND warehouse_id = ' . $warehouseId : '') . '
                               GROUP BY product_id) as inv_stock'), 
                      'products.id', '=', 'inv_stock.product_id')
            ->whereBetween('stock_movements.movement_date', [$dateFrom, $dateTo])
            ->whereIn('stock_movements.movement_type', ['outbound', 'picking'])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('stock_movements.warehouse_id', $warehouseId);
            })
            ->selectRaw('
                products.id,
                products.name,
                products.sku,
                COALESCE(inv_stock.total_stock, 0) as stock,
                SUM(stock_movements.quantity) as total_moved
            ')
            ->groupBy('products.id', 'products.name', 'products.sku', 'inv_stock.total_stock')
            ->having('total_moved', '>', 0)
            ->orderBy('total_moved')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->turnover_ratio = $item->stock > 0 ? $item->total_moved / $item->stock : 0;
                return $item;
            });

        // Monthly turnover trend
        $monthlyTurnover = StockMovement::whereBetween('movement_date', [now()->subMonths(6), now()])
            ->whereIn('movement_type', ['outbound', 'picking'])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->selectRaw('DATE_FORMAT(movement_date, "%Y-%m") as month, SUM(quantity) as total_out')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('reports.kpi.inventory-turnover', compact(
            'warehouses',
            'turnoverByCategory',
            'topMovingProducts',
            'slowMovingProducts',
            'monthlyTurnover',
            'dateFrom',
            'dateTo',
            'warehouseId'
        ));
    }
}