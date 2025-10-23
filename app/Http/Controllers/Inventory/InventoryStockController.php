<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StorageBin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventoryStockController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryStock::with(['warehouse', 'storageBin', 'product', 'customer', 'vendor', 'pallet']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Location Type
        if ($request->filled('location_type')) {
            $query->where('location_type', $request->location_type);
        }

        // Filter by Product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $stocks = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        
        return view('inventory.stocks.index', compact('stocks', 'warehouses', 'products'));
    }

    public function show(InventoryStock $inventoryStock)
    {
        $inventoryStock->load(['warehouse', 'storageBin', 'product', 'customer', 'vendor', 'pallet']);
        
        return view('inventory.stocks.show', compact('inventoryStock'));
    }

    public function byProduct(Product $product, Request $request)
    {
        $query = InventoryStock::with(['warehouse', 'storageBin'])
            ->where('product_id', $product->id);

        // Filter by Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stocks = $query->orderBy('expiry_date', 'asc')->paginate(20);
        $warehouses = Warehouse::where('is_active', true)->get();
        
        // Summary
        $totalQuantity = InventoryStock::where('product_id', $product->id)->sum('quantity');
        $availableQuantity = InventoryStock::where('product_id', $product->id)->sum('available_quantity');
        $reservedQuantity = InventoryStock::where('product_id', $product->id)->sum('reserved_quantity');
        
        return view('inventory.stocks.by-product', compact('product', 'stocks', 'warehouses', 'totalQuantity', 'availableQuantity', 'reservedQuantity'));
    }

    public function byWarehouse(Warehouse $warehouse, Request $request)
    {
        $query = InventoryStock::with(['product', 'storageBin'])
            ->where('warehouse_id', $warehouse->id);

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Location Type
        if ($request->filled('location_type')) {
            $query->where('location_type', $request->location_type);
        }

        $stocks = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Summary
        $totalProducts = InventoryStock::where('warehouse_id', $warehouse->id)
            ->distinct('product_id')->count('product_id');
        $totalQuantity = InventoryStock::where('warehouse_id', $warehouse->id)->sum('quantity');
        $availableQuantity = InventoryStock::where('warehouse_id', $warehouse->id)->sum('available_quantity');
        
        return view('inventory.stocks.by-warehouse', compact('warehouse', 'stocks', 'totalProducts', 'totalQuantity', 'availableQuantity'));
    }

    public function byBin(StorageBin $storageBin, Request $request)
    {
        $query = InventoryStock::with(['product', 'warehouse'])
            ->where('storage_bin_id', $storageBin->id);

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stocks = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Summary
        $totalQuantity = InventoryStock::where('storage_bin_id', $storageBin->id)->sum('quantity');
        $availableQuantity = InventoryStock::where('storage_bin_id', $storageBin->id)->sum('available_quantity');
        
        return view('inventory.stocks.by-bin', compact('storageBin', 'stocks', 'totalQuantity', 'availableQuantity'));
    }

    public function expiring(Request $request)
    {
        $days = $request->input('days', 30); // Default 30 days
        $expiryDate = Carbon::now()->addDays($days);

        $query = InventoryStock::with(['product', 'warehouse', 'storageBin'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $expiryDate)
            ->where('expiry_date', '>', Carbon::now())
            ->where('quantity', '>', 0);

        // Filter by Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $stocks = $query->orderBy('expiry_date', 'asc')->paginate(20);
        $warehouses = Warehouse::where('is_active', true)->get();
        
        return view('inventory.stocks.expiring', compact('stocks', 'warehouses', 'days'));
    }

    public function expired(Request $request)
    {
        $query = InventoryStock::with(['product', 'warehouse', 'storageBin'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', Carbon::now())
            ->where('quantity', '>', 0);

        // Filter by Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $stocks = $query->orderBy('expiry_date', 'desc')->paginate(20);
        $warehouses = Warehouse::where('is_active', true)->get();
        
        return view('inventory.stocks.expired', compact('stocks', 'warehouses'));
    }

    public function lowStock(Request $request)
    {
        // This would typically compare against minimum stock levels
        // For now, we'll show products with quantity below a threshold
        $threshold = $request->input('threshold', 10);

        $query = InventoryStock::with(['product', 'warehouse', 'storageBin'])
            ->where('available_quantity', '<=', $threshold)
            ->where('available_quantity', '>', 0);

        // Filter by Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $stocks = $query->orderBy('available_quantity', 'asc')->paginate(20);
        $warehouses = Warehouse::where('is_active', true)->get();
        
        return view('inventory.stocks.low-stock', compact('stocks', 'warehouses', 'threshold'));
    }

    public function stockCard(Product $product, Request $request)
    {
        $query = InventoryStock::with(['warehouse', 'storageBin'])
            ->where('product_id', $product->id);

        // Filter by Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stocks = $query->orderBy('created_at', 'desc')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        
        // Calculate running balance
        $runningBalance = 0;
        $stockCard = [];
        
        foreach ($stocks as $stock) {
            $runningBalance += $stock->quantity;
            $stockCard[] = [
                'date' => $stock->created_at,
                'warehouse' => $stock->warehouse->name,
                'bin' => $stock->storageBin->code,
                'batch' => $stock->batch_number,
                'in' => $stock->quantity > 0 ? $stock->quantity : 0,
                'out' => $stock->quantity < 0 ? abs($stock->quantity) : 0,
                'balance' => $runningBalance,
                'status' => $stock->status,
            ];
        }
        
        return view('inventory.stocks.stock-card', compact('product', 'stockCard', 'warehouses'));
    }
}