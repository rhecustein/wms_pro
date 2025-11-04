@extends('layouts.app')

@section('title', 'Stock Summary - ' . $product->name)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                Stock Summary
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $product->name }} ({{ $product->sku }})</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('master.products.show', $product) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to Product
            </a>
            <a href="{{ route('master.products.movements', $product) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                <i class="fas fa-history mr-2"></i>View Movements
            </a>
        </div>
    </div>

    {{-- Product Overview Card --}}
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg text-white p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>
                <div class="flex flex-wrap gap-2 text-sm">
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full">
                        <i class="fas fa-barcode mr-1"></i>{{ $product->sku }}
                    </span>
                    @if($product->brand)
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full">
                        <i class="fas fa-copyright mr-1"></i>{{ $product->brand }}
                    </span>
                    @endif
                    @if($product->category)
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full">
                        <i class="fas fa-tag mr-1"></i>{{ $product->category->name }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="text-center">
                <p class="text-sm opacity-90 mb-1">Current Stock</p>
                <p class="text-4xl font-bold">{{ number_format($product->current_stock) }}</p>
                <p class="text-xs opacity-75 mt-1">{{ $product->unit->short_code ?? 'Units' }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm opacity-90 mb-1">Stock Value</p>
                <p class="text-2xl font-bold">Rp {{ number_format($product->calculateTotalValue(), 0, ',', '.') }}</p>
                <p class="text-xs opacity-75 mt-1">at selling price</p>
            </div>
        </div>
    </div>

    {{-- Summary Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-1">Total Quantity</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($totalQuantity) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $product->unit->short_code ?? 'Units' }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-1">Storage Locations</p>
                    <p class="text-3xl font-bold text-green-600">{{ $totalLocations }}</p>
                    <p class="text-xs text-gray-500 mt-1">Different bins</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-warehouse text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-1">Reorder Level</p>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($product->reorder_level) }}</p>
                    @if($product->current_stock <= $product->reorder_level)
                        <p class="text-xs text-red-600 mt-1 font-semibold">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Reorder needed
                        </p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">Stock adequate</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sync-alt text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-1">Stock Status</p>
                    <div class="mt-2">
                        {!! $product->stock_status_badge !!}
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Min: {{ $product->minimum_stock }} | Max: {{ $product->maximum_stock }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock by Warehouse --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600 border-b">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-warehouse mr-2"></i>
                Stock by Warehouse
            </h3>
        </div>
        
        @if($byWarehouse->count() > 0)
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($byWarehouse as $warehouseName => $stocks)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-lg font-semibold text-gray-900">
                                    <i class="fas fa-building text-purple-600 mr-2"></i>
                                    {{ $warehouseName }}
                                </h4>
                                <span class="px-4 py-2 bg-purple-100 text-purple-800 rounded-lg font-bold">
                                    {{ number_format($stocks->sum('quantity')) }} {{ $product->unit->short_code ?? 'Units' }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($stocks as $stock)
                                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900 font-mono">
                                                    <i class="fas fa-cube text-blue-500 mr-1"></i>
                                                    {{ $stock->storageBin->code }}
                                                </p>
                                                @if($stock->storageBin->storageArea)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <i class="fas fa-layer-group mr-1"></i>
                                                        {{ $stock->storageBin->storageArea->name }}
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="text-lg font-bold text-gray-900">
                                                {{ number_format($stock->quantity, 0) }}
                                            </span>
                                        </div>
                                        
                                        @if($stock->batch_number || $stock->serial_number)
                                            <div class="mt-2 pt-2 border-t border-gray-200">
                                                @if($stock->batch_number)
                                                    <p class="text-xs text-gray-600 font-mono">
                                                        <i class="fas fa-boxes text-blue-400 mr-1"></i>
                                                        Batch: {{ $stock->batch_number }}
                                                    </p>
                                                @endif
                                                @if($stock->serial_number)
                                                    <p class="text-xs text-gray-600 font-mono">
                                                        <i class="fas fa-barcode text-green-400 mr-1"></i>
                                                        SN: {{ $stock->serial_number }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif

                                        @if($stock->expiry_date)
                                            @php
                                                $expiryDate = \Carbon\Carbon::parse($stock->expiry_date);
                                                $daysToExpiry = now()->diffInDays($expiryDate, false);
                                            @endphp
                                            <div class="mt-2 pt-2 border-t border-gray-200">
                                                <p class="text-xs {{ $daysToExpiry < 0 ? 'text-red-600' : ($daysToExpiry <= 30 ? 'text-orange-600' : 'text-gray-600') }}">
                                                    <i class="fas fa-calendar-times mr-1"></i>
                                                    Exp: {{ $expiryDate->format('d M Y') }}
                                                    @if($daysToExpiry >= 0)
                                                        ({{ $daysToExpiry }}d)
                                                    @else
                                                        <span class="font-bold">(EXPIRED)</span>
                                                    @endif
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box-open text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">No Stock in Warehouses</h3>
                <p class="text-gray-600 mb-4">This product has no inventory in any storage bins yet</p>
                <p class="text-sm text-gray-500">Stock: {{ number_format($product->current_stock) }} {{ $product->unit->short_code ?? 'Units' }} (not allocated to bins)</p>
            </div>
        @endif
    </div>

    {{-- Detailed Stock Table --}}
    @if($inventoryStocks->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 border-b">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-list mr-2"></i>
                Detailed Stock Information
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warehouse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Storage Bin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch/Serial</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($inventoryStocks as $stock)
                        @php
                            $expiryStatus = 'good';
                            if ($stock->expiry_date) {
                                $expiryDate = \Carbon\Carbon::parse($stock->expiry_date);
                                $daysToExpiry = now()->diffInDays($expiryDate, false);
                                if ($daysToExpiry < 0) {
                                    $expiryStatus = 'expired';
                                } elseif ($daysToExpiry <= 30) {
                                    $expiryStatus = 'warning';
                                } elseif ($daysToExpiry <= 90) {
                                    $expiryStatus = 'caution';
                                }
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $stock->storageBin->warehouse->name }}
                                </div>
                                @if($stock->storageBin->storageArea)
                                    <div class="text-xs text-gray-500">
                                        {{ $stock->storageBin->storageArea->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono font-semibold text-sm text-gray-900">
                                    {{ $stock->storageBin->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    @if($stock->batch_number)
                                        <div class="font-mono bg-blue-50 px-2 py-1 rounded inline-block">
                                            <i class="fas fa-boxes text-blue-400 mr-1"></i>{{ $stock->batch_number }}
                                        </div>
                                    @endif
                                    @if($stock->serial_number)
                                        <div class="font-mono bg-green-50 px-2 py-1 rounded inline-block">
                                            <i class="fas fa-barcode text-green-400 mr-1"></i>{{ $stock->serial_number }}
                                        </div>
                                    @endif
                                    @if(!$stock->batch_number && !$stock->serial_number)
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold text-gray-900">{{ number_format($stock->quantity, 0) }}</span>
                                <div class="text-xs text-gray-500">{{ $stock->unit_of_measure }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    @if($stock->manufacturing_date)
                                        <div class="text-gray-600">
                                            <i class="fas fa-industry mr-1"></i>
                                            Mfg: {{ \Carbon\Carbon::parse($stock->manufacturing_date)->format('d M Y') }}
                                        </div>
                                    @endif
                                    @if($stock->expiry_date)
                                        <div class="{{ $expiryStatus === 'expired' ? 'text-red-600' : ($expiryStatus === 'warning' ? 'text-orange-600' : 'text-gray-600') }}">
                                            <i class="fas fa-calendar-times mr-1"></i>
                                            Exp: {{ $expiryDate->format('d M Y') }}
                                            @if(isset($daysToExpiry) && $daysToExpiry >= 0)
                                                ({{ $daysToExpiry }}d)
                                            @elseif(isset($daysToExpiry))
                                                <span class="font-bold">(EXPIRED)</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if(!$stock->manufacturing_date && !$stock->expiry_date)
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($expiryStatus === 'expired')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>Expired
                                    </span>
                                @elseif($expiryStatus === 'warning')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <span class="w-2 h-2 bg-orange-500 rounded-full mr-1"></span>Expiring Soon
                                    </span>
                                @elseif($expiryStatus === 'caution')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>Caution
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>Good
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection