{{-- resources/views/master/products/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-box text-blue-600 mr-2"></i>
                Product Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View complete product information</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            <a href="{{ route('master.products.edit', $product) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            @if($product->is_active)
                <form action="{{ route('master.products.deactivate', $product) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="return confirm('Are you sure you want to deactivate this product?')">
                        <i class="fas fa-times-circle mr-2"></i>Deactivate
                    </button>
                </form>
            @else
                <form action="{{ route('master.products.activate', $product) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check-circle mr-2"></i>Activate
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            
            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">SKU</label>
                        <p class="text-gray-900 font-mono font-semibold">{{ $product->sku }}</p>
                    </div>

                    @if($product->barcode)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Barcode</label>
                            <p class="text-gray-900 font-mono">{{ $product->barcode }}</p>
                        </div>
                    @endif

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Product Name</label>
                        <p class="text-gray-900 font-semibold text-lg">{{ $product->name }}</p>
                    </div>

                    @if($product->description)
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                            <p class="text-gray-900">{{ $product->description }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                        @if($product->category)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                {{ $product->category->name }}
                            </span>
                        @else
                            <p class="text-gray-400">Uncategorized</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Unit of Measure</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ strtoupper($product->unit_of_measure) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Packaging Type</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-box mr-1"></i>
                            {{ ucfirst($product->packaging_type) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
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
                </div>
            </div>

            {{-- Physical Properties --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-ruler-combined text-blue-600 mr-2"></i>
                    Physical Properties
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if($product->weight_kg)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Weight</label>
                            <p class="text-gray-900 font-semibold">{{ $product->weight_kg }} KG</p>
                        </div>
                    @endif

                    @if($product->length_cm)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Length</label>
                            <p class="text-gray-900 font-semibold">{{ $product->length_cm }} CM</p>
                        </div>
                    @endif

                    @if($product->width_cm)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Width</label>
                            <p class="text-gray-900 font-semibold">{{ $product->width_cm }} CM</p>
                        </div>
                    @endif

                    @if($product->height_cm)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Height</label>
                            <p class="text-gray-900 font-semibold">{{ $product->height_cm }} CM</p>
                        </div>
                    @endif

                    @if($product->length_cm && $product->width_cm && $product->height_cm)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Volume</label>
                            <p class="text-gray-900 font-semibold">
                                {{ number_format($product->length_cm * $product->width_cm * $product->height_cm / 1000000, 2) }} m³
                            </p>
                        </div>
                    @endif
                </div>

                @if(!$product->weight_kg && !$product->length_cm && !$product->width_cm && !$product->height_cm)
                    <p class="text-gray-400 text-center py-4">No physical properties defined</p>
                @endif
            </div>

            {{-- Inventory Settings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-cogs text-blue-600 mr-2"></i>
                    Inventory Settings
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if($product->reorder_level)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Reorder Level</label>
                            <p class="text-gray-900 font-semibold">{{ number_format($product->reorder_level) }}</p>
                        </div>
                    @endif

                    @if($product->reorder_quantity)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Reorder Quantity</label>
                            <p class="text-gray-900 font-semibold">{{ number_format($product->reorder_quantity) }}</p>
                        </div>
                    @endif

                    @if($product->min_stock_level)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Min Stock Level</label>
                            <p class="text-gray-900 font-semibold">{{ number_format($product->min_stock_level) }}</p>
                        </div>
                    @endif

                    @if($product->max_stock_level)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Max Stock Level</label>
                            <p class="text-gray-900 font-semibold">{{ number_format($product->max_stock_level) }}</p>
                        </div>
                    @endif

                    @if($product->shelf_life_days)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Shelf Life</label>
                            <p class="text-gray-900 font-semibold">{{ $product->shelf_life_days }} Days</p>
                        </div>
                    @endif
                </div>

                @if(!$product->reorder_level && !$product->reorder_quantity && !$product->min_stock_level && !$product->max_stock_level && !$product->shelf_life_days)
                    <p class="text-gray-400 text-center py-4">No inventory settings defined</p>
                @endif
            </div>

            {{-- Temperature Settings --}}
            @if($product->temperature_min || $product->temperature_max)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-temperature-low text-blue-600 mr-2"></i>
                        Temperature Requirements
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        @if($product->temperature_min)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Minimum Temperature</label>
                                <p class="text-gray-900 font-semibold">{{ $product->temperature_min }}°C</p>
                            </div>
                        @endif

                        @if($product->temperature_max)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Maximum Temperature</label>
                                <p class="text-gray-900 font-semibold">{{ $product->temperature_max }}°C</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            
            {{-- Product Image --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-image text-blue-600 mr-2"></i>Product Image
                </h3>
                
                <div class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-box text-8xl text-gray-400"></i>
                    @endif
                </div>
            </div>

            {{-- Tracking Features --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-clipboard-check text-blue-600 mr-2"></i>Tracking Features
                </h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Batch Tracking</span>
                        @if($product->is_batch_tracked)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>Enabled
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="fas fa-times mr-1"></i>Disabled
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Serial Tracking</span>
                        @if($product->is_serial_tracked)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>Enabled
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="fas fa-times mr-1"></i>Disabled
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Expiry Tracking</span>
                        @if($product->is_expiry_tracked)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>Enabled
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="fas fa-times mr-1"></i>Disabled
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Hazardous Material</span>
                        @if($product->is_hazmat)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Yes
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="fas fa-check mr-1"></i>No
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-bolt text-blue-600 mr-2"></i>Quick Actions
                </h3>
                
                <div class="space-y-2">
                    <a href="{{ route('master.products.stock-summary', $product) }}" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-chart-bar mr-2"></i>Stock Summary
                    </a>
                    <a href="{{ route('master.products.movements', $product) }}" class="block w-full px-4 py-2 bg-indigo-600 text-white text-center rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-exchange-alt mr-2"></i>View Movements
                    </a>
                </div>
            </div>

            {{-- Metadata --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info text-blue-600 mr-2"></i>Metadata
                </h3>
                
                <div class="space-y-3 text-sm">
                    @if($product->createdBy)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Created By</label>
                            <p class="text-gray-900">{{ $product->createdBy->name }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-gray-900">{{ $product->created_at->format('d M Y H:i') }}</p>
                    </div>

                    @if($product->updatedBy)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated By</label>
                            <p class="text-gray-900">{{ $product->updatedBy->name }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-gray-900">{{ $product->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection