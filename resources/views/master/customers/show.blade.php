{{-- resources/views/master/customers/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user text-blue-600 mr-2"></i>
                Customer Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View complete customer information</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.customers.edit', $customer) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('master.customers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            
            {{-- Customer Info Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center mr-4 {{ $customer->customer_type === 'vip' ? 'bg-yellow-100' : ($customer->customer_type === 'wholesale' ? 'bg-purple-100' : 'bg-blue-100') }}">
                            <i class="fas {{ $customer->customer_type === 'vip' ? 'fa-crown text-yellow-600 text-2xl' : ($customer->customer_type === 'wholesale' ? 'fa-boxes text-purple-600 text-2xl' : 'fa-user text-blue-600 text-2xl') }}"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h2>
                            <p class="text-gray-600">{{ $customer->company_name ?? '-' }}</p>
                            <span class="inline-block mt-2 text-sm font-mono font-semibold text-gray-700 bg-gray-100 px-3 py-1 rounded">
                                {{ $customer->code }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        @if($customer->is_active)
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-800">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                Inactive
                            </span>
                        @endif
                        
                        @if($customer->customer_type === 'vip')
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-yellow-100 text-yellow-800 mt-2">
                                <i class="fas fa-crown mr-2"></i>VIP Customer
                            </span>
                        @elseif($customer->customer_type === 'wholesale')
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-purple-100 text-purple-800 mt-2">
                                <i class="fas fa-boxes mr-2"></i>Wholesale
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="flex space-x-3 pb-6 border-b border-gray-200">
                    <a href="{{ route('master.customers.orders', $customer) }}" class="flex-1 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition text-center">
                        <i class="fas fa-shopping-cart mr-2"></i>View Orders
                    </a>
                    <a href="{{ route('master.customers.stock', $customer) }}" class="flex-1 px-4 py-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition text-center">
                        <i class="fas fa-boxes mr-2"></i>View Stock
                    </a>
                    @if($customer->is_active)
                        <form action="{{ route('master.customers.deactivate', $customer) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition">
                                <i class="fas fa-ban mr-2"></i>Deactivate
                            </button>
                        </form>
                    @else
                        <form action="{{ route('master.customers.activate', $customer) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition">
                                <i class="fas fa-check mr-2"></i>Activate
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Contact Information --}}
                <div class="pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-address-card text-blue-600 mr-2"></i>
                        Contact Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($customer->email)
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-envelope text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Email</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $customer->email }}</p>
                                </div>
                            </div>
                        @endif

                        @if($customer->phone)
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-phone text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Phone</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $customer->phone }}</p>
                                </div>
                            </div>
                        @endif

                        @if($customer->tax_id)
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-file-invoice text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Tax ID / NPWP</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $customer->tax_id }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Address Information --}}
            @if($customer->address || $customer->city)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-map-marked-alt text-blue-600 mr-2"></i>
                        Address Information
                    </h3>
                    
                    <div class="space-y-3">
                        @if($customer->address)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Street Address</p>
                                <p class="text-sm text-gray-900">{{ $customer->address }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            @if($customer->city)
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">City</p>
                                    <p class="text-sm text-gray-900">{{ $customer->city }}</p>
                                </div>
                            @endif

                            @if($customer->province)
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Province</p>
                                    <p class="text-sm text-gray-900">{{ $customer->province }}</p>
                                </div>
                            @endif

                            @if($customer->postal_code)
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Postal Code</p>
                                    <p class="text-sm text-gray-900">{{ $customer->postal_code }}</p>
                                </div>
                            @endif

                            <div>
                                <p class="text-xs text-gray-500 mb-1">Country</p>
                                <p class="text-sm text-gray-900">{{ $customer->country }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Contact Person --}}
            @if($customer->contact_person || $customer->contact_phone)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user-tie text-blue-600 mr-2"></i>
                        Contact Person
                    </h3>
                    
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user-tie text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $customer->contact_person ?? '-' }}</p>
                            <p class="text-xs text-gray-600">{{ $customer->contact_phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Notes --}}
            @if($customer->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note text-blue-600 mr-2"></i>
                        Notes
                    </h3>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $customer->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            
            {{-- Financial Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                    Financial Information
                </h3>
                
                <div class="space-y-4">
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-xs text-green-600 mb-1">Credit Limit</p>
                        @if($customer->credit_limit)
                            <p class="text-2xl font-bold text-green-700">Rp {{ number_format($customer->credit_limit, 0, ',', '.') }}</p>
                        @else
                            <p class="text-lg font-medium text-gray-400">Not Set</p>
                        @endif
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-xs text-blue-600 mb-1">Payment Terms</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $customer->payment_terms_days }} Days</p>
                    </div>
                </div>
            </div>

            {{-- Activity Log --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Activity Log
                </h3>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-plus text-green-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 font-medium">Created</p>
                            <p class="text-xs text-gray-500">{{ $customer->created_at->format('d M Y, H:i') }}</p>
                            @if($customer->creator)
                                <p class="text-xs text-gray-500">by {{ $customer->creator->name }}</p>
                            @endif
                        </div>
                    </div>

                    @if($customer->updated_at != $customer->created_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-edit text-blue-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 font-medium">Last Updated</p>
                                <p class="text-xs text-gray-500">{{ $customer->updated_at->format('d M Y, H:i') }}</p>
                                @if($customer->updater)
                                    <p class="text-xs text-gray-500">by {{ $customer->updater->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-red-50 rounded-xl border border-red-200 p-6">
                <h3 class="text-lg font-semibold text-red-800 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Danger Zone
                </h3>
                
                <p class="text-sm text-red-700 mb-4">Once you delete this customer, there is no going back. Please be certain.</p>
                
                <form action="{{ route('master.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Are you absolutely sure? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        <i class="fas fa-trash mr-2"></i>Delete Customer
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection