@extends('layouts.app')

@section('title', 'Edit Delivery Order')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-yellow-600 mr-2"></i>
                Edit Delivery Order
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $deliveryOrder->do_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('outbound.delivery-orders.show', $deliveryOrder) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('outbound.delivery-orders.update', $deliveryOrder) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">DO Number</label>
                            <input type="text" value="{{ $deliveryOrder->do_number }}" readonly class="w-full rounded-lg border-gray-300 bg-gray-50">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sales Order *</label>
                            <select name="sales_order_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Sales Order</option>
                                @foreach($salesOrders as $so)
                                    <option value="{{ $so->id }}" {{ old('sales_order_id', $deliveryOrder->sales_order_id) == $so->id ? 'selected' : '' }}>
                                        {{ $so->so_number }} - {{ $so->customer->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sales_order_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Packing Order</label>
                            <select name="packing_order_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Packing Order (Optional)</option>
                                @foreach($packingOrders as $po)
                                    <option value="{{ $po->id }}" {{ old('packing_order_id', $deliveryOrder->packing_order_id) == $po->id ? 'selected' : '' }}>
                                        {{ $po->packing_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('packing_order_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse *</label>
                            <select name="warehouse_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $deliveryOrder->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                            <select name="customer_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $deliveryOrder->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date *</label>
                            <input type="datetime-local" name="delivery_date" value="{{ old('delivery_date', $deliveryOrder->delivery_date->format('Y-m-d\TH:i')) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('delivery_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Boxes *</label>
                            <input type="number" name="total_boxes" value="{{ old('total_boxes', $deliveryOrder->total_boxes) }}" min="0" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('total_boxes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Weight (kg) *</label>
                            <input type="number" name="total_weight_kg" value="{{ old('total_weight_kg', $deliveryOrder->total_weight_kg) }}" step="0.01" min="0" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('total_weight_kg')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Recipient Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user mr-2 text-green-600"></i>Recipient Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Name</label>
                            <input type="text" name="recipient_name" value="{{ old('recipient_name', $deliveryOrder->recipient_name) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('recipient_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Phone</label>
                            <input type="text" name="recipient_phone" value="{{ old('recipient_phone', $deliveryOrder->recipient_phone) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('recipient_phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Address</label>
                            <textarea name="shipping_address" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('shipping_address', $deliveryOrder->shipping_address) }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Vehicle & Driver --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-truck mr-2 text-orange-600"></i>Vehicle & Driver Assignment
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle</label>
                            <select name="vehicle_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Vehicle (Optional)</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $deliveryOrder->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->name }} - {{ $vehicle->plate_number ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Driver</label>
                            <select name="driver_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Driver (Optional)</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id', $deliveryOrder->driver_id) == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('driver_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>Additional Notes
                    </h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Add any additional notes or special instructions...">{{ old('notes', $deliveryOrder->notes) }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Form Actions --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-600 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Update Delivery Order
                        </button>
                        
                        <a href="{{ route('outbound.delivery-orders.show', $deliveryOrder) }}" class="block w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>

                {{-- Current Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-600 mb-3">Current Status</h3>
                    {!! $deliveryOrder->status_badge !!}
                    <p class="text-xs text-gray-500 mt-2">Status: {{ ucfirst(str_replace('_', ' ', $deliveryOrder->status)) }}</p>
                </div>

                {{-- Last Updated --}}
                @if($deliveryOrder->updatedBy)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-sm font-semibold text-gray-600 mb-3">Last Updated</h3>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-user text-indigo-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">{{ $deliveryOrder->updatedBy->name }}</p>
                                <p class="text-xs text-gray-500">{{ $deliveryOrder->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </form>

</div>
@endsection