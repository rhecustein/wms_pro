{{-- resources/views/inbound/shipments/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Create Inbound Shipment')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    Create Inbound Shipment
                </h1>
                <p class="text-gray-600 mt-2">Register a new incoming shipment from supplier</p>
            </div>
            <a href="{{ route('inbound.shipments.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl mb-6 shadow-sm animate-fade-in">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Error Messages --}}
    @if(session('error'))
        <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-circle text-white"></i>
                    </div>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center mb-3">
                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-circle text-white"></i>
                </div>
                <span class="font-semibold text-lg">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-13 space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inbound.shipments.store') }}" method="POST" id="shipmentForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Information --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Basic Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Purchase Order --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Purchase Order <span class="text-red-500">*</span>
                                </label>
                                <select name="purchase_order_id" id="purchase_order_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" required>
                                    <option value="">Select Purchase Order</option>
                                    @foreach($purchaseOrders as $po)
                                        <option value="{{ $po->id }}" 
                                            data-supplier="{{ $po->supplier_id }}"
                                            data-warehouse="{{ $po->warehouse_id }}"
                                            {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
                                            {{ $po->po_number }} - {{ $po->supplier->name }} ({{ ucfirst($po->status) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('purchase_order_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Supplier --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <select name="supplier_id" id="supplier_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }} ({{ $supplier->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Warehouse --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Warehouse <span class="text-red-500">*</span>
                                </label>
                                <select name="warehouse_id" id="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }} ({{ $warehouse->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Scheduled Date --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-check text-gray-400 mr-1"></i>Scheduled Date
                                </label>
                                <input type="datetime-local" name="scheduled_date" value="{{ old('scheduled_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                @error('scheduled_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Shipment Date --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar text-gray-400 mr-1"></i>Shipment Date
                                </label>
                                <input type="datetime-local" name="shipment_date" value="{{ old('shipment_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                @error('shipment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Arrival Date --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>Expected Arrival Date <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="arrival_date" value="{{ old('arrival_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" required>
                                @error('arrival_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Dock Number --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>Dock Number
                                </label>
                                <input type="text" name="dock_number" value="{{ old('dock_number') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="e.g., DOCK-A1">
                                @error('dock_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shipment Details --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-boxes text-purple-600 mr-2"></i>
                            Shipment Details
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Expected Pallets --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-pallet text-gray-400 mr-1"></i>Expected Pallets
                                </label>
                                <input type="number" name="expected_pallets" value="{{ old('expected_pallets') }}" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="0">
                                @error('expected_pallets')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Expected Boxes --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-box text-gray-400 mr-1"></i>Expected Boxes
                                </label>
                                <input type="number" name="expected_boxes" value="{{ old('expected_boxes') }}" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="0">
                                @error('expected_boxes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Expected Weight --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-weight text-gray-400 mr-1"></i>Expected Weight (kg)
                                </label>
                                <input type="number" name="expected_weight" value="{{ old('expected_weight') }}" min="0" step="0.01" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="0.00">
                                @error('expected_weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Vehicle & Driver Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-truck text-green-600 mr-2"></i>
                            Vehicle & Driver Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Vehicle from Database --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-truck-pickup text-gray-400 mr-1"></i>Select Vehicle (Optional)
                                </label>
                                <select name="vehicle_id" id="vehicle_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->vehicle_number }} - {{ $vehicle->license_plate }} ({{ ucfirst($vehicle->vehicle_type) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Or enter vehicle information manually below</p>
                            </div>

                            {{-- Vehicle Type --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Vehicle Type
                                </label>
                                <select name="vehicle_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    <option value="">Select Type</option>
                                    <option value="truck" {{ old('vehicle_type') == 'truck' ? 'selected' : '' }}>Truck</option>
                                    <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>Van</option>
                                    <option value="container" {{ old('vehicle_type') == 'container' ? 'selected' : '' }}>Container</option>
                                </select>
                                @error('vehicle_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Vehicle Number --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Vehicle Number / License Plate
                                </label>
                                <input type="text" name="vehicle_number" value="{{ old('vehicle_number') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="e.g., B 1234 XYZ">
                                @error('vehicle_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Container Number --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-container-storage text-gray-400 mr-1"></i>Container Number
                                </label>
                                <input type="text" name="container_number" value="{{ old('container_number') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="e.g., CONT-12345">
                                @error('container_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Seal Number --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock text-gray-400 mr-1"></i>Seal Number
                                </label>
                                <input type="text" name="seal_number" value="{{ old('seal_number') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="e.g., SEAL-12345">
                                @error('seal_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Driver Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user text-gray-400 mr-1"></i>Driver Name
                                </label>
                                <input type="text" name="driver_name" value="{{ old('driver_name') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="Driver's full name">
                                @error('driver_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Driver Phone --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-phone text-gray-400 mr-1"></i>Driver Phone
                                </label>
                                <input type="text" name="driver_phone" value="{{ old('driver_phone') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="e.g., +62 812 3456 7890">
                                @error('driver_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Driver ID Number --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-id-card text-gray-400 mr-1"></i>Driver ID Number
                                </label>
                                <input type="text" name="driver_id_number" value="{{ old('driver_id_number') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="e.g., KTP/SIM Number">
                                @error('driver_id_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shipping Documents --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-indigo-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-file-alt text-indigo-600 mr-2"></i>
                            Shipping Documents
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Bill of Lading --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-file-invoice text-gray-400 mr-1"></i>Bill of Lading Number
                                </label>
                                <input type="text" name="bill_of_lading" value="{{ old('bill_of_lading') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="e.g., BOL-12345">
                                @error('bill_of_lading')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Packing List --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-clipboard-list text-gray-400 mr-1"></i>Packing List Number
                                </label>
                                <input type="text" name="packing_list" value="{{ old('packing_list') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="e.g., PL-12345">
                                @error('packing_list')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Additional Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4 border-b border-yellow-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                            Additional Notes
                        </h2>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="Add any additional notes or special instructions...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Quick Info --}}
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200 overflow-hidden">
                    <div class="px-6 py-4 bg-blue-600 border-b border-blue-700">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Quick Tips
                        </h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3 text-sm text-blue-900">
                            <li class="flex items-start">
                                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <span><strong>Purchase Order:</strong> Select PO to auto-fill supplier and warehouse</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <span><strong>Vehicle:</strong> Choose from database or enter manually</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <span><strong>Quantities:</strong> Expected values help track receiving progress</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <span><strong>Documents:</strong> Keep track of Bill of Lading and Packing List</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                            Actions
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <button type="submit" id="submitBtn" class="w-full px-6 py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg shadow-blue-500/50 flex items-center justify-center font-semibold">
                            <i class="fas fa-save mr-2"></i>
                            <span id="btnText">Create Shipment</span>
                            <span id="btnLoading" class="hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Creating...
                            </span>
                        </button>
                        
                        <a href="{{ route('inbound.shipments.index') }}" class="w-full px-6 py-3.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all flex items-center justify-center font-semibold">
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
    // Auto-fill supplier and warehouse when PO is selected
    document.getElementById('purchase_order_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const supplierId = selectedOption.dataset.supplier;
        const warehouseId = selectedOption.dataset.warehouse;

        if (supplierId) {
            document.getElementById('supplier_id').value = supplierId;
        }
        if (warehouseId) {
            document.getElementById('warehouse_id').value = warehouseId;
        }
    });

    // Form submission handling with loading state and debug
    const form = document.getElementById('shipmentForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');

    form.addEventListener('submit', function(e) {
        console.log('🚀 Form is being submitted...');
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');

        // Debug: Log form data
        const formData = new FormData(form);
        console.log('📦 Form Data:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }

        // Debug: Check required fields
        const requiredFields = ['purchase_order_id', 'supplier_id', 'warehouse_id', 'arrival_date'];
        let missingFields = [];
        requiredFields.forEach(field => {
            const value = formData.get(field);
            if (!value) {
                missingFields.push(field);
            }
        });

        if (missingFields.length > 0) {
            console.error('❌ Missing required fields:', missingFields);
            e.preventDefault();
            
            // Re-enable button
            submitBtn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');

            // Show alert
            alert('Please fill in all required fields: ' + missingFields.join(', '));
            return false;
        }

        console.log('✅ All validations passed. Submitting form...');
        console.log('🎯 Action URL:', form.action);
        console.log('📍 Method:', form.method);
    });

    // Debug: Log when page loads
    console.log('📄 Create Shipment page loaded');
    console.log('🔧 Debug mode:', {{ config('app.debug') ? 'true' : 'false' }});
    console.log('🔐 CSRF Token:', '{{ csrf_token() }}');
</script>
@endpush
@endsection
