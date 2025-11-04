@extends('layouts.app')

@section('title', 'Stock Movements - ' . $product->name)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-exchange-alt text-blue-600 mr-2"></i>
                Stock Movements
            </h1>
            <div class="flex items-center gap-2 mt-1">
                <p class="text-sm text-gray-600">{{ $product->name }}</p>
                <span class="text-gray-400">•</span>
                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-mono">{{ $product->sku }}</span>
                @if($product->category)
                    <span class="text-gray-400">•</span>
                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs">{{ $product->category->name }}</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('master.products.show', $product) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                <i class="fas fa-eye mr-2"></i>View Product
            </a>
            <a href="{{ route('master.products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- Product Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Current Stock</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($product->current_stock) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $product->unit->short_code ?? 'units' }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Movements</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $movements->total() }}</p>
                    <p class="text-xs text-gray-500 mt-1">All time</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Reorder Level</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($product->reorder_level ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Threshold</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sync-alt text-xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Stock Status</p>
                    <div class="mt-1">
                        {!! $product->stock_status_badge !!}
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('master.products.movements', $product) }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i>Date From
                    </label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-check mr-1"></i>Date To
                    </label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-filter mr-1"></i>Movement Type
                    </label>
                    <select name="movement_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="receipt" {{ request('movement_type') == 'receipt' ? 'selected' : '' }}>Receipt</option>
                        <option value="issue" {{ request('movement_type') == 'issue' ? 'selected' : '' }}>Issue</option>
                        <option value="transfer" {{ request('movement_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                        <option value="return" {{ request('movement_type') == 'return' ? 'selected' : '' }}>Return</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sort mr-1"></i>Sort By
                    </label>
                    <select name="sort" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('master.products.movements', $product) }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                       title="Reset Filters">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Movements Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($movements->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Movement Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performed By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($movements as $movement)
                            <tr class="hover:bg-gray-50 transition">
                                {{-- Date & Time --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $movement->movement_date->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>{{ $movement->movement_date->format('H:i') }}
                                    </div>
                                </td>

                                {{-- Movement Type --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeColors = [
                                            'receipt' => 'bg-green-100 text-green-800',
                                            'issue' => 'bg-red-100 text-red-800',
                                            'transfer' => 'bg-blue-100 text-blue-800',
                                            'adjustment' => 'bg-yellow-100 text-yellow-800',
                                            'return' => 'bg-purple-100 text-purple-800',
                                        ];
                                        $typeIcons = [
                                            'receipt' => 'fa-arrow-down',
                                            'issue' => 'fa-arrow-up',
                                            'transfer' => 'fa-exchange-alt',
                                            'adjustment' => 'fa-balance-scale',
                                            'return' => 'fa-undo',
                                        ];
                                        $color = $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-800';
                                        $icon = $typeIcons[$movement->movement_type] ?? 'fa-question';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                        <i class="fas {{ $icon }} mr-1"></i>
                                        {{ ucfirst($movement->movement_type) }}
                                    </span>
                                </td>

                                {{-- From Location --}}
                                <td class="px-6 py-4">
                                    @if($movement->fromBin)
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-warehouse text-gray-400 mr-1"></i>
                                            {{ $movement->fromBin->warehouse->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $movement->fromBin->name }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- To Location --}}
                                <td class="px-6 py-4">
                                    @if($movement->toBin)
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-warehouse text-gray-400 mr-1"></i>
                                            {{ $movement->toBin->warehouse->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $movement->toBin->name }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Quantity --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold {{ in_array($movement->movement_type, ['receipt', 'return']) ? 'text-green-600' : 'text-red-600' }}">
                                        {{ in_array($movement->movement_type, ['receipt', 'return']) ? '+' : '-' }}{{ number_format($movement->quantity) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $product->unit->short_code ?? 'units' }}
                                    </div>
                                </td>

                                {{-- Balance After --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($movement->balance_after ?? 0) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        After movement
                                    </div>
                                </td>

                                {{-- Reference --}}
                                <td class="px-6 py-4">
                                    @if($movement->reference_number)
                                        <div class="text-sm font-mono text-blue-600">
                                            {{ $movement->reference_number }}
                                        </div>
                                    @endif
                                    @if($movement->notes)
                                        <div class="text-xs text-gray-500 mt-1" title="{{ $movement->notes }}">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            {{ Str::limit($movement->notes, 30) }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Performed By --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                            <i class="fas fa-user text-blue-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $movement->performedBy->name ?? 'System' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $movement->performedBy->email ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($movements->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $movements->links() }}
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exchange-alt text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">No Movement History</h3>
                <p class="text-gray-600 mb-4">Stock movements will appear here once transactions are recorded</p>
                
                @if(request()->hasAny(['date_from', 'date_to', 'movement_type', 'sort']))
                    <a href="{{ route('master.products.movements', $product) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-redo mr-2"></i>Clear Filters
                    </a>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection