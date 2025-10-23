@extends('layouts.app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-file-invoice text-blue-600 mr-2"></i>
                Purchase Order Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $purchaseOrder->po_number }}</p>
        </div>
        <div class="flex items-center space-x-2">
            @if(in_array($purchaseOrder->status, ['draft', 'submitted']))
                <a href="{{ route('inbound.purchase-orders.edit', $purchaseOrder) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('inbound.purchase-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Basic Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">PO Number</label>
                        <p class="text-base font-semibold text-gray-900 mt-1 font-mono">{{ $purchaseOrder->po_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1">{!! $purchaseOrder->status_badge !!}</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">PO Date</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>
                            {{ $purchaseOrder->po_date->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Expected Delivery</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">
                            @if($purchaseOrder->expected_delivery_date)
                                <i class="fas fa-truck text-gray-400 mr-1"></i>
                                {{ $purchaseOrder->expected_delivery_date->format('d M Y') }}
                            @else
                                <span class="text-gray-400">Not set</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Payment Terms</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">
                            {{ $purchaseOrder->payment_terms ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Currency</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">{{ $purchaseOrder->currency }}</p>
                    </div>
                </div>
            </div>

            {{-- Vendor & Warehouse Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-building text-purple-600 mr-2"></i>
                    Vendor & Warehouse
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Vendor --}}
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-building text-white text-xl"></i>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-blue-600 uppercase">Vendor</label>
                                <h3 class="text-base font-bold text-gray-900">{{ $purchaseOrder->vendor->name }}</h3>
                            </div>
                        </div>
                        @if($purchaseOrder->vendor->code)
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Code:</span> {{ $purchaseOrder->vendor->code }}
                            </p>
                        @endif
                        @if($purchaseOrder->vendor->email)
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-envelope mr-1"></i> {{ $purchaseOrder->vendor->email }}
                            </p>
                        @endif
                        @if($purchaseOrder->vendor->phone)
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-phone mr-1"></i> {{ $purchaseOrder->vendor->phone }}
                            </p>
                        @endif
                    </div>

                    {{-- Warehouse --}}
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-warehouse text-white text-xl"></i>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-purple-600 uppercase">Warehouse</label>
                                <h3 class="text-base font-bold text-gray-900">{{ $purchaseOrder->warehouse->name }}</h3>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Code:</span> {{ $purchaseOrder->warehouse->code }}
                        </p>
                        @if($purchaseOrder->warehouse->address)
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i> {{ $purchaseOrder->warehouse->address }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Financial Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                    Financial Details
                </h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-600">Tax Amount</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-600">Discount Amount</span>
                        <span class="text-lg font-semibold text-red-600">- {{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->discount_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 bg-blue-50 -mx-6 px-6 py-4">
                        <span class="text-xl font-bold text-gray-800">Total Amount</span>
                        <span class="text-2xl font-bold text-blue-600">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($purchaseOrder->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                        Notes
                    </h2>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $purchaseOrder->notes }}</p>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Action Buttons --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-tasks text-blue-600 mr-2"></i>
                    Actions
                </h2>
                
                <div class="space-y-2">
                    @if($purchaseOrder->status === 'draft')
                        <form action="{{ route('inbound.purchase-orders.update-status', $purchaseOrder) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="submitted">
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-paper-plane mr-2"></i>Submit PO
                            </button>
                        </form>
                    @endif

                    @if($purchaseOrder->status === 'submitted')
                        <form action="{{ route('inbound.purchase-orders.update-status', $purchaseOrder) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                <i class="fas fa-check-circle mr-2"></i>Confirm PO
                            </button>
                        </form>
                    @endif

                    @if(in_array($purchaseOrder->status, ['confirmed', 'partial']))
                        <form action="{{ route('inbound.purchase-orders.update-status', $purchaseOrder) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-check-double mr-2"></i>Complete PO
                            </button>
                        </form>
                    @endif

                    @if(!in_array($purchaseOrder->status, ['completed', 'cancelled']))
                        <form action="{{ route('inbound.purchase-orders.update-status', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this PO?')">
                            @csrf
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-times-circle mr-2"></i>Cancel PO
                            </button>
                        </form>
                    @endif

                    <button onclick="window.print()" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-print mr-2"></i>Print PO
                    </button>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-purple-600 mr-2"></i>
                    Timeline
                </h2>
                
                <div class="space-y-4">
                    <div class="flex">
                        <div class="flex flex-col items-center mr-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-green-600 text-sm"></i>
                            </div>
                            <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
                        </div>
                        <div class="flex-1 pb-4">
                            <p class="text-sm font-semibold text-gray-900">Created</p>
                            <p class="text-xs text-gray-500">{{ $purchaseOrder->created_at->format('d M Y H:i') }}</p>
                            @if($purchaseOrder->creator)
                                <p class="text-xs text-gray-500">by {{ $purchaseOrder->creator->name }}</p>
                            @endif
                        </div>
                    </div>

                    @if($purchaseOrder->updated_at != $purchaseOrder->created_at)
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-edit text-blue-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Last Updated</p>
                                <p class="text-xs text-gray-500">{{ $purchaseOrder->updated_at->format('d M Y H:i') }}</p>
                                @if($purchaseOrder->updater)
                                    <p class="text-xs text-gray-500">by {{ $purchaseOrder->updater->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Status Guide --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-question-circle text-orange-600 mr-2"></i>
                    Status Guide
                </h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex items-start">
                        {!! '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 mr-2">Draft</span>' !!}
                        <span class="text-gray-600 flex-1">Initial state, can be edited</span>
                    </div>
                    <div class="flex items-start">
                        {!! '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">Submitted</span>' !!}
                        <span class="text-gray-600 flex-1">Sent to vendor</span>
                    </div>
                    <div class="flex items-start">
                        {!! '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 mr-2">Confirmed</span>' !!}
                        <span class="text-gray-600 flex-1">Vendor confirmed</span>
                    </div>
                    <div class="flex items-start">
                        {!! '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mr-2">Partial</span>' !!}
                        <span class="text-gray-600 flex-1">Partially received</span>
                    </div>
                    <div class="flex items-start">
                        {!! '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-2">Completed</span>' !!}
                        <span class="text-gray-600 flex-1">Fully received</span>
                    </div>
                    <div class="flex items-start">
                        {!! '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 mr-2">Cancelled</span>' !!}
                        <span class="text-gray-600 flex-1">PO cancelled</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush
@endsection