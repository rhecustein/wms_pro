{{-- resources/views/master/storage-areas/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Storage Area')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                Edit Storage Area
            </h1>
            <p class="text-sm text-gray-600 mt-1">Update storage area information</p>
        </div>
        <a href="{{ route('master.storage-areas.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('master.storage-areas.update', $storageArea) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                
                {{-- Basic Information --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Warehouse --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('warehouse_id') border-red-500 @enderror" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $storageArea->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Code --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" value="{{ old('code', $storageArea->code) }}" placeholder="SPR-001" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror" required>
                            @error('code')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Unique code within the warehouse</p>
                        </div>

                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $storageArea->name) }}" placeholder="Storage Area Name" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('type') border-red-500 @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type', $storageArea->type) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Specifications --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-ruler-combined text-blue-600 mr-2"></i>
                        Specifications
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        {{-- Area --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Area (m²)
                            </label>
                            <input type="number" name="area_sqm" value="{{ old('area_sqm', $storageArea->area_sqm) }}" step="0.01" min="0" placeholder="0.00" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('area_sqm') border-red-500 @enderror">
                            @error('area_sqm')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Height --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Height (m)
                            </label>
                            <input type="number" name="height_meters" value="{{ old('height_meters', $storageArea->height_meters) }}" step="0.01" min="0" placeholder="0.00" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('height_meters') border-red-500 @enderror">
                            @error('height_meters')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Capacity --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Capacity (Pallets)
                            </label>
                            <input type="number" name="capacity_pallets" value="{{ old('capacity_pallets', $storageArea->capacity_pallets) }}" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('capacity_pallets') border-red-500 @enderror">
                            @error('capacity_pallets')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Additional Information --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                        Additional Information
                    </h3>
                    
                    {{-- Description --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" rows="4" placeholder="Enter description..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $storageArea->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $storageArea->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('master.storage-areas.index') }}" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Update Storage Area
                </button>
            </div>

        </form>
    </div>

</div>
@endsection