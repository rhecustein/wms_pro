{{-- resources/views/inventory/stocks/by-warehouse.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock by Warehouse')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-warehouse text-purple-600 mr-2"></i>
                Stock by Warehouse
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $warehouse->name }} - {{ $warehouse->city }}</p>
        </div>
        <a href="{{ route('inventory.stocks.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Unique Products</p>
                    <h3 class="text-2xl font-bold text-blue-600">{{ number_format($totalProducts) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Different SKUs</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Quantity</p>
                    <h3 class="text-2xl font-bold text-purple-600">{{ number_format($totalQuantity) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">All Products</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cubes text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Available</p>
                    <h3 class="text-2xl font-bold text-green-600">{{ number_format($availableQuantity) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Ready to Ship</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Stock Items</p>
                    <h3 class="text-2xl font-bold text-orange-600">{{ $stocks->total() }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Total Records</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Warehouse Information --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Warehouse Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Warehouse Code</label>
                <p class="text-base font-mono font-semibold text-gray-900">{{ $warehouse->code }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Location</label>
                <p class="text-base text-gray-900">{{ $warehouse->city }}, {{ $warehouse->province }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Manager</label>
                <p class="text-base text-gray-900">{{ $warehouse->manager->name ?? 'Not assigned' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Storage Bins</label>
                <p class="text-base text-gray-900">{{ $warehouse->storage_bins_count ?? 0 }} bins</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('inventory.stocks.by-warehouse', $warehouse) }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="quarantine" {{ request('status') === 'quarantine' ? 'selected' : '' }}>Quarantine</option>
                        <option value="damaged" {{ request('status') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                {{-- Location Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location Type</label>
                    <select name="location_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Locations</option>
                        <option value="pick_face" {{ request('location_type') === 'pick_face' ? 'selected' : '' }}>Pick Face</option>
                        <option value="high_rack" {{ request('location_type') === 'high_rack' ? 'selected' : '' }}>High Rack</option>
                        <option value="staging" {{ request('location_type') === 'staging' ? 'selected' : '' }}>Staging</option>
                        <option value="quarantine" {{ request('location_type') === 'quarantine' ? 'selected' : '' }}>Quarantine</option>
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('inventory.stocks.by-warehouse', $warehouse) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stock Items Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Stock Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Storage Bin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Serial</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $stock->product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $stock->product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-mono font-semibold text-gray-900">{{ $stock->storageBin->code }}</div>
                                <div class="text-xs text-gray-500">{{ $stock->storageBin->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($stock->batch_number)
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">Batch:</span> {{ $stock->batch_number }}
                                    </div>
                                @endif
                                @if($stock->serial_number)
                                    <div class="text-xs text-gray-500">
                                        <span class="font-medium">Serial:</span> {{ $stock->serial_number }}
                                    </div>
                                @endif
                                @if(!$stock->batch_number && !$stock->serial_number)
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($stock->quantity) }}</span>
                                <span class="text-xs text-gray-500">{{ $stock->unit_of_measure }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-orange-600">{{ number_format($stock->reserved_quantity) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-green-600">{{ number_format($stock->available_quantity) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ ucfirst(str_replace('_', ' ', $stock->location_type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'available' => 'bg-green-100 text-green-800',
                                        'reserved' => 'bg-orange-100 text-orange-800',
                                        'quarantine' => 'bg-yellow-100 text-yellow-800',
                                        'damaged' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$stock->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($stock->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('inventory.stocks.show', $stock) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-boxes text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Stock Found</h3>
                                    <p class="text-gray-600">This warehouse has no stock items</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($stocks->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $stocks->links() }}
            </div>
        @endif
    </div>

</div>
@endsection