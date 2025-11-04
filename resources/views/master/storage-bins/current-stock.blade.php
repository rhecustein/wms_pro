@extends('layouts.app')

@section('title', 'Current Stock - ' . $storageBin->code)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-boxes text-blue-600 mr-2"></i>
                Current Stock in Bin
            </h1>
            <p class="text-sm text-gray-600 mt-1">View current inventory in storage bin {{ $storageBin->code }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('master.storage-bins.show', $storageBin) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                <i class="fas fa-info-circle mr-2"></i>Bin Details
            </a>
            <a href="{{ route('master.storage-bins.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <div class="flex">
                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                <div>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                <div>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Storage Bin Summary Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-warehouse mr-2"></i>
                Storage Bin Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Bin Code --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Bin Code</label>
                    <p class="text-lg">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-100 text-blue-800 font-mono font-semibold text-xl">
                            {{ $storageBin->code }}
                        </span>
                    </p>
                </div>

                {{-- Location --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Location</label>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-900">
                            <span class="font-medium">Aisle:</span> {{ $storageBin->aisle }} | 
                            <span class="font-medium">Row:</span> {{ $storageBin->row }}
                        </p>
                        <p class="text-sm text-gray-900">
                            <span class="font-medium">Column:</span> {{ $storageBin->column }} | 
                            <span class="font-medium">Level:</span> {{ $storageBin->level }}
                        </p>
                    </div>
                </div>

                {{-- Warehouse --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Warehouse</label>
                    <p class="text-base font-semibold text-gray-900">{{ $storageBin->warehouse->name }}</p>
                    @if($storageBin->storageArea)
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-layer-group mr-1"></i>{{ $storageBin->storageArea->name }}
                        </p>
                    @endif
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <p class="text-lg">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($storageBin->status === 'available') bg-green-100 text-green-800
                            @elseif($storageBin->status === 'occupied') bg-blue-100 text-blue-800
                            @elseif($storageBin->status === 'reserved') bg-yellow-100 text-yellow-800
                            @elseif($storageBin->status === 'blocked') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            <span class="w-2 h-2 rounded-full mr-2
                                @if($storageBin->status === 'available') bg-green-500
                                @elseif($storageBin->status === 'occupied') bg-blue-500
                                @elseif($storageBin->status === 'reserved') bg-yellow-500
                                @elseif($storageBin->status === 'blocked') bg-red-500
                                @else bg-gray-500
                                @endif"></span>
                            {{ ucfirst($storageBin->status) }}
                        </span>
                    </p>
                </div>
            </div>

            {{-- Additional Info Row --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 pt-6 border-t border-gray-200">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Bin Type</label>
                    <p class="text-sm text-gray-900 capitalize">
                        <i class="fas fa-tag mr-1 text-gray-400"></i>
                        {{ str_replace('_', ' ', $storageBin->bin_type) }}
                    </p>
                </div>
                @if($storageBin->customer)
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Customer</label>
                    <p class="text-sm text-gray-900">
                        <i class="fas fa-user mr-1 text-gray-400"></i>
                        {{ $storageBin->customer->name }}
                    </p>
                </div>
                @endif
                @if($storageBin->is_hazmat)
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Special</label>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Hazmat Storage
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Get stock items data --}}
    @php
        $stockItems = \App\Models\InventoryStock::where('storage_bin_id', $storageBin->id)
            ->with(['product', 'unit'])
            ->where('quantity', '>', 0)
            ->get();
        
        $totalQuantity = $stockItems->sum('quantity');
        $totalWeight = $stockItems->sum(function($item) {
            return ($item->product->weight_kg ?? 0) * $item->quantity;
        });
        $totalVolume = $stockItems->sum(function($item) {
            return ($item->product->volume_cbm ?? 0) * $item->quantity;
        });
    @endphp

    {{-- Capacity Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Current Quantity --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Current Quantity</p>
                    <p class="text-3xl font-bold text-blue-600">
                        {{ number_format($totalQuantity) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Units in stock</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stockItems->count() }} different items</p>
                </div>
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box-open text-3xl text-blue-600"></i>
                </div>
            </div>
        </div>

        {{-- Weight Utilization --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Weight Capacity</p>
                    @if($storageBin->max_weight_kg)
                        @php
                            $currentWeight = $totalWeight;
                            $maxWeight = $storageBin->max_weight_kg;
                            $weightPercentage = $maxWeight > 0 ? min(($currentWeight / $maxWeight) * 100, 100) : 0;
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">
                            {{ number_format($currentWeight, 2) }} kg
                        </p>
                        <div class="mt-2">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span>{{ number_format($weightPercentage, 1) }}% used</span>
                                <span>Max: {{ number_format($maxWeight, 0) }} kg</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300
                                    @if($weightPercentage < 50) bg-green-500
                                    @elseif($weightPercentage < 80) bg-yellow-500
                                    @else bg-red-500
                                    @endif" 
                                    style="width: {{ $weightPercentage }}%"></div>
                            </div>
                        </div>
                    @else
                        <p class="text-lg text-gray-400">Not set</p>
                        <p class="text-xs text-gray-500 mt-1">Current: {{ number_format($totalWeight, 2) }} kg</p>
                    @endif
                </div>
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-weight-hanging text-3xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        {{-- Volume Utilization --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Volume Capacity</p>
                    @if($storageBin->max_volume_cbm)
                        @php
                            $currentVolume = $totalVolume;
                            $maxVolume = $storageBin->max_volume_cbm;
                            $volumePercentage = $maxVolume > 0 ? min(($currentVolume / $maxVolume) * 100, 100) : 0;
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">
                            {{ number_format($currentVolume, 3) }} m³
                        </p>
                        <div class="mt-2">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span>{{ number_format($volumePercentage, 1) }}% used</span>
                                <span>Max: {{ number_format($maxVolume, 2) }} m³</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300
                                    @if($volumePercentage < 50) bg-green-500
                                    @elseif($volumePercentage < 80) bg-yellow-500
                                    @else bg-red-500
                                    @endif" 
                                    style="width: {{ $volumePercentage }}%"></div>
                            </div>
                        </div>
                    @else
                        <p class="text-lg text-gray-400">Not set</p>
                        <p class="text-xs text-gray-500 mt-1">Current: {{ number_format($totalVolume, 3) }} m³</p>
                    @endif
                </div>
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-cube text-3xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock Items Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-clipboard-list mr-2"></i>
                Stock Items in Bin
            </h2>
            <div class="flex space-x-2">
                <button onclick="window.location.reload()" class="px-3 py-1 bg-white bg-opacity-20 text-white rounded hover:bg-opacity-30 transition">
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
                <button onclick="window.print()" class="px-3 py-1 bg-white bg-opacity-20 text-white rounded hover:bg-opacity-30 transition">
                    <i class="fas fa-print mr-1"></i>Print
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Serial</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stockItems as $item)
                        @php
                            $expiryDate = $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date) : null;
                            $daysToExpiry = $expiryDate ? now()->diffInDays($expiryDate, false) : null;
                            $expiryStatus = 'good';
                            
                            if ($daysToExpiry !== null) {
                                if ($daysToExpiry < 0) {
                                    $expiryStatus = 'expired';
                                } elseif ($daysToExpiry <= 30) {
                                    $expiryStatus = 'warning';
                                } elseif ($daysToExpiry <= 90) {
                                    $expiryStatus = 'caution';
                                }
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</div>
                                    <div class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    @if($item->batch_number)
                                        <div class="text-gray-900 font-mono text-xs bg-gray-100 px-2 py-1 rounded inline-block mb-1">
                                            <i class="fas fa-boxes text-gray-400 mr-1"></i>{{ $item->batch_number }}
                                        </div>
                                    @endif
                                    @if($item->serial_number)
                                        <div class="text-gray-900 font-mono text-xs bg-blue-50 px-2 py-1 rounded inline-block">
                                            <i class="fas fa-barcode text-blue-400 mr-1"></i>{{ $item->serial_number }}
                                        </div>
                                    @endif
                                    @if(!$item->batch_number && !$item->serial_number)
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold text-gray-900">{{ number_format($item->quantity, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $item->unit_of_measure }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    @if($item->manufacturing_date)
                                        <div class="text-gray-600">
                                            <i class="fas fa-industry text-gray-400 mr-1"></i>
                                            Mfg: {{ \Carbon\Carbon::parse($item->manufacturing_date)->format('d M Y') }}
                                        </div>
                                    @endif
                                    @if($item->expiry_date)
                                        <div class="
                                            @if($expiryStatus === 'expired') text-red-600
                                            @elseif($expiryStatus === 'warning') text-orange-600
                                            @elseif($expiryStatus === 'caution') text-yellow-600
                                            @else text-gray-600
                                            @endif">
                                            <i class="fas fa-calendar-times mr-1"></i>
                                            Exp: {{ $expiryDate->format('d M Y') }}
                                            @if($daysToExpiry !== null && $daysToExpiry >= 0)
                                                <span class="text-xs">({{ $daysToExpiry }}d)</span>
                                            @elseif($daysToExpiry !== null && $daysToExpiry < 0)
                                                <span class="text-xs font-bold">(EXPIRED)</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if(!$item->manufacturing_date && !$item->expiry_date)
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($expiryStatus === 'expired')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                        Expired
                                    </span>
                                @elseif($expiryStatus === 'warning')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <span class="w-2 h-2 bg-orange-500 rounded-full mr-1"></span>
                                        Expiring Soon
                                    </span>
                                @elseif($expiryStatus === 'caution')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
                                        Caution
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                        Good
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('master.storage-bins.transfer-stock', $storageBin) }}" 
                                       class="text-green-600 hover:text-green-900" 
                                       title="Transfer Stock">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                    <a href="{{ route('master.storage-bins.adjust-inventory', $storageBin) }}" 
                                       class="text-purple-600 hover:text-purple-900" 
                                       title="Adjust Quantity">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Empty State --}}
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box-open text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Stock in This Bin</h3>
                                    <p class="text-gray-600 mb-4">This storage bin is currently empty</p>
                                    @if(in_array($storageBin->status, ['available', 'occupied']))
                                        <div class="flex flex-wrap gap-2 justify-center">
                                            <a href="{{ route('master.storage-bins.add-stock', $storageBin) }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                                                <i class="fas fa-plus mr-2"></i>Add Stock
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-lg">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            This bin is currently <strong>{{ $storageBin->status }}</strong>
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer Summary --}}
        @if($stockItems->count() > 0)
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-6 text-sm text-gray-600">
                    <div>
                        <span class="font-medium text-gray-900">Total Items:</span> {{ $stockItems->count() }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-900">Total Quantity:</span> {{ number_format($totalQuantity, 2) }}
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    Last updated: {{ now()->format('d M Y, H:i') }}
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('master.storage-bins.add-stock', $storageBin) }}" 
           class="flex items-center justify-center px-4 py-3 bg-blue-50 border-2 border-blue-200 text-blue-700 rounded-lg hover:bg-blue-100 transition group">
            <i class="fas fa-plus-circle mr-2 group-hover:scale-110 transition-transform"></i>
            Add Stock
        </a>
        <a href="{{ route('master.storage-bins.transfer-stock', $storageBin) }}" 
           class="flex items-center justify-center px-4 py-3 bg-green-50 border-2 border-green-200 text-green-700 rounded-lg hover:bg-green-100 transition group">
            <i class="fas fa-exchange-alt mr-2 group-hover:scale-110 transition-transform"></i>
            Transfer Stock
        </a>
        <a href="{{ route('master.storage-bins.adjust-inventory', $storageBin) }}" 
           class="flex items-center justify-center px-4 py-3 bg-purple-50 border-2 border-purple-200 text-purple-700 rounded-lg hover:bg-purple-100 transition group">
            <i class="fas fa-edit mr-2 group-hover:scale-110 transition-transform"></i>
            Adjust Inventory
        </a>
        <a href="{{ route('master.storage-bins.history', $storageBin) }}" 
           class="flex items-center justify-center px-4 py-3 bg-orange-50 border-2 border-orange-200 text-orange-700 rounded-lg hover:bg-orange-100 transition group">
            <i class="fas fa-history mr-2 group-hover:scale-110 transition-transform"></i>
            View History
        </a>
    </div>

</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto refresh functionality (optional - uncomment if needed)
    // setInterval(() => {
    //     location.reload();
    // }, 60000); // Refresh every 60 seconds

    // Confirm before leaving if there are pending actions
    let hasUnsavedChanges = false;
    
    window.addEventListener('beforeunload', function (e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
</script>
@endpush

@endsection