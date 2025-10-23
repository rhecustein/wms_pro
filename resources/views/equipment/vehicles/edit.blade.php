{{-- resources/views/equipment/vehicles/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-blue-600 mr-2"></i>
                Edit Vehicle
            </h1>
            <p class="text-sm text-gray-600 mt-1">Update vehicle information: {{ $vehicle->vehicle_number }}</p>
        </div>
        <a href="{{ route('equipment.vehicles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('equipment.vehicles.update', $vehicle) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Information --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Vehicle Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="vehicle_number" value="{{ old('vehicle_number', $vehicle->vehicle_number) }}" readonly class="w-full rounded-lg border-gray-300 bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                            @error('vehicle_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- License Plate --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                License Plate <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="license_plate" value="{{ old('license_plate', $vehicle->license_plate) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="B 1234 XYZ">
                            @error('license_plate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Vehicle Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Type <span class="text-red-500">*</span>
                            </label>
                            <select name="vehicle_type" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Type</option>
                                <option value="truck" {{ old('vehicle_type', $vehicle->vehicle_type) === 'truck' ? 'selected' : '' }}>Truck</option>
                                <option value="van" {{ old('vehicle_type', $vehicle->vehicle_type) === 'van' ? 'selected' : '' }}>Van</option>
                                <option value="forklift" {{ old('vehicle_type', $vehicle->vehicle_type) === 'forklift' ? 'selected' : '' }}>Forklift</option>
                                <option value="reach_truck" {{ old('vehicle_type', $vehicle->vehicle_type) === 'reach_truck' ? 'selected' : '' }}>Reach Truck</option>
                            </select>
                            @error('vehicle_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Brand --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Brand
                            </label>
                            <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Toyota, Mitsubishi, etc.">
                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Model --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Model
                            </label>
                            <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Dyna, Canter, etc.">
                            @error('model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Year --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Year
                            </label>
                            <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') + 1 }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="2024">
                            @error('year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Capacity Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-weight text-purple-600 mr-2"></i>
                        Capacity Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Capacity KG --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Capacity (KG)
                            </label>
                            <input type="number" name="capacity_kg" value="{{ old('capacity_kg', $vehicle->capacity_kg) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="1000">
                            @error('capacity_kg')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Capacity CBM --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Capacity (m³)
                            </label>
                            <input type="number" name="capacity_cbm" value="{{ old('capacity_cbm', $vehicle->capacity_cbm) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="10.5">
                            @error('capacity_cbm')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Fuel Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Fuel Type
                            </label>
                            <input type="text" name="fuel_type" value="{{ old('fuel_type', $vehicle->fuel_type) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Diesel, Petrol, Electric">
                            @error('fuel_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Maintenance Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tools text-yellow-600 mr-2"></i>
                        Maintenance Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Odometer --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Odometer (KM)
                            </label>
                            <input type="number" name="odometer_km" value="{{ old('odometer_km', $vehicle->odometer_km) }}" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="0">
                            @error('odometer_km')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Maintenance Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Last Maintenance Date
                            </label>
                            <input type="date" name="last_maintenance_date" value="{{ old('last_maintenance_date', $vehicle->last_maintenance_date?->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('last_maintenance_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Next Maintenance Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Next Maintenance Date
                            </label>
                            <input type="date" name="next_maintenance_date" value="{{ old('next_maintenance_date', $vehicle->next_maintenance_date?->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('next_maintenance_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-green-600 mr-2"></i>
                        Additional Notes
                    </h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Additional information about the vehicle...">{{ old('notes', $vehicle->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Status Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-cog text-gray-600 mr-2"></i>
                        Status & Ownership
                    </h3>
                    
                    <div class="space-y-4">
                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="available" {{ old('status', $vehicle->status) === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="in_use" {{ old('status', $vehicle->status) === 'in_use' ? 'selected' : '' }}>In Use</option>
                                <option value="maintenance" {{ old('status', $vehicle->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="inactive" {{ old('status', $vehicle->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ownership --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ownership <span class="text-red-500">*</span>
                            </label>
                            <select name="ownership" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="owned" {{ old('ownership', $vehicle->ownership) === 'owned' ? 'selected' : '' }}>Owned</option>
                                <option value="rented" {{ old('ownership', $vehicle->ownership) === 'rented' ? 'selected' : '' }}>Rented</option>
                                <option value="leased" {{ old('ownership', $vehicle->ownership) === 'leased' ? 'selected' : '' }}>Leased</option>
                            </select>
                            @error('ownership')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Audit Information --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Audit Information</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Created:</span>
                            <span class="font-semibold">{{ $vehicle->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($vehicle->creator)
                        <div class="flex justify-between">
                            <span>Created by:</span>
                            <span class="font-semibold">{{ $vehicle->creator->name }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span>Updated:</span>
                            <span class="font-semibold">{{ $vehicle->updated_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($vehicle->updater)
                        <div class="flex justify-between">
                            <span>Updated by:</span>
                            <span class="font-semibold">{{ $vehicle->updater->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Update Vehicle
                        </button>
                        <a href="{{ route('equipment.vehicles.show', $vehicle) }}" class="block w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </a>
                        <a href="{{ route('equipment.vehicles.index') }}" class="block w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>
@endsection