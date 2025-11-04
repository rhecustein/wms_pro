@extends('layouts.app')

@section('title', 'Products Management')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-box text-blue-600 mr-2"></i>
                Products Management
            </h1>
            <p class="text-sm text-gray-600 mt-1">Manage all product inventory and information</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('master.products.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Add New Product
            </a>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition inline-flex items-center">
                <i class="fas fa-file-import mr-2"></i>Import
            </button>
            <button class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition inline-flex items-center">
                <i class="fas fa-file-export mr-2"></i>Export
            </button>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-500 hover:text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <span class="text-red-800 font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $products->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-xl text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Products</p>
                    <p class="text-2xl font-bold text-green-600">{{ $products->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Low Stock</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $products->filter(fn($p) => $p->current_stock <= $p->reorder_level && $p->current_stock > 0)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Out of Stock</p>
                    <p class="text-2xl font-bold text-red-600">{{ $products->where('current_stock', 0)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('master.products.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="SKU, Barcode, Name, Brand..." 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Category Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('master.products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tracking</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            {{-- Image --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-16 h-16 rounded-lg object-cover border border-gray-200">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-box text-2xl text-gray-400"></i>
                                    </div>
                                @endif
                            </td>

                            {{-- Product Info --}}
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $product->name }}</div>
                                    @if($product->brand)
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            <i class="fas fa-copyright mr-1"></i>{{ $product->brand }}
                                        </div>
                                    @endif
                                    <div class="text-xs text-gray-500 space-x-2 mt-1">
                                        <span class="font-mono">
                                            <i class="fas fa-barcode mr-1"></i>{{ $product->sku }}
                                        </span>
                                        @if($product->barcode)
                                            <span class="font-mono">
                                                <i class="fas fa-qrcode mr-1"></i>{{ $product->barcode }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($product->unit)
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-ruler mr-1"></i>Unit: {{ $product->unit->short_code }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="px-6 py-4">
                                @if($product->category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-tag mr-1"></i>{{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $product->type_badge !!}
                            </td>

                            {{-- Price --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs">
                                    <div class="font-semibold text-gray-900">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </div>
                                    <div class="text-gray-500">
                                        Buy: Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                    </div>
                                    @if($product->profit_margin > 0)
                                        <div class="text-green-600 font-medium">
                                            <i class="fas fa-arrow-up mr-1"></i>{{ number_format($product->profit_margin, 1) }}%
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Stock --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-bold {{ $product->stock_status_color === 'red' ? 'text-red-600' : ($product->stock_status_color === 'yellow' ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($product->current_stock) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {!! $product->stock_status_badge !!}
                                    </div>
                                    @if($product->reorder_level)
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-sync-alt mr-1"></i>Reorder: {{ $product->reorder_level }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Tracking --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($product->is_batch_tracked)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800">
                                            <i class="fas fa-layer-group mr-1"></i>Batch
                                        </span>
                                    @endif
                                    @if($product->is_serialized)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800">
                                            <i class="fas fa-hashtag mr-1"></i>Serial
                                        </span>
                                    @endif
                                    @if($product->is_taxable)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-orange-100 text-orange-800">
                                            <i class="fas fa-percentage mr-1"></i>{{ $product->tax_rate }}%
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $product->status_badge !!}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('master.products.show', $product) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('master.products.edit', $product) }}" 
                                       class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('master.products.stock-summary', $product) }}" 
                                       class="text-purple-600 hover:text-purple-900" title="Stock Summary">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('master.products.movements', $product) }}" 
                                       class="text-green-600 hover:text-green-900" title="Stock Movements">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                    @if($product->is_active)
                                        <form action="{{ route('master.products.deactivate', $product) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-orange-600 hover:text-orange-900" title="Deactivate">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('master.products.activate', $product) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Activate">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('master.products.destroy', $product) }}" 
                                          method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this product?\n\nProduct: {{ $product->name }}\nSKU: {{ $product->sku }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Products Found</h3>
                                    <p class="text-gray-600 mb-4">Get started by creating your first product</p>
                                    <a href="{{ route('master.products.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                                        <i class="fas fa-plus mr-2"></i>Add Product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection