{{-- resources/views/inventory/stocks/by-bin.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock by Bin')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>
                Stock by Storage Bin
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $storageBin->code }} - {{ $storageBin->name }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('inventory.stocks.by-warehouse', $storageBin->warehouse) }}" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition">
                <i class="fas fa-warehouse mr-2"></i>Warehouse View
            </a>
            <a href="{{ route('inventory.stocks.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Quantity</p>
                    <h3 class="text-2xl font-bold text-indigo-600">{{ number_format($totalQuantity) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">In This Bin</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cubes text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Available</p>
                    <h3 class="text-2xl font-bold text-green-600">{{ number_format($availableQuantity) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Ready to Pick</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Unique Products</p>
                    <h3 class="text-2xl font-bold text-blue-600">{{ $stocks->total() }}</h3>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Bin Capacity</p>
                    @php
                        $capacityUsed = $storageBin->max_capacity > 0 
                            ? round(($totalQuantity / $storageBin->max_capacity) * 100, 1) 
                            : 0;
                    @endphp
                    <h3 class="text-2xl font-bold text-orange-600">{{ $capacityUsed }}%</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($storageBin->max_capacity) }} max</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-pie text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Storage Bin Information --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Storage Bin Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Bin Code</label>
                <p class="text-base font-mono font-semibold text-gray-900">{{ $storageBin->code }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Bin Name</label>
                <p class="text-base text-gray-900">{{ $storageBin->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Warehouse</label>
                <p class="text-base text-gray-900">{{ $storageBin->warehouse->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Zone</label>
                <p class="text-base text-gray-900">{{ $storageBin->zone ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Bin Type</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    {{ ucfirst(str_replace('_', ' ', $storageBin->bin_type)) }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                @php
                    $binStatusColors = [
                        'available' => 'bg-green-100 text-green-800',
                        'occupied' => 'bg-blue-100 text-blue-800',
                        'full' => 'bg-orange-100 text-orange-800',
                        'maintenance' => 'bg-yellow-100 text-yellow-800',
                        'blocked' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $binStatusColors[$storageBin->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($storageBin->status) }}
                </span>
            </div>
        </div>
        
        @if($storageBin->aisle || $storageBin->rack || $storageBin->shelf || $storageBin->level)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-200">
            @if($storageBin->aisle)
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Aisle</label>
                <p class="text-base text-gray-900">{{ $storageBin->aisle }}</p>
            </div>
            @endif
            @if($storageBin->rack)
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Rack</label>
                <p class="text-base text-gray-900">{{ $storageBin->rack }}</p>
            </div>
            @endif
            @if($storageBin->shelf)
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Shelf</label>
                <p class="text-base text-gray-900">{{ $storageBin->shelf }}</p>
            </div>
            @endif
            @if($storageBin->level)
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Level</label>
                <p class="text-base text-gray-900">{{ $storageBin->level }}</p>
            </div>
            @endif
        </div>
        @endif

        @if($storageBin->dimensions)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <label class="block text-sm font-medium text-gray-600 mb-1">Dimensions</label>
            <p class="text-base text-gray-900">{{ $storageBin->dimensions }}</p>
        </div>
        @endif
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('inventory.stocks.by-bin', $storageBin) }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="quarantine" {{ request('status') === 'quarantine' ? 'selected' : '' }}>Quarantine</option>
                        <option value="damaged" {{ request('status') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                {{-- Sort By --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select name="sort" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Latest Added</option>
                        <option value="quantity" {{ request('sort') === 'quantity' ? 'selected' : '' }}>Quantity</option>
                        <option value="product_name" {{ request('sort') === 'product_name' ? 'selected' : '' }}>Product Name</option>
                        <option value="expiry_date" {{ request('sort') === 'expiry_date' ? 'selected' : '' }}>Expiry Date</option>
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('inventory.stocks.by-bin', $storageBin) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stock Items Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Stock Items in This Bin</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Serial</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mfg/Expiry Date</th>
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
                            <td class="px-6 py-4">
                                @if($stock->manufacturing_date)
                                    <div class="text-xs text-gray-500">
                                        <span class="font-medium">Mfg:</span> {{ \Carbon\Carbon::parse($stock->manufacturing_date)->format('M d, Y') }}
                                    </div>
                                @endif
                                @if($stock->expiry_date)
                                    @php
                                        $expiryDate = \Carbon\Carbon::parse($stock->expiry_date);
                                        $daysUntilExpiry = now()->diffInDays($expiryDate, false);
                                        $isExpired = $daysUntilExpiry < 0;
                                        $isExpiringSoon = $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
                                    @endphp
                                    
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">Exp:</span> {{ $expiryDate->format('M d, Y') }}
                                    </div>
                                    @if($isExpired)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Expired
                                        </span>
                                    @elseif($isExpiringSoon)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                            <i class="fas fa-clock mr-1"></i>{{ abs($daysUntilExpiry) }} days
                                        </span>
                                    @endif
                                @endif
                                @if(!$stock->manufacturing_date && !$stock->expiry_date)
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
                                <a href="{{ route('inventory.stocks.show', $stock) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventory.stocks.by-product', $stock->product) }}" class="text-purple-600 hover:text-purple-900" title="View Product Stock">
                                    <i class="fas fa-box"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-inbox text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Bin is Empty</h3>
                                    <p class="text-gray-600">This storage bin currently has no stock items</p>
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

    {{-- Bin Utilization Visual --}}
    @if($stocks->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        {{-- Capacity Overview --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>
                Capacity Overview
            </h3>
            
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Current Usage</span>
                    <span class="text-sm font-semibold text-indigo-600">{{ number_format($totalQuantity) }} / {{ number_format($storageBin->max_capacity) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                    @php
                        $percentage = $storageBin->max_capacity > 0 ? ($totalQuantity / $storageBin->max_capacity) * 100 : 0;
                        $barColor = $percentage >= 90 ? 'bg-red-500' : ($percentage >= 70 ? 'bg-orange-500' : 'bg-green-500');
                    @endphp
                    <div class="{{ $barColor }} h-4 rounded-full transition-all duration-500" style="width: {{ min($percentage, 100) }}%"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs text-gray-500">{{ round($percentage, 1) }}% utilized</span>
                    @if($percentage >= 90)
                        <span class="text-xs font-medium text-red-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Near Full
                        </span>
                    @elseif($percentage >= 70)
                        <span class="text-xs font-medium text-orange-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>High Usage
                        </span>
                    @else
                        <span class="text-xs font-medium text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>Normal
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($availableQuantity) }}</div>
                    <div class="text-xs text-gray-600 mt-1">Available</div>
                </div>
                <div class="text-center p-3 bg-orange-50 rounded-lg">
                    @php
                        $reservedQty = $stocks->sum('reserved_quantity');
                    @endphp
                    <div class="text-2xl font-bold text-orange-600">{{ number_format($reservedQty) }}</div>
                    <div class="text-xs text-gray-600 mt-1">Reserved</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    @php
                        $remaining = max(0, $storageBin->max_capacity - $totalQuantity);
                    @endphp
                    <div class="text-2xl font-bold text-gray-600">{{ number_format($remaining) }}</div>
                    <div class="text-xs text-gray-600 mt-1">Remaining</div>
                </div>
            </div>
        </div>

        {{-- Stock Breakdown by Product --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-boxes text-blue-600 mr-2"></i>
                Top Products in Bin
            </h3>
            <div class="space-y-3">
                @php
                    $topProducts = $stocks->sortByDesc('quantity')->take(5);
                @endphp
                @foreach($topProducts as $stock)
                    @php
                        $productPercentage = $totalQuantity > 0 ? round(($stock->quantity / $totalQuantity) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center flex-1">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-900 truncate">{{ $stock->product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $stock->product->sku }}</div>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <div class="text-sm font-bold text-blue-600">{{ number_format($stock->quantity) }}</div>
                            <div class="text-xs text-gray-500">{{ $productPercentage }}%</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
@endsection