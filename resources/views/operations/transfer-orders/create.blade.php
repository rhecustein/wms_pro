@extends('layouts.app')

@section('title', 'Create Transfer Order')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-indigo-600 mr-2"></i>
                Create Transfer Order
            </h1>
            <p class="text-sm text-gray-600 mt-1">Create a new warehouse transfer order</p>
        </div>
        <div>
            <a href="{{ route('operations.transfer-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('operations.transfer-orders.store') }}" method="POST" id="transferOrderForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Transfer Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transfer Number</label>
                            <input type="text" value="{{ $transferNumber }}" class="w-full rounded-lg border-gray-300 bg-gray-100" disabled>
                            <p class="text-xs text-gray-500 mt-1">Auto-generated</p>
                        </div>
                        
                        {{-- Transfer Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transfer Type <span class="text-red-500">*</span></label>
                            <select name="transfer_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Transfer Type</option>
                                <option value="inter_warehouse" {{ old('transfer_type') === 'inter_warehouse' ? 'selected' : '' }}>Inter Warehouse</option>
                                <option value="internal_bin" {{ old('transfer_type') === 'internal_bin' ? 'selected' : '' }}>Internal Bin</option>
                                <option value="consolidation" {{ old('transfer_type') === 'consolidation' ? 'selected' : '' }}>Consolidation</option>
                            </select>
                            @error('transfer_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- From Warehouse --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">From Warehouse <span class="text-red-500">*</span></label>
                            <select name="from_warehouse_id" id="from_warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('from_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('from_warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- To Warehouse --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">To Warehouse <span class="text-red-500">*</span></label>
                            <select name="to_warehouse_id" id="to_warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('to_warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Transfer Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transfer Date <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="transfer_date" value="{{ old('transfer_date', now()->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            @error('transfer_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Expected Arrival Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expected Arrival Date</label>
                            <input type="datetime-local" name="expected_arrival_date" value="{{ old('expected_arrival_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('expected_arrival_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Vehicle --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle</label>
                            <select name="vehicle_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->vehicle_number }} - {{ $vehicle->vehicle_type ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Driver --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Driver</label>
                            <select name="driver_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('driver_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    {{-- Notes --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Additional notes or instructions...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Transfer Items --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-boxes text-purple-600 mr-2"></i>
                            Transfer Items
                        </h2>
                        <button type="button" onclick="addItem()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>
                    
                    <div id="items-container" class="space-y-4">
                        {{-- Items will be added here dynamically --}}
                    </div>
                    
                    <div id="no-items-message" class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-boxes text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Items Added</h3>
                        <p class="text-gray-600 mb-4">Click "Add Item" to start adding products to this transfer order</p>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Summary Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Summary</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Items:</span>
                            <span class="font-bold text-gray-900" id="total-items">0</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Quantity:</span>
                            <span class="font-bold text-gray-900" id="total-quantity">0</span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Create Transfer Order
                        </button>
                        
                        <a href="{{ route('operations.transfer-orders.index') }}" class="w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold inline-block text-center">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>

                {{-- Help Card --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h3 class="text-sm font-bold text-blue-800 mb-2 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Quick Tips
                    </h3>
                    <ul class="text-xs text-blue-700 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                            <span>Select different warehouses for source and destination</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                            <span>Add at least one item to the transfer order</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                            <span>Assign vehicle and driver for transportation</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                            <span>Set expected arrival date for tracking</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>

    </form>

</div>

{{-- Item Template --}}
<template id="item-template">
    <div class="item-row border border-gray-200 rounded-lg p-4 bg-gray-50">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-semibold text-gray-800">Item <span class="item-number"></span></h4>
            <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Product --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product <span class="text-red-500">*</span></label>
                <select name="items[INDEX][product_id]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
            
            {{-- From Storage Bin --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Storage Bin</label>
                <input type="number" name="items[INDEX][from_storage_bin_id]" placeholder="Bin ID" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Optional</p>
            </div>
            
            {{-- To Storage Bin --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Storage Bin</label>
                <input type="number" name="items[INDEX][to_storage_bin_id]" placeholder="Bin ID" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Optional</p>
            </div>
            
            {{-- Batch Number --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                <input type="text" name="items[INDEX][batch_number]" placeholder="BATCH-001" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            {{-- Serial Number --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                <input type="text" name="items[INDEX][serial_number]" placeholder="SN-001" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            {{-- Quantity --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                <input type="number" name="items[INDEX][quantity_requested]" min="1" placeholder="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 item-quantity" required onchange="updateSummary()">
            </div>
            
            {{-- Unit of Measure --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit <span class="text-red-500">*</span></label>
                <select name="items[INDEX][unit_of_measure]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">Select Unit</option>
                    <option value="PCS">PCS</option>
                    <option value="BOX">BOX</option>
                    <option value="CARTON">CARTON</option>
                    <option value="PALLET">PALLET</option>
                    <option value="KG">KG</option>
                    <option value="LITER">LITER</option>
                </select>
            </div>
            
            {{-- Notes --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="items[INDEX][notes]" rows="2" placeholder="Item notes..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
let itemIndex = 0;

function addItem() {
    const template = document.getElementById('item-template');
    const container = document.getElementById('items-container');
    const noItemsMessage = document.getElementById('no-items-message');
    
    // Clone template
    const clone = template.content.cloneNode(true);
    
    // Replace INDEX with actual index
    const div = document.createElement('div');
    div.innerHTML = clone.firstElementChild.outerHTML.replace(/INDEX/g, itemIndex);
    
    // Add to container
    container.appendChild(div.firstChild);
    
    // Update item number
    const itemNumbers = document.querySelectorAll('.item-number');
    itemNumbers[itemNumbers.length - 1].textContent = '#' + (itemIndex + 1);
    
    // Hide no items message
    noItemsMessage.style.display = 'none';
    
    itemIndex++;
    updateSummary();
}

function removeItem(button) {
    const itemRow = button.closest('.item-row');
    itemRow.remove();
    
    // Update item numbers
    const itemNumbers = document.querySelectorAll('.item-number');
    itemNumbers.forEach((el, index) => {
        el.textContent = '#' + (index + 1);
    });
    
    // Show no items message if no items
    const container = document.getElementById('items-container');
    const noItemsMessage = document.getElementById('no-items-message');
    if (container.children.length === 0) {
        noItemsMessage.style.display = 'block';
    }
    
    updateSummary();
}

function updateSummary() {
    const quantities = document.querySelectorAll('.item-quantity');
    let totalItems = 0;
    let totalQuantity = 0;
    
    quantities.forEach(input => {
        if (input.value) {
            totalItems++;
            totalQuantity += parseInt(input.value) || 0;
        }
    });
    
    document.getElementById('total-items').textContent = totalItems;
    document.getElementById('total-quantity').textContent = totalQuantity.toLocaleString();
}

// Validate form before submit
document.getElementById('transferOrderForm').addEventListener('submit', function(e) {
    const container = document.getElementById('items-container');
    if (container.children.length === 0) {
        e.preventDefault();
        alert('Please add at least one item to the transfer order');
        return false;
    }
    
    const fromWarehouse = document.getElementById('from_warehouse_id').value;
    const toWarehouse = document.getElementById('to_warehouse_id').value;
    
    if (fromWarehouse === toWarehouse && fromWarehouse !== '') {
        e.preventDefault();
        alert('Source and destination warehouses must be different');
        return false;
    }
});

// Add first item on page load if there are old items (validation errors)
document.addEventListener('DOMContentLoaded', function() {
    @if(old('items'))
        @foreach(old('items') as $index => $item)
            addItem();
            // Set old values
            const lastItem = document.querySelector('#items-container .item-row:last-child');
            if (lastItem) {
                lastItem.querySelector('select[name*="product_id"]').value = '{{ $item['product_id'] ?? '' }}';
                lastItem.querySelector('input[name*="from_storage_bin_id"]').value = '{{ $item['from_storage_bin_id'] ?? '' }}';
                lastItem.querySelector('input[name*="to_storage_bin_id"]').value = '{{ $item['to_storage_bin_id'] ?? '' }}';
                lastItem.querySelector('input[name*="batch_number"]').value = '{{ $item['batch_number'] ?? '' }}';
                lastItem.querySelector('input[name*="serial_number"]').value = '{{ $item['serial_number'] ?? '' }}';
                lastItem.querySelector('input[name*="quantity_requested"]').value = '{{ $item['quantity_requested'] ?? '' }}';
                lastItem.querySelector('select[name*="unit_of_measure"]').value = '{{ $item['unit_of_measure'] ?? '' }}';
                lastItem.querySelector('textarea[name*="notes"]').value = '{{ $item['notes'] ?? '' }}';
            }
        @endforeach
        updateSummary();
    @endif
});
</script>
@endpush

@endsection