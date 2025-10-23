{{-- resources/views/master/vendors/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Vendor')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-truck text-blue-600 mr-2"></i>
                Create New Vendor
            </h1>
            <p class="text-sm text-gray-600 mt-1">Add a new vendor to the system</p>
        </div>
        <a href="{{ route('master.vendors.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong>There were some errors with your submission:</strong>
            </div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('master.vendors.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Information --}}
            <div class="lg:col-span-2">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Code --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vendor Code <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="code" 
                                value="{{ old('code') }}" 
                                placeholder="VEND001" 
                                required 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror"
                            >
                            @error('code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Unique vendor identification code</p>
                        </div>

                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vendor Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}" 
                                placeholder="Enter vendor name"
                                required 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                            >
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Company Name --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Company Name
                            </label>
                            <input 
                                type="text" 
                                name="company_name" 
                                value="{{ old('company_name') }}" 
                                placeholder="Enter company name"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('company_name') border-red-500 @enderror"
                            >
                            @error('company_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Official registered company name</p>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    placeholder="vendor@example.com"
                                    class="w-full pl-10 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                                >
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    name="phone" 
                                    value="{{ old('phone') }}" 
                                    placeholder="+62 xxx xxxx xxxx"
                                    class="w-full pl-10 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-500 @enderror"
                                >
                            </div>
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Address Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>Address Information
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        {{-- Address --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Street Address
                            </label>
                            <textarea 
                                name="address" 
                                rows="3" 
                                placeholder="Enter complete street address"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('address') border-red-500 @enderror"
                            >{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- City --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    City
                                </label>
                                <input 
                                    type="text" 
                                    name="city" 
                                    value="{{ old('city') }}" 
                                    placeholder="Enter city"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('city') border-red-500 @enderror"
                                >
                                @error('city')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Province --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Province / State
                                </label>
                                <input 
                                    type="text" 
                                    name="province" 
                                    value="{{ old('province') }}" 
                                    placeholder="Enter province"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('province') border-red-500 @enderror"
                                >
                                @error('province')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Postal Code --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Postal Code
                                </label>
                                <input 
                                    type="text" 
                                    name="postal_code" 
                                    value="{{ old('postal_code') }}" 
                                    placeholder="Enter postal code"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('postal_code') border-red-500 @enderror"
                                >
                                @error('postal_code')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Country --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="country" 
                                    value="{{ old('country', 'Indonesia') }}" 
                                    required 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('country') border-red-500 @enderror"
                                >
                                @error('country')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Person --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user text-blue-600 mr-2"></i>Contact Person Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Contact Person --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Person Name
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user-tie text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    name="contact_person" 
                                    value="{{ old('contact_person') }}" 
                                    placeholder="Enter contact person name"
                                    class="w-full pl-10 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('contact_person') border-red-500 @enderror"
                                >
                            </div>
                            @error('contact_person')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contact Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Person Phone
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-mobile-alt text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    name="contact_phone" 
                                    value="{{ old('contact_phone') }}" 
                                    placeholder="+62 xxx xxxx xxxx"
                                    class="w-full pl-10 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('contact_phone') border-red-500 @enderror"
                                >
                            </div>
                            @error('contact_phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    This person will be the primary contact for all communications with this vendor.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                
                {{-- Financial Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-dollar-sign text-blue-600 mr-2"></i>Financial Information
                    </h3>
                    
                    <div class="space-y-4">
                        {{-- Tax ID --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tax ID / NPWP
                            </label>
                            <input 
                                type="text" 
                                name="tax_id" 
                                value="{{ old('tax_id') }}" 
                                placeholder="XX.XXX.XXX.X-XXX.XXX"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('tax_id') border-red-500 @enderror"
                            >
                            @error('tax_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Tax identification number</p>
                        </div>

                        {{-- Payment Terms --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Terms (Days) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    name="payment_terms_days" 
                                    value="{{ old('payment_terms_days', 30) }}" 
                                    min="0" 
                                    required 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('payment_terms_days') border-red-500 @enderror"
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">days</span>
                                </div>
                            </div>
                            @error('payment_terms_days')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Number of days for payment after invoice date</p>
                        </div>

                        {{-- Payment Terms Examples --}}
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs font-medium text-gray-700 mb-2">Common Payment Terms:</p>
                            <div class="space-y-1 text-xs text-gray-600">
                                <div class="flex justify-between">
                                    <span>Net 7</span>
                                    <span class="text-gray-400">7 days</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Net 15</span>
                                    <span class="text-gray-400">15 days</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Net 30</span>
                                    <span class="text-gray-400">30 days</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Net 60</span>
                                    <span class="text-gray-400">60 days</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-toggle-on text-blue-600 mr-2"></i>Vendor Status
                    </h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center cursor-pointer p-3 rounded-lg border-2 border-gray-200 hover:border-green-300 transition">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                value="1" 
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                            >
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Active Vendor</span>
                                <p class="text-xs text-gray-500">Vendor can receive purchase orders</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note text-blue-600 mr-2"></i>Additional Notes
                    </h3>
                    
                    <textarea 
                        name="notes" 
                        rows="6" 
                        placeholder="Enter any additional information about this vendor..."
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror"
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                        Include important information like certifications, specialties, or special requirements.
                    </p>
                </div>
            </div>

        </div>

        {{-- Form Actions --}}
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                Fields marked with <span class="text-red-500">*</span> are required
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('master.vendors.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>Create Vendor
                </button>
            </div>
        </div>
    </form>

</div>
@endsection