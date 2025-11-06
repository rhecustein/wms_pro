{{-- resources/views/equipment/vehicles/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Vehicle')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-truck text-blue-600 mr-2"></i>
                Create New Vehicle
            </h1>
            <p class="text-sm text-gray-600 mt-1">Add a new vehicle to your fleet</p>
        </div>
        <a href="{{ route('equipment.vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('equipment.vehicles.store') }}" method="POST" id="vehicleForm" novalidate>
        @csrf
        
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
                            <label for="vehicle_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="vehicle_number"
                                name="vehicle_number" 
                                value="{{ old('vehicle_number', $vehicleNumber) }}" 
                                readonly 
                                class="w-full rounded-lg border-gray-300 bg-gray-50 cursor-not-allowed focus:border-blue-500 focus:ring-blue-500"
                            >
                            @error('vehicle_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- License Plate --}}
                        <div>
                            <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-2">
                                License Plate <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="license_plate"
                                name="license_plate" 
                                value="{{ old('license_plate') }}" 
                                required 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('license_plate') border-red-500 @enderror" 
                                placeholder="B 1234 XYZ"
                                maxlength="255"
                            >
                            @error('license_plate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Vehicle Type --}}
                        <div>
                            <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Type <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="vehicle_type"
                                name="vehicle_type" 
                                required 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('vehicle_type') border-red-500 @enderror"
                            >
                                <option value="">Select Type</option>
                                <option value="truck" {{ old('vehicle_type') === 'truck' ? 'selected' : '' }}>Truck</option>
                                <option value="van" {{ old('vehicle_type') === 'van' ? 'selected' : '' }}>Van</option>
                                <option value="forklift" {{ old('vehicle_type') === 'forklift' ? 'selected' : '' }}>Forklift</option>
                                <option value="reach_truck" {{ old('vehicle_type') === 'reach_truck' ? 'selected' : '' }}>Reach Truck</option>
                            </select>
                            @error('vehicle_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Brand --}}
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">
                                Brand
                            </label>
                            <input 
                                type="text" 
                                id="brand"
                                name="brand" 
                                value="{{ old('brand') }}" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('brand') border-red-500 @enderror" 
                                placeholder="Toyota, Mitsubishi, etc."
                                maxlength="255"
                            >
                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Model --}}
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                                Model
                            </label>
                            <input 
                                type="text" 
                                id="model"
                                name="model" 
                                value="{{ old('model') }}" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('model') border-red-500 @enderror" 
                                placeholder="Dyna, Canter, etc."
                                maxlength="255"
                            >
                            @error('model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Year --}}
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                                Year
                            </label>
                            <input 
                                type="number" 
                                id="year"
                                name="year" 
                                value="{{ old('year') }}" 
                                min="1900" 
                                max="{{ date('Y') + 1 }}" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('year') border-red-500 @enderror" 
                                placeholder="{{ date('Y') }}"
                            >
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
                            <label for="capacity_kg" class="block text-sm font-medium text-gray-700 mb-2">
                                Capacity (KG)
                            </label>
                            <input 
                                type="number" 
                                id="capacity_kg"
                                name="capacity_kg" 
                                value="{{ old('capacity_kg') }}" 
                                step="0.01" 
                                min="0" 
                                max="999999.99"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('capacity_kg') border-red-500 @enderror" 
                                placeholder="1000.00"
                            >
                            @error('capacity_kg')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Capacity CBM --}}
                        <div>
                            <label for="capacity_cbm" class="block text-sm font-medium text-gray-700 mb-2">
                                Capacity (m³)
                            </label>
                            <input 
                                type="number" 
                                id="capacity_cbm"
                                name="capacity_cbm" 
                                value="{{ old('capacity_cbm') }}" 
                                step="0.01" 
                                min="0" 
                                max="999999.99"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('capacity_cbm') border-red-500 @enderror" 
                                placeholder="10.50"
                            >
                            @error('capacity_cbm')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Fuel Type --}}
                        <div>
                            <label for="fuel_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Fuel Type
                            </label>
                            <input 
                                type="text" 
                                id="fuel_type"
                                name="fuel_type" 
                                value="{{ old('fuel_type') }}" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('fuel_type') border-red-500 @enderror" 
                                placeholder="Diesel, Petrol, Electric"
                                maxlength="255"
                            >
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
                            <label for="odometer_km" class="block text-sm font-medium text-gray-700 mb-2">
                                Odometer (KM)
                            </label>
                            <input 
                                type="number" 
                                id="odometer_km"
                                name="odometer_km" 
                                value="{{ old('odometer_km', 0) }}" 
                                min="0" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('odometer_km') border-red-500 @enderror" 
                                placeholder="0"
                            >
                            @error('odometer_km')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Maintenance Date --}}
                        <div>
                            <label for="last_maintenance_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Last Maintenance Date
                            </label>
                            <input 
                                type="date" 
                                id="last_maintenance_date"
                                name="last_maintenance_date" 
                                value="{{ old('last_maintenance_date') }}" 
                                max="{{ date('Y-m-d') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('last_maintenance_date') border-red-500 @enderror"
                            >
                            @error('last_maintenance_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Next Maintenance Date --}}
                        <div>
                            <label for="next_maintenance_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Next Maintenance Date
                            </label>
                            <input 
                                type="date" 
                                id="next_maintenance_date"
                                name="next_maintenance_date" 
                                value="{{ old('next_maintenance_date') }}" 
                                min="{{ date('Y-m-d') }}" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('next_maintenance_date') border-red-500 @enderror"
                            >
                            @error('next_maintenance_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500" id="dateValidationMessage"></p>
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
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea 
                            id="notes"
                            name="notes" 
                            rows="4" 
                            maxlength="5000"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror" 
                            placeholder="Additional information about the vehicle..."
                        >{{ old('notes') }}</textarea>
                        <div class="flex justify-between mt-1">
                            @error('notes')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="text-xs text-gray-500">Maximum 5000 characters</p>
                            @enderror
                            <p class="text-xs text-gray-500"><span id="notesCount">0</span>/5000</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Status Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-cog text-gray-600 mr-2"></i>
                        Status & Ownership
                    </h3>
                    
                    <div class="space-y-4">
                        {{-- Status --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="status"
                                name="status" 
                                required 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                            >
                                <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="in_use" {{ old('status') === 'in_use' ? 'selected' : '' }}>In Use</option>
                                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ownership --}}
                        <div>
                            <label for="ownership" class="block text-sm font-medium text-gray-700 mb-2">
                                Ownership <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="ownership"
                                name="ownership" 
                                required 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('ownership') border-red-500 @enderror"
                            >
                                <option value="owned" {{ old('ownership', 'owned') === 'owned' ? 'selected' : '' }}>Owned</option>
                                <option value="rented" {{ old('ownership') === 'rented' ? 'selected' : '' }}>Rented</option>
                                <option value="leased" {{ old('ownership') === 'leased' ? 'selected' : '' }}>Leased</option>
                            </select>
                            @error('ownership')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        {{-- Quick Info --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h4 class="text-sm font-semibold text-blue-800 mb-2 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Quick Tips
                            </h4>
                            <ul class="text-xs text-blue-700 space-y-1.5">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600 flex-shrink-0"></i>
                                    <span>Vehicle number is auto-generated</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600 flex-shrink-0"></i>
                                    <span>License plate must be unique</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600 flex-shrink-0"></i>
                                    <span>Set maintenance dates for reminders</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="space-y-3">
                            <button 
                                type="submit" 
                                class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submitBtn"
                            >
                                <i class="fas fa-save mr-2"></i>Create Vehicle
                            </button>
                            <a 
                                href="{{ route('equipment.vehicles.index') }}" 
                                class="block w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold"
                            >
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notes character counter
    const notesTextarea = document.getElementById('notes');
    const notesCount = document.getElementById('notesCount');
    
    if (notesTextarea && notesCount) {
        notesTextarea.addEventListener('input', function() {
            notesCount.textContent = this.value.length;
        });
        notesCount.textContent = notesTextarea.value.length;
    }

    // Date validation
    const lastMaintenanceDate = document.getElementById('last_maintenance_date');
    const nextMaintenanceDate = document.getElementById('next_maintenance_date');
    const dateValidationMessage = document.getElementById('dateValidationMessage');

    function validateDates() {
        if (lastMaintenanceDate.value && nextMaintenanceDate.value) {
            const lastDate = new Date(lastMaintenanceDate.value);
            const nextDate = new Date(nextMaintenanceDate.value);
            
            if (nextDate <= lastDate) {
                dateValidationMessage.textContent = 'Next maintenance must be after last maintenance';
                dateValidationMessage.classList.add('text-red-500');
                dateValidationMessage.classList.remove('text-gray-500');
                return false;
            } else {
                dateValidationMessage.textContent = 'Dates are valid';
                dateValidationMessage.classList.add('text-green-500');
                dateValidationMessage.classList.remove('text-red-500', 'text-gray-500');
                return true;
            }
        }
        dateValidationMessage.textContent = '';
        return true;
    }

    if (lastMaintenanceDate && nextMaintenanceDate) {
        lastMaintenanceDate.addEventListener('change', validateDates);
        nextMaintenanceDate.addEventListener('change', validateDates);
    }

    // Form validation before submit
    const form = document.getElementById('vehicleForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateDates()) {
                e.preventDefault();
                alert('Please fix the maintenance date issue before submitting.');
                return false;
            }
            
            // Disable submit button to prevent double submission
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
            }
        });
    }

    // Auto-format license plate to uppercase
    const licensePlate = document.getElementById('license_plate');
    if (licensePlate) {
        licensePlate.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
});
</script>
@endpush
@endsection