{{-- resources/views/inventory/stocks/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-box text-blue-600 mr-2"></i>
                Stock Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View detailed information about this stock item</p>
        </div>
        <a href="{{ route('inventory.stocks.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Information --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Product Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-box-open text-blue-600 mr-2"></i>
                    Product Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Product Name</label>
                        <p class="text-base font-semibold text-gray-900">{{ $inventoryStock->product->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">SKU</label>
                        <p class="text-base font-mono text-gray-900">{{ $inventoryStock->product->sku }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Batch Number</label>
                        <p class="text-base text-gray-900">{{ $inventoryStock->batch_number ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Serial Number</label>
                        <p class="text-base text-gray-900">{{ $inventoryStock->serial_number ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Location Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>
                    Location Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Warehouse</label>
                        <p class="text-base font-semibold text-gray-900">{{ $inventoryStock->warehouse->name }}</p>
                        <p class="text-sm text-gray-500">{{ $inventoryStock->warehouse->city }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Storage Bin</label>
                        <p class="text-base font-mono font-semibold text-gray-900">{{ $inventoryStock->storageBin->code }}</p>
                        <p class="text-sm text-gray-500">{{ $inventoryStock->storageBin->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Location Type</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            {{ ucfirst(str_replace('_', ' ', $inventoryStock->location_type)) }}
                        </span>
                    </div>
                    @if($inventoryStock->pallet)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Pallet</label>
                        <p class="text-base text-gray-900">{{ $inventoryStock->pallet->code }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Quantity Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-cubes text-green-600 mr-2"></i>
                    Quantity Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-blue-600 mb-1">Total Quantity</label>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($inventoryStock->quantity) }}</p>
                        <p class="text-sm text-blue-600">{{ $inventoryStock->unit_of_measure }}</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-orange-600 mb-1">Reserved</label>
                        <p class="text-2xl font-bold text-orange-900">{{ number_format($inventoryStock->reserved_quantity) }}</p>
                        <p class="text-sm text-orange-600">{{ $inventoryStock->unit_of_measure }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-green-600 mb-1">Available</label>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($inventoryStock->available_quantity) }}</p>
                        <p class="text-sm text-green-600">{{ $inventoryStock->unit_of_measure }}</p>
                    </div>
                </div>
            </div>

            {{-- Date Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-calendar text-orange-600 mr-2"></i>
                    Date Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Manufacturing Date</label>
                        <p class="text-base text-gray-900">
                            {{ $inventoryStock->manufacturing_date ? \Carbon\Carbon::parse($inventoryStock->manufacturing_date)->format('d M Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Expiry Date</label>
                        @if($inventoryStock->expiry_date)
                            @php
                                $daysToExpiry = \Carbon\Carbon::parse($inventoryStock->expiry_date)->diffInDays(now(), false);
                                $isExpired = $daysToExpiry > 0;
                            @endphp
                            <p class="text-base {{ $isExpired ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                {{ \Carbon\Carbon::parse($inventoryStock->expiry_date)->format('d M Y') }}
                            </p>
                            @if($isExpired)
                                <p class="text-sm text-red-600">Expired {{ $daysToExpiry }} days ago</p>
                            @else
                                <p class="text-sm text-gray-500">{{ abs($daysToExpiry) }} days remaining</p>
                            @endif
                        @else
                            <p class="text-base text-gray-900">-</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Received Date</label>
                        <p class="text-base text-gray-900">
                            {{ $inventoryStock->received_date ? \Carbon\Carbon::parse($inventoryStock->received_date)->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Additional Information --}}
            @if($inventoryStock->customer || $inventoryStock->vendor || $inventoryStock->cost_per_unit)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Additional Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($inventoryStock->customer)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Customer</label>
                        <p class="text-base text-gray-900">{{ $inventoryStock->customer->name }}</p>
                    </div>
                    @endif
                    @if($inventoryStock->vendor)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Vendor</label>
                        <p class="text-base text-gray-900">{{ $inventoryStock->vendor->name }}</p>
                    </div>
                    @endif
                    @if($inventoryStock->cost_per_unit)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cost Per Unit</label>
                        <p class="text-base font-semibold text-gray-900">Rp {{ number_format($inventoryStock->cost_per_unit, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Notes --}}
            @if($inventoryStock->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                    Notes
                </h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $inventoryStock->notes }}</p>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Status Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Status</h3>
                @php
                    $statusInfo = [
                        'available' => ['color' => 'green', 'icon' => 'check-circle', 'label' => 'Available'],
                        'reserved' => ['color' => 'orange', 'icon' => 'lock', 'label' => 'Reserved'],
                        'quarantine' => ['color' => 'yellow', 'icon' => 'exclamation-triangle', 'label' => 'Quarantine'],
                        'damaged' => ['color' => 'red', 'icon' => 'times-circle', 'label' => 'Damaged'],
                        'expired' => ['color' => 'gray', 'icon' => 'ban', 'label' => 'Expired'],
                    ];
                    $status = $statusInfo[$inventoryStock->status] ?? $statusInfo['available'];
                @endphp
                <div class="flex items-center justify-center p-4 bg-{{ $status['color'] }}-50 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-{{ $status['icon'] }} text-4xl text-{{ $status['color'] }}-600 mb-2"></i>
                        <p class="text-lg font-semibold text-{{ $status['color'] }}-900">{{ $status['label'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('inventory.stocks.by-product', $inventoryStock->product) }}" class="block w-full px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition text-center">
                        <i class="fas fa-list mr-2"></i>View All Stock for This Product
                    </a>
                    <a href="{{ route('inventory.stocks.by-warehouse', $inventoryStock->warehouse) }}" class="block w-full px-4 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition text-center">
                        <i class="fas fa-warehouse mr-2"></i>View All Stock in Warehouse
                    </a>
                    <a href="{{ route('inventory.stocks.by-bin', $inventoryStock->storageBin) }}" class="block w-full px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition text-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>View All Stock in This Bin
                    </a>
                </div>
            </div>

            {{-- Record Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Record Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-sm text-gray-900">{{ $inventoryStock->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-sm text-gray-900">{{ $inventoryStock->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection