{{-- resources/views/master/storage-areas/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Storage Area Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                Storage Area Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View detailed information about this storage area</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('master.storage-areas.edit', $storageArea) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('master.storage-areas.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Information --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Basic Information Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Code --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Code</label>
                            <p class="text-base font-mono font-semibold text-gray-900">{{ $storageArea->code }}</p>
                        </div>

                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                            <p class="text-base font-semibold text-gray-900">{{ $storageArea->name }}</p>
                        </div>

                        {{-- Warehouse --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Warehouse</label>
                            <p class="text-base text-gray-900 flex items-center">
                                <i class="fas fa-warehouse text-gray-400 mr-2"></i>
                                {{ $storageArea->warehouse->name }}
                            </p>
                        </div>

                        {{-- Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $storageArea->type_badge_color }}-100 text-{{ $storageArea->type_badge_color }}-800">
                                {{ $storageArea->type_name }}
                            </span>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            @if($storageArea->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>

                    </div>

                    {{-- Description --}}
                    @if($storageArea->description)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
                            <p class="text-base text-gray-700">{{ $storageArea->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Specifications Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-ruler-combined text-blue-600 mr-2"></i>
                        Specifications
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        {{-- Area --}}
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-ruler-combined text-blue-600"></i>
                                </div>
                                <label class="text-sm font-medium text-gray-600">Area</label>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $storageArea->area_sqm ? number_format($storageArea->area_sqm, 2) : '-' }}
                            </p>
                            @if($storageArea->area_sqm)
                                <p class="text-sm text-gray-600">square meters</p>
                            @endif
                        </div>

                        {{-- Height --}}
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-arrows-alt-v text-purple-600"></i>
                                </div>
                                <label class="text-sm font-medium text-gray-600">Height</label>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $storageArea->height_meters ? number_format($storageArea->height_meters, 2) : '-' }}
                            </p>
                            @if($storageArea->height_meters)
                                <p class="text-sm text-gray-600">meters</p>
                            @endif
                        </div>

                        {{-- Capacity --}}
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-pallet text-green-600"></i>
                                </div>
                                <label class="text-sm font-medium text-gray-600">Capacity</label>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $storageArea->capacity_pallets ? number_format($storageArea->capacity_pallets) : '-' }}
                            </p>
                            @if($storageArea->capacity_pallets)
                                <p class="text-sm text-gray-600">pallets</p>
                            @endif
                        </div>

                    </div>

                    {{-- Calculated Volume --}}
                    @if($storageArea->area_sqm && $storageArea->height_meters)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">Total Volume</span>
                                <span class="text-lg font-bold text-gray-900">
                                    {{ number_format($storageArea->area_sqm * $storageArea->height_meters, 2) }} m³
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Storage Bins Card --}}
            @if($storageArea->storageBins && $storageArea->storageBins->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-boxes text-blue-600 mr-2"></i>
                            Storage Bins
                        </h3>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                            {{ $storageArea->storageBins->count() }} bins
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($storageArea->storageBins->take(12) as $bin)
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <i class="fas fa-box text-gray-400 mb-2"></i>
                                    <p class="text-sm font-mono font-semibold text-gray-900">{{ $bin->code }}</p>
                                </div>
                            @endforeach
                        </div>
                        @if($storageArea->storageBins->count() > 12)
                            <p class="text-sm text-gray-500 text-center mt-4">
                                And {{ $storageArea->storageBins->count() - 12 }} more bins...
                            </p>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Quick Stats --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                        Quick Stats
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    
                    {{-- Total Bins --}}
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Bins</span>
                        <span class="text-lg font-bold text-gray-900">
                            {{ $storageArea->storageBins ? $storageArea->storageBins->count() : 0 }}
                        </span>
                    </div>

                    <div class="border-t border-gray-200"></div>

                    {{-- Active Status --}}
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        @if($storageArea->is_active)
                            <span class="text-sm font-semibold text-green-600">Active</span>
                        @else
                            <span class="text-sm font-semibold text-red-600">Inactive</span>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Metadata Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-info text-blue-600 mr-2"></i>
                        Metadata
                    </h3>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    
                    {{-- Created By --}}
                    @if($storageArea->createdBy)
                        <div>
                            <label class="block text-gray-500 mb-1">Created By</label>
                            <p class="text-gray-900">{{ $storageArea->createdBy->name }}</p>
                            <p class="text-xs text-gray-500">{{ $storageArea->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    @endif

                    {{-- Updated By --}}
                    @if($storageArea->updatedBy && $storageArea->updated_at != $storageArea->created_at)
                        <div class="pt-4 border-t border-gray-200">
                            <label class="block text-gray-500 mb-1">Last Updated By</label>
                            <p class="text-gray-900">{{ $storageArea->updatedBy->name }}</p>
                            <p class="text-xs text-gray-500">{{ $storageArea->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Actions Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-cog text-blue-600 mr-2"></i>
                        Actions
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    
                    <a href="{{ route('master.storage-areas.edit', $storageArea) }}" class="block w-full px-4 py-2 bg-yellow-600 text-white text-center rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-edit mr-2"></i>Edit Storage Area
                    </a>

                    <form action="{{ route('master.storage-areas.destroy', $storageArea) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this storage area?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="block w-full px-4 py-2 bg-red-600 text-white text-center rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i>Delete Storage Area
                        </button>
                    </form>

                </div>
            </div>

        </div>

    </div>

</div>
@endsection