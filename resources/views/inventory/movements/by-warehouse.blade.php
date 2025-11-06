{{-- resources/views/inventory/movements/by-warehouse.blade.php --}}
@extends('layouts.app')

@section('title', 'Warehouse Movement History')

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
                    <i class="fas fa-warehouse text-purple-600 mr-2"></i>
                    Warehouse Movement History
                </h1>
                <p class="text-sm text-gray-600 mt-1">Track all movements in this warehouse</p>
            </div>
        </div>
    </div>

    {{-- Warehouse Information Card --}}
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center">
            <div class="w-16 h-16 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-warehouse text-3xl"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold mb-1">{{ $warehouse->name }}</h2>
                <div class="flex items-center gap-4 text-sm opacity-90">
                    <span><i class="fas fa-barcode mr-1"></i>Code: {{ $warehouse->code }}</span>
                    @if(isset($warehouse->location))
                        <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $warehouse->location }}</span>
                    @endif
                    @if(isset($warehouse->type))
                        <span><i class="fas fa-tag mr-1"></i>{{ ucfirst($warehouse->type) }}</span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                @if(isset($warehouse->capacity))
                    <p class="text-sm opacity-90 mb-1">Capacity</p>
                    <p class="text-3xl font-bold">{{ number_format($warehouse->capacity) }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('inventory.movements.by-warehouse', $warehouse) }}" class="space-y-4">
            
            {{-- Search Bar --}}
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by reference, product name or SKU..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </div>

            {{-- Filter Dropdowns --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                {{-- Movement Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Movement Type</label>
                    <select name="movement_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">All Types</option>
                        @foreach($movementTypes as $value => $label)
                            <option value="{{ $value }}" {{ request('movement_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
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
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" 
                           name="date_to" 
                           value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

            </div>

            {{-- Filter Actions --}}
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('inventory.movements.by-warehouse', $warehouse) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Inbound</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $movements->where('movement_type', 'inbound')->count() }}
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
                    <p class="text-sm text-gray-600 mb-1">Outbound</p>
                    <p class="text-2xl font-bold text-red-600">
                        {{ $movements->where('movement_type', 'outbound')->count() }}
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
                    <p class="text-sm text-gray-600 mb-1">Unique Products</p>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ $movements->unique('product_id')->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-xl text-blue-600"></i>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
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
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $movement->product->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            SKU: {{ $movement->product->sku }}
                                        </div>
                                    </div>
                                </div>
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
                                @if($movement->batch_number)
                                    <div class="text-xs text-gray-500">Batch: {{ $movement->batch_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movement->reference_number)
                                    <div class="text-sm text-gray-900 font-mono">{{ $movement->reference_number }}</div>
                                    @if($movement->reference_type)
                                        <div class="text-xs text-gray-500">{{ ucwords(str_replace('_', ' ', $movement->reference_type)) }}</div>
                                    @endif
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
                                   class="text-purple-600 hover:text-purple-800 font-medium">
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
                                    <p class="text-sm">No movement history for this warehouse</p>
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