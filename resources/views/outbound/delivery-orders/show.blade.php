@extends('layouts.app')

@section('title', 'Delivery Order Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-truck text-blue-600 mr-2"></i>
                Delivery Order Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $deliveryOrder->do_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('outbound.delivery-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <a href="{{ route('outbound.delivery-orders.print', $deliveryOrder) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" target="_blank">
                <i class="fas fa-print mr-2"></i>Print
            </a>
            <a href="{{ route('outbound.delivery-orders.tracking', $deliveryOrder) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-route mr-2"></i>Track
            </a>
            @if(in_array($deliveryOrder->status, ['prepared', 'loaded']))
                <a href="{{ route('outbound.delivery-orders.edit', $deliveryOrder) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
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
            
            {{-- Status & Actions Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-traffic-light mr-2 text-gray-600"></i>Status & Actions
                    </h2>
                    {!! $deliveryOrder->status_badge !!}
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @if($deliveryOrder->status === 'prepared')
                        <form action="{{ route('outbound.delivery-orders.load', $deliveryOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                                <i class="fas fa-truck-loading mr-1"></i>Load
                            </button>
                        </form>
                    @endif

                    @if($deliveryOrder->status === 'loaded')
                        <form action="{{ route('outbound.delivery-orders.dispatch', $deliveryOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm">
                                <i class="fas fa-shipping-fast mr-1"></i>Dispatch
                            </button>
                        </form>
                    @endif

                    @if($deliveryOrder->status === 'in_transit')
                        <button type="button" onclick="document.getElementById('deliverModal').classList.remove('hidden')" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                            <i class="fas fa-check-circle mr-1"></i>Deliver
                        </button>
                    @endif

                    @if(!in_array($deliveryOrder->status, ['delivered', 'cancelled']))
                        <button type="button" onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                            <i class="fas fa-times-circle mr-1"></i>Cancel
                        </button>
                    @endif

                    @if($deliveryOrder->status === 'in_transit' || $deliveryOrder->status === 'delivered')
                        <a href="{{ route('outbound.delivery-orders.proof', $deliveryOrder) }}" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm text-center">
                            <i class="fas fa-camera mr-1"></i>Proof
                        </a>
                    @endif
                </div>
            </div>

            {{-- Delivery Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>Delivery Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">DO Number</label>
                        <p class="text-sm font-mono font-semibold text-gray-900">{{ $deliveryOrder->do_number }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Sales Order</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->salesOrder->so_number ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Packing Order</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->packingOrder->packing_number ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Warehouse</label>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-warehouse text-purple-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->warehouse->name }}</p>
                                <p class="text-xs text-gray-500">{{ $deliveryOrder->warehouse->code }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Delivery Date</label>
                        <p class="text-sm font-semibold text-gray-900">
                            <i class="fas fa-calendar mr-1 text-blue-500"></i>
                            {{ $deliveryOrder->delivery_date->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Total Items</label>
                        <p class="text-sm font-semibold text-gray-900">
                            <i class="fas fa-boxes mr-1 text-indigo-500"></i>
                            {{ number_format($deliveryOrder->total_boxes) }} boxes
                            <span class="text-gray-500 ml-2">
                                ({{ number_format($deliveryOrder->total_weight_kg, 2) }} kg)
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Customer & Recipient Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user-tie mr-2 text-green-600"></i>Customer & Recipient
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Customer</label>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-building text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->customer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $deliveryOrder->customer->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Recipient</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->recipient_name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $deliveryOrder->recipient_phone ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Shipping Address</label>
                        <p class="text-sm text-gray-900">{{ $deliveryOrder->shipping_address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Vehicle & Driver Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-truck mr-2 text-orange-600"></i>Vehicle & Driver
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Vehicle</label>
                        @if($deliveryOrder->vehicle)
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-truck text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->vehicle->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $deliveryOrder->vehicle->plate_number ?? '-' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Not Assigned</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Driver</label>
                        @if($deliveryOrder->driver)
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->driver->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $deliveryOrder->driver->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Not Assigned</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Delivery Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-history mr-2 text-purple-600"></i>Delivery Timeline
                </h2>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-box text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Prepared</p>
                            <p class="text-xs text-gray-500">{{ $deliveryOrder->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    @if($deliveryOrder->loaded_at)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-truck-loading text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Loaded</p>
                                <p class="text-xs text-gray-500">{{ $deliveryOrder->loaded_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($deliveryOrder->departed_at)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-shipping-fast text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Departed / In Transit</p>
                                <p class="text-xs text-gray-500">{{ $deliveryOrder->departed_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($deliveryOrder->delivered_at)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Delivered</p>
                                <p class="text-xs text-gray-500">{{ $deliveryOrder->delivered_at->format('d M Y, H:i') }}</p>
                                @if($deliveryOrder->received_by_name)
                                    <p class="text-xs text-gray-500 mt-1">Received by: {{ $deliveryOrder->received_by_name }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Notes --}}
            @if($deliveryOrder->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>Notes
                    </h2>
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $deliveryOrder->notes }}</p>
                </div>
            @endif

            {{-- Delivery Proof --}}
            @if($deliveryOrder->delivery_proof_image)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-image mr-2 text-red-600"></i>Delivery Proof
                    </h2>
                    <img src="{{ Storage::url($deliveryOrder->delivery_proof_image) }}" alt="Delivery Proof" class="w-full max-w-md rounded-lg border border-gray-200">
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Quick Stats --}}
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
                <h3 class="text-lg font-semibold mb-4">
                    <i class="fas fa-chart-bar mr-2"></i>Quick Stats
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm opacity-90">Total Boxes</span>
                        <span class="text-xl font-bold">{{ number_format($deliveryOrder->total_boxes) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm opacity-90">Total Weight</span>
                        <span class="text-xl font-bold">{{ number_format($deliveryOrder->total_weight_kg, 2) }} kg</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm opacity-90">Status</span>
                        <span class="text-sm font-semibold">{{ ucfirst(str_replace('_', ' ', $deliveryOrder->status)) }}</span>
                    </div>
                </div>
            </div>

            {{-- Created By --}}
            @if($deliveryOrder->createdBy)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-600 mb-3">Created By</h3>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->createdBy->name }}</p>
                            <p class="text-xs text-gray-500">{{ $deliveryOrder->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Updated By --}}
            @if($deliveryOrder->updatedBy)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-600 mb-3">Last Updated By</h3>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->updatedBy->name }}</p>
                            <p class="text-xs text-gray-500">{{ $deliveryOrder->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>

{{-- Deliver Modal --}}
<div id="deliverModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Confirm Delivery</h3>
            <button onclick="document.getElementById('deliverModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('outbound.delivery-orders.deliver', $deliveryOrder) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Received By Name *</label>
                    <input type="text" name="received_by_name" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Signature (Optional)</label>
                    <textarea name="received_by_signature" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Digital signature or notes"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Proof Image (Optional)</label>
                    <input type="file" name="delivery_proof_image" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button type="button" onclick="document.getElementById('deliverModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-check mr-2"></i>Confirm Delivery
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Cancel Delivery Order</h3>
            <button onclick="document.getElementById('cancelModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('outbound.delivery-orders.cancel', $deliveryOrder) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason</label>
                <textarea name="cancel_reason" rows="4" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Please provide a reason for cancellation"></textarea>
            </div>

            <div class="flex space-x-3">
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Close
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-times-circle mr-2"></i>Cancel Order
                </button>
            </div>
        </form>
    </div>
</div>

@endsection