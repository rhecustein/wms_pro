@extends('layouts.app')

@section('title', 'View Product Category')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-eye text-blue-600 mr-2"></i>
                Category Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View complete category information</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.product-categories.edit', $productCategory) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('master.product-categories.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Information --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Basic Information Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Basic Information
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-tag mr-1"></i>Category Name
                            </label>
                            <p class="text-base font-semibold text-gray-900">{{ $productCategory->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-barcode mr-1"></i>Category Code
                            </label>
                            <p class="text-base font-mono font-semibold text-gray-900">{{ $productCategory->code }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            <i class="fas fa-align-left mr-1"></i>Description
                        </label>
                        <p class="text-sm text-gray-900">{{ $productCategory->description ?? '-' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-sitemap mr-1"></i>Parent Category
                            </label>
                            @if($productCategory->parent)
                                <a href="{{ route('master.product-categories.show', $productCategory->parent) }}" 
                                   class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition">
                                    <i class="fas fa-link mr-1"></i>{{ $productCategory->parent->name }}
                                </a>
                            @else
                                <p class="text-sm text-gray-400">No Parent (Main Category)</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-toggle-on mr-1"></i>Status
                            </label>
                            @if($productCategory->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-sort-numeric-down mr-1"></i>Sort Order
                            </label>
                            <p class="text-base font-semibold text-gray-900">{{ $productCategory->sort_order }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Child Categories Card --}}
            @if($productCategory->children->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600 border-b">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-sitemap mr-2"></i>
                            Child Categories ({{ $productCategory->children->count() }})
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($productCategory->children as $child)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-purple-100 mr-3">
                                            <i class="fas fa-folder text-purple-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $child->name }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $child->code }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('master.product-categories.show', $child) }}" 
                                       class="text-purple-600 hover:text-purple-900" title="View Details">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Quick Actions Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-700 to-gray-800 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('master.product-categories.edit', $productCategory) }}" 
                       class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition">
                        <i class="fas fa-edit w-5 mr-3 text-blue-600"></i>
                        Edit Category
                    </a>
                    <form action="{{ route('master.product-categories.destroy', $productCategory) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this category?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-red-50 hover:text-red-700 transition">
                            <i class="fas fa-trash w-5 mr-3 text-red-600"></i>
                            Delete Category
                        </button>
                    </form>
                </div>
            </div>

            {{-- Audit Information Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-history mr-2"></i>
                        Audit Information
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            <i class="fas fa-user-plus mr-1"></i>Created By
                        </label>
                        <p class="text-sm font-semibold text-gray-900">{{ $productCategory->createdBy?->name ?? 'System' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-clock mr-1"></i>{{ $productCategory->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            <i class="fas fa-user-edit mr-1"></i>Last Updated By
                        </label>
                        <p class="text-sm font-semibold text-gray-900">{{ $productCategory->updatedBy?->name ?? 'System' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-clock mr-1"></i>{{ $productCategory->updated_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Statistics Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-orange-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Statistics
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-purple-100 mr-3">
                                <i class="fas fa-folder text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Subcategories</p>
                                <p class="text-lg font-bold text-gray-900">{{ $productCategory->children->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-t pt-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-100 mr-3">
                                <i class="fas fa-box text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Total Products</p>
                                <p class="text-lg font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection