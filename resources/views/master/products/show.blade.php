@extends('layouts.app')

@section('title', 'View Product - ' . $product->name)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-eye text-blue-600 mr-2"></i>
                Product Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">Complete information for {{ $product->name }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('master.products.edit', $product) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('master.products.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

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
                            <p class="text-lg font-semibold text-gray-900">{{ $product->name }}</p>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-copyright mr-1"></i>Brand
                            </label>
                            <p class="text-base font-semibold text-gray-900">{{ $product->brand ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-box mr-1"></i>Product Type
                            </label>
                            {!! $product->type_badge !!}
                        </div>
                    </div>

                    @if($product->supplier)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            <i class="fas fa-truck mr-1"></i>Supplier
                        </label>
                        <p class="text-base font-semibold text-gray-900">{{ $product->supplier->name }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            <i class="fas fa-align-left mr-1"></i>Description
                        </label>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $product->description ?? '-' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-toggle-on mr-1"></i>Status
                            </label>
                            {!! $product->status_badge !!}
                        </div>
                        @if($product->notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-sticky-note mr-1"></i>Notes
                            </label>
                            <p class="text-sm text-gray-700">{{ $product->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pricing Information Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        Pricing Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">
                                <i class="fas fa-shopping-cart mr-1"></i>Purchase Price
                            </label>
                            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">
                                <i class="fas fa-money-bill-wave mr-1"></i>Selling Price
                            </label>
                            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">
                                <i class="fas fa-tag mr-1"></i>Minimum Price
                            </label>
                            <p class="text-2xl font-bold text-orange-600">Rp {{ number_format($product->minimum_selling_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-blue-700 mb-1">
                                <i class="fas fa-chart-line mr-1"></i>Profit Margin
                            </label>
                            <p class="text-3xl font-bold text-blue-900">{{ number_format($product->profit_margin, 1) }}%</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-green-700 mb-1">
                                <i class="fas fa-coins mr-1"></i>Profit Amount
                            </label>
                            <p class="text-3xl font-bold text-green-900">Rp {{ number_format($product->profit_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if($product->is_taxable)
                    <div class="mt-6 pt-6 border-t">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    <i class="fas fa-percentage mr-1"></i>Tax Information
                                </label>
                                <p class="text-sm text-gray-700">This product is taxable</p>
                            </div>
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-lg font-bold bg-orange-100 text-orange-800">
                                {{ number_format($product->tax_rate, 2) }}%
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Stock Management Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-warehouse mr-2"></i>
                        Stock Management
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <label class="block text-xs font-medium text-gray-500 mb-2">
                                <i class="fas fa-boxes mr-1"></i>Current Stock
                            </label>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($product->current_stock) }}</p>
                            {!! $product->stock_status_badge !!}
                        </div>
                        <div class="text-center">
                            <label class="block text-xs font-medium text-gray-500 mb-2">
                                <i class="fas fa-arrow-down mr-1"></i>Min Stock
                            </label>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($product->minimum_stock) }}</p>
                        </div>
                        <div class="text-center">
                            <label class="block text-xs font-medium text-gray-500 mb-2">
                                <i class="fas fa-arrow-up mr-1"></i>Max Stock
                            </label>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($product->maximum_stock) }}</p>
                        </div>
                        <div class="text-center">
                            <label class="block text-xs font-medium text-gray-500 mb-2">
                                <i class="fas fa-sync-alt mr-1"></i>Reorder Level
                            </label>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($product->reorder_level) }}</p>
                        </div>
                    </div>

                    @if($product->unit)
                    <div class="mt-6 pt-6 border-t">
                        <label class="block text-sm font-medium text-gray-500 mb-2">
                            <i class="fas fa-ruler mr-1"></i>Unit of Measure
                        </label>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-lg font-bold bg-blue-100 text-blue-800">
                                {{ $product->unit->short_code }}
                            </span>
                            <span class="text-sm text-gray-600">{{ $product->unit->name }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Physical Properties Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-orange-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-cube mr-2"></i>
                        Physical Properties
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">
                                <i class="fas fa-weight-hanging mr-1"></i>Weight
                            </label>
                            <p class="text-xl font-semibold text-gray-900">
                                {{ $product->weight ? number_format($product->weight, 2) . ' kg' : '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">
                                <i class="fas fa-cube mr-1"></i>Dimensions (L × W × H)
                            </label>
                            <p class="text-xl font-semibold text-gray-900">
                                {{ $product->formatted_dimensions }}
                            </p>
                        </div>
                    </div>

                    @if($product->length && $product->width && $product->height)
                    <div class="mt-6 pt-6 border-t">
                        <label class="block text-sm font-medium text-gray-500 mb-2">
                            <i class="fas fa-expand mr-1"></i>Calculated Volume
                        </label>
                        <p class="text-xl font-semibold text-gray-900">
                            {{ $product->formatted_volume }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Calculated from dimensions (L × W × H)</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Inventory Tracking Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-teal-500 to-teal-600 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        Inventory Tracking
                    </h3>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-500 mb-3">Tracking Options</label>
                    <div class="flex flex-wrap gap-3">
                        @if($product->is_batch_tracked)
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-layer-group mr-2"></i>Batch Tracked
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-500">
                                <i class="fas fa-layer-group mr-2"></i>Batch Tracking: No
                            </span>
                        @endif

                        @if($product->is_serialized)
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-hashtag mr-2"></i>Serial Tracked
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-500">
                                <i class="fas fa-hashtag mr-2"></i>Serial Tracking: No
                            </span>
                        @endif
                    </div>
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
                             class="w-full rounded-lg border border-gray-200 shadow-sm">
                    @else
                        <div class="w-full h-64 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300">
                            <div class="text-center">
                                <i class="fas fa-image text-6xl text-gray-400 mb-3"></i>
                                <p class="text-sm text-gray-500 font-medium">No image available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Stock Value Card --}}
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg overflow-hidden text-white">
                <div class="px-6 py-4 border-b border-blue-400">
                    <h3 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-chart-line mr-2"></i>
                        Stock Value
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Total Value (Selling)</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($product->calculateTotalValue(), 0, ',', '.') }}</p>
                    </div>
                    <div class="pt-4 border-t border-blue-400">
                        <p class="text-sm opacity-90 mb-1">Total Cost</p>
                        <p class="text-xl font-bold">Rp {{ number_format($product->calculateTotalCost(), 0, ',', '.') }}</p>
                    </div>
                    <div class="pt-4 border-t border-blue-400">
                        <p class="text-sm opacity-90 mb-1">Potential Profit</p>
                        <p class="text-xl font-bold">Rp {{ number_format($product->calculateTotalProfit(), 0, ',', '.') }}</p>
                    </div>
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
                    <a href="{{ route('master.products.stock-summary', $product) }}" 
                       class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition">
                        <i class="fas fa-chart-bar w-5 mr-3 text-purple-600"></i>
                        Stock Summary
                    </a>
                    <a href="{{ route('master.products.movements', $product) }}" 
                       class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-green-50 hover:text-green-700 transition">
                        <i class="fas fa-history w-5 mr-3 text-green-600"></i>
                        View Movements
                    </a>
                    
                    @if($product->is_active)
                    <form action="{{ route('master.products.deactivate', $product) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-orange-50 hover:text-orange-700 transition">
                            <i class="fas fa-ban w-5 mr-3 text-orange-600"></i>
                            Deactivate Product
                        </button>
                    </form>
                    @else
                    <form action="{{ route('master.products.activate', $product) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-green-50 hover:text-green-700 transition">
                            <i class="fas fa-check-circle w-5 mr-3 text-green-600"></i>
                            Activate Product
                        </button>
                    </form>
                    @endif

                    <form action="{{ route('master.products.destroy', $product) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this product?\n\nProduct: {{ $product->name }}\nSKU: {{ $product->sku }}\n\nThis action cannot be undone.')">
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
                <div class="px-6 py-4 bg-gradient-to-r from-gray-600 to-gray-700 border-b">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-user-clock mr-2"></i>
                        Audit Information
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            <i class="fas fa-user-plus mr-1"></i>Created By
                        </label>
                        <p class="text-sm font-semibold text-gray-900">{{ $product->createdBy?->name ?? 'System' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-clock mr-1"></i>{{ $product->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div class="border-t pt-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">
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