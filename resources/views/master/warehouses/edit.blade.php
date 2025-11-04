@extends('layouts.app')

@section('title', 'Edit Warehouse - ' . $warehouse->name)

@section('content')
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-edit text-blue-600 mr-3"></i>Edit Warehouse
            </h1>
            <p class="text-gray-600 mt-1">Update warehouse information - {{ $warehouse->code }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('master.warehouses.show', $warehouse) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-eye mr-2"></i>View Details
            </a>
            <a href="{{ route('master.warehouses.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- ALERT INFO --}}
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Note:</strong> Changes to warehouse information will be logged. Make sure all information is accurate before saving.
                </p>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('master.warehouses.update', $warehouse) }}" method="POST" class="space-y-6" id="editWarehouseForm">
        @csrf
        @method('PUT')

        {{-- Basic Information --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-info-circle mr-2"></i>Basic Information
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Code --}}
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Warehouse Code <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="code" 
                            id="code" 
                            value="{{ old('code', $warehouse->code) }}" 
                            required 
                            placeholder="e.g., WH001, JKT01"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror"
                        >
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-lightbulb mr-1"></i>Unique identifier for this warehouse
                        </p>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Warehouse Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $warehouse->name) }}" 
                            required 
                            placeholder="e.g., Central Warehouse Jakarta"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-lightbulb mr-1"></i>Full warehouse name for easy identification
                        </p>
                    </div>

                    {{-- Manager --}}
                    <div>
                        <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tie text-purple-500 mr-1"></i>Warehouse Manager
                        </label>
                        <select 
                            name="manager_id" 
                            id="manager_id" 
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('manager_id') border-red-500 @enderror"
                        >
                            <option value="">-- Select Manager (Optional) --</option>
                            @foreach($managers as $manager)
                                <option 
                                    value="{{ $manager->id }}" 
                                    {{ old('manager_id', $warehouse->manager_id) == $manager->id ? 'selected' : '' }}
                                >
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('manager_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-lightbulb mr-1"></i>Person responsible for warehouse operations
                        </p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-toggle-on text-green-500 mr-1"></i>Warehouse Status
                        </label>
                        <div class="flex items-center space-x-4 mt-3">
                            <label class="inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="is_active" 
                                    value="1" 
                                    {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }} 
                                    class="sr-only peer"
                                    id="is_active_toggle"
                                >
                                <div class="relative w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900" id="status_label">
                                    {{ old('is_active', $warehouse->is_active) ? 'Active' : 'Inactive' }}
                                </span>
                            </label>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-lightbulb mr-1"></i>Active warehouses are available for operations
                        </p>
                    </div>
                </div>

                {{-- Current Status Badge --}}
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-gray-400 text-xl mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Last Updated</p>
                                <p class="text-xs text-gray-500">{{ $warehouse->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user text-gray-400 text-xl mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Updated By</p>
                                <p class="text-xs text-gray-500">{{ $warehouse->updater->name ?? $warehouse->creator->name ?? 'System' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Location Information --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-map-marker-alt mr-2"></i>Location Information
                </h3>
            </div>
            <div class="p-6 space-y-6">
                {{-- Address --}}
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map text-blue-500 mr-1"></i>Full Address
                    </label>
                    <textarea 
                        name="address" 
                        id="address" 
                        rows="3" 
                        placeholder="Enter complete warehouse address..."
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('address') border-red-500 @enderror"
                    >{{ old('address', $warehouse->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-lightbulb mr-1"></i>Street address, building name, or landmarks
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- City --}}
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-city text-indigo-500 mr-1"></i>City
                        </label>
                        <input 
                            type="text" 
                            name="city" 
                            id="city" 
                            value="{{ old('city', $warehouse->city) }}" 
                            placeholder="e.g., Jakarta"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('city') border-red-500 @enderror"
                        >
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Province --}}
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag text-yellow-500 mr-1"></i>Province
                        </label>
                        <input 
                            type="text" 
                            name="province" 
                            id="province" 
                            value="{{ old('province', $warehouse->province) }}" 
                            placeholder="e.g., DKI Jakarta"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('province') border-red-500 @enderror"
                        >
                        @error('province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Postal Code --}}
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-red-500 mr-1"></i>Postal Code
                        </label>
                        <input 
                            type="text" 
                            name="postal_code" 
                            id="postal_code" 
                            value="{{ old('postal_code', $warehouse->postal_code) }}" 
                            placeholder="e.g., 12345"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('postal_code') border-red-500 @enderror"
                        >
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Country --}}
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-globe text-green-500 mr-1"></i>Country
                        </label>
                        <input 
                            type="text" 
                            name="country" 
                            id="country" 
                            value="{{ old('country', $warehouse->country) }}" 
                            placeholder="Indonesia"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('country') border-red-500 @enderror"
                        >
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Latitude --}}
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-pin text-purple-500 mr-1"></i>Latitude
                        </label>
                        <input 
                            type="number" 
                            step="0.00000001" 
                            name="latitude" 
                            id="latitude" 
                            value="{{ old('latitude', $warehouse->latitude) }}" 
                            placeholder="-6.2088"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('latitude') border-red-500 @enderror"
                        >
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Longitude --}}
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-pin text-orange-500 mr-1"></i>Longitude
                        </label>
                        <input 
                            type="number" 
                            step="0.00000001" 
                            name="longitude" 
                            id="longitude" 
                            value="{{ old('longitude', $warehouse->longitude) }}" 
                            placeholder="106.8456"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('longitude') border-red-500 @enderror"
                        >
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Map Preview --}}
                @if($warehouse->latitude && $warehouse->longitude)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-location-arrow text-blue-500 text-xl"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-blue-800">Current Coordinates</p>
                            <p class="text-xs text-blue-600 mt-1">
                                Lat: {{ $warehouse->latitude }}, Long: {{ $warehouse->longitude }}
                            </p>
                            <a 
                                href="https://www.google.com/maps?q={{ $warehouse->latitude }},{{ $warehouse->longitude }}" 
                                target="_blank" 
                                class="text-xs text-blue-700 hover:text-blue-900 font-medium mt-2 inline-block"
                            >
                                <i class="fas fa-external-link-alt mr-1"></i>View on Google Maps
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Contact & Specifications --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-700 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-phone mr-2"></i>Contact & Specifications
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone-alt text-green-500 mr-1"></i>Phone Number
                        </label>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone', $warehouse->phone) }}" 
                            placeholder="+62 21 1234567"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-500 @enderror"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-lightbulb mr-1"></i>Main contact number for warehouse
                        </p>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-blue-500 mr-1"></i>Email Address
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email', $warehouse->email) }}" 
                            placeholder="warehouse@company.com"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-lightbulb mr-1"></i>Email for warehouse communications
                        </p>
                    </div>

                    {{-- Total Area --}}
                    <div>
                        <label for="total_area_sqm" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-expand-arrows-alt text-yellow-500 mr-1"></i>Total Area (m²)
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                step="0.01" 
                                name="total_area_sqm" 
                                id="total_area_sqm" 
                                value="{{ old('total_area_sqm', $warehouse->total_area_sqm) }}" 
                                placeholder="1000.00"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-12 @error('total_area_sqm') border-red-500 @enderror"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">m²</span>
                            </div>
                        </div>
                        @error('total_area_sqm')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-lightbulb mr-1"></i>Total warehouse floor area in square meters
                        </p>
                    </div>

                    {{-- Height --}}
                    <div>
                        <label for="height_meters" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-arrows-alt-v text-indigo-500 mr-1"></i>Height (meters)
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                step="0.01" 
                                name="height_meters" 
                                id="height_meters" 
                                value="{{ old('height_meters', $warehouse->height_meters) }}" 
                                placeholder="8.00"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-12 @error('height_meters') border-red-500 @enderror"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">m</span>
                            </div>
                        </div>
                        @error('height_meters')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-lightbulb mr-1"></i>Maximum ceiling height for storage capacity
                        </p>
                    </div>
                </div>

                {{-- Capacity Overview --}}
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-5">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-calculator text-purple-600 mr-2"></i>Capacity Overview
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Current Area</p>
                            <p class="text-lg font-bold text-purple-700" id="display_area">
                                {{ number_format($warehouse->total_area_sqm ?? 0, 0) }} m²
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Current Height</p>
                            <p class="text-lg font-bold text-blue-700" id="display_height">
                                {{ number_format($warehouse->height_meters ?? 0, 1) }} m
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Volume</p>
                            <p class="text-lg font-bold text-green-700" id="display_volume">
                                {{ number_format(($warehouse->total_area_sqm ?? 0) * ($warehouse->height_meters ?? 0), 0) }} m³
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Storage Bins</p>
                            <p class="text-lg font-bold text-orange-700">{{ $warehouse->storageBins()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Validation Errors Summary --}}
        @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        There were {{ $errors->count() }} error(s) with your submission
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- SUBMIT BUTTONS --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    <span>All changes will be logged in activity history</span>
                </div>
                <div class="flex space-x-3 w-full sm:w-auto">
                    <a 
                        href="{{ route('master.warehouses.show', $warehouse) }}" 
                        class="flex-1 sm:flex-none bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-center"
                    >
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="flex-1 sm:flex-none bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
                    >
                        <i class="fas fa-save mr-2"></i>Update Warehouse
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Toggle Active Status Label
    document.getElementById('is_active_toggle')?.addEventListener('change', function() {
        const label = document.getElementById('status_label');
        if (this.checked) {
            label.textContent = 'Active';
        } else {
            label.textContent = 'Inactive';
        }
    });

    // Auto-calculate volume when area or height changes
    const areaInput = document.getElementById('total_area_sqm');
    const heightInput = document.getElementById('height_meters');

    function updateCapacityDisplay() {
        const area = parseFloat(areaInput?.value || 0);
        const height = parseFloat(heightInput?.value || 0);
        const volume = area * height;
        
        document.getElementById('display_area').textContent = area.toFixed(2) + ' m²';
        document.getElementById('display_height').textContent = height.toFixed(2) + ' m';
        document.getElementById('display_volume').textContent = volume.toFixed(2) + ' m³';
    }

    areaInput?.addEventListener('input', updateCapacityDisplay);
    heightInput?.addEventListener('input', updateCapacityDisplay);

    // Form validation before submit
    document.getElementById('editWarehouseForm')?.addEventListener('submit', function(e) {
        const code = document.getElementById('code').value.trim();
        const name = document.getElementById('name').value.trim();

        if (!code || !name) {
            e.preventDefault();
            alert('Please fill in all required fields (Code and Name)');
            return false;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating Warehouse...';
    });

    // Prevent double submission
    let submitted = false;
    document.getElementById('editWarehouseForm')?.addEventListener('submit', function(e) {
        if (submitted) {
            e.preventDefault();
            return false;
        }
        submitted = true;
    });

    // Coordinate validation
    document.getElementById('latitude')?.addEventListener('input', function(e) {
        const value = parseFloat(e.target.value);
        if (value && (value < -90 || value > 90)) {
            this.classList.add('border-red-500');
        } else {
            this.classList.remove('border-red-500');
        }
    });

    document.getElementById('longitude')?.addEventListener('input', function(e) {
        const value = parseFloat(e.target.value);
        if (value && (value < -180 || value > 180)) {
            this.classList.add('border-red-500');
        } else {
            this.classList.remove('border-red-500');
        }
    });

    // Auto-uppercase warehouse code
    document.getElementById('code')?.addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush

@push('styles')
<style>
    input:focus, select:focus, textarea:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    input, select, textarea, button {
        transition: all 0.2s ease-in-out;
    }

    button[type="submit"]:hover {
        transform: translateY(-1px);
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .fa-spinner {
        animation: spin 1s linear infinite;
    }
</style>
@endpush