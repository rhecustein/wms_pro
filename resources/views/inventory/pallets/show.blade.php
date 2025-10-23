{{-- resources/views/inventory/pallets/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Pallet Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('inventory.pallets.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i>Back to Pallets
            </a>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-pallet text-blue-600 mr-2"></i>
                {{ $pallet->pallet_number }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">Pallet Details and Information</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('inventory.pallets.history', $pallet) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-history mr-2"></i>History
            </a>
            <a href="{{ route('inventory.pallets.edit', $pallet) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Information --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Status Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-500">Status</span>
                        @php
                            $statusColors = [
                                'empty' => 'bg-gray-100 text-gray-800',
                                'loaded' => 'bg-yellow-100 text-yellow-800',
                                'in_transit' => 'bg-blue-100 text-blue-800',
                                'damaged' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$pallet->status] ?? 'bg-gray-100 text-gray-800' }}">
                        <span class="w-2 h-2 bg-current rounded-full mr-2"></span>
                        {{ ucfirst(str_replace('_', ' ', $pallet->status)) }}
                    </span>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-500">Condition</span>
                        @php
                            $conditionColors = [
                                'good' => 'bg-green-100 text-green-800',
                                'fair' => 'bg-yellow-100 text-yellow-800',
                                'poor' => 'bg-orange-100 text-orange-800',
                                'damaged' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $conditionColors[$pallet->condition] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($pallet->condition) }}
                    </span>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-500">Availability</span>
                    </div>
                    @if($pallet->is_available)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Available
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-2"></i>Unavailable
                        </span>
                    @endif
                </div>
            </div>

            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Basic Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Pallet Number</label>
                        <p class="text-gray-900 font-mono font-semibold text-lg">{{ $pallet->pallet_number }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Pallet Type</label>
                        <p class="text-gray-900 font-semibold">{{ ucfirst($pallet->pallet_type) }}</p>
                    </div>

                    @if($pallet->barcode)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Barcode</label>
                            <p class="text-gray-900 font-mono">{{ $pallet->barcode }}</p>
                        </div>
                    @endif

                    @if($pallet->qr_code)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">QR Code</label>
                            <p class="text-gray-900 font-mono">{{ $pallet->qr_code }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Dimensions & Capacity --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-ruler-combined text-blue-600 mr-2"></i>
                    Dimensions & Capacity
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Dimensions (W × D × H)</label>
                        <p class="text-gray-900 font-semibold text-lg">
                            {{ $pallet->width_cm }} × {{ $pallet->depth_cm }} × {{ $pallet->height_cm }} cm
                        </p>
                        <p class="text-sm text-gray-500 mt-1">Volume: {{ number_format($pallet->volume, 2) }} m³</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Weight Capacity</label>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Current</span>
                                <span class="text-gray-900 font-semibold">{{ $pallet->current_weight_kg }} kg</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Maximum</span>
                                <span class="text-gray-900 font-semibold">{{ $pallet->max_weight_kg }} kg</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full transition-all" style="width: {{ min($pallet->capacity_utilization, 100) }}%"></div>
                            </div>
                            <p class="text-sm text-gray-500">{{ number_format($pallet->capacity_utilization, 1) }}% utilized</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                    Location
                </h2>

                @if($pallet->storageBin)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-warehouse text-blue-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Storage Bin</span>
                        </div>
                        <p class="text-gray-900 font-semibold text-lg">{{ $pallet->storageBin->code }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $pallet->storageBin->location }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-building mr-1"></i>
                            {{ $pallet->storageBin->warehouse->name }}
                        </p>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                        <i class="fas fa-map-marker-alt text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500">Not assigned to any storage bin</p>
                    </div>
                @endif
            </div>

            {{-- Additional Notes --}}
            @if($pallet->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-blue-600 mr-2"></i>
                        Notes
                    </h2>
                    <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $pallet->notes }}</p>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt text-blue-600 mr-2"></i>
                    Quick Actions
                </h2>

                <div class="space-y-2">
                    @if($pallet->is_available)
                        <form action="{{ route('inventory.pallets.deactivate', $pallet) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition text-left">
                                <i class="fas fa-ban mr-2"></i>Deactivate Pallet
                            </button>
                        </form>
                    @else
                        <form action="{{ route('inventory.pallets.activate', $pallet) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition text-left">
                                <i class="fas fa-check-circle mr-2"></i>Activate Pallet
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('inventory.pallets.edit', $pallet) }}" class="block w-full px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition">
                        <i class="fas fa-edit mr-2"></i>Edit Information
                    </a>

                    <form action="{{ route('inventory.pallets.destroy', $pallet) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this pallet?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition text-left">
                            <i class="fas fa-trash mr-2"></i>Delete Pallet
                        </button>
                    </form>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clock text-blue-600 mr-2"></i>
                    Timeline
                </h2>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3"></div>
                        <div>
                            <p class="text-sm text-gray-500">Created At</p>
                            <p class="text-gray-900 font-medium">{{ $pallet->created_at->format('d M Y, H:i') }}</p>
                            @if($pallet->createdBy)
                                <p class="text-xs text-gray-500">by {{ $pallet->createdBy->name }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></div>
                        <div>
                            <p class="text-sm text-gray-500">Last Updated</p>
                            <p class="text-gray-900 font-medium">{{ $pallet->updated_at->format('d M Y, H:i') }}</p>
                            @if($pallet->updatedBy)
                                <p class="text-xs text-gray-500">by {{ $pallet->updatedBy->name }}</p>
                            @endif
                        </div>
                    </div>

                    @if($pallet->last_used_date)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-purple-600 rounded-full mt-2 mr-3"></div>
                            <div>
                                <p class="text-sm text-gray-500">Last Used</p>
                                <p class="text-gray-900 font-medium">{{ $pallet->last_used_date->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Statistics --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                    Statistics
                </h2>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Volume</span>
                        <span class="font-semibold text-gray-900">{{ number_format($pallet->volume, 2) }} m³</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Weight Used</span>
                        <span class="font-semibold text-gray-900">{{ number_format($pallet->capacity_utilization, 1) }}%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Remaining Capacity</span>
                        <span class="font-semibold text-gray-900">{{ $pallet->max_weight_kg - $pallet->current_weight_kg }} kg</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection