{{-- resources/views/master/storage-bins/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Storage Bin Details - ' . $storageBin->code)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center flex-wrap gap-2">
                <i class="fas fa-box text-blue-600"></i>
                Storage Bin: <span class="font-mono">{{ $storageBin->code }}</span>
                
                @if($storageBin->is_hazmat)
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>HAZMAT
                    </span>
                @endif
                
                @if($storageBin->is_temperature_controlled)
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-thermometer-half mr-2"></i>Temp Controlled
                    </span>
                @endif
                
                @if($storageBin->is_locked)
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-orange-100 text-orange-800">
                        <i class="fas fa-lock mr-2"></i>Locked
                    </span>
                @endif
            </h1>
            <p class="text-sm text-gray-600 mt-1">Complete storage bin information and current status</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('master.storage-bins.edit', $storageBin) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition inline-flex items-center">
                <i class="fas fa-edit mr-2"></i>Edit
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Status Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Status Information
                    </h2>
                    @if($storageBin->is_active)
                        <form action="{{ route('master.storage-bins.deactivate', $storageBin) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to deactivate this bin?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm">
                                <i class="fas fa-toggle-on mr-2"></i>Deactivate
                            </button>
                        </form>
                    @else
                        <form action="{{ route('master.storage-bins.activate', $storageBin) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm">
                                <i class="fas fa-toggle-off mr-2"></i>Activate
                            </button>
                        </form>
                    @endif
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {{-- Status --}}
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Status</div>
                        @if($storageBin->status === 'available')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>Available
                            </span>
                        @elseif($storageBin->status === 'occupied')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>Occupied
                            </span>
                        @elseif($storageBin->status === 'reserved')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>Reserved
                            </span>
                        @elseif($storageBin->status === 'blocked')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>Blocked
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>Maintenance
                            </span>
                        @endif
                    </div>

                    {{-- Bin Type --}}
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Bin Type</div>
                        <div class="font-semibold text-blue-600">{{ ucwords(str_replace('_', ' ', $storageBin->bin_type)) }}</div>
                    </div>

                    {{-- Active Status --}}
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Active</div>
                        <div class="font-semibold {{ $storageBin->is_active ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas fa-{{ $storageBin->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                            {{ $storageBin->is_active ? 'Yes' : 'No' }}
                        </div>
                    </div>

                    {{-- Occupied Status --}}
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Occupied</div>
                        <div class="font-semibold {{ $storageBin->is_occupied ? 'text-blue-600' : 'text-gray-600' }}">
                            <i class="fas fa-{{ $storageBin->is_occupied ? 'box' : 'box-open' }} mr-1"></i>
                            {{ $storageBin->is_occupied ? 'Yes' : 'No' }}
                        </div>
                    </div>
                </div>

                {{-- Additional Status Indicators --}}
                <div class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap gap-2">
                    @if($storageBin->is_hazmat)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Hazmat Approved
                        </span>
                    @endif
                    
                    @if($storageBin->is_temperature_controlled)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-thermometer-half mr-1"></i>
                            Temp: {{ $storageBin->min_temperature_c }}°C to {{ $storageBin->max_temperature_c }}°C
                        </span>
                    @endif
                    
                    @if($storageBin->is_locked)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <i class="fas fa-lock mr-1"></i>Locked
                        </span>
                    @endif
                    
                    @if($storageBin->picking_priority > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-sort-amount-up mr-1"></i>Priority: {{ $storageBin->picking_priority }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Location Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                    Location Details
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Warehouse --}}
                    <div>
                        <label class="text-sm text-gray-600">Warehouse</label>
                        <div class="mt-1 flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-warehouse text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $storageBin->warehouse->name }}</div>
                                <div class="text-sm text-gray-600">{{ $storageBin->warehouse->code }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Storage Area --}}
                    <div>
                        <label class="text-sm text-gray-600">Storage Area</label>
                        <div class="mt-1">
                            @if($storageBin->storageArea)
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-layer-group text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $storageBin->storageArea->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $storageBin->storageArea->code }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-400 italic">Not assigned to any area</div>
                            @endif
                        </div>
                    </div>

                    {{-- Bin Coordinates --}}
                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-600 mb-2 block">Bin Coordinates</label>
                        <div class="grid grid-cols-4 gap-3">
                            <div class="p-3 bg-gray-50 rounded-lg text-center border border-gray-200">
                                <div class="text-xs text-gray-600 mb-1">Aisle</div>
                                <div class="text-lg font-bold text-gray-900">{{ $storageBin->aisle }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg text-center border border-gray-200">
                                <div class="text-xs text-gray-600 mb-1">Row</div>
                                <div class="text-lg font-bold text-gray-900">{{ $storageBin->row }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg text-center border border-gray-200">
                                <div class="text-xs text-gray-600 mb-1">Column</div>
                                <div class="text-lg font-bold text-gray-900">{{ $storageBin->column }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg text-center border border-gray-200">
                                <div class="text-xs text-gray-600 mb-1">Level</div>
                                <div class="text-lg font-bold text-gray-900">{{ $storageBin->level }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Restriction --}}
                    @if($storageBin->customer)
                        <div class="md:col-span-2">
                            <label class="text-sm text-gray-600">Dedicated Customer</label>
                            <div class="mt-1 flex items-center p-3 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $storageBin->customer->name }}</div>
                                    <div class="text-sm text-gray-600">This bin is reserved for this customer</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Physical Dimensions --}}
            @if($storageBin->bin_length_cm || $storageBin->bin_width_cm || $storageBin->bin_height_cm)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-ruler-combined text-indigo-600 mr-2"></i>
                    Physical Dimensions
                </h2>

                <div class="grid grid-cols-3 gap-4">
                    @if($storageBin->bin_length_cm)
                    <div class="text-center p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                        <div class="text-sm text-gray-600 mb-1">Length</div>
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($storageBin->bin_length_cm, 0) }}</div>
                        <div class="text-xs text-gray-500">cm</div>
                    </div>
                    @endif

                    @if($storageBin->bin_width_cm)
                    <div class="text-center p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                        <div class="text-sm text-gray-600 mb-1">Width</div>
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($storageBin->bin_width_cm, 0) }}</div>
                        <div class="text-xs text-gray-500">cm</div>
                    </div>
                    @endif

                    @if($storageBin->bin_height_cm)
                    <div class="text-center p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                        <div class="text-sm text-gray-600 mb-1">Height</div>
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($storageBin->bin_height_cm, 0) }}</div>
                        <div class="text-xs text-gray-500">cm</div>
                    </div>
                    @endif
                </div>

                @php
                    $totalVolume = 0;
                    if($storageBin->bin_length_cm && $storageBin->bin_width_cm && $storageBin->bin_height_cm) {
                        $totalVolume = ($storageBin->bin_length_cm * $storageBin->bin_width_cm * $storageBin->bin_height_cm) / 1000000;
                    }
                @endphp

                @if($totalVolume > 0)
                <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                    <div class="text-sm text-gray-600">Total Bin Volume</div>
                    <div class="text-xl font-bold text-indigo-600 mt-1">{{ number_format($totalVolume, 3) }} m³</div>
                </div>
                @endif
            </div>
            @endif

            {{-- Capacity Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                    Capacity & Utilization
                </h2>

                <div class="space-y-4">
                    {{-- Weight Capacity --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                <i class="fas fa-weight-hanging mr-2 text-blue-600"></i>Weight Capacity
                            </span>
                            <span class="text-sm font-semibold">
                                {{ number_format($storageBin->current_weight_kg, 2) }} / 
                                {{ $storageBin->max_weight_kg ? number_format($storageBin->max_weight_kg, 2) : '∞' }} kg
                            </span>
                        </div>
                        @if($storageBin->max_weight_kg)
                            @php
                                $weightPercent = min(100, ($storageBin->current_weight_kg / $storageBin->max_weight_kg) * 100);
                                $weightColor = $weightPercent > 90 ? 'bg-red-600' : ($weightPercent > 70 ? 'bg-yellow-500' : 'bg-blue-600');
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="{{ $weightColor }} h-3 rounded-full transition-all duration-300" style="width: {{ $weightPercent }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ number_format($weightPercent, 1) }}% utilized</div>
                        @else
                            <div class="text-sm text-gray-500 italic">No weight limit set</div>
                        @endif
                    </div>

                    {{-- Volume Capacity --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                <i class="fas fa-cube mr-2 text-green-600"></i>Volume Capacity
                            </span>
                            <span class="text-sm font-semibold">
                                {{ number_format($storageBin->current_volume_cbm, 3) }} / 
                                {{ $storageBin->max_volume_cbm ? number_format($storageBin->max_volume_cbm, 3) : '∞' }} m³
                            </span>
                        </div>
                        @if($storageBin->max_volume_cbm)
                            @php
                                $volumePercent = min(100, ($storageBin->current_volume_cbm / $storageBin->max_volume_cbm) * 100);
                                $volumeColor = $volumePercent > 90 ? 'bg-red-600' : ($volumePercent > 70 ? 'bg-yellow-500' : 'bg-green-600');
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="{{ $volumeColor }} h-3 rounded-full transition-all duration-300" style="width: {{ $volumePercent }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ number_format($volumePercent, 1) }}% utilized</div>
                        @else
                            <div class="text-sm text-gray-500 italic">No volume limit set</div>
                        @endif
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                <i class="fas fa-boxes mr-2 text-purple-600"></i>Current Quantity
                            </span>
                            <span class="text-lg font-bold text-purple-600">{{ number_format($storageBin->current_quantity, 2) }} units</span>
                        </div>
                        @if($storageBin->max_quantity)
                            @php
                                $qtyPercent = min(100, ($storageBin->current_quantity / $storageBin->max_quantity) * 100);
                                $qtyColor = $qtyPercent > 90 ? 'bg-red-600' : ($qtyPercent > 70 ? 'bg-yellow-500' : 'bg-purple-600');
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-3 mt-2">
                                <div class="{{ $qtyColor }} h-3 rounded-full transition-all duration-300" style="width: {{ $qtyPercent }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>{{ number_format($qtyPercent, 1) }}% of max capacity</span>
                                <span>Max: {{ number_format($storageBin->max_quantity, 0) }} units</span>
                            </div>
                        @endif
                        @if($storageBin->min_quantity > 0)
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>Minimum threshold: {{ number_format($storageBin->min_quantity, 0) }} units
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Identification & Tracking --}}
            @if($storageBin->barcode || $storageBin->rfid_tag)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-barcode text-gray-600 mr-2"></i>
                    Identification & Tracking
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($storageBin->barcode)
                    <div>
                        <label class="text-sm text-gray-600 block mb-2">Barcode</label>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="font-mono text-sm text-gray-900">{{ $storageBin->barcode }}</div>
                        </div>
                    </div>
                    @endif

                    @if($storageBin->rfid_tag)
                    <div>
                        <label class="text-sm text-gray-600 block mb-2">RFID Tag</label>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="font-mono text-sm text-gray-900">{{ $storageBin->rfid_tag }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Audit Information --}}
            @if($storageBin->last_count_date || $storageBin->last_movement_date)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-gray-600 mr-2"></i>
                    Audit Trail
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($storageBin->last_count_date)
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-clipboard-check text-blue-600"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Last Physical Count</div>
                                <div class="font-semibold text-gray-900">{{ $storageBin->last_count_date->format('d M Y, H:i') }}</div>
                                @if($storageBin->lastCountBy)
                                    <div class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-user mr-1"></i>by {{ $storageBin->lastCountBy->name }}
                                    </div>
                                @endif
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $storageBin->last_count_date->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($storageBin->last_movement_date)
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-exchange-alt text-green-600"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Last Stock Movement</div>
                                <div class="font-semibold text-gray-900">{{ $storageBin->last_movement_date->format('d M Y, H:i') }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $storageBin->last_movement_date->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Additional Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clipboard-list text-orange-600 mr-2"></i>
                    Additional Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Packaging Restriction</label>
                        <div class="mt-1 font-semibold text-gray-900">
                            @if($storageBin->packaging_restriction && $storageBin->packaging_restriction !== 'none')
                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-orange-100 text-orange-800 text-sm">
                                    <i class="fas fa-box-open mr-1"></i>
                                    {{ ucfirst($storageBin->packaging_restriction) }} Only
                                </span>
                            @else
                                <span class="text-gray-500">No Restriction</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Picking Priority</label>
                        <div class="mt-1">
                            @if($storageBin->picking_priority > 0)
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $storageBin->picking_priority }}%"></div>
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $storageBin->picking_priority }}</span>
                                </div>
                            @else
                                <span class="text-gray-500">Not set</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Created At</label>
                        <div class="mt-1 text-sm text-gray-900">
                            {{ $storageBin->created_at->format('d M Y, H:i') }}
                            <span class="text-gray-500">({{ $storageBin->created_at->diffForHumans() }})</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Last Updated</label>
                        <div class="mt-1 text-sm text-gray-900">
                            {{ $storageBin->updated_at->format('d M Y, H:i') }}
                            <span class="text-gray-500">({{ $storageBin->updated_at->diffForHumans() }})</span>
                        </div>
                    </div>
                </div>

                @if($storageBin->notes)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <label class="text-sm text-gray-600 mb-2 block">Notes</label>
                        <div class="p-3 bg-gray-50 rounded-lg text-sm text-gray-700">
                            {{ $storageBin->notes }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                    Quick Actions
                </h2>

                <div class="space-y-3">
                    <a href="{{ route('master.storage-bins.current-stock', $storageBin) }}" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                        <i class="fas fa-box-open mr-2"></i>
                        View Current Stock
                    </a>
                    
                    <a href="{{ route('master.storage-bins.edit', $storageBin) }}" class="w-full px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Bin
                    </a>

                    @if($storageBin->is_active)
                        <form action="{{ route('master.storage-bins.deactivate', $storageBin) }}" method="POST" onsubmit="return confirm('Are you sure you want to deactivate this bin?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition flex items-center justify-center">
                                <i class="fas fa-toggle-on mr-2"></i>
                                Deactivate Bin
                            </button>
                        </form>
                    @else
                        <form action="{{ route('master.storage-bins.activate', $storageBin) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition flex items-center justify-center">
                                <i class="fas fa-toggle-off mr-2"></i>
                                Activate Bin
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('master.storage-bins.destroy', $storageBin) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this storage bin? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Bin
                        </button>
                    </form>
                </div>
            </div>

            {{-- Bin Code Display --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-qrcode text-gray-600 mr-2"></i>
                    Bin Identifier
                </h2>

                <div class="text-center">
                    <div class="inline-block p-4 bg-gray-50 rounded-lg border-2 border-gray-300">
                        <div class="text-3xl font-mono font-bold text-gray-900 mb-2">{{ $storageBin->code }}</div>
                        <div class="text-sm text-gray-600">Storage Bin Code</div>
                    </div>
                    
                    @if($storageBin->barcode)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="text-xs text-gray-600 mb-1">Barcode</div>
                        <div class="text-sm font-mono text-gray-900 bg-white p-2 rounded border border-gray-300">
                            {{ $storageBin->barcode }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Related Links --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-link text-purple-600 mr-2"></i>
                    Related Information
                </h2>

                <div class="space-y-2">
                    <a href="{{ route('master.warehouses.show', $storageBin->warehouse) }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <i class="fas fa-warehouse text-blue-600 mr-3"></i>
                        <span class="text-sm font-medium text-gray-700">View Warehouse</span>
                    </a>

                    @if($storageBin->storageArea)
                        <a href="{{ route('master.storage-areas.show', $storageBin->storageArea) }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-layer-group text-purple-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-700">View Storage Area</span>
                        </a>
                    @endif

                    @if($storageBin->customer)
                        <a href="{{ route('master.customers.show', $storageBin->customer) }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-user text-green-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-700">View Customer</span>
                        </a>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection