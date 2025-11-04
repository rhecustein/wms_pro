<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit', 'supplier', 'createdBy', 'updatedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Type Filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Stock Status Filter
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'overstock':
                    $query->overstock();
                    break;
            }
        }

        // Supplier Filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Serialized Filter
        if ($request->filled('is_serialized')) {
            $query->where('is_serialized', $request->is_serialized === '1');
        }

        // Batch Tracked Filter
        if ($request->filled('is_batch_tracked')) {
            $query->where('is_batch_tracked', $request->is_batch_tracked === '1');
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        
        // Get filter options
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $types = Product::getProductTypes();

        return view('master.products.index', compact('products', 'categories', 'suppliers', 'types'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        
        return view('master.products.create', compact('categories', 'units', 'suppliers'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand' => 'nullable|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            
            // Pricing
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_selling_price' => 'nullable|numeric|min:0',
            
            // Stock Management
            'minimum_stock' => 'nullable|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'current_stock' => 'nullable|integer|min:0',
            
            // Physical Properties
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            
            // Tax
            'is_taxable' => 'boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            
            // Status
            'type' => 'required|in:raw_material,finished_goods,spare_parts,consumable',
            'is_active' => 'boolean',
            'is_serialized' => 'boolean',
            'is_batch_tracked' => 'boolean',
            
            // Images
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            
            'notes' => 'nullable|string|max:1000',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Set defaults
        $validated['is_taxable'] = $request->has('is_taxable');
        $validated['is_active'] = $request->has('is_active');
        $validated['is_serialized'] = $request->has('is_serialized');
        $validated['is_batch_tracked'] = $request->has('is_batch_tracked');
        $validated['created_by'] = auth()->id();

        $product = Product::create($validated);

        return redirect()->route('master.products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'supplier', 'createdBy', 'updatedBy']);
        return view('master.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        
        return view('master.products.edit', compact('product', 'categories', 'units', 'suppliers'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand' => 'nullable|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            
            // Pricing
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_selling_price' => 'nullable|numeric|min:0',
            
            // Stock Management
            'minimum_stock' => 'nullable|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'current_stock' => 'nullable|integer|min:0',
            
            // Physical Properties
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            
            // Tax
            'is_taxable' => 'boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            
            // Status
            'type' => 'required|in:raw_material,finished_goods,spare_parts,consumable',
            'is_active' => 'boolean',
            'is_serialized' => 'boolean',
            'is_batch_tracked' => 'boolean',
            
            // Images
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            
            'notes' => 'nullable|string|max:1000',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Set defaults
        $validated['is_taxable'] = $request->has('is_taxable');
        $validated['is_active'] = $request->has('is_active');
        $validated['is_serialized'] = $request->has('is_serialized');
        $validated['is_batch_tracked'] = $request->has('is_batch_tracked');
        $validated['updated_by'] = auth()->id();

        $product->update($validated);

        return redirect()->route('master.products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Check if product has stock
        if ($product->current_stock > 0) {
            return back()->with('error', 'Cannot delete product with existing stock.');
        }

        // Check if product has inventory movements
        if ($product->stockMovements()->exists()) {
            return back()->with('error', 'Cannot delete product with stock movement history.');
        }

        // Delete image if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('master.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Activate product
     */
    public function activate(Product $product)
    {
        $product->update(['is_active' => true, 'updated_by' => auth()->id()]);
        
        return back()->with('success', 'Product activated successfully.');
    }

    /**
     * Deactivate product
     */
    public function deactivate(Product $product)
    {
        $product->update(['is_active' => false, 'updated_by' => auth()->id()]);
        
        return back()->with('success', 'Product deactivated successfully.');
    }

    /**
     * Show product stock summary
     */
    public function stockSummary(Product $product)
    {
        $product->load(['category', 'unit', 'supplier']);
        
        // Get inventory stocks by storage bins
        $inventoryStocks = $product->inventoryStocks()
            ->with(['storageBin.warehouse', 'storageBin.storageArea'])
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();
        
        // Calculate statistics
        $totalQuantity = $inventoryStocks->sum('quantity');
        $totalLocations = $inventoryStocks->pluck('storage_bin_id')->unique()->count();
        
        // Group by warehouse
        $byWarehouse = $inventoryStocks->groupBy(function($item) {
            return $item->storageBin->warehouse->name;
        });
        
        return view('master.products.stock-summary', compact(
            'product',
            'inventoryStocks',
            'totalQuantity',
            'totalLocations',
            'byWarehouse'
        ));
    }

    /**
     * Show product stock movements
     */
    public function movements(Product $product)
    {
        $product->load(['category', 'unit']);
        
        // Get stock movements
        $movements = $product->stockMovements()
            ->with(['fromBin', 'toBin', 'performedBy'])
            ->latest('movement_date')
            ->paginate(20);
        
        return view('master.products.movements', compact('product', 'movements'));
    }

    /**
     * Import products from file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        // TODO: Implement import logic using Laravel Excel or similar
        // This would typically:
        // 1. Read the uploaded file
        // 2. Validate each row
        // 3. Create/update products in database
        // 4. Return summary of imported/failed records

        return back()->with('info', 'Import functionality coming soon.');
    }

    /**
     * Export products to file
     */
    public function export(Request $request)
    {
        // TODO: Implement export logic using Laravel Excel or similar
        // This would typically:
        // 1. Query products based on filters
        // 2. Generate Excel/CSV file
        // 3. Download file

        return back()->with('info', 'Export functionality coming soon.');
    }

    /**
     * Get products by category (AJAX)
     */
    public function getByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->select('id', 'sku', 'name', 'selling_price', 'current_stock')
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }

    /**
     * Get product details (AJAX)
     */
    public function getDetails($productId)
    {
        $product = Product::with(['category', 'unit', 'supplier'])
            ->findOrFail($productId);

        return response()->json([
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'description' => $product->description,
            'unit' => $product->unit->short_code ?? null,
            'weight' => $product->weight,
            'purchase_price' => $product->purchase_price,
            'selling_price' => $product->selling_price,
            'current_stock' => $product->current_stock,
            'is_serialized' => $product->is_serialized,
            'is_batch_tracked' => $product->is_batch_tracked,
        ]);
    }

    /**
     * Dashboard statistics
     */
    public function statistics()
    {
        $stats = Product::getStockStatistics();
        
        return response()->json($stats);
    }
}