<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductCategory::with(['parent', 'children', 'createdBy', 'updatedBy']);

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        // Filter by parent
        if ($request->has('parent_id') && $request->parent_id != '') {
            $query->where('parent_id', $request->parent_id);
        }

        // Sorting
        $sortField = $request->get('sort_by', 'sort_order');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $categories = $query->paginate(10)->withQueryString();
        $parents = ProductCategory::whereNull('parent_id')->where('is_active', true)->get();

        return view('master.product-categories.index', compact('categories', 'parents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parents = ProductCategory::whereNull('parent_id')
                                  ->where('is_active', true)
                                  ->orderBy('sort_order')
                                  ->get();
        
        return view('master.product-categories.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
            'code' => 'required|string|max:255|unique:product_categories,code',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        ProductCategory::create($validated);

        return redirect()->route('product-categories.index')
                        ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        $productCategory->load(['parent', 'children', 'createdBy', 'updatedBy']);
        
        return view('master.product-categories.show', compact('productCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        $parents = ProductCategory::whereNull('parent_id')
                                  ->where('is_active', true)
                                  ->where('id', '!=', $productCategory->id)
                                  ->orderBy('sort_order')
                                  ->get();
        
        return view('master.product-categories.edit', compact('productCategory', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $productCategory->id,
            'code' => 'required|string|max:255|unique:product_categories,code,' . $productCategory->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] == $productCategory->id) {
            return back()->withErrors(['parent_id' => 'Category cannot be its own parent.']);
        }

        $validated['updated_by'] = Auth::id();

        $productCategory->update($validated);

        return redirect()->route('product-categories.index')
                        ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        // Check if category has children
        if ($productCategory->children()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category with child categories.']);
        }

        $productCategory->delete();

        return redirect()->route('product-categories.index')
                        ->with('success', 'Category deleted successfully.');
    }

    /**
     * Restore the specified soft deleted resource.
     */
    public function restore($id)
    {
        $category = ProductCategory::withTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('product-categories.index')
                        ->with('success', 'Category restored successfully.');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete($id)
    {
        $category = ProductCategory::withTrashed()->findOrFail($id);
        $category->forceDelete();

        return redirect()->route('product-categories.index')
                        ->with('success', 'Category permanently deleted.');
    }
}