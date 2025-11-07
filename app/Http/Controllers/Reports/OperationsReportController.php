<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\InventoryStock;
use App\Models\InboundShipment;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OperationsReportController extends Controller
{
    public function dailySummary(Request $request)
    {
        // Get filter parameters
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $warehouseId = $request->input('warehouse_id');
        
        // Get all warehouses for filter
        $warehouses = Warehouse::orderBy('name')->get();
        
        // Parse dates
        $selectedDate = Carbon::parse($date);
        $startOfDay = $selectedDate->copy()->startOfDay();
        $endOfDay = $selectedDate->copy()->endOfDay();
        
        // Base query untuk warehouse filter
        $warehouseQuery = function($query) use ($warehouseId) {
            if ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            }
        };
        
        // ===================================
        // INBOUND OPERATIONS
        // ===================================
        $inboundReceipts = InboundShipment::when($warehouseId, $warehouseQuery)
            ->whereBetween('arrival_date', [$startOfDay, $endOfDay])
            ->whereIn('status', ['received', 'completed'])
            ->with('purchaseOrder')
            ->get();
            
        $inboundData = [
            'total_receipts' => $inboundReceipts->count(),
                
            'scheduled_receipts' => InboundShipment::when($warehouseId, $warehouseQuery)
                ->whereBetween('scheduled_date', [$startOfDay, $endOfDay])
                ->where('status', 'scheduled')
                ->count(),
                
            'pallets_received' => $inboundReceipts->sum('received_pallets'),
                
            'boxes_received' => $inboundReceipts->sum('received_boxes'),
            
            'value_received' => $inboundReceipts->sum(function($shipment) {
                return $shipment->purchaseOrder ? $shipment->purchaseOrder->total_amount : 0;
            }),
                
            'in_transit' => InboundShipment::when($warehouseId, $warehouseQuery)
                ->where('status', 'in_transit')
                ->count(),
                
            'unloading' => InboundShipment::when($warehouseId, $warehouseQuery)
                ->whereIn('status', ['unloading', 'inspection'])
                ->count(),
        ];
        
        // ===================================
        // OUTBOUND OPERATIONS
        // ===================================
        $outboundData = [
            'total_orders' => SalesOrder::when($warehouseId, $warehouseQuery)
                ->whereBetween('order_date', [$startOfDay, $endOfDay])
                ->count(),
                
            'shipped_orders' => SalesOrder::when($warehouseId, $warehouseQuery)
                ->where('status', 'shipped')
                ->whereBetween('order_date', [$startOfDay, $endOfDay])
                ->count(),
                
            'delivered_orders' => SalesOrder::when($warehouseId, $warehouseQuery)
                ->where('status', 'delivered')
                ->whereBetween('order_date', [$startOfDay, $endOfDay])
                ->count(),
                
            'picking_orders' => SalesOrder::when($warehouseId, $warehouseQuery)
                ->where('status', 'picking')
                ->count(),
                
            'packing_orders' => SalesOrder::when($warehouseId, $warehouseQuery)
                ->where('status', 'packing')
                ->count(),
                
            'value_shipped' => SalesOrder::when($warehouseId, $warehouseQuery)
                ->whereIn('status', ['shipped', 'delivered'])
                ->whereBetween('order_date', [$startOfDay, $endOfDay])
                ->sum('total_amount'),
        ];
        
        // Calculate fulfillment rate
        $outboundData['fulfillment_rate'] = $outboundData['total_orders'] > 0 
            ? ($outboundData['shipped_orders'] / $outboundData['total_orders']) * 100 
            : 0;
        
        // ===================================
        // STOCK MOVEMENTS
        // ===================================
        $movementData = [
            'total_movements' => StockMovement::when($warehouseId, $warehouseQuery)
                ->whereBetween('movement_date', [$startOfDay, $endOfDay])
                ->count(),
                
            'inbound_moves' => StockMovement::when($warehouseId, $warehouseQuery)
                ->whereIn('movement_type', ['inbound', 'putaway', 'transfer_in'])
                ->whereBetween('movement_date', [$startOfDay, $endOfDay])
                ->count(),
                
            'outbound_moves' => StockMovement::when($warehouseId, $warehouseQuery)
                ->whereIn('movement_type', ['outbound', 'picking', 'transfer_out'])
                ->whereBetween('movement_date', [$startOfDay, $endOfDay])
                ->count(),
                
            'adjustments' => StockMovement::when($warehouseId, $warehouseQuery)
                ->where('movement_type', 'adjustment')
                ->whereBetween('movement_date', [$startOfDay, $endOfDay])
                ->count(),
        ];
        
        // ===================================
        // INVENTORY STATUS
        // ===================================
        $inventoryData = [
            'total_skus' => InventoryStock::when($warehouseId, $warehouseQuery)
                ->distinct('product_id')
                ->count('product_id'),
                
            'total_quantity' => InventoryStock::when($warehouseId, $warehouseQuery)
                ->sum('quantity'),
                
            'available_quantity' => InventoryStock::when($warehouseId, $warehouseQuery)
                ->sum(DB::raw('quantity - reserved_quantity')),
                
            'reserved_quantity' => InventoryStock::when($warehouseId, $warehouseQuery)
                ->sum('reserved_quantity'),
                
            'total_value' => DB::table('inventory_stocks')
                ->when($warehouseId, function($query) use ($warehouseId) {
                    $query->where('warehouse_id', $warehouseId);
                })
                ->select(DB::raw('SUM(quantity * COALESCE(cost_per_unit, 0)) as total_value'))
                ->value('total_value') ?? 0,
                
            'quarantine_items' => InventoryStock::when($warehouseId, $warehouseQuery)
                ->where('status', 'quarantine')
                ->count(),
                
            'damaged_items' => InventoryStock::when($warehouseId, $warehouseQuery)
                ->where('status', 'damaged')
                ->count(),
                
            'expired_items' => InventoryStock::when($warehouseId, $warehouseQuery)
                ->where('status', 'expired')
                ->count(),
        ];
        
        // ===================================
        // PENDING ORDERS
        // ===================================
        $pendingOrders = [
            'purchase_orders' => PurchaseOrder::when($warehouseId, $warehouseQuery)
                ->where('status', 'approved')
                ->count(),
                
            'sales_orders' => SalesOrder::when($warehouseId, $warehouseQuery)
                ->whereIn('status', ['pending', 'processing', 'picking'])
                ->count(),
        ];
        
        // ===================================
        // HOURLY ACTIVITY (for chart)
        // ===================================
        $hourlyActivity = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourStart = $selectedDate->copy()->setHour($hour)->startOfHour();
            $hourEnd = $hourStart->copy()->endOfHour();
            
            $hourlyActivity[] = [
                'hour' => $hour,
                'inbound' => InboundShipment::when($warehouseId, $warehouseQuery)
                    ->whereBetween('arrival_date', [$hourStart, $hourEnd])
                    ->whereIn('status', ['received', 'completed'])
                    ->count(),
                'outbound' => SalesOrder::when($warehouseId, $warehouseQuery)
                    ->whereIn('status', ['shipped', 'delivered'])
                    ->whereBetween('order_date', [$hourStart, $hourEnd])
                    ->count(),
                'movements' => StockMovement::when($warehouseId, $warehouseQuery)
                    ->whereBetween('movement_date', [$hourStart, $hourEnd])
                    ->count(),
            ];
        }
        
        // ===================================
        // TOP PRODUCTS (most active today)
        // ===================================
        $topProducts = StockMovement::when($warehouseId, $warehouseQuery)
            ->whereBetween('movement_date', [$startOfDay, $endOfDay])
            ->select('product_id', DB::raw('COUNT(*) as movement_count'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('movement_count')
            ->limit(10)
            ->with('product')
            ->get();
        
        // ===================================
        // RECENT ACTIVITIES
        // ===================================
        $recentActivities = StockMovement::when($warehouseId, $warehouseQuery)
            ->whereBetween('movement_date', [$startOfDay, $endOfDay])
            ->with(['product', 'warehouse', 'fromBin', 'toBin', 'user'])
            ->orderByDesc('movement_date')
            ->limit(15)
            ->get();
        
        return view('reports.operations.daily-summary', compact(
            'date',
            'selectedDate',
            'warehouseId',
            'warehouses',
            'inboundData',
            'outboundData',
            'movementData',
            'inventoryData',
            'pendingOrders',
            'hourlyActivity',
            'topProducts',
            'recentActivities'
        ));
    }
    
    public function warehouseUtilization(Request $request)
    {
        // TODO: Implement warehouse utilization report
        return view('reports.operations.warehouse-utilization');
    }
    
    public function laborProductivity(Request $request)
    {
        // TODO: Implement labor productivity report
        return view('reports.operations.labor-productivity');
    }
    
    public function equipmentUtilization(Request $request)
    {
        // TODO: Implement equipment utilization report
        return view('reports.operations.equipment-utilization');
    }
    
    public function spaceUtilization(Request $request)
    {
        // TODO: Implement space utilization report
        return view('reports.operations.space-utilization');
    }
}