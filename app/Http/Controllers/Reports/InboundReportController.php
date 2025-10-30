<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Inbound;
use App\Models\InboundItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InboundReportController extends Controller
{
    // Receiving Report
    public function receivingReport(Request $request)
    {
        $query = Inbound::with(['supplier', 'warehouse', 'items.product']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('received_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('received_date', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('inbound_number', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('supplier', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $inbounds = $query->latest('received_date')->paginate(15);

        // Statistics
        $stats = [
            'total_received' => (clone $query)->whereNotNull('received_date')->count(),
            'today_received' => Inbound::whereDate('received_date', today())->count(),
            'total_items' => (clone $query)->sum('total_items'),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'total_suppliers' => (clone $query)->distinct('supplier_id')->count('supplier_id'),
        ];

        // Get suppliers and warehouses for filters
        $suppliers = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $statuses = ['pending', 'received', 'quality_check', 'completed', 'cancelled'];

        return view('reports.inbound.receiving-report', compact(
            'inbounds',
            'stats',
            'suppliers',
            'warehouses',
            'statuses'
        ));
    }

    // Putaway Report
    public function putawayReport(Request $request)
    {
        $query = InboundItem::with(['inbound.supplier', 'inbound.warehouse', 'product', 'storageBin']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereHas('inbound', function($q) use ($request) {
                $q->whereDate('received_date', '>=', $request->start_date);
            });
        }

        if ($request->filled('end_date')) {
            $query->whereHas('inbound', function($q) use ($request) {
                $q->whereDate('received_date', '<=', $request->end_date);
            });
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->whereHas('inbound', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            });
        }

        // Filter by storage bin
        if ($request->filled('storage_bin_id')) {
            $query->where('storage_bin_id', $request->storage_bin_id);
        }

        // Filter by putaway status
        if ($request->filled('putaway_status')) {
            if ($request->putaway_status === 'completed') {
                $query->whereNotNull('storage_bin_id');
            } else {
                $query->whereNull('storage_bin_id');
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('product', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%');
                })
                ->orWhereHas('inbound', function($q) use ($request) {
                    $q->where('inbound_number', 'like', '%' . $request->search . '%');
                });
            });
        }

        $items = $query->latest('created_at')->paginate(15);

        // Statistics
        $totalQuery = InboundItem::query();
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $totalQuery->whereHas('inbound', function($q) use ($request) {
                if ($request->filled('start_date')) {
                    $q->whereDate('received_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $q->whereDate('received_date', '<=', $request->end_date);
                }
            });
        }

        $stats = [
            'total_items' => (clone $totalQuery)->sum('quantity_received'),
            'putaway_completed' => (clone $totalQuery)->whereNotNull('storage_bin_id')->sum('quantity_received'),
            'putaway_pending' => (clone $totalQuery)->whereNull('storage_bin_id')->sum('quantity_received'),
            'total_skus' => (clone $totalQuery)->distinct('product_id')->count('product_id'),
            'total_bins_used' => (clone $totalQuery)->whereNotNull('storage_bin_id')->distinct('storage_bin_id')->count('storage_bin_id'),
        ];

        $stats['putaway_rate'] = $stats['total_items'] > 0 
            ? round(($stats['putaway_completed'] / $stats['total_items']) * 100, 2) 
            : 0;

        // Get warehouses and storage bins for filters
        $warehouses = Warehouse::orderBy('name')->get();
        $storageBins = \App\Models\StorageBin::orderBy('code')->get();

        return view('reports.inbound.putaway-report', compact(
            'items',
            'stats',
            'warehouses',
            'storageBins'
        ));
    }

    // Vendor Performance Report
    public function vendorPerformance(Request $request)
    {
        $query = Supplier::withCount(['inbounds' => function($q) use ($request) {
            if ($request->filled('start_date')) {
                $q->whereDate('received_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $q->whereDate('received_date', '<=', $request->end_date);
            }
        }])->with(['inbounds' => function($q) use ($request) {
            if ($request->filled('start_date')) {
                $q->whereDate('received_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $q->whereDate('received_date', '<=', $request->end_date);
            }
        }]);

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $suppliers = $query->having('inbounds_count', '>', 0)
            ->orderByDesc('inbounds_count')
            ->paginate(15);

        // Calculate performance metrics for each supplier
        foreach ($suppliers as $supplier) {
            $inbounds = $supplier->inbounds;
            
            $totalDeliveries = $inbounds->count();
            $onTimeDeliveries = $inbounds->filter(function($inbound) {
                return $inbound->received_date && $inbound->expected_date && 
                       $inbound->received_date->lte($inbound->expected_date);
            })->count();
            
            $completedDeliveries = $inbounds->where('status', 'completed')->count();
            $cancelledDeliveries = $inbounds->where('status', 'cancelled')->count();
            
            $totalItems = $inbounds->sum('total_items');
            
            $supplier->performance = [
                'total_deliveries' => $totalDeliveries,
                'on_time_deliveries' => $onTimeDeliveries,
                'on_time_rate' => $totalDeliveries > 0 ? round(($onTimeDeliveries / $totalDeliveries) * 100, 2) : 0,
                'completed_deliveries' => $completedDeliveries,
                'completion_rate' => $totalDeliveries > 0 ? round(($completedDeliveries / $totalDeliveries) * 100, 2) : 0,
                'cancelled_deliveries' => $cancelledDeliveries,
                'cancellation_rate' => $totalDeliveries > 0 ? round(($cancelledDeliveries / $totalDeliveries) * 100, 2) : 0,
                'total_items' => $totalItems,
                'avg_items_per_delivery' => $totalDeliveries > 0 ? round($totalItems / $totalDeliveries, 2) : 0,
            ];
        }

        // Overall statistics
        $allInbounds = Inbound::query();
        if ($request->filled('start_date')) {
            $allInbounds->whereDate('received_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $allInbounds->whereDate('received_date', '<=', $request->end_date);
        }

        $totalInbounds = $allInbounds->count();
        $onTimeInbounds = (clone $allInbounds)->whereNotNull('received_date')
            ->whereNotNull('expected_date')
            ->whereRaw('received_date <= expected_date')
            ->count();

        $stats = [
            'total_suppliers' => $suppliers->total(),
            'total_deliveries' => $totalInbounds,
            'overall_on_time_rate' => $totalInbounds > 0 ? round(($onTimeInbounds / $totalInbounds) * 100, 2) : 0,
            'active_suppliers' => Supplier::whereHas('inbounds', function($q) use ($request) {
                $q->where('status', '!=', 'cancelled');
                if ($request->filled('start_date')) {
                    $q->whereDate('received_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $q->whereDate('received_date', '<=', $request->end_date);
                }
            })->count(),
        ];

        return view('reports.inbound.vendor-performance', compact('suppliers', 'stats'));
    }

    // Receiving Accuracy Report
    public function receivingAccuracy(Request $request)
    {
        $query = InboundItem::with(['inbound.supplier', 'product']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereHas('inbound', function($q) use ($request) {
                $q->whereDate('received_date', '>=', $request->start_date);
            });
        }

        if ($request->filled('end_date')) {
            $query->whereHas('inbound', function($q) use ($request) {
                $q->whereDate('received_date', '<=', $request->end_date);
            });
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->whereHas('inbound', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        // Filter by accuracy status
        if ($request->filled('accuracy_status')) {
            if ($request->accuracy_status === 'accurate') {
                $query->whereColumn('quantity_received', '=', 'quantity_expected');
            } elseif ($request->accuracy_status === 'over') {
                $query->whereColumn('quantity_received', '>', 'quantity_expected');
            } elseif ($request->accuracy_status === 'under') {
                $query->whereColumn('quantity_received', '<', 'quantity_expected');
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('product', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%');
                })
                ->orWhereHas('inbound', function($q) use ($request) {
                    $q->where('inbound_number', 'like', '%' . $request->search . '%');
                });
            });
        }

        $items = $query->latest('created_at')->paginate(15);

        // Calculate accuracy for each item
        foreach ($items as $item) {
            $item->variance = $item->quantity_received - $item->quantity_expected;
            $item->variance_percentage = $item->quantity_expected > 0 
                ? round((($item->quantity_received - $item->quantity_expected) / $item->quantity_expected) * 100, 2) 
                : 0;
            $item->is_accurate = $item->quantity_received == $item->quantity_expected;
        }

        // Statistics
        $totalQuery = InboundItem::query();
        if ($request->filled('start_date') || $request->filled('end_date') || $request->filled('supplier_id')) {
            $totalQuery->whereHas('inbound', function($q) use ($request) {
                if ($request->filled('start_date')) {
                    $q->whereDate('received_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $q->whereDate('received_date', '<=', $request->end_date);
                }
                if ($request->filled('supplier_id')) {
                    $q->where('supplier_id', $request->supplier_id);
                }
            });
        }

        $totalItems = (clone $totalQuery)->count();
        $accurateItems = (clone $totalQuery)->whereColumn('quantity_received', '=', 'quantity_expected')->count();
        $overReceived = (clone $totalQuery)->whereColumn('quantity_received', '>', 'quantity_expected')->count();
        $underReceived = (clone $totalQuery)->whereColumn('quantity_received', '<', 'quantity_expected')->count();

        $totalExpected = (clone $totalQuery)->sum('quantity_expected');
        $totalReceived = (clone $totalQuery)->sum('quantity_received');

        $stats = [
            'total_items' => $totalItems,
            'accurate_items' => $accurateItems,
            'accuracy_rate' => $totalItems > 0 ? round(($accurateItems / $totalItems) * 100, 2) : 0,
            'over_received' => $overReceived,
            'under_received' => $underReceived,
            'total_expected' => $totalExpected,
            'total_received' => $totalReceived,
            'total_variance' => $totalReceived - $totalExpected,
        ];

        // Get suppliers for filter
        $suppliers = Supplier::orderBy('name')->get();

        return view('reports.inbound.receiving-accuracy', compact('items', 'stats', 'suppliers'));
    }

    
}