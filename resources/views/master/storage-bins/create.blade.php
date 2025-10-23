{{-- resources/views/master/storage-bins/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Storage Bin')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus text-blue-600 mr-2"></i>
                Create New Storage Bin
            </h1>
            <p class="text-sm text-gray-600 mt-1">Add a new storage bin to your warehouse</p>
        </div>
        <a href="{{ route('master.storage-bins.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <form action="{{ route('master.storage-bins.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Information --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Warehouse --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('warehouse_id') border-red-500 @enderror">
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Storage Area --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Storage Area</label>
                            <select name="storage_area_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('storage_area_id') border-red-500 @enderror">
                                <option value="">Select Storage Area (Optional)</option>
                                @foreach($storageAreas as $area)
                                    <option value="{{ $area->id }}" {{ old('storage_area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('storage_area_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Aisle --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Aisle <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="aisle" value="{{ old('aisle') }}" required maxlength="10" placeholder="AA" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('aisle') border-red-500 @enderror">
                            @error('aisle')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Row --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Row <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="row" value="{{ old('row') }}" required maxlength="10" placeholder="01" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('row') border-red-500 @enderror">
                            @error('row')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Column --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Column <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="column" value="{{ old('column') }}" required maxlength="10" placeholder="01" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('column') border-red-500 @enderror">
                            @error('column')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Level --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Level <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="level" value="{{ old('level') }}" required maxlength="10" placeholder="A" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('level') border-red-500 @enderror">
                            @error('level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Generated Code Preview --}}
                        <div class="md:col-span-2 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-sm text-gray-600 mb-2">Generated Bin Code:</p>
                            <p class="text-xl font-mono font-bold text-blue-600" id="codePreview">-</p>
                            <p class="text-xs text-gray-500 mt-1">Code will be auto-generated based on: Aisle + Row + Column + Level</p>
                        </div>
                    </div>
                </div>

                {{-- Capacity & Configuration --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-cogs text-purple-600 mr-2"></i>
                        Capacity & Configuration
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                                <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ old('status') === 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="reserved" {{ old('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                                <option value="blocked" {{ old('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Bin Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Bin Type <span class="text-red-500">*</span>
                            </label>
                            <select name="bin_type" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('bin_type') border-red-500 @enderror">
                                <option value="high_rack" {{ old('bin_type') === 'high_rack' ? 'selected' : '' }}>High Rack</option>
                                <option value="pick_face" {{ old('bin_type') === 'pick_face' ? 'selected' : '' }}>Pick Face</option>
                                <option value="staging" {{ old('bin_type') === 'staging' ? 'selected' : '' }}>Staging</option>
                                <option value="quarantine" {{ old('bin_type') === 'quarantine' ? 'selected' : '' }}>Quarantine</option>
                            </select>
                            @error('bin_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Max Weight --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Weight (kg)</label>
                            <input type="number" step="0.01" name="max_weight_kg" value="{{ old('max_weight_kg') }}" placeholder="1000.00" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('max_weight_kg') border-red-500 @enderror">
                            @error('max_weight_kg')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Max Volume --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Volume (m³)</label>
                            <input type="number" step="0.01" name="max_volume_cbm" value="{{ old('max_volume_cbm') }}" placeholder="2.50" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('max_volume_cbm') border-red-500 @enderror">
                            @error('max_volume_cbm')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Packaging Restriction --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Packaging Restriction</label>
                            <select name="packaging_restriction" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('packaging_restriction') border-red-500 @enderror">
                                <option value="">None</option>
                                <option value="drum" {{ old('packaging_restriction') === 'drum' ? 'selected' : '' }}>Drum Only</option>
                                <option value="carton" {{ old('packaging_restriction') === 'carton' ? 'selected' : '' }}>Carton Only</option>
                                <option value="pallet" {{ old('packaging_restriction') === 'pallet' ? 'selected' : '' }}>Pallet Only</option>
                            </select>
                            @error('packaging_restriction')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Customer --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dedicated Customer</label>
                            <select name="customer_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('customer_id') border-red-500 @enderror">
                                <option value="">No Dedicated Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3" placeholder="Additional notes about this bin..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                
                {{-- Status Flags --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-flag text-orange-600 mr-2"></i>
                        Status Flags
                    </h2>

                    <div class="space-y-4">
                        {{-- Is Active --}}
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <label class="font-medium text-gray-700">Active Status</label>
                                <p class="text-xs text-gray-500">Enable this storage bin</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        {{-- Is Hazmat --}}
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                            <div>
                                <label class="font-medium text-red-700">Hazmat Storage</label>
                                <p class="text-xs text-red-600">For hazardous materials</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_hazmat" value="1" class="sr-only peer" {{ old('is_hazmat') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Create Storage Bin
                        </button>
                        <a href="{{ route('master.storage-bins.index') }}" class="w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </div>

                {{-- Help Card --}}
                <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
                    <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Quick Guide
                    </h3>
                    <ul class="space-y-2 text-sm text-blue-800">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600"></i>
                            <span>Bin code will be auto-generated from location</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600"></i>
                            <span>Use Generate Bins for bulk creation</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600"></i>
                            <span>Set capacity limits to prevent overloading</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600"></i>
                            <span>Mark hazmat bins for safety compliance</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </form>

</div>

<script>
// Auto-generate code preview
document.addEventListener('DOMContentLoaded', function() {
    const aisleInput = document.querySelector('input[name="aisle"]');
    const rowInput = document.querySelector('input[name="row"]');
    const columnInput = document.querySelector('input[name="column"]');
    const levelInput = document.querySelector('input[name="level"]');
    const codePreview = document.getElementById('codePreview');

    function updateCodePreview() {
        const aisle = (aisleInput.value || '').toUpperCase();
        const row = (rowInput.value || '').padStart(2, '0');
        const column = (columnInput.value || '').padStart(2, '0');
        const level = (levelInput.value || '').toUpperCase();
        
        if (aisle && row && column && level) {
            codePreview.textContent = aisle + row + column + level;
        } else {
            codePreview.textContent = '-';
        }
    }

    [aisleInput, rowInput, columnInput, levelInput].forEach(input => {
        input.addEventListener('input', updateCodePreview);
    });
});
</script>

@endsection