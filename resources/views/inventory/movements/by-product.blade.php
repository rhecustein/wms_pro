{{-- resources/views/inventory/movements/by-product.blade.php --}}
@extends('layouts.app')

@section('title', 'Product Movement History')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <a href="{{ route('inventory.movements.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i>Back to All Movements
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-box text-blue-600 mr-2"></i>
                    Product Movement History
                </h1>
                <p class="text-sm text-gray-600 mt-1">Track movements for this product</p>
            </div>
        </div>
    </div>

    {{-- Product Information Card --}}
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center">
            <div class="w-16 h-16 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-box text-3xl"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold mb-1">{{ $product->name }}</h2>
                <div class="flex items-center gap-4 text-sm opacity-90">
                    <span><i class="fas fa-barcode mr-1"></i>SKU: {{ $product->sku }}</span>
                    @if(isset($product->barcode))
                        <span><i class="fas fa-qrcode mr-1"></i>Barcode: {{ $product->barcode }}</span>
                    @endif
                    @if(isset($product->category))
                        <span><i class="fas fa-tag mr-1"></i>{{ $product->category }}</span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                @if(isset($product->current_stock))
                    <p class="text-sm opacity-90 mb-1">Current Stock</p>
                    <p class="text-3xl font-bold">{{ number_format($product->current_stock) }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('inventory.movements.by-product', $product) }}" class="space-y-4">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Movement Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Movement Type</label>
                    <select name="movement_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Types</option>
                        @foreach($movementTypes as $value => $label)
                            <option value="{{ $value }}" {{ request('movement_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Warehouse Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse</label>
                    <select name="warehouse_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" 
                           name="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" 
                           name="date_to" 
                           value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

            </div>

            {{-- Filter Actions --}}
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('inventory.movements.by-product', $product) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Clear Filters
                </a>
            </div>

        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Movements</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $movements->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total In</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ number_format($movements->whereIn('movement_type', ['inbound', 'putaway', 'replenishment'])->sum('quantity')) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Out</p>
                    <p class="text-2xl font-bold text-red-600">
                        {{ number_format($movements->whereIn('movement_type', ['outbound', 'picking'])->sum('quantity')) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Adjustments</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ $movements->where('movement_type', 'adjustment')->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sync text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Movements Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Serial</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performed By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $movement->movement_date->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $movement->movement_date->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $typeColors = [
                                        'inbound' => 'bg-green-100 text-green-800',
                                        'outbound' => 'bg-red-100 text-red-800',
                                        'transfer' => 'bg-blue-100 text-blue-800',
                                        'adjustment' => 'bg-yellow-100 text-yellow-800',
                                        'putaway' => 'bg-purple-100 text-purple-800',
                                        'picking' => 'bg-orange-100 text-orange-800',
                                        'replenishment' => 'bg-indigo-100 text-indigo-800'
                                    ];
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($movement->movement_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $movement->warehouse->name }}</div>
                                <div class="text-xs text-gray-500">{{ $movement->warehouse->code }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($movement->fromBin || $movement->toBin)
                                    <div class="text-xs space-y-1">
                                        @if($movement->fromBin)
                                            <div class="flex items-center text-red-600">
                                                <i class="fas fa-sign-out-alt mr-1"></i>
                                                <span>{{ $movement->fromBin->code }}</span>
                                            </div>
                                        @endif
                                        @if($movement->toBin)
                                            <div class="flex items-center text-green-600">
                                                <i class="fas fa-sign-in-alt mr-1"></i>
                                                <span>{{ $movement->toBin->code }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ number_format($movement->quantity) }} {{ $movement->unit_of_measure }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($movement->batch_number || $movement->serial_number)
                                    <div class="text-xs space-y-1">
                                        @if($movement->batch_number)
                                            <div class="text-gray-600">
                                                <span class="font-medium">Batch:</span> {{ $movement->batch_number }}
                                            </div>
                                        @endif
                                        @if($movement->serial_number)
                                            <div class="text-gray-600">
                                                <span class="font-medium">Serial:</span> {{ $movement->serial_number }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movement->performedBy)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                            <i class="fas fa-user text-xs text-gray-600"></i>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $movement->performedBy->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('inventory.movements.show', $movement) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-inbox text-5xl mb-4"></i>
                                    <p class="text-lg font-medium">No movements found</p>
                                    <p class="text-sm">No movement history for this product</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($movements->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $movements->links() }}
            </div>
        @endif

    </div>

</div>
@endsection