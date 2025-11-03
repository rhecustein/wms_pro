@extends('layouts.app')

@section('title', 'Products Management')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-box text-blue-600 mr-2"></i>
                Products Management
            </h1>
            <p class="text-sm text-gray-600 mt-1">Manage all product inventory and information</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.products.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Add New Product
            </a>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-import mr-2"></i>Import
            </button>
            <button class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition">
                <i class="fas fa-file-export mr-2"></i>Export
            </button>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('master.products.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="SKU, Barcode, Name..." 
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

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Unit Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <select name="unit_of_measure" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Units</option>
                        <option value="pcs" {{ request('unit_of_measure') === 'pcs' ? 'selected' : '' }}>PCS</option>
                        <option value="box" {{ request('unit_of_measure') === 'box' ? 'selected' : '' }}>Box</option>
                        <option value="pallet" {{ request('unit_of_measure') === 'pallet' ? 'selected' : '' }}>Pallet</option>
                        <option value="kg" {{ request('unit_of_measure') === 'kg' ? 'selected' : '' }}>KG</option>
                        <option value="liter" {{ request('unit_of_measure') === 'liter' ? 'selected' : '' }}>Liter</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit/Packaging</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dimensions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition">
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
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $product->name }}</div>
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
                                    @if($product->is_hazmat)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Hazmat
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($product->category)
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-tag mr-1"></i>{{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">
                                        <i class="fas fa-ruler mr-1 text-blue-500"></i>{{ strtoupper($product->unit_of_measure) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-box mr-1 text-green-500"></i>{{ ucfirst($product->packaging_type) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->weight_kg || $product->length_cm)
                                    <div class="text-xs text-gray-600">
                                        @if($product->weight_kg)
                                            <div><i class="fas fa-weight-hanging mr-1"></i>{{ $product->weight_kg }} kg</div>
                                        @endif
                                        @if($product->length_cm)
                                            <div><i class="fas fa-cube mr-1"></i>{{ $product->length_cm }}×{{ $product->width_cm }}×{{ $product->height_cm }} cm</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    @if($product->is_batch_tracked)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-layer-group mr-1"></i>Batch
                                        </span>
                                    @endif
                                    @if($product->is_serial_tracked)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-hashtag mr-1"></i>Serial
                                        </span>
                                    @endif
                                    @if($product->is_expiry_tracked)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-800">
                                            <i class="fas fa-calendar-times mr-1"></i>Expiry
                                        </span>
                                    @endif
                                    @if($product->reorder_level)
                                        <div class="text-gray-600">
                                            <i class="fas fa-sync-alt mr-1"></i>Reorder: {{ $product->reorder_level }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
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
                                    <a href="#" class="text-purple-600 hover:text-purple-900" title="Stock Summary">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <form action="{{ route('master.products.destroy', $product) }}" 
                                          method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
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
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Products Found</h3>
                                    <p class="text-gray-600 mb-4">Get started by creating your first product</p>
                                    <a href="{{ route('master.products.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
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