{{-- resources/views/inventory/pallets/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Pallet')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <a href="{{ route('inventory.pallets.show', $pallet) }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i>Back to Pallet Details
        </a>
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit text-blue-600 mr-2"></i>
            Edit Pallet: {{ $pallet->pallet_number }}
        </h1>
        <p class="text-sm text-gray-600 mt-1">Update pallet information</p>
    </div>

    <form action="{{ route('inventory.pallets.update', $pallet) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Information --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pallet Number</label>
                            <input type="text" value="{{ $pallet->pallet_number }}" disabled class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-600">
                            <p class="text-xs text-gray-500 mt-1">Cannot be changed</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pallet Type <span class="text-red-500">*</span></label>
                            <select name="pallet_type" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('pallet_type') border-red-500 @enderror">
                                <option value="standard" {{ old('pallet_type', $pallet->pallet_type) === 'standard' ? 'selected' : '' }}>Standard</option>
                                <option value="euro" {{ old('pallet_type', $pallet->pallet_type) === 'euro' ? 'selected' : '' }}>Euro</option>
                                <option value="custom" {{ old('pallet_type', $pallet->pallet_type) === 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            @error('pallet_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select name="status" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                                <option value="empty" {{ old('status', $pallet->status) === 'empty' ? 'selected' : '' }}>Empty</option>
                                <option value="loaded" {{ old('status', $pallet->status) === 'loaded' ? 'selected' : '' }}>Loaded</option>
                                <option value="in_transit" {{ old('status', $pallet->status) === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="damaged" {{ old('status', $pallet->status) === 'damaged' ? 'selected' : '' }}>Damaged</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Condition <span class="text-red-500">*</span></label>
                            <select name="condition" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('condition') border-red-500 @enderror">
                                <option value="good" {{ old('condition', $pallet->condition) === 'good' ? 'selected' : '' }}>Good</option>
                                <option value="fair" {{ old('condition', $pallet->condition) === 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ old('condition', $pallet->condition) === 'poor' ? 'selected' : '' }}>Poor</option>
                                <option value="damaged" {{ old('condition', $pallet->condition) === 'damaged' ? 'selected' : '' }}>Damaged</option>
                            </select>
                            @error('condition')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Storage Bin</label>
                            <select name="storage_bin_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('storage_bin_id') border-red-500 @enderror">
                                <option value="">Not Assigned</option>
                                @foreach($storageBins as $bin)
                                    <option value="{{ $bin->id }}" {{ old('storage_bin_id', $pallet->storage_bin_id) == $bin->id ? 'selected' : '' }}>
                                        {{ $bin->code }} - {{ $bin->warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('storage_bin_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Dimensions --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-ruler-combined text-blue-600 mr-2"></i>
                        Dimensions
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Width (cm) <span class="text-red-500">*</span></label>
                            <input type="number" name="width_cm" step="0.01" value="{{ old('width_cm', $pallet->width_cm) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('width_cm') border-red-500 @enderror">
                            @error('width_cm')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Depth (cm) <span class="text-red-500">*</span></label>
                            <input type="number" name="depth_cm" step="0.01" value="{{ old('depth_cm', $pallet->depth_cm) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('depth_cm') border-red-500 @enderror">
                            @error('depth_cm')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Height (cm) <span class="text-red-500">*</span></label>
                            <input type="number" name="height_cm" step="0.01" value="{{ old('height_cm', $pallet->height_cm) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('height_cm') border-red-500 @enderror">
                            @error('height_cm')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Weight Capacity --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-weight-hanging text-blue-600 mr-2"></i>
                        Weight Capacity
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Weight (kg) <span class="text-red-500">*</span></label>
                            <input type="number" name="max_weight_kg" step="0.01" value="{{ old('max_weight_kg', $pallet->max_weight_kg) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('max_weight_kg') border-red-500 @enderror">
                            @error('max_weight_kg')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Weight (kg) <span class="text-red-500">*</span></label>
                            <input type="number" name="current_weight_kg" step="0.01" value="{{ old('current_weight_kg', $pallet->current_weight_kg) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('current_weight_kg') border-red-500 @enderror">
                            @error('current_weight_kg')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Tracking Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-barcode text-blue-600 mr-2"></i>
                        Tracking
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $pallet->barcode) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('barcode') border-red-500 @enderror">
                            @error('barcode')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">QR Code</label>
                            <input type="text" name="qr_code" value="{{ old('qr_code', $pallet->qr_code) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('qr_code') border-red-500 @enderror">
                            @error('qr_code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Additional Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-blue-600 mr-2"></i>
                        Notes
                    </h2>

                    <textarea name="notes" rows="4" placeholder="Add any additional notes..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $pallet->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="space-y-2">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            <i class="fas fa-save mr-2"></i>Update Pallet
                        </button>
                        <a href="{{ route('inventory.pallets.show', $pallet) }}" class="block w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-medium">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection