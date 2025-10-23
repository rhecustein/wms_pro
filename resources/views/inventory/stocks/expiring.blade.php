{{-- resources/views/inventory/stocks/expiring.blade.php --}}
@extends('layouts.app')

@section('title', 'Expiring Stock')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-clock text-orange-600 mr-2"></i>
                Expiring Stock
            </h1>
            <p class="text-sm text-gray-600 mt-1">Products expiring within {{ $days }} days</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('inventory.stocks.expired') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-ban mr-2"></i>View Expired
            </a>
            <a href="{{ route('inventory.stocks.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    {{-- Alert Banner --}}
    @if($stocks->count() > 0)
    <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-6 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-orange-800">Attention Required!</h3>
                <p class="text-sm text-orange-700 mt-1">
                    You have <strong>{{ $stocks->total() }}</strong> stock items that will expire within the next {{ $days }} days. Please take appropriate action.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('inventory.stocks.expiring') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Days Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Days to Expiry</label>
                    <select name="days" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 Days</option>
                        <option value="14" {{ $days == 14 ? 'selected' : '' }}>14 Days</option>
                        <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Days</option>
                        <option value="60" {{ $days == 60 ? 'selected' : '' }}>60 Days</option>
                        <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Days</option>
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
                    <a href="{{ route('inventory.stocks.expiring') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Expiring Stock Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse/Bin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Left</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                        @php
                            $daysLeft = abs(\Carbon\Carbon::parse($stock->expiry_date)->diffInDays(now(), false));
                            $urgency = $daysLeft <= 7 ? 'critical' : ($daysLeft <= 14 ? 'high' : ($daysLeft <= 30 ? 'medium' : 'low'));
                            $urgencyColors = [
                                'critical' => 'bg-red-100 text-red-800',
                                'high' => 'bg-orange-100 text-orange-800',
                                'medium' => 'bg-yellow-100 text-yellow-800',
                                'low' => 'bg-blue-100 text-blue-800',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
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
                                <div class="text-sm font-semibold text-orange-600">
                                    {{ \Carbon\Carbon::parse($stock->expiry_date)->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold {{ $urgency === 'critical' ? 'text-red-600' : ($urgency === 'high' ? 'text-orange-600' : 'text-gray-900') }}">
                                    {{ $daysLeft }}
                                </div>
                                <div class="text-xs text-gray-500">days left</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $urgencyColors[$urgency] }}">
                                    @if($urgency === 'critical')
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                    @endif
                                    {{ ucfirst($urgency) }}
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
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Expiring Stock</h3>
                                    <p class="text-gray-600">All products are safe from expiration within the next {{ $days }} days</p>
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