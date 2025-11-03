@extends('layouts.app')

@section('title', 'Current Stock - ' . $storageBin->code)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-boxes text-blue-600 mr-2"></i>
                Current Stock in Bin
            </h1>
            <p class="text-sm text-gray-600 mt-1">View current inventory in storage bin {{ $storageBin->code }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.storage-bins.show', $storageBin) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-info-circle mr-2"></i>Bin Details
            </a>
            <a href="{{ route('master.storage-bins.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- Storage Bin Summary Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-warehouse mr-2"></i>
                Storage Bin Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
                    <p class="text-lg font-semibold text-gray-900">{{ $storageBin->warehouse->name }}</p>
                    @if($storageBin->storageArea)
                        <p class="text-sm text-gray-600">
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
        </div>
    </div>

    {{-- Capacity Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Current Quantity --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Current Quantity</p>
                    <p class="text-3xl font-bold text-blue-600">
                        {{ number_format($storageBin->current_quantity ?? 0) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Units in stock</p>
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
                            $currentWeight = $storageBin->current_weight_kg ?? 0;
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
                    @endif
                </div>
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center ml-4">
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
                            $currentVolume = $storageBin->current_volume_cbm ?? 0;
                            $maxVolume = $storageBin->max_volume_cbm;
                            $volumePercentage = $maxVolume > 0 ? min(($currentVolume / $maxVolume) * 100, 100) : 0;
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">
                            {{ number_format($currentVolume, 2) }} m³
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
                    @endif
                </div>
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center ml-4">
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
                <button class="px-3 py-1 bg-white bg-opacity-20 text-white rounded hover:bg-opacity-30 transition">
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
                <button class="px-3 py-1 bg-white bg-opacity-20 text-white rounded hover:bg-opacity-30 transition">
                    <i class="fas fa-file-export mr-1"></i>Export
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Serial</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weight</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Example data - Replace with actual stock items from relationship --}}
                    @php
                        // This is example data. Replace with actual inventory items
                        // $stockItems = $storageBin->inventoryItems; or similar
                        $hasStock = false; // Set to true if you have actual data
                    @endphp

                    @if($hasStock)
                        {{-- Loop through actual stock items here --}}
                        {{-- @foreach($stockItems as $item) --}}
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Sample Product Name</div>
                                    <div class="text-xs text-gray-500">SKU: PROD-001</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="text-gray-900 font-mono">BATCH-2025-001</div>
                                    <div class="text-xs text-gray-500">SN: SN123456</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">150</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">PCS</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">50.5 kg</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">0.75 m³</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">31 Dec 2025</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                    Good
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="#" class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="text-green-600 hover:text-green-900" title="Move Stock">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                    <a href="#" class="text-purple-600 hover:text-purple-900" title="Adjust Quantity">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        {{-- @endforeach --}}
                    @else
                        {{-- Empty State --}}
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box-open text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Stock in This Bin</h3>
                                    <p class="text-gray-600 mb-4">This storage bin is currently empty</p>
                                    @if($storageBin->status === 'available')
                                        <div class="flex space-x-2">
                                            <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                                <i class="fas fa-plus mr-2"></i>Add Stock
                                            </button>
                                            <button class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                                <i class="fas fa-truck-loading mr-2"></i>Receive Inventory
                                            </button>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500">
                                            This bin is currently {{ $storageBin->status }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="{{ route('master.storage-bins.add-stock', $storageBin) }}" class="flex items-center justify-center px-4 py-3 bg-blue-50 border-2 border-blue-200 text-blue-700 rounded-lg hover:bg-blue-100 transition">
            <i class="fas fa-plus-circle mr-2"></i>
            Add Stock
        </a>
        <a href="{{ route('master.storage-bins.transfer-stock', $storageBin) }}" class="flex items-center justify-center px-4 py-3 bg-green-50 border-2 border-green-200 text-green-700 rounded-lg hover:bg-green-100 transition">
            <i class="fas fa-exchange-alt mr-2"></i>
            Transfer Stock
        </a>
        <a href="{{ route('master.storage-bins.adjust-inventory', $storageBin) }}" class="flex items-center justify-center px-4 py-3 bg-purple-50 border-2 border-purple-200 text-purple-700 rounded-lg hover:bg-purple-100 transition">
            <i class="fas fa-edit mr-2"></i>
            Adjust Inventory
        </a>
        <a href="{{ route('master.storage-bins.history', $storageBin) }}" class="flex items-center justify-center px-4 py-3 bg-orange-50 border-2 border-orange-200 text-orange-700 rounded-lg hover:bg-orange-100 transition">
            <i class="fas fa-history mr-2"></i>
            View History
        </a>
    </div>

</div>

@push('scripts')
<script>
    // Auto refresh functionality (optional)
    // setInterval(() => {
    //     location.reload();
    // }, 60000); // Refresh every 60 seconds
</script>
@endpush

@endsection