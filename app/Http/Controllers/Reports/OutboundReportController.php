<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use App\Models\PickingList;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OutboundReportController extends Controller
{
    /**
     * Picking Report
     */
    public function pickingReport(Request $request)
    {
        $query = PickingList::with(['warehouse', 'user', 'items.product']);

        // Filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('picking_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('picking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('picking_date', '<=', $request->date_to);
        }

        $pickingLists = $query->latest('picking_date')->paginate(20);

        // Statistics
        $totalPicking = PickingList::count();
        $completedPicking = PickingList::where('status', 'completed')->count();
        $pendingPicking = PickingList::where('status', 'pending')->count();
        $totalItems = PickingList::sum('total_items');

        $warehouses = Warehouse::all();
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];

        return view('reports.outbound.picking-report', compact(
            'pickingLists',
            'warehouses',
            'statuses',
            'totalPicking',
            'completedPicking',
            'pendingPicking',
            'totalItems'
        ));
    }

    /**
     * Shipping Report
     */
    public function shippingReport(Request $request)
    {
        $query = DeliveryOrder::with(['customer', 'warehouse', 'salesOrder', 'items.product']);

        // Filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('do_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('delivery_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('delivery_date', '<=', $request->date_to);
        }

        $deliveryOrders = $query->latest('delivery_date')->paginate(20);

        // Statistics
        $totalShipments = DeliveryOrder::count();
        $deliveredShipments = DeliveryOrder::where('status', 'delivered')->count();
        $inTransitShipments = DeliveryOrder::where('status', 'in_transit')->count();
        $totalValue = DeliveryOrder::sum('total_amount');

        $warehouses = Warehouse::all();
        $statuses = ['pending', 'packed', 'in_transit', 'delivered', 'cancelled'];

        return view('reports.outbound.shipping-report', compact(
            'deliveryOrders',
            'warehouses',
            'statuses',
            'totalShipments',
            'deliveredShipments',
            'inTransitShipments',
            'totalValue'
        ));
    }

    /**
     * Customer Orders Report
     */
    public function customerOrders(Request $request)
    {
        $query = SalesOrder::with(['customer', 'warehouse', 'items.product']);

        // Filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $salesOrders = $query->latest('order_date')->paginate(20);

        // Statistics
        $totalOrders = SalesOrder::count();
        $completedOrders = SalesOrder::where('status', 'completed')->count();
        $pendingOrders = SalesOrder::where('status', 'pending')->count();
        $totalRevenue = SalesOrder::where('status', 'completed')->sum('total_amount');

        // Top Customers
        $topCustomers = SalesOrder::select('customer_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->groupBy('customer_id')
            ->with('customer')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        $customers = Customer::all();
        $warehouses = Warehouse::all();
        $statuses = ['pending', 'confirmed', 'processing', 'completed', 'cancelled'];

        return view('reports.outbound.customer-orders', compact(
            'salesOrders',
            'customers',
            'warehouses',
            'statuses',
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'totalRevenue',
            'topCustomers'
        ));
    }

    /**
     * Picking Accuracy Report
     */
    public function pickingAccuracy(Request $request)
    {
        $query = PickingList::with(['warehouse', 'user', 'items']);

        // Filters
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('picking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('picking_date', '<=', $request->date_to);
        }

        $pickingLists = $query->where('status', 'completed')->latest('picking_date')->paginate(20);

        // Calculate accuracy
        $totalPicks = PickingList::where('status', 'completed')->count();
        $accuratePicks = PickingList::where('status', 'completed')
            ->where('is_accurate', true)
            ->count();
        
        $accuracyRate = $totalPicks > 0 ? round(($accuratePicks / $totalPicks) * 100, 2) : 0;

        // Accuracy by Warehouse
        $warehouseAccuracy = PickingList::select('warehouse_id', 
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_accurate = 1 THEN 1 ELSE 0 END) as accurate'))
            ->where('status', 'completed')
            ->groupBy('warehouse_id')
            ->with('warehouse')
            ->get()
            ->map(function($item) {
                $item->accuracy_rate = $item->total > 0 ? round(($item->accurate / $item->total) * 100, 2) : 0;
                return $item;
            });

        $warehouses = Warehouse::all();

        return view('reports.outbound.picking-accuracy', compact(
            'pickingLists',
            'warehouses',
            'totalPicks',
            'accuratePicks',
            'accuracyRate',
            'warehouseAccuracy'
        ));
    }

    /**
     * On-Time Delivery Report
     */
    public function onTimeDelivery(Request $request)
    {
        $query = DeliveryOrder::with(['customer', 'warehouse', 'salesOrder']);

        // Filters
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('delivery_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('delivery_date', '<=', $request->date_to);
        }

        $deliveryOrders = $query->where('status', 'delivered')->latest('delivery_date')->paginate(20);

        // Calculate on-time delivery
        $totalDeliveries = DeliveryOrder::where('status', 'delivered')->count();
        $onTimeDeliveries = DeliveryOrder::where('status', 'delivered')
            ->whereColumn('actual_delivery_date', '<=', 'expected_delivery_date')
            ->count();
        
        $onTimeRate = $totalDeliveries > 0 ? round(($onTimeDeliveries / $totalDeliveries) * 100, 2) : 0;

        // On-time by Warehouse
        $warehouseOnTime = DeliveryOrder::select('warehouse_id', 
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN actual_delivery_date <= expected_delivery_date THEN 1 ELSE 0 END) as on_time'))
            ->where('status', 'delivered')
            ->groupBy('warehouse_id')
            ->with('warehouse')
            ->get()
            ->map(function($item) {
                $item->on_time_rate = $item->total > 0 ? round(($item->on_time / $item->total) * 100, 2) : 0;
                return $item;
            });

        $warehouses = Warehouse::all();

        return view('reports.outbound.on-time-delivery', compact(
            'deliveryOrders',
            'warehouses',
            'totalDeliveries',
            'onTimeDeliveries',
            'onTimeRate',
            'warehouseOnTime'
        ));
    }
}