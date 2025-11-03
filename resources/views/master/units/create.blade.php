@extends('layouts.app')

@section('title', 'Create New Unit')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                Create New Unit
            </h1>
            <p class="text-sm text-gray-600 mt-1">Add a new measurement unit to the system</p>
        </div>
        <a href="{{ route('master.units.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('master.units.store') }}" method="POST">
            @csrf
            
            <div class="p-6 space-y-6">
                
                {{-- Basic Information --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Unit Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Unit Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                                   placeholder="e.g., Kilogram, Piece, Box" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Short Code --}}
                        <div>
                            <label for="short_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Short Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="short_code" id="short_code" value="{{ old('short_code') }}" 
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('short_code') border-red-500 @enderror" 
                                   placeholder="e.g., KG, PC, BX" maxlength="10" required>
                            @error('short_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Maximum 10 characters</p>
                        </div>

                        {{-- Type --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('type') border-red-500 @enderror" required>
                                <option value="">Select Type</option>
                                <option value="base" {{ old('type') === 'base' ? 'selected' : '' }}>Base</option>
                                <option value="weight" {{ old('type') === 'weight' ? 'selected' : '' }}>Weight</option>
                                <option value="volume" {{ old('type') === 'volume' ? 'selected' : '' }}>Volume</option>
                                <option value="length" {{ old('type') === 'length' ? 'selected' : '' }}>Length</option>
                                <option value="area" {{ old('type') === 'area' ? 'selected' : '' }}>Area</option>
                                <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Conversion Settings --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-exchange-alt text-green-600 mr-2"></i>
                        Conversion Settings
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Base Unit --}}
                        <div>
                            <label for="base_unit_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Base Unit
                            </label>
                            <select name="base_unit_id" id="base_unit_id" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('base_unit_id') border-red-500 @enderror">
                                <option value="">None (This is a base unit)</option>
                                @foreach($baseUnits as $baseUnit)
                                    <option value="{{ $baseUnit->id }}" {{ old('base_unit_id') == $baseUnit->id ? 'selected' : '' }}>
                                        {{ $baseUnit->name }} ({{ strtoupper($baseUnit->short_code) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('base_unit_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Select if this unit converts to another base unit</p>
                        </div>

                        {{-- Conversion Rate --}}
                        <div>
                            <label for="base_unit_conversion" class="block text-sm font-medium text-gray-700 mb-2">
                                Conversion Rate
                            </label>
                            <input type="number" name="base_unit_conversion" id="base_unit_conversion" 
                                   value="{{ old('base_unit_conversion', 1) }}" 
                                   step="0.0001" min="0"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('base_unit_conversion') border-red-500 @enderror" 
                                   placeholder="1.0000">
                            @error('base_unit_conversion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">How many base units equal 1 of this unit</p>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3" 
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror" 
                              placeholder="Additional notes or description about this unit...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('master.units.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Create Unit
                </button>
            </div>

        </form>
    </div>

</div>
@endsection