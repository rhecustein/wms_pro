{{-- resources/views/inventory/movements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock Movements')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-exchange-alt text-blue-600 mr-2"></i>
                Stock Movements
            </h1>
            <p class="text-sm text-gray-600 mt-1">Track all inventory movement activities</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('inventory.movements.export') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-export mr-2"></i>Export
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <span>{{ session('info') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-blue-700 hover:text-blue-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('inventory.movements.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Reference, Product, Batch..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Movement Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Movement Type</label>
                    <select name="movement_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach($movementTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('movement_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Warehouse Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse</label>
                    <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reference Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reference Type</label>
                    <select name="reference_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All References</option>
                        @foreach($referenceTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('reference_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end">
                    <button type="button" onclick="document.getElementById('dateFilters').classList.toggle('hidden')" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-calendar mr-2"></i>Date Range
                    </button>
                </div>
            </div>

            {{-- Date Range Filters (Hidden by default) --}}
            <div id="dateFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 hidden">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('inventory.movements.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Stock Movements Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From/To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-semibold text-gray-900">{{ $movement->movement_date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $movement->movement_date->format('H:i') }}</div>
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
                                    $typeIcons = [
                                        'inbound' => 'fa-arrow-down',
                                        'outbound' => 'fa-arrow-up',
                                        'transfer' => 'fa-exchange-alt',
                                        'adjustment' => 'fa-adjust',
                                        'putaway' => 'fa-inbox',
                                        'picking' => 'fa-hand-holding-box',
                                        'replenishment' => 'fa-sync'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $typeIcons[$movement->movement_type] ?? 'fa-circle' }} mr-1"></i>
                                    {{ ucfirst($movement->movement_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $movement->product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $movement->product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="text-gray-900">
                                        <i class="fas fa-warehouse text-gray-400 mr-1"></i>
                                        {{ $movement->warehouse->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $movement->warehouse->code }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    @if($movement->fromBin)
                                        <div class="text-gray-900">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                            From: {{ $movement->fromBin->code }}
                                        </div>
                                    @endif
                                    @if($movement->toBin)
                                        <div class="text-gray-900">
                                            <i class="fas fa-map-marker-alt text-green-500 mr-1"></i>
                                            To: {{ $movement->toBin->code }}
                                        </div>
                                    @endif
                                    @if(!$movement->fromBin && !$movement->toBin)
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-semibold {{ in_array($movement->movement_type, ['inbound', 'transfer']) ? 'text-green-600' : 'text-red-600' }}">
                                        {{ in_array($movement->movement_type, ['inbound', 'transfer']) ? '+' : '-' }}{{ $movement->quantity }} {{ $movement->unit_of_measure }}
                                    </div>
                                    @if($movement->batch_number)
                                        <div class="text-xs text-gray-500">Batch: {{ $movement->batch_number }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    @if($movement->reference_number)
                                        <div class="text-gray-900 font-medium">{{ $movement->reference_number }}</div>
                                    @endif
                                    @if($movement->reference_type)
                                        <div class="text-xs text-gray-500">{{ ucwords(str_replace('_', ' ', $movement->reference_type)) }}</div>
                                    @endif
                                    @if(!$movement->reference_number && !$movement->reference_type)
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('inventory.movements.show', $movement) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-exchange-alt text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Stock Movements Found</h3>
                                    <p class="text-gray-600">No inventory movements recorded yet or try adjusting filters</p>
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