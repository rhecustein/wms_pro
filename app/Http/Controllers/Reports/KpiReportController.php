<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\Product;
use App\Models\Warehouse;
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

        // Order Fulfillment Rate
        $totalOrders = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->where('movement_type', 'out')
            ->where('reference_type', 'order')
            ->count();

        $fulfilledOrders = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->where('movement_type', 'out')
            ->where('reference_type', 'order')
            ->where('status', 'completed')
            ->count();

        $fulfillmentRate = $totalOrders > 0 ? ($fulfilledOrders / $totalOrders) * 100 : 0;

        // Inventory Turnover
        $totalMovements = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->where('movement_type', 'out')
            ->sum('quantity');

        $avgInventory = Product::avg('stock') ?? 1;
        $inventoryTurnover = $avgInventory > 0 ? $totalMovements / $avgInventory : 0;

        // Efficiency Score (processing time)
        $avgProcessingTime = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereNotNull('processed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, processed_at)) as avg_time')
            ->value('avg_time') ?? 0;

        $efficiencyScore = $avgProcessingTime > 0 ? max(0, 100 - ($avgProcessingTime * 2)) : 100;

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
            'dateTo'
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
            'dateTo'
        ));
    }

    public function efficiency(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $warehouseId = $request->input('warehouse_id');

        $warehouses = Warehouse::orderBy('name')->get();

        // Processing time metrics
        $processingMetrics = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereNotNull('processed_at')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->selectRaw('
                movement_type,
                COUNT(*) as total_movements,
                AVG(TIMESTAMPDIFF(MINUTE, created_at, processed_at)) as avg_processing_time,
                MIN(TIMESTAMPDIFF(MINUTE, created_at, processed_at)) as min_processing_time,
                MAX(TIMESTAMPDIFF(MINUTE, created_at, processed_at)) as max_processing_time
            ')
            ->groupBy('movement_type')
            ->get();

        // Daily efficiency trend
        $dailyEfficiency = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereNotNull('processed_at')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->selectRaw('DATE(movement_date) as date, COUNT(*) as total, AVG(TIMESTAMPDIFF(MINUTE, created_at, processed_at)) as avg_time')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Warehouse efficiency comparison
        $warehouseEfficiency = StockMovement::with('warehouse')
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereNotNull('processed_at')
            ->selectRaw('warehouse_id, COUNT(*) as total_movements, AVG(TIMESTAMPDIFF(MINUTE, created_at, processed_at)) as avg_time')
            ->groupBy('warehouse_id')
            ->get();

        return view('reports.kpi.efficiency', compact(
            'warehouses',
            'processingMetrics',
            'dailyEfficiency',
            'warehouseEfficiency',
            'dateFrom',
            'dateTo'
        ));
    }

    public function orderFulfillment(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $warehouseId = $request->input('warehouse_id');

        $warehouses = Warehouse::orderBy('name')->get();

        // Overall fulfillment metrics
        $fulfillmentMetrics = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->where('movement_type', 'out')
            ->where('reference_type', 'order')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_orders
            ')
            ->first();

        // Daily fulfillment trend
        $dailyFulfillment = StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
            ->where('movement_type', 'out')
            ->where('reference_type', 'order')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->selectRaw('
                DATE(movement_date) as date,
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fulfillment by warehouse
        $warehouseFulfillment = StockMovement::with('warehouse')
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->where('movement_type', 'out')
            ->where('reference_type', 'order')
            ->selectRaw('
                warehouse_id,
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_orders
            ')
            ->groupBy('warehouse_id')
            ->get();

        return view('reports.kpi.order-fulfillment', compact(
            'warehouses',
            'fulfillmentMetrics',
            'dailyFulfillment',
            'warehouseFulfillment',
            'dateFrom',
            'dateTo'
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
            ->whereBetween('stock_movements.movement_date', [$dateFrom, $dateTo])
            ->where('stock_movements.movement_type', 'out')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('stock_movements.warehouse_id', $warehouseId);
            })
            ->selectRaw('
                products.category,
                SUM(stock_movements.quantity) as total_out,
                AVG(products.stock) as avg_stock
            ')
            ->groupBy('products.category')
            ->get()
            ->map(function($item) {
                $item->turnover_ratio = $item->avg_stock > 0 ? $item->total_out / $item->avg_stock : 0;
                return $item;
            });

        // Top moving products
        $topMovingProducts = StockMovement::join('products', 'stock_movements.product_id', '=', 'products.id')
            ->whereBetween('stock_movements.movement_date', [$dateFrom, $dateTo])
            ->where('stock_movements.movement_type', 'out')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('stock_movements.warehouse_id', $warehouseId);
            })
            ->selectRaw('
                products.id,
                products.name,
                products.sku,
                products.stock,
                SUM(stock_movements.quantity) as total_moved
            ')
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.stock')
            ->orderByDesc('total_moved')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->turnover_ratio = $item->stock > 0 ? $item->total_moved / $item->stock : 0;
                return $item;
            });

        // Slow moving products
        $slowMovingProducts = StockMovement::join('products', 'stock_movements.product_id', '=', 'products.id')
            ->whereBetween('stock_movements.movement_date', [$dateFrom, $dateTo])
            ->where('stock_movements.movement_type', 'out')
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('stock_movements.warehouse_id', $warehouseId);
            })
            ->selectRaw('
                products.id,
                products.name,
                products.sku,
                products.stock,
                SUM(stock_movements.quantity) as total_moved
            ')
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.stock')
            ->orderBy('total_moved')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->turnover_ratio = $item->stock > 0 ? $item->total_moved / $item->stock : 0;
                return $item;
            });

        // Monthly turnover trend
        $monthlyTurnover = StockMovement::whereBetween('movement_date', [now()->subMonths(6), now()])
            ->where('movement_type', 'out')
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
            'dateTo'
        ));
    }
}