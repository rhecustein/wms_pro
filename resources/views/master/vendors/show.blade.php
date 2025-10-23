{{-- resources/views/master/vendors/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Vendor Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-truck text-blue-600 mr-2"></i>
                Vendor Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View complete vendor information</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.vendors.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            <a href="{{ route('master.vendors.edit', $vendor) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            @if($vendor->is_active)
                <form action="{{ route('master.vendors.deactivate', $vendor) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="return confirm('Are you sure you want to deactivate this vendor?')">
                        <i class="fas fa-times-circle mr-2"></i>Deactivate
                    </button>
                </form>
            @else
                <form action="{{ route('master.vendors.activate', $vendor) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check-circle mr-2"></i>Activate
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            
            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Vendor Code</label>
                        <p class="text-gray-900 font-mono font-semibold">{{ $vendor->code }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        @if($vendor->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                Inactive
                            </span>
                        @endif
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Vendor Name</label>
                        <p class="text-gray-900 font-semibold text-lg">{{ $vendor->name }}</p>
                    </div>

                    @if($vendor->company_name)
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Company Name</label>
                            <p class="text-gray-900">{{ $vendor->company_name }}</p>
                        </div>
                    @endif

                    @if($vendor->email)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <p class="text-gray-900">
                                <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                {{ $vendor->email }}
                            </p>
                        </div>
                    @endif

                    @if($vendor->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                            <p class="text-gray-900">
                                <i class="fas fa-phone text-gray-400 mr-1"></i>
                                {{ $vendor->phone }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Address Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                    Address Information
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    @if($vendor->address)
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                            <p class="text-gray-900">{{ $vendor->address }}</p>
                        </div>
                    @endif

                    @if($vendor->city)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">City</label>
                            <p class="text-gray-900">{{ $vendor->city }}</p>
                        </div>
                    @endif

                    @if($vendor->province)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Province</label>
                            <p class="text-gray-900">{{ $vendor->province }}</p>
                        </div>
                    @endif

                    @if($vendor->postal_code)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Postal Code</label>
                            <p class="text-gray-900">{{ $vendor->postal_code }}</p>
                        </div>
                    @endif

                    @if($vendor->country)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Country</label>
                            <p class="text-gray-900">{{ $vendor->country }}</p>
                        </div>
                    @endif
                </div>

                @if(!$vendor->address && !$vendor->city && !$vendor->province && !$vendor->postal_code)
                    <p class="text-gray-400 text-center py-4">No address information available</p>
                @endif
            </div>

            {{-- Contact Person --}}
            @if($vendor->contact_person || $vendor->contact_phone)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user text-blue-600 mr-2"></i>
                        Contact Person
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        @if($vendor->contact_person)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                                <p class="text-gray-900">{{ $vendor->contact_person }}</p>
                            </div>
                        @endif

                        @if($vendor->contact_phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                                <p class="text-gray-900">
                                    <i class="fas fa-phone text-gray-400 mr-1"></i>
                                    {{ $vendor->contact_phone }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Notes --}}
            @if($vendor->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-blue-600 mr-2"></i>
                        Notes
                    </h3>
                    
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $vendor->notes }}</p>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            
            {{-- Financial Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-dollar-sign text-blue-600 mr-2"></i>Financial Information
                </h3>
                
                <div class="space-y-4">
                    @if($vendor->tax_id)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tax ID / NPWP</label>
                            <p class="text-gray-900 font-mono">{{ $vendor->tax_id }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Payment Terms</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ $vendor->payment_terms_days }} Days
                        </span>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-bolt text-blue-600 mr-2"></i>Quick Actions
                </h3>
                
                <div class="space-y-2">
                    <a href="{{ route('master.vendors.purchase-orders', $vendor) }}" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-shopping-cart mr-2"></i>View Purchase Orders
                    </a>
                </div>
            </div>

            {{-- Metadata --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info text-blue-600 mr-2"></i>Metadata
                </h3>
                
                <div class="space-y-3 text-sm">
                    @if($vendor->createdBy)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Created By</label>
                            <p class="text-gray-900">{{ $vendor->createdBy->name }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-gray-900">{{ $vendor->created_at->format('d M Y H:i') }}</p>
                    </div>

                    @if($vendor->updatedBy)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated By</label>
                            <p class="text-gray-900">{{ $vendor->updatedBy->name }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-gray-900">{{ $vendor->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection