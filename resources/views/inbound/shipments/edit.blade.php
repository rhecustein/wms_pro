{{-- resources/views/inbound/shipments/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Inbound Shipment')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-blue-600 mr-2"></i>
                Edit Inbound Shipment
            </h1>
            <p class="text-sm text-gray-600 mt-1">Update shipment: {{ $shipment->shipment_number }}</p>
        </div>
        <a href="{{ route('inbound.shipments.show', $shipment) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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
            <ul class="list-disc list-inside ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inbound.shipments.update', $shipment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Information --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Shipment Number (Read-only) --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Shipment Number
                            </label>
                            <input type="text" value="{{ $shipment->shipment_number }}" class="w-full rounded-lg border-gray-300 bg-gray-50" readonly>
                        </div>

                        {{-- Purchase Order --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Purchase Order <span class="text-gray-400">(Optional)</span>
                            </label>
                            <select name="purchase_order_id" id="purchase_order_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Purchase Order</option>
                                @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}" 
                                        data-vendor="{{ $po->vendor_id }}"
                                        data-warehouse="{{ $po->warehouse_id }}"
                                        {{ old('purchase_order_id', $shipment->purchase_order_id) == $po->id ? 'selected' : '' }}>
                                        {{ $po->po_number }} - {{ $po->vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('purchase_order_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Arrival Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Arrival Date <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="arrival_date" value="{{ old('arrival_date', $shipment->arrival_date->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            @error('arrival_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Vendor --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vendor <span class="text-red-500">*</span>
                            </label>
                            <select name="vendor_id" id="vendor_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id', $shipment->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vendor_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Warehouse --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" id="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $shipment->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Expected Pallets --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Expected Pallets
                            </label>
                            <input type="number" name="expected_pallets" value="{{ old('expected_pallets', $shipment->expected_pallets) }}" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="0">
                            @error('expected_pallets')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Dock Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Dock Number
                            </label>
                            <input type="text" name="dock_number" value="{{ old('dock_number', $shipment->dock_number) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., DOCK-A1">
                            @error('dock_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Vehicle & Driver Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-truck text-green-600 mr-2"></i>
                        Vehicle & Driver Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Vehicle Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Number
                            </label>
                            <input type="text" name="vehicle_number" value="{{ old('vehicle_number', $shipment->vehicle_number) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., B 1234 XYZ">
                            @error('vehicle_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Seal Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Seal Number
                            </label>
                            <input type="text" name="seal_number" value="{{ old('seal_number', $shipment->seal_number) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., SEAL-12345">
                            @error('seal_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Driver Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Driver Name
                            </label>
                            <input type="text" name="driver_name" value="{{ old('driver_name', $shipment->driver_name) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Driver's full name">
                            @error('driver_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Driver Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Driver Phone
                            </label>
                            <input type="text" name="driver_phone" value="{{ old('driver_phone', $shipment->driver_phone) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., +62 812 3456 7890">
                            @error('driver_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Additional Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                        Additional Notes
                    </h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Add any additional notes or special instructions...">{{ old('notes', $shipment->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Current Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h3>
                    <div class="text-center py-4">
                        {!! $shipment->status_badge !!}
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Update Shipment
                        </button>
                        
                        <a href="{{ route('inbound.shipments.show', $shipment) }}" class="w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

@push('scripts')
<script>
    // Auto-fill vendor and warehouse when PO is selected
    document.getElementById('purchase_order_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const vendorId = selectedOption.dataset.vendor;
        const warehouseId = selectedOption.dataset.warehouse;

        if (vendorId) {
            document.getElementById('vendor_id').value = vendorId;
        }
        if (warehouseId) {
            document.getElementById('warehouse_id').value = warehouseId;
        }
    });
</script>
@endpush
@endsection