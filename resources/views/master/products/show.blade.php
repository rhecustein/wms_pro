@extends('layouts.app')

@section('title', 'View Product')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-eye text-blue-600 mr-2"></i>
                Product Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View complete product information</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.products.edit', $product) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('master.products.index') }}" 
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
                                <i class="fas fa-tag mr-1"></i>Product Name
                            </label>
                            <p class="text-base font-semibold text-gray-900">{{ $product->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-layer-group mr-1"></i>Category
                            </label>
                            @if($product->category)
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-folder mr-1"></i>{{ $product->category->name }}
                                </span>
                            @else
                                <p class="text-sm text-gray-400">No Category</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-barcode mr-1"></i>SKU
                            </label>
                            <p class="text-base font-mono font-semibold text-gray-900">{{ $product->sku }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-qrcode mr-1"></i>Barcode
                            </label>
                            <p class="text-base font-mono font-semibold text-gray-900">{{ $product->barcode ?? '-' }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            <i class="fas fa-align-left mr-1"></i>Description
                        </label>
                        <p class="text-sm text-gray-900">{{ $product->description ?? '-' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-toggle-on mr-1"></i>Status
                            </label>
                            @if($product->is_active)
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
                        @if($product->is_hazmat)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Hazardous
                                </label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Hazmat
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Physical Properties Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-cube mr-2"></i>
                        Physical Properties
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-ruler mr-1"></i>Unit
                            </label>
                            <p class="text-base font-semibold text-gray-900">{{ strtoupper($product->unit_of_measure) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-box mr-1"></i>Packaging
                            </label>
                            <p class="text-base font-semibold text-gray-900">{{ ucfirst($product->packaging_type) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-weight-hanging mr-1"></i>Weight
                            </label>
                            <p class="text-base font-semibold text-gray-900">{{ $product->weight_kg ? $product->weight_kg . ' kg' : '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-cube mr-1"></i>Dimensions
                            </label>
                            <p class="text-sm font-semibold text-gray-900">
                                @if($product->length_cm)
                                    {{ $product->length_cm }}×{{ $product->width_cm }}×{{ $product->height_cm }} cm
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inventory Tracking Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-warehouse mr-2"></i>
                        Inventory Tracking
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Tracking Options</label>
                        <div class="flex flex-wrap gap-2">
                            @if($product->is_batch_tracked)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-layer-group mr-1"></i>Batch Tracked
                                </span>
                            @endif
                            @if($product->is_serial_tracked)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-hashtag mr-1"></i>Serial Tracked
                                </span>
                            @endif
                            @if($product->is_expiry_tracked)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-calendar-times mr-1"></i>Expiry Tracked
                                </span>
                            @endif
                            @if(!$product->is_batch_tracked && !$product->is_serial_tracked && !$product->is_expiry_tracked)
                                <span class="text-sm text-gray-400">No tracking enabled</span>
                            @endif
                        </div>
                    </div>

                    @if($product->shelf_life_days)
                        <div class="border-t pt-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-hourglass-half mr-1"></i>Shelf Life
                            </label>
                            <p class="text-base font-semibold text-gray-900">{{ $product->shelf_life_days }} days</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 border-t pt-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">
                                <i class="fas fa-sync-alt mr-1"></i>Reorder Level
                            </label>
                            <p class="text-lg font-bold text-gray-900">{{ $product->reorder_level ?? '0' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">
                                <i class="fas fa-shopping-cart mr-1"></i>Reorder Qty
                            </label>
                            <p class="text-lg font-bold text-gray-900">{{ $product->reorder_quantity ?? '0' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">
                                <i class="fas fa-arrow-down mr-1"></i>Min Stock
                            </label>
                            <p class="text-lg font-bold text-gray-900">{{ $product->min_stock_level ?? '0' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">
                                <i class="fas fa-arrow-up mr-1"></i>Max Stock
                            </label>
                            <p class="text-lg font-bold text-gray-900">{{ $product->max_stock_level ?? '0' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Storage Conditions Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-orange-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-temperature-high mr-2"></i>
                        Storage Conditions
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($product->temperature_min !== null || $product->temperature_max !== null)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-thermometer-half mr-1"></i>Temperature Range
                            </label>
                            <p class="text-base font-semibold text-gray-900">
                                {{ $product->temperature_min ?? 'N/A' }}°C - {{ $product->temperature_max ?? 'N/A' }}°C
                            </p>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">No specific storage conditions</p>
                    @endif
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Product Image --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-image mr-2"></i>
                        Product Image
                    </h3>
                </div>
                <div class="p-6">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full rounded-lg border border-gray-200">
                    @else
                        <div class="w-full h-48 rounded-lg bg-gray-100 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-box text-5xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">No image available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-700 to-gray-800 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('master.products.edit', $product) }}" 
                       class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition">
                        <i class="fas fa-edit w-5 mr-3 text-blue-600"></i>
                        Edit Product
                    </a>
                    <a href="#" 
                       class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition">
                        <i class="fas fa-chart-bar w-5 mr-3 text-purple-600"></i>
                        Stock Summary
                    </a>
                    <a href="#" 
                       class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-green-50 hover:text-green-700 transition">
                        <i class="fas fa-history w-5 mr-3 text-green-600"></i>
                        View Movements
                    </a>
                    <form action="{{ route('master.products.destroy', $product) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-red-50 hover:text-red-700 transition">
                            <i class="fas fa-trash w-5 mr-3 text-red-600"></i>
                            Delete Product
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
                        <p class="text-sm font-semibold text-gray-900">{{ $product->createdBy?->name ?? 'System' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-clock mr-1"></i>{{ $product->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            <i class="fas fa-user-edit mr-1"></i>Last Updated By
                        </label>
                        <p class="text-sm font-semibold text-gray-900">{{ $product->updatedBy?->name ?? 'System' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-clock mr-1"></i>{{ $product->updated_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection