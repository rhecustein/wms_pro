{{-- resources/views/master/vendors/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-blue-600 mr-2"></i>
                Edit Vendor
            </h1>
            <p class="text-sm text-gray-600 mt-1">Update vendor information</p>
        </div>
        <a href="{{ route('master.vendors.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('master.vendors.update', $vendor) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Code <span class="text-red-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code', $vendor->code) }}" placeholder="VEND001" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror">
                            @error('code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $vendor->name) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Company Name --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $vendor->company_name) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('company_name') border-red-500 @enderror">
                            @error('company_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $vendor->email) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $vendor->phone) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('address') border-red-500 @enderror">{{ old('address', $vendor->address) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- City --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" name="city" value="{{ old('city', $vendor->city) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('city') border-red-500 @enderror">
                                @error('city')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Province --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                                <input type="text" name="province" value="{{ old('province', $vendor->province) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('province') border-red-500 @enderror">
                                @error('province')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Postal Code --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code', $vendor->postal_code) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('postal_code') border-red-500 @enderror">
                                @error('postal_code')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Country --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country <span class="text-red-500">*</span></label>
                                <input type="text" name="country" value="{{ old('country', $vendor->country) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('country') border-red-500 @enderror">
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
                        <i class="fas fa-user text-blue-600 mr-2"></i>Contact Person
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Contact Person --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person Name</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person', $vendor->contact_person) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('contact_person') border-red-500 @enderror">
                            @error('contact_person')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contact Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $vendor->contact_phone) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('contact_phone') border-red-500 @enderror">
                            @error('contact_phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tax ID / NPWP</label>
                            <input type="text" name="tax_id" value="{{ old('tax_id', $vendor->tax_id) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('tax_id') border-red-500 @enderror">
                            @error('tax_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Payment Terms --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Terms (Days) <span class="text-red-500">*</span></label>
                            <input type="number" name="payment_terms_days" value="{{ old('payment_terms_days', $vendor->payment_terms_days) }}" min="0" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('payment_terms_days') border-red-500 @enderror">
                            @error('payment_terms_days')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Number of days for payment after invoice date</p>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-toggle-on text-blue-600 mr-2"></i>Status
                    </h3>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $vendor->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-3 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note text-blue-600 mr-2"></i>Notes
                    </h3>
                    
                    <textarea name="notes" rows="4" placeholder="Additional notes about this vendor..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $vendor->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Metadata --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info text-blue-600 mr-2"></i>Metadata
                    </h3>
                    
                    <div class="space-y-2 text-sm">
                        @if($vendor->createdBy)
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-user-plus w-5"></i>
                                <span class="ml-2">Created by: {{ $vendor->createdBy->name }}</span>
                            </div>
                        @endif
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar-plus w-5"></i>
                            <span class="ml-2">{{ $vendor->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($vendor->updatedBy)
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-user-edit w-5"></i>
                                <span class="ml-2">Updated by: {{ $vendor->updatedBy->name }}</span>
                            </div>
                        @endif
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar-check w-5"></i>
                            <span class="ml-2">{{ $vendor->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Form Actions --}}
        <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ route('master.vendors.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Update Vendor
            </button>
        </div>
    </form>

</div>
@endsection