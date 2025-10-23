{{-- resources/views/master/storage-areas/by-warehouse.blade.php --}}
@extends('layouts.app')

@section('title', 'Storage Areas - ' . $warehouse->name)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                Storage Areas - {{ $warehouse->name }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                <i class="fas fa-warehouse text-gray-400 mr-1"></i>
                {{ $warehouse->city }}, {{ $warehouse->province }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('master.storage-areas.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Storage Area
            </a>
            <a href="{{ route('master.warehouses.show', $warehouse) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Warehouse
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        
        {{-- Total Areas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Areas</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $storageAreas->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        {{-- Active Areas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Areas</p>
                    <p class="text-3xl font-bold text-green-600">{{ $storageAreas->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        {{-- Total Capacity --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Capacity</p>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($storageAreas->sum('capacity_pallets')) }}</p>
                    <p class="text-xs text-gray-500">pallets</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pallet text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        {{-- Total Area --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Area</p>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($storageAreas->sum('area_sqm'), 2) }}</p>
                    <p class="text-xs text-gray-500">m²</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ruler-combined text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Storage Areas Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        @forelse($storageAreas as $area)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                
                {{-- Card Header --}}
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $area->name }}</h3>
                            <p class="text-sm font-mono text-gray-600">{{ $area->code }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-layer-group text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-6">
                    
                    {{-- Type Badge --}}
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $area->type_badge_color }}-100 text-{{ $area->type_badge_color }}-800">
                            {{ strtoupper(str_replace('_', ' ', $area->type)) }}
                        </span>
                    </div>

                    {{-- Specifications --}}
                    <div class="space-y-3 mb-4">
                        @if($area->area_sqm)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-ruler-combined text-gray-400 mr-2 w-4"></i>
                                    Area
                                </span>
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($area->area_sqm, 2) }} m²</span>
                            </div>
                        @endif

                        @if($area->height_meters)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-arrows-alt-v text-gray-400 mr-2 w-4"></i>
                                    Height
                                </span>
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($area->height_meters, 2) }} m</span>
                            </div>
                        @endif

                        @if($area->capacity_pallets)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-pallet text-gray-400 mr-2 w-4"></i>
                                    Capacity
                                </span>
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($area->capacity_pallets) }} pallets</span>
                            </div>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        @if($area->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                Inactive
                            </span>
                        @endif
                    </div>

                    {{-- Description --}}
                    @if($area->description)
                        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($area->description, 80) }}</p>
                    @endif

                </div>

                {{-- Card Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('master.storage-areas.show', $area) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('master.storage-areas.edit', $area) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('master.storage-areas.destroy', $area) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this storage area?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @if($area->storage_bins_count)
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-boxes mr-1"></i>{{ $area->storage_bins_count }} bins
                        </span>
                    @endif
                </div>

            </div>
        @empty
            <div class="col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-layer-group text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Storage Areas Found</h3>
                    <p class="text-gray-600 mb-4">This warehouse doesn't have any storage areas yet</p>
                    <a href="{{ route('master.storage-areas.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Add Storage Area
                    </a>
                </div>
            </div>
        @endforelse

    </div>

</div>
@endsection