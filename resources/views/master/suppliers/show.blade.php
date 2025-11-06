{{-- resources/views/master/suppliers/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Supplier Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-boxes text-blue-600 mr-2"></i>
                Supplier Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View complete supplier information</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('master.suppliers.edit', $supplier) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('master.suppliers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
       {{-- Main Information --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h3>
                    @if($supplier->is_active)
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
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Supplier Code</label>
                        <p class="text-base font-semibold text-gray-900 font-mono">{{ $supplier->code }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Supplier Name</label>
                        <p class="text-base font-semibold text-gray-900">{{ $supplier->name }}</p>
                    </div>

                    @if($supplier->company_name)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Company Name</label>
                        <p class="text-base text-gray-900">{{ $supplier->company_name }}</p>
                    </div>
                    @endif

                    @if($supplier->email)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-base text-gray-900">
                            <a href="mailto:{{ $supplier->email }}" class="text-blue-600 hover:underline">
                                {{ $supplier->email }}
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($supplier->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                        <p class="text-base text-gray-900">
                            <a href="tel:{{ $supplier->phone }}" class="text-blue-600 hover:underline">
                                {{ $supplier->phone }}
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($supplier->mobile)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Mobile</label>
                        <p class="text-base text-gray-900">
                            <a href="tel:{{ $supplier->mobile }}" class="text-blue-600 hover:underline">
                                {{ $supplier->mobile }}
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($supplier->fax)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fax</label>
                        <p class="text-base text-gray-900">{{ $supplier->fax }}</p>
                    </div>
                    @endif

                    @if($supplier->website)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Website</label>
                        <p class="text-base text-gray-900">
                            <a href="{{ $supplier->website }}" target="_blank" class="text-blue-600 hover:underline">
                                {{ $supplier->website }}
                            </a>
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Address Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                    Address Information
                </h3>
                
                <div class="space-y-4">
                    @if($supplier->address)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                        <p class="text-base text-gray-900">{{ $supplier->address }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($supplier->city)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">City</label>
                            <p class="text-base text-gray-900">{{ $supplier->city }}</p>
                        </div>
                        @endif

                        @if($supplier->state)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">State/Province</label>
                            <p class="text-base text-gray-900">{{ $supplier->state }}</p>
                        </div>
                        @endif

                        @if($supplier->postal_code)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Postal Code</label>
                            <p class="text-base text-gray-900">{{ $supplier->postal_code }}</p>
                        </div>
                        @endif

                        @if($supplier->country)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Country</label>
                            <p class="text-base text-gray-900">{{ $supplier->country }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Contact Person --}}
            @if($supplier->contact_person || $supplier->contact_email || $supplier->contact_phone)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user text-green-600 mr-2"></i>
                    Contact Person
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if($supplier->contact_person)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                        <p class="text-base text-gray-900">{{ $supplier->contact_person }}</p>
                    </div>
                    @endif

                    @if($supplier->contact_email)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-base text-gray-900">
                            <a href="mailto:{{ $supplier->contact_email }}" class="text-blue-600 hover:underline">
                                {{ $supplier->contact_email }}
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($supplier->contact_phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                        <p class="text-base text-gray-900">
                            <a href="tel:{{ $supplier->contact_phone }}" class="text-blue-600 hover:underline">
                                {{ $supplier->contact_phone }}
                            </a>
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Bank Information --}}
            @if($supplier->bank_name || $supplier->bank_account_number || $supplier->bank_account_name)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-university text-purple-600 mr-2"></i>
                    Bank Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if($supplier->bank_name)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Bank Name</label>
                        <p class="text-base text-gray-900">{{ $supplier->bank_name }}</p>
                    </div>
                    @endif

                    @if($supplier->bank_account_number)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Account Number</label>
                        <p class="text-base text-gray-900 font-mono">{{ $supplier->bank_account_number }}</p>
                    </div>
                    @endif

                    @if($supplier->bank_account_name)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Account Name</label>
                        <p class="text-base text-gray-900">{{ $supplier->bank_account_name }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Notes --}}
            @if($supplier->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                    Notes
                </h3>
                <p class="text-base text-gray-900">{{ $supplier->notes }}</p>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Tax Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-file-invoice text-orange-600 mr-2"></i>
                    Tax Information
                </h3>
                
                <div class="space-y-4">
                    @if($supplier->tax_number)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tax Number (NPWP)</label>
                        <p class="text-base text-gray-900 font-mono">{{ $supplier->tax_number }}</p>
                    </div>
                    @endif

                    @if($supplier->tax_name)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tax Name</label>
                        <p class="text-base text-gray-900">{{ $supplier->tax_name }}</p>
                    </div>
                    @endif

                    @if(!$supplier->tax_number && !$supplier->tax_name)
                    <p class="text-sm text-gray-500 italic">No tax information available</p>
                    @endif
                </div>
            </div>

            {{-- Payment Terms --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-credit-card text-indigo-600 mr-2"></i>
                    Payment Terms
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Payment Term Days</label>
                        <p class="text-base text-gray-900">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Net {{ $supplier->payment_term_days }} Days
                            </span>
                        </p>
                    </div>

                    @if($supplier->payment_method)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Payment Method</label>
                        <p class="text-base text-gray-900">{{ $supplier->payment_method }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Supplier Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-cog text-gray-600 mr-2"></i>
                    Supplier Details
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Supplier Type</label>
                        <p class="text-base text-gray-900">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ $supplier->type === 'manufacturer' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                {{ $supplier->type === 'distributor' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $supplier->type === 'wholesaler' ? 'bg-pink-100 text-pink-800' : '' }}
                                {{ $supplier->type === 'retailer' ? 'bg-orange-100 text-orange-800' : '' }}">
                                {{ ucfirst($supplier->type) }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Rating</label>
                        <p class="text-base text-gray-900">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $supplier->rating === 'A' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $supplier->rating === 'B' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $supplier->rating === 'C' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $supplier->rating === 'D' ? 'bg-red-100 text-red-800' : '' }}">
                                <i class="fas fa-star mr-1"></i>
                                Rating {{ $supplier->rating }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Audit Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clock text-gray-600 mr-2"></i>
                    Audit Information
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-base text-gray-900">{{ $supplier->created_at->format('d M Y H:i') }}</p>
                        @if($supplier->creator)
                        <p class="text-xs text-gray-500 mt-1">by {{ $supplier->creator->name }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-base text-gray-900">{{ $supplier->updated_at->format('d M Y H:i') }}</p>
                        @if($supplier->updater)
                        <p class="text-xs text-gray-500 mt-1">by {{ $supplier->updater->name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-tools text-gray-600 mr-2"></i>
                    Actions
                </h3>
                
                <div class="space-y-3">
                    <a href="{{ route('master.suppliers.edit', $supplier) }}" class="block w-full px-4 py-2 bg-yellow-600 text-white text-center rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-edit mr-2"></i>Edit Supplier
                    </a>
                    
                    <form action="{{ route('master.suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="block w-full px-4 py-2 bg-red-600 text-white text-center rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i>Delete Supplier
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection