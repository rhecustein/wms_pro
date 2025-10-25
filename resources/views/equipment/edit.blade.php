{{-- resources/views/equipment/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Equipment')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-yellow-600 mr-2"></i>
                Edit Equipment
            </h1>
            <p class="text-sm text-gray-600 mt-1">Update equipment information and details</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('equipment.equipments.show', $equipment) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-eye mr-2"></i>View Details
            </a>
            <a href="{{ route('equipment.equipments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                <div class="flex-1">
                    <p class="font-semibold mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Edit Form --}}
    <form action="{{ route('equipment.equipments.update', $equipment) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Information --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Equipment Number Display --}}
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100 mb-1">Equipment Number</p>
                            <p class="text-2xl font-bold font-mono">{{ $equipment->equipment_number }}</p>
                        </div>
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tools text-3xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Equipment Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Equipment Type <span class="text-red-500">*</span>
                            </label>
                            <select name="equipment_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('equipment_type') border-red-500 @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('equipment_type', $equipment->equipment_type) === $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('equipment_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Warehouse --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('warehouse_id') border-red-500 @enderror" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $equipment->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Brand --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand', $equipment->brand) }}" placeholder="e.g., Toyota, Crown" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('brand') border-red-500 @enderror">
                            @error('brand')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Model --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                            <input type="text" name="model" value="{{ old('model', $equipment->model) }}" placeholder="e.g., 8FBN25, RC5500" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('model') border-red-500 @enderror">
                            @error('model')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Serial Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                            <input type="text" name="serial_number" value="{{ old('serial_number', $equipment->serial_number) }}" placeholder="e.g., SN123456789" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('serial_number') border-red-500 @enderror">
                            @error('serial_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror" required>
                                <option value="">Select Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ old('status', $equipment->status) === $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Maintenance Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-wrench text-yellow-600 mr-2"></i>
                        Maintenance Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Last Maintenance Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Maintenance Date</label>
                            <input type="date" name="last_maintenance_date" value="{{ old('last_maintenance_date', $equipment->last_maintenance_date?->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('last_maintenance_date') border-red-500 @enderror">
                            @error('last_maintenance_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Next Maintenance Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Next Maintenance Date</label>
                            <input type="date" name="next_maintenance_date" value="{{ old('next_maintenance_date', $equipment->next_maintenance_date?->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('next_maintenance_date') border-red-500 @enderror">
                            @error('next_maintenance_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Operating Hours --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Operating Hours</label>
                            <input type="number" name="operating_hours" value="{{ old('operating_hours', $equipment->operating_hours) }}" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('operating_hours') border-red-500 @enderror">
                            @error('operating_hours')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Additional Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-green-600 mr-2"></i>
                        Additional Notes
                    </h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="4" placeholder="Enter any additional information about this equipment..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $equipment->notes) }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Action Buttons --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Update Equipment
                        </button>
                        
                        <a href="{{ route('equipment.equipments.show', $equipment) }}" class="w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </div>

                {{-- Equipment Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Equipment Info</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-semibold">{{ $equipment->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-semibold">{{ $equipment->updated_at->format('d M Y') }}</span>
                        </div>
                        @if($equipment->createdBy)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created By:</span>
                            <span class="font-semibold">{{ $equipment->createdBy->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Status Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Current Status</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Equipment Status</p>
                            {!! $equipment->status_badge !!}
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Maintenance Status</p>
                            {!! $equipment->maintenance_status_badge !!}
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection