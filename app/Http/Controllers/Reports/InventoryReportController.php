<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryReportController extends Controller
{
    /**
     * Stock Summary Report
     */
    public function stockSummary(Request $request)
    {
        $warehouses = Warehouse::all();
        $categories = ProductCategory::all();
        
        $query = Product::with(['category'])
            ->where('is_active', true);
        
        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->whereHas('stockMovements', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            });
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }
        
        $products = $query->paginate(20);
        
        // Statistics
        $totalProducts = Product::where('is_active', true)->count();
        $totalStockValue = Product::where('is_active', true)->sum('stock_value');
        $lowStockItems = Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->whereNotNull('reorder_level')
            ->count();
        $outOfStockItems = Product::where('is_active', true)
            ->where('current_stock', 0)
            ->count();
        
        return view('reports.inventory.stock-summary', compact(
            'products',
            'warehouses',
            'categories',
            'totalProducts',
            'totalStockValue',
            'lowStockItems',
            'outOfStockItems'
        ));
    }
    
    /**
     * Stock Movements Report
     */
    public function stockMovements(Request $request)
    {
        $warehouses = Warehouse::all();
        
        $query = StockMovement::with(['product', 'warehouse', 'user']);
        
        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('movement_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('movement_date', '<=', $request->end_date);
        }
        
        // Movement type filter
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }
        
        // Warehouse filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }
        
        $movements = $query->orderBy('movement_date', 'desc')->paginate(20);
        
        // Statistics
        $totalMovements = StockMovement::count();
        $totalIn = StockMovement::where('movement_type', 'in')->sum('quantity');
        $totalOut = StockMovement::where('movement_type', 'out')->sum('quantity');
        $totalAdjustment = StockMovement::where('movement_type', 'adjustment')->sum('quantity');
        
        $movementTypes = ['in', 'out', 'adjustment', 'transfer', 'return'];
        
        return view('reports.inventory.stock-movements', compact(
            'movements',
            'warehouses',
            'movementTypes',
            'totalMovements',
            'totalIn',
            'totalOut',
            'totalAdjustment'
        ));
    }
    
    /**
     * Aging Report
     */
    public function agingReport(Request $request)
    {
        $warehouses = Warehouse::all();
        $categories = ProductCategory::all();
        
        $query = Product::with(['category', 'stockMovements'])
            ->where('is_active', true)
            ->where('current_stock', '>', 0);
        
        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->whereHas('stockMovements', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            });
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        $products = $query->get()->map(function($product) {
            $lastMovement = $product->stockMovements()
                ->where('movement_type', 'in')
                ->orderBy('movement_date', 'desc')
                ->first();
            
            $product->last_received = $lastMovement ? $lastMovement->movement_date : null;
            $product->days_in_stock = $lastMovement 
                ? Carbon::parse($lastMovement->movement_date)->diffInDays(now()) 
                : 0;
            
            // Categorize aging
            if ($product->days_in_stock <= 30) {
                $product->aging_category = '0-30 days';
            } elseif ($product->days_in_stock <= 60) {
                $product->aging_category = '31-60 days';
            } elseif ($product->days_in_stock <= 90) {
                $product->aging_category = '61-90 days';
            } elseif ($product->days_in_stock <= 180) {
                $product->aging_category = '91-180 days';
            } else {
                $product->aging_category = '180+ days';
            }
            
            return $product;
        });
        
        // Filter by aging category
        if ($request->filled('aging_category')) {
            $products = $products->where('aging_category', $request->aging_category);
        }
        
        // Statistics
        $aging0_30 = $products->where('aging_category', '0-30 days')->count();
        $aging31_60 = $products->where('aging_category', '31-60 days')->count();
        $aging61_90 = $products->where('aging_category', '61-90 days')->count();
        $aging91_180 = $products->where('aging_category', '91-180 days')->count();
        $aging180plus = $products->where('aging_category', '180+ days')->count();
        
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($request->page ?? 1, 20),
            $products->count(),
            20,
            $request->page ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        $agingCategories = ['0-30 days', '31-60 days', '61-90 days', '91-180 days', '180+ days'];
        
        return view('reports.inventory.aging-report', compact(
            'products',
            'warehouses',
            'categories',
            'agingCategories',
            'aging0_30',
            'aging31_60',
            'aging61_90',
            'aging91_180',
            'aging180plus'
        ));
    }
    
    /**
     * Expiry Report
     */
    public function expiryReport(Request $request)
    {
        $warehouses = Warehouse::all();
        $categories = ProductCategory::all();
        
        $query = Product::with(['category'])
            ->where('is_active', true)
            ->where('is_expiry_tracked', true)
            ->where('current_stock', '>', 0);
        
        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->whereHas('stockMovements', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            });
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by expiry status
        if ($request->filled('expiry_status')) {
            $now = now();
            
            switch ($request->expiry_status) {
                case 'expired':
                    // Products with shelf life that has passed
                    $query->whereRaw('DATE_ADD(created_at, INTERVAL shelf_life_days DAY) < ?', [$now]);
                    break;
                case 'expiring_soon':
                    // Expiring within 30 days
                    $query->whereRaw('DATE_ADD(created_at, INTERVAL shelf_life_days DAY) BETWEEN ? AND ?', 
                        [$now, $now->copy()->addDays(30)]);
                    break;
                case 'expiring_3_months':
                    // Expiring within 3 months
                    $query->whereRaw('DATE_ADD(created_at, INTERVAL shelf_life_days DAY) BETWEEN ? AND ?', 
                        [$now->copy()->addDays(30), $now->copy()->addMonths(3)]);
                    break;
                case 'good':
                    // Good condition (more than 3 months)
                    $query->whereRaw('DATE_ADD(created_at, INTERVAL shelf_life_days DAY) > ?', 
                        [$now->copy()->addMonths(3)]);
                    break;
            }
        }
        
        $products = $query->get()->map(function($product) {
            // Calculate expiry date based on created_at + shelf_life_days
            if ($product->shelf_life_days) {
                $product->expiry_date = Carbon::parse($product->created_at)
                    ->addDays($product->shelf_life_days);
            } else {
                $product->expiry_date = null;
            }
            return $product;
        })->filter(function($product) {
            return $product->expiry_date !== null;
        });
        
        // Sort by expiry date
        $products = $products->sortBy('expiry_date');
        
        // Statistics
        $expired = $products->filter(function($product) {
            return $product->expiry_date < now();
        })->count();
        
        $expiringSoon = $products->filter(function($product) {
            return $product->expiry_date >= now() && 
                   $product->expiry_date <= now()->addDays(30);
        })->count();
        
        $expiring3Months = $products->filter(function($product) {
            return $product->expiry_date > now()->addDays(30) && 
                   $product->expiry_date <= now()->addMonths(3);
        })->count();
        
        $goodCondition = $products->filter(function($product) {
            return $product->expiry_date > now()->addMonths(3);
        })->count();
        
        // Paginate
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($request->page ?? 1, 20),
            $products->count(),
            20,
            $request->page ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        $expiryStatuses = [
            'expired' => 'Expired',
            'expiring_soon' => 'Expiring Soon (30 days)',
            'expiring_3_months' => 'Expiring in 3 Months',
            'good' => 'Good Condition'
        ];
        
        return view('reports.inventory.expiry-report', compact(
            'products',
            'warehouses',
            'categories',
            'expiryStatuses',
            'expired',
            'expiringSoon',
            'expiring3Months',
            'goodCondition'
        ));
    }
    
    /**
     * Low Stock Report
     */
    public function lowStockReport(Request $request)
    {
        $warehouses = Warehouse::all();
        $categories = ProductCategory::all();
        
        $query = Product::with(['category'])
            ->where('is_active', true)
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->whereNotNull('reorder_level');
        
        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->whereHas('stockMovements', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            });
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }
        
        $products = $query->orderBy('current_stock', 'asc')->paginate(20);
        
        // Statistics
        $criticalStock = Product::where('is_active', true)
            ->where('current_stock', 0)
            ->whereNotNull('reorder_level')
            ->count();
            
        $lowStock = Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->where('current_stock', '>', 0)
            ->whereNotNull('reorder_level')
            ->count();
            
        $totalValue = Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->whereNotNull('reorder_level')
            ->sum('stock_value');
        
        return view('reports.inventory.low-stock-report', compact(
            'products',
            'warehouses',
            'categories',
            'criticalStock',
            'lowStock',
            'totalValue'
        ));
    }
    
    /**
     * Dead Stock Report
     */
    public function deadStockReport(Request $request)
    {
        $warehouses = Warehouse::all();
        $categories = ProductCategory::all();
        $days = $request->input('days', 90); // Default 90 days
        
        $query = Product::with(['category', 'stockMovements'])
            ->where('is_active', true)
            ->where('current_stock', '>', 0);
        
        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->whereHas('stockMovements', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            });
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        $products = $query->get()->filter(function($product) use ($days) {
            $lastMovement = $product->stockMovements()
                ->where('movement_type', 'out')
                ->orderBy('movement_date', 'desc')
                ->first();
            
            if (!$lastMovement) {
                $product->last_movement_date = null;
                $product->days_inactive = Carbon::parse($product->created_at)->diffInDays(now());
                return true; // Never moved out
            }
            
            $daysSinceLastMovement = Carbon::parse($lastMovement->movement_date)->diffInDays(now());
            
            $product->last_movement_date = $lastMovement->movement_date;
            $product->days_inactive = $daysSinceLastMovement;
            
            return $daysSinceLastMovement >= $days;
        });
        
        // Statistics
        $totalDeadStock = $products->count();
        $totalValue = $products->sum('stock_value');
        $totalQuantity = $products->sum('current_stock');
        
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($request->page ?? 1, 20),
            $products->count(),
            20,
            $request->page ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('reports.inventory.dead-stock-report', compact(
            'products',
            'warehouses',
            'categories',
            'days',
            'totalDeadStock',
            'totalValue',
            'totalQuantity'
        ));
    }
    
    /**
     * Stock Valuation Report
     */
    public function stockValuation(Request $request)
    {
        $warehouses = Warehouse::all();
        $categories = ProductCategory::all();
        
        $query = Product::with(['category'])
            ->where('is_active', true)
            ->where('current_stock', '>', 0);
        
        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->whereHas('stockMovements', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            });
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }
        
        $products = $query->get();
        
        // Sort by value
        if ($request->filled('sort_by') && $request->sort_by === 'value') {
            $products = $products->sortByDesc('stock_value');
        }
        
        // Statistics
        $totalValue = $products->sum('stock_value');
        $totalProducts = $products->count();
        $totalQuantity = $products->sum('current_stock');
        $averageValue = $totalProducts > 0 ? $totalValue / $totalProducts : 0;
        
        // Top 10 by value
        $topByValue = $products->sortByDesc('stock_value')->take(10);
        
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($request->page ?? 1, 20),
            $products->count(),
            20,
            $request->page ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('reports.inventory.stock-valuation', compact(
            'products',
            'warehouses',
            'categories',
            'totalValue',
            'totalProducts',
            'totalQuantity',
            'averageValue',
            'topByValue'
        ));
    }

    
}