<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard
     */
    public function index(Request $request)
    {
        // Get selected warehouse from request or use default
        $selectedWarehouseId = $request->input('warehouse_id', null);
        
        // Get date range from request or use default (last 30 days)
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get all warehouses for filter dropdown
        $warehouses = DB::table('warehouses')
            ->where('is_active', true)
            ->select('id', 'name', 'code')
            ->get();

        // 1. KEY METRICS - Top Summary Cards
        $metrics = $this->getKeyMetrics($selectedWarehouseId);

        // 2. INVENTORY SUMMARY
        $inventorySummary = $this->getInventorySummary($selectedWarehouseId);

        // 3. TODAY'S ACTIVITIES
        $todayActivities = $this->getTodayActivities($selectedWarehouseId);

        // 4. PENDING TASKS
        $pendingTasks = $this->getPendingTasks($selectedWarehouseId);

        // 5. RECENT ORDERS
        $recentOrders = $this->getRecentOrders($selectedWarehouseId);

        // 6. ALERTS & NOTIFICATIONS
        $alerts = $this->getAlerts($selectedWarehouseId);

        // 7. CHARTS DATA
        $charts = $this->getChartsData($selectedWarehouseId, $startDate, $endDate);

        // 8. TOP PRODUCTS
        $topProducts = $this->getTopProducts($selectedWarehouseId);

        // 9. WAREHOUSE UTILIZATION
        $warehouseUtilization = $this->getWarehouseUtilization($selectedWarehouseId);

        // 10. RECENT ACTIVITIES LOG
        $recentActivities = $this->getRecentActivities($selectedWarehouseId);

        return view('dashboard.index', compact(
            'warehouses',
            'selectedWarehouseId',
            'startDate',
            'endDate',
            'metrics',
            'inventorySummary',
            'todayActivities',
            'pendingTasks',
            'recentOrders',
            'alerts',
            'charts',
            'topProducts',
            'warehouseUtilization',
            'recentActivities'
        ));
    }

    /**
     * Get key metrics for dashboard cards
     */
    private function getKeyMetrics($warehouseId = null)
    {
        $query = DB::table('inventory_stocks');
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        // Total Products
        $totalProducts = DB::table('products')
            ->where('is_active', true)
            ->count();

        // Total Stock Value
        $totalStockValue = $query->clone()
            ->where('status', 'available')
            ->sum(DB::raw('quantity * cost_per_unit'));

        // Total SKUs
        $totalSKUs = $query->clone()
            ->where('quantity', '>', 0)
            ->distinct('product_id')
            ->count('product_id');

        // Total Stock Quantity
        $totalStockQty = $query->clone()
            ->where('status', 'available')
            ->sum('quantity');

        // Inbound Orders Today
        $inboundToday = DB::table('good_receivings')
            ->whereDate('receiving_date', Carbon::today())
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->count();

        // Outbound Orders Today
        $outboundToday = DB::table('sales_orders')
            ->whereDate('order_date', Carbon::today())
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->count();

        // Pending Pickings
        $pendingPickings = DB::table('picking_orders')
            ->whereIn('status', ['pending', 'assigned', 'in_progress'])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->count();

        // Pending Deliveries
        $pendingDeliveries = DB::table('delivery_orders')
            ->whereIn('status', ['prepared', 'loaded', 'in_transit'])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->count();

        return [
            'total_products' => $totalProducts,
            'total_stock_value' => $totalStockValue,
            'total_skus' => $totalSKUs,
            'total_stock_qty' => $totalStockQty,
            'inbound_today' => $inboundToday,
            'outbound_today' => $outboundToday,
            'pending_pickings' => $pendingPickings,
            'pending_deliveries' => $pendingDeliveries,
        ];
    }

    /**
     * Get inventory summary
     */
    private function getInventorySummary($warehouseId = null)
    {
        $query = DB::table('inventory_stocks');
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return [
            'available' => $query->clone()->where('status', 'available')->sum('quantity'),
            'reserved' => $query->clone()->where('status', 'reserved')->sum('quantity'),
            'quarantine' => $query->clone()->where('status', 'quarantine')->sum('quantity'),
            'damaged' => $query->clone()->where('status', 'damaged')->sum('quantity'),
            'expired' => $query->clone()->where('status', 'expired')->sum('quantity'),
        ];
    }

    /**
     * Get today's activities
     */
    private function getTodayActivities($warehouseId = null)
    {
        $today = Carbon::today();

        return [
            'goods_received' => DB::table('good_receivings')
                ->whereDate('receiving_date', $today)
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->where('status', 'completed')
                ->count(),
                
            'putaways_completed' => DB::table('putaway_tasks')
                ->whereDate('completed_at', $today)
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->where('status', 'completed')
                ->count(),
                
            'orders_picked' => DB::table('picking_orders')
                ->whereDate('completed_at', $today)
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->where('status', 'completed')
                ->count(),
                
            'orders_packed' => DB::table('packing_orders')
                ->whereDate('completed_at', $today)
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->where('status', 'completed')
                ->count(),
                
            'orders_shipped' => DB::table('delivery_orders')
                ->whereDate('delivered_at', $today)
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->where('status', 'delivered')
                ->count(),
                
            'replenishments' => DB::table('replenishment_tasks')
                ->whereDate('completed_at', $today)
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->where('status', 'completed')
                ->count(),
        ];
    }

    /**
     * Get pending tasks
     */
    private function getPendingTasks($warehouseId = null)
    {
        return [
            'putaway' => DB::table('putaway_tasks')
                ->whereIn('status', ['pending', 'assigned'])
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->count(),
                
            'picking' => DB::table('picking_orders')
                ->whereIn('status', ['pending', 'assigned'])
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->count(),
                
            'packing' => DB::table('packing_orders')
                ->whereIn('status', ['pending'])
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->count(),
                
            'replenishment' => DB::table('replenishment_tasks')
                ->whereIn('status', ['pending', 'assigned'])
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->count(),
                
            'quality_check' => DB::table('good_receivings')
                ->where('quality_status', 'pending')
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->count(),
                
            'stock_adjustment' => DB::table('stock_adjustments')
                ->where('status', 'draft')
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->count(),
        ];
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders($warehouseId = null)
    {
        // Recent Sales Orders
        $salesOrders = DB::table('sales_orders as so')
            ->join('customers as c', 'so.customer_id', '=', 'c.id')
            ->select(
                'so.id',
                'so.so_number',
                'c.name as customer_name',
                'so.order_date',
                'so.status',
                'so.total_amount'
            )
            ->when($warehouseId, fn($q) => $q->where('so.warehouse_id', $warehouseId))
            ->orderBy('so.created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Purchase Orders
        $purchaseOrders = DB::table('purchase_orders as po')
            ->join('vendors as v', 'po.vendor_id', '=', 'v.id')
            ->select(
                'po.id',
                'po.po_number',
                'v.name as vendor_name',
                'po.po_date',
                'po.status',
                'po.total_amount'
            )
            ->when($warehouseId, fn($q) => $q->where('po.warehouse_id', $warehouseId))
            ->orderBy('po.created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'sales_orders' => $salesOrders,
            'purchase_orders' => $purchaseOrders,
        ];
    }

    /**
     * Get alerts and notifications
     */
    private function getAlerts($warehouseId = null)
    {
        $alerts = [];

        // Low Stock Alert
        $lowStockCount = DB::table('inventory_stocks as inv')
            ->join('products as p', 'inv.product_id', '=', 'p.id')
            ->where('inv.status', 'available')
            ->when($warehouseId, fn($q) => $q->where('inv.warehouse_id', $warehouseId))
            ->whereColumn('inv.quantity', '<=', 'p.min_stock_level')
            ->count();

        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'title' => 'Low Stock Alert',
                'message' => "{$lowStockCount} products are below minimum stock level",
                'link' => route('inventory.stocks.low-stock'),
            ];
        }

        // Expiring Soon Alert (within 30 days)
        $expiringSoonCount = DB::table('inventory_stocks')
            ->where('status', 'available')
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->count();

        if ($expiringSoonCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'clock',
                'title' => 'Expiring Soon',
                'message' => "{$expiringSoonCount} items will expire within 30 days",
                'link' => route('inventory.stocks.expiring'),
            ];
        }

        // Expired Items Alert
        $expiredCount = DB::table('inventory_stocks')
            ->where('status', 'available')
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::now())
            ->count();

        if ($expiredCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'times-circle',
                'title' => 'Expired Items',
                'message' => "{$expiredCount} items have expired and need to be removed",
                'link' => route('inventory.stocks.expired'),
            ];
        }

        // Pending Approval Alert
        $pendingApprovals = DB::table('stock_adjustments')
            ->where('status', 'draft')
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->count();

        if ($pendingApprovals > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'clipboard-check',
                'title' => 'Pending Approvals',
                'message' => "{$pendingApprovals} stock adjustments require approval",
                'link' => route('inventory.adjustments.index'),
            ];
        }

        // Overdue Deliveries Alert
        $overdueDeliveries = DB::table('delivery_orders')
            ->whereIn('status', ['prepared', 'loaded', 'in_transit'])
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->where('delivery_date', '<', Carbon::now())
            ->count();

        if ($overdueDeliveries > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'truck',
                'title' => 'Overdue Deliveries',
                'message' => "{$overdueDeliveries} deliveries are overdue",
                'link' => route('outbound.delivery-orders.index'),
            ];
        }

        return $alerts;
    }

    /**
     * Get charts data
     */
    private function getChartsData($warehouseId, $startDate, $endDate)
    {
        // Inbound vs Outbound Trend (Last 7 days)
        $dates = collect();
        $inboundData = collect();
        $outboundData = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates->push(Carbon::parse($date)->format('M d'));

            // Inbound count
            $inbound = DB::table('good_receivings')
                ->whereDate('receiving_date', $date)
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->where('status', 'completed')
                ->count();
            $inboundData->push($inbound);

            // Outbound count
            $outbound = DB::table('delivery_orders')
                ->whereDate('delivery_date', $date)
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->whereIn('status', ['delivered', 'in_transit'])
                ->count();
            $outboundData->push($outbound);
        }

        // Order Status Distribution
        $orderStatusData = DB::table('sales_orders')
            ->select('status', DB::raw('count(*) as count'))
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('order_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Stock by Category
        $stockByCategory = DB::table('inventory_stocks as inv')
            ->join('products as p', 'inv.product_id', '=', 'p.id')
            ->join('product_categories as pc', 'p.category_id', '=', 'pc.id')
            ->select('pc.name', DB::raw('sum(inv.quantity) as total'))
            ->where('inv.status', 'available')
            ->when($warehouseId, fn($q) => $q->where('inv.warehouse_id', $warehouseId))
            ->groupBy('pc.id', 'pc.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return [
            'inbound_outbound' => [
                'labels' => $dates,
                'inbound' => $inboundData,
                'outbound' => $outboundData,
            ],
            'order_status' => $orderStatusData,
            'stock_by_category' => $stockByCategory,
        ];
    }

    /**
     * Get top products (most moved)
     */
    private function getTopProducts($warehouseId = null)
    {
        return DB::table('stock_movements as sm')
            ->join('products as p', 'sm.product_id', '=', 'p.id')
            ->select(
                'p.id',
                'p.sku',
                'p.name',
                DB::raw('sum(sm.quantity) as total_moved'),
                DB::raw('count(*) as movement_count')
            )
            ->when($warehouseId, fn($q) => $q->where('sm.warehouse_id', $warehouseId))
            ->whereBetween('sm.movement_date', [Carbon::now()->subDays(7), Carbon::now()])
            ->groupBy('p.id', 'p.sku', 'p.name')
            ->orderBy('total_moved', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get warehouse utilization
     */
    private function getWarehouseUtilization($warehouseId = null)
    {
        if ($warehouseId) {
            $warehouse = DB::table('warehouses')->find($warehouseId);
            
            // Count total bins
            $totalBins = DB::table('storage_bins as sb')
                ->join('storage_areas as sa', 'sb.storage_area_id', '=', 'sa.id')
                ->where('sa.warehouse_id', $warehouseId)
                ->where('sb.is_active', true)
                ->count();

            // Count occupied bins
            $occupiedBins = DB::table('storage_bins as sb')
                ->join('storage_areas as sa', 'sb.storage_area_id', '=', 'sa.id')
                ->where('sa.warehouse_id', $warehouseId)
                ->where('sb.is_occupied', true)
                ->count();

            $utilizationPercent = $totalBins > 0 ? round(($occupiedBins / $totalBins) * 100, 2) : 0;

            return [
                'warehouse_name' => $warehouse->name,
                'total_bins' => $totalBins,
                'occupied_bins' => $occupiedBins,
                'available_bins' => $totalBins - $occupiedBins,
                'utilization_percent' => $utilizationPercent,
            ];
        }

        // All warehouses summary
        return DB::table('warehouses as w')
            ->leftJoin('storage_areas as sa', 'w.id', '=', 'sa.warehouse_id')
            ->leftJoin('storage_bins as sb', 'sa.id', '=', 'sb.storage_area_id')
            ->select(
                'w.name',
                DB::raw('count(distinct sb.id) as total_bins'),
                DB::raw('sum(case when sb.is_occupied = 1 then 1 else 0 end) as occupied_bins')
            )
            ->where('w.is_active', true)
            ->groupBy('w.id', 'w.name')
            ->get();
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($warehouseId = null)
    {
        return DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select(
                'activity_logs.*',
                'users.name as user_name'
            )
            ->orderBy('activity_logs.created_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get quick stats for API/AJAX
     */
    public function quickStats(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        
        $stats = [
            'metrics' => $this->getKeyMetrics($warehouseId),
            'today_activities' => $this->getTodayActivities($warehouseId),
            'pending_tasks' => $this->getPendingTasks($warehouseId),
        ];

        return response()->json($stats);
    }

    /**
     * Refresh dashboard data via AJAX
     */
    public function refresh(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        return response()->json([
            'metrics' => $this->getKeyMetrics($warehouseId),
            'inventory_summary' => $this->getInventorySummary($warehouseId),
            'today_activities' => $this->getTodayActivities($warehouseId),
            'pending_tasks' => $this->getPendingTasks($warehouseId),
            'alerts' => $this->getAlerts($warehouseId),
            'charts' => $this->getChartsData($warehouseId, $startDate, $endDate),
        ]);
    }
}