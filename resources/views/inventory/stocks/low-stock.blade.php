{{-- resources/views/inventory/stocks/low-stock.blade.php --}}
@extends('layouts.app')

@section('title', 'Low Stock Alert')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                Low Stock Alert
            </h1>
            <p class="text-sm text-gray-600 mt-1">Products with available quantity at or below threshold</p>
        </div>
        <a href="{{ route('inventory.stocks.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    {{-- Alert Banner --}}
    @if($stocks->count() > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Low Stock Warning!</h3>
                <p class="text-sm text-yellow-700 mt-1">
                    You have <strong>{{ $stocks->total() }}</strong> stock items with low available quantity. Consider replenishing these items soon.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('inventory.stocks.low-stock') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Threshold Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Threshold</label>
                    <select name="threshold" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="5" {{ $threshold == 5 ? 'selected' : '' }}>5 or less</option>
                        <option value="10" {{ $threshold == 10 ? 'selected' : '' }}>10 or less</option>
                        <option value="20" {{ $threshold == 20 ? 'selected' : '' }}>20 or less</option>
                        <option value="50" {{ $threshold == 50 ? 'selected' : '' }}>50 or less</option>
                        <option value="100" {{ $threshold == 100 ? 'selected' : '' }}>100 or less</option>
                    </select>
                </div>

                {{-- Warehouse Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse</label>
                    <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('inventory.stocks.low-stock') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Low Stock Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse/Bin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alert Level</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                        @php
                            $availableQty = $stock->available_quantity;
                            $alertLevel = $availableQty <= 5 ? 'critical' : ($availableQty <= 10 ? 'high' : 'medium');
                            $alertColors = [
                                'critical' => 'bg-red-100 text-red-800',
                                'high' => 'bg-orange-100 text-orange-800',
                                'medium' => 'bg-yellow-100 text-yellow-800',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ $alertLevel === 'critical' ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $stock->product->name }}</div>
                                        <div class="text-xs text-gray-500">SKU: {{ $stock->product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <i class="fas fa-warehouse text-purple-600 mr-1"></i>
                                    {{ $stock->warehouse->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $stock->storageBin->code }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $stock->batch_number ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($stock->quantity) }}</span>
                                <span class="text-xs text-gray-500">{{ $stock->unit_of_measure }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-orange-600">{{ number_format($stock->reserved_quantity) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold {{ $alertLevel === 'critical' ? 'text-red-600' : ($alertLevel === 'high' ? 'text-orange-600' : 'text-yellow-600') }}">
                                    {{ number_format($availableQty) }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $stock->unit_of_measure }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $alertColors[$alertLevel] }}">
                                    @if($alertLevel === 'critical')
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                    @elseif($alertLevel === 'high')
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                    @endif
                                    {{ ucfirst($alertLevel) }}
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
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-check-circle text-4xl text-green-600"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">All Stock Levels Are Good</h3>
                                    <p class="text-gray-600">No products with low stock quantity at this time</p>
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