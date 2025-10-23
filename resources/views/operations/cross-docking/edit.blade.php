{{-- resources/views/operations/cross-docking/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Cross Docking Order')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-yellow-600 mr-2"></i>
                Edit Cross Docking Order
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $crossDocking->cross_dock_number }}</p>
        </div>
        <a href="{{ route('operations.cross-docking.show', $crossDocking) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Details
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('operations.cross-docking.update', $crossDocking) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Information --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Warehouse --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" id="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ (old('warehouse_id', $crossDocking->warehouse_id) == $warehouse->id) ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Product --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Product <span class="text-red-500">*</span>
                            </label>
                            <select name="product_id" id="product_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ (old('product_id', $crossDocking->product_id) == $product->id) ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="quantity" value="{{ old('quantity', $crossDocking->quantity) }}" min="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            @error('quantity')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Unit of Measure --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Unit of Measure <span class="text-red-500">*</span>
                            </label>
                            <select name="unit_of_measure" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Unit</option>
                                <option value="PCS" {{ old('unit_of_measure', $crossDocking->unit_of_measure) == 'PCS' ? 'selected' : '' }}>PCS</option>
                                <option value="BOX" {{ old('unit_of_measure', $crossDocking->unit_of_measure) == 'BOX' ? 'selected' : '' }}>BOX</option>
                                <option value="CARTON" {{ old('unit_of_measure', $crossDocking->unit_of_measure) == 'CARTON' ? 'selected' : '' }}>CARTON</option>
                                <option value="PALLET" {{ old('unit_of_measure', $crossDocking->unit_of_measure) == 'PALLET' ? 'selected' : '' }}>PALLET</option>
                                <option value="KG" {{ old('unit_of_measure', $crossDocking->unit_of_measure) == 'KG' ? 'selected' : '' }}>KG</option>
                                <option value="LTR" {{ old('unit_of_measure', $crossDocking->unit_of_measure) == 'LTR' ? 'selected' : '' }}>LTR</option>
                            </select>
                            @error('unit_of_measure')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Scheduled Date --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Scheduled Date & Time <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="scheduled_date" value="{{ old('scheduled_date', $crossDocking->scheduled_date->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            @error('scheduled_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Dock Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-door-open text-green-600 mr-2"></i>
                        Dock Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Dock In --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Receiving Dock (In)
                            </label>
                            <input type="text" name="dock_in" value="{{ old('dock_in', $crossDocking->dock_in) }}" placeholder="e.g., Dock A1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('dock_in')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Dock Out --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Shipping Dock (Out)
                            </label>
                            <input type="text" name="dock_out" value="{{ old('dock_out', $crossDocking->dock_out) }}" placeholder="e.g., Dock B2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('dock_out')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                        Additional Notes
                    </h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Add any special instructions or notes...">{{ old('notes', $crossDocking->notes) }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Side Information --}}
            <div class="lg:col-span-1">
                {{-- Related Orders --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-link text-purple-600 mr-2"></i>
                        Related Orders
                    </h2>

                    {{-- Inbound Shipment --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Inbound Shipment
                        </label>
                        <select name="inbound_shipment_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Inbound Shipment</option>
                            @foreach($inboundShipments as $shipment)
                                <option value="{{ $shipment->id }}" {{ (old('inbound_shipment_id', $crossDocking->inbound_shipment_id) == $shipment->id) ? 'selected' : '' }}>
                                    {{ $shipment->shipment_number }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Optional: Link to receiving order</p>
                        @error('inbound_shipment_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Outbound Order --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Outbound Order
                        </label>
                        <select name="outbound_order_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Outbound Order</option>
                            @foreach($outboundOrders as $order)
                                <option value="{{ $order->id }}" {{ (old('outbound_order_id', $crossDocking->outbound_order_id) == $order->id) ? 'selected' : '' }}>
                                    {{ $order->order_number }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Optional: Link to shipping order</p>
                        @error('outbound_order_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Current Status Info --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mt-6">
                    <h3 class="text-sm font-semibold text-yellow-800 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Current Status
                    </h3>
                    <div class="mb-2">{!! $crossDocking->status_badge !!}</div>
                    <p class="text-xs text-yellow-700">
                        Only scheduled cross docking orders can be edited. Once the process starts, modifications are not allowed.
                    </p>
                </div>

                {{-- Audit Info --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 mt-6">
                    <h2 class="text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-history mr-1"></i>
                        Audit Information
                    </h2>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium text-gray-900">{{ $crossDocking->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($crossDocking->creator)
                            <div class="flex justify-between">
                                <span class="text-gray-600">By:</span>
                                <span class="font-medium text-gray-900">{{ $crossDocking->creator->name }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{ $crossDocking->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-between mt-6">
            <div>
                <form action="{{ route('operations.cross-docking.destroy', $crossDocking) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this cross docking order?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('operations.cross-docking.show', $crossDocking) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Update Cross Docking Order
                </button>
            </div>
        </div>

    </form>

</div>
@endsection