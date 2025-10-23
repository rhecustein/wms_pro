{{-- resources/views/master/storage-bins/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Storage Bin Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-box text-blue-600 mr-2"></i>
                Storage Bin: {{ $storageBin->code }}
                @if($storageBin->is_hazmat)
                    <span class="ml-3 inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>HAZMAT
                    </span>
                @endif
            </h1>
            <p class="text-sm text-gray-600 mt-1">Complete storage bin information and current status</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.storage-bins.edit', $storageBin) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('master.storage-bins.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

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
                        <form action="{{ route('master.storage-bins.deactivate', $storageBin) }}" method="POST" class="inline">
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
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Status</div>
                        @if($storageBin->status === 'available')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>Available
                            </span>
                        @elseif($storageBin->status === 'occupied')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>Occupied
                            </span>
                        @elseif($storageBin->status === 'reserved')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>Reserved
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

                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Bin Type</div>
                        <div class="font-semibold text-blue-600">{{ ucwords(str_replace('_', ' ', $storageBin->bin_type)) }}</div>
                    </div>

                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Active</div>
                        <div class="font-semibold {{ $storageBin->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $storageBin->is_active ? 'Yes' : 'No' }}
                        </div>
                    </div>

                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Hazmat</div>
                        <div class="font-semibold {{ $storageBin->is_hazmat ? 'text-red-600' : 'text-gray-600' }}">
                            {{ $storageBin->is_hazmat ? 'Yes' : 'No' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                    Location Details
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                <div class="text-gray-400">Not assigned to any area</div>
                            @endif
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-600 mb-2 block">Bin Coordinates</label>
                        <div class="grid grid-cols-4 gap-3">
                            <div class="p-3 bg-gray-50 rounded-lg text-center">
                                <div class="text-xs text-gray-600 mb-1">Aisle</div>
                                <div class="text-lg font-bold text-gray-900">{{ $storageBin->aisle }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg text-center">
                                <div class="text-xs text-gray-600 mb-1">Row</div>
                                <div class="text-lg font-bold text-gray-900">{{ $storageBin->row }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg text-center">
                                <div class="text-xs text-gray-600 mb-1">Column</div>
                                <div class="text-lg font-bold text-gray-900">{{ $storageBin->column }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg text-center">
                                <div class="text-xs text-gray-600 mb-1">Level</div>
                                <div class="text-lg font-bold text-gray-900">{{ $storageBin->level }}</div>
                            </div>
                        </div>
                    </div>

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

            {{-- Capacity Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                    Capacity Information
                </h2>

                <div class="space-y-4">
                    {{-- Weight --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                <i class="fas fa-weight-hanging mr-2 text-blue-600"></i>Weight
                            </span>
                            <span class="text-sm font-semibold">
                                {{ number_format($storageBin->current_weight_kg, 2) }} / 
                                {{ $storageBin->max_weight_kg ? number_format($storageBin->max_weight_kg, 2) : '∞' }} kg
                            </span>
                        </div>
                        @if($storageBin->max_weight_kg)
                            @php
                                $weightPercent = min(100, ($storageBin->current_weight_kg / $storageBin->max_weight_kg) * 100);
                                $weightColor = $weightPercent > 90 ? 'bg-red-600' : ($weightPercent > 70 ? 'bg-yellow-600' : 'bg-blue-600');
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="{{ $weightColor }} h-3 rounded-full transition-all duration-300" style="width: {{ $weightPercent }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ number_format($weightPercent, 1) }}% utilized</div>
                        @else
                            <div class="text-sm text-gray-500">No weight limit set</div>
                        @endif
                    </div>

                    {{-- Volume --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                <i class="fas fa-cube mr-2 text-green-600"></i>Volume
                            </span>
                            <span class="text-sm font-semibold">
                                {{ number_format($storageBin->current_volume_cbm, 2) }} / 
                                {{ $storageBin->max_volume_cbm ? number_format($storageBin->max_volume_cbm, 2) : '∞' }} m³
                            </span>
                        </div>
                        @if($storageBin->max_volume_cbm)
                            @php
                                $volumePercent = min(100, ($storageBin->current_volume_cbm / $storageBin->max_volume_cbm) * 100);
                                $volumeColor = $volumePercent > 90 ? 'bg-red-600' : ($volumePercent > 70 ? 'bg-yellow-600' : 'bg-green-600');
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="{{ $volumeColor }} h-3 rounded-full transition-all duration-300" style="width: {{ $volumePercent }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ number_format($volumePercent, 1) }}% utilized</div>
                        @else
                            <div class="text-sm text-gray-500">No volume limit set</div>
                        @endif
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                <i class="fas fa-boxes mr-2 text-purple-600"></i>Current Quantity
                            </span>
                            <span class="text-lg font-bold text-purple-600">{{ number_format($storageBin->current_quantity, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

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
                            {{ $storageBin->packaging_restriction ? ucfirst($storageBin->packaging_restriction) . ' Only' : 'No Restriction' }}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Created At</label>
                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $storageBin->created_at->format('d M Y H:i') }}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Last Updated</label>
                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $storageBin->updated_at->format('d M Y H:i') }}
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
                        <form action="{{ route('master.storage-bins.deactivate', $storageBin) }}" method="POST">
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

            {{-- Bin Code QR --}}
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