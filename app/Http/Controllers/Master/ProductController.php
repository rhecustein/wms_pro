<?php
// app/Http/Controllers/Master/ProductController.php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'createdBy', 'updatedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Unit Filter
        if ($request->filled('unit_of_measure')) {
            $query->where('unit_of_measure', $request->unit_of_measure);
        }

        // Packaging Filter
        if ($request->filled('packaging_type')) {
            $query->where('packaging_type', $request->packaging_type);
        }

        // Hazmat Filter
        if ($request->filled('is_hazmat')) {
            $query->where('is_hazmat', $request->is_hazmat === '1');
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();

        return view('master.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        return view('master.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:product_categories,id',
            'unit_of_measure' => 'required|in:pcs,box,pallet,kg,liter',
            'weight_kg' => 'nullable|numeric|min:0',
            'length_cm' => 'nullable|numeric|min:0',
            'width_cm' => 'nullable|numeric|min:0',
            'height_cm' => 'nullable|numeric|min:0',
            'packaging_type' => 'required|in:drum,carton,pallet,bag,bulk',
            'is_batch_tracked' => 'boolean',
            'is_serial_tracked' => 'boolean',
            'is_expiry_tracked' => 'boolean',
            'shelf_life_days' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'is_hazmat' => 'boolean',
            'temperature_min' => 'nullable|numeric',
            'temperature_max' => 'nullable|numeric|gte:temperature_min',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['created_by'] = auth()->id();

        Product::create($validated);

        return redirect()->route('master.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'createdBy', 'updatedBy']);
        return view('master.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        return view('master.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:product_categories,id',
            'unit_of_measure' => 'required|in:pcs,box,pallet,kg,liter',
            'weight_kg' => 'nullable|numeric|min:0',
            'length_cm' => 'nullable|numeric|min:0',
            'width_cm' => 'nullable|numeric|min:0',
            'height_cm' => 'nullable|numeric|min:0',
            'packaging_type' => 'required|in:drum,carton,pallet,bag,bulk',
            'is_batch_tracked' => 'boolean',
            'is_serial_tracked' => 'boolean',
            'is_expiry_tracked' => 'boolean',
            'shelf_life_days' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'is_hazmat' => 'boolean',
            'temperature_min' => 'nullable|numeric',
            'temperature_max' => 'nullable|numeric|gte:temperature_min',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['updated_by'] = auth()->id();

        $product->update($validated);

        return redirect()->route('master.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('master.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function activate(Product $product)
    {
        $product->update(['is_active' => true]);
        return back()->with('success', 'Product activated successfully.');
    }

    public function deactivate(Product $product)
    {
        $product->update(['is_active' => false]);
        return back()->with('success', 'Product deactivated successfully.');
    }

    public function stockSummary(Product $product)
    {
        // TODO: Implement stock summary logic
        return view('master.products.stock-summary', compact('product'));
    }

    public function movements(Product $product)
    {
        // TODO: Implement movements logic
        return view('master.products.movements', compact('product'));
    }

    public function import(Request $request)
    {
        // TODO: Implement import logic
        return back()->with('success', 'Products imported successfully.');
    }

    public function export(Request $request)
    {
        // TODO: Implement export logic
        return back()->with('success', 'Products exported successfully.');
    }
}