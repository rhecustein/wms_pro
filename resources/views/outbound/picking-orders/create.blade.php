{{-- resources/views/outbound/picking-orders/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Create Picking Order')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
        
        {{-- Modern Header --}}
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('outbound.picking-orders.index') }}" 
                   class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent flex items-center">
                        <i class="fas fa-plus-circle text-green-600 mr-3"></i>
                        Create Picking Order
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Generate a new picking order for warehouse operations</p>
                </div>
            </div>
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl p-5 shadow-lg animate-fade-in">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-800 mb-2">Please fix the following errors:</h3>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm text-red-700">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('outbound.picking-orders.store') }}" method="POST" id="pickingOrderForm" novalidate>
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Form --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Basic Information Card --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Basic Information</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Warehouse --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Warehouse <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="warehouse_id" id="warehouseSelect" 
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all" 
                                            required>
                                        <option value="">Select Warehouse First...</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-warehouse absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('warehouse_id')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Sales Order --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Sales Order <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="sales_order_id" id="salesOrderSelect" 
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all" 
                                            required disabled>
                                        <option value="">Select Warehouse First...</option>
                                        @foreach($salesOrders as $so)
                                            <option value="{{ $so->id }}" {{ old('sales_order_id') == $so->id ? 'selected' : '' }}>
                                                {{ $so->so_number }} - {{ $so->customer->name ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-file-invoice absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('sales_order_id')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Picking Date --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Picking Date <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="datetime-local" name="picking_date" 
                                           value="{{ old('picking_date', now()->format('Y-m-d\TH:i')) }}" 
                                           class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all" 
                                           required>
                                    <i class="fas fa-calendar-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('picking_date')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Picking Type --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Picking Type <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="picking_type" 
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all" 
                                            required>
                                        <option value="single_order" {{ old('picking_type', 'single_order') == 'single_order' ? 'selected' : '' }}>Single Order</option>
                                        <option value="batch" {{ old('picking_type') == 'batch' ? 'selected' : '' }}>Batch</option>
                                        <option value="wave" {{ old('picking_type') == 'wave' ? 'selected' : '' }}>Wave</option>
                                        <option value="zone" {{ old('picking_type') == 'zone' ? 'selected' : '' }}>Zone</option>
                                    </select>
                                    <i class="fas fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('picking_type')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Priority --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="priority" 
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all" 
                                            required>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    <i class="fas fa-flag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('priority')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Assign To --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Assign To (Optional)
                                </label>
                                <div class="relative">
                                    <select name="assigned_to" 
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                                        <option value="">Unassigned</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('assigned_to')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="mt-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3" 
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all" 
                                      placeholder="Add any special instructions or notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Picking Items Card --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-list text-white"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Picking Items</h2>
                            </div>
                        </div>

                        {{-- Loading State --}}
                        <div id="loadingState" class="hidden text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mb-4"></div>
                            <p class="text-gray-600 font-semibold">Loading items...</p>
                        </div>

                        {{-- Items Container --}}
                        <div id="itemsContainer" class="space-y-4">
                            <div class="text-center py-12 text-gray-500" id="emptyState">
                                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-box-open text-4xl text-gray-400"></i>
                                </div>
                                <p class="font-semibold text-gray-700">Select a sales order to load items</p>
                                <p class="text-sm text-gray-500 mt-1">Choose a warehouse first, then select a sales order</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 sticky top-6">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-calculator text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Summary</h2>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border-2 border-blue-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-700">Total Items:</span>
                                    <span class="text-2xl font-bold text-blue-600" id="totalItems">0</span>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border-2 border-green-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-700">Total Quantity:</span>
                                    <span class="text-2xl font-bold text-green-600" id="totalQuantity">0</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            <button type="submit" id="submitBtn" 
                                    class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all font-bold shadow-lg hover:shadow-xl disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed disabled:shadow-none">
                                <i class="fas fa-save mr-2"></i>Create Picking Order
                            </button>
                            <a href="{{ route('outbound.picking-orders.index') }}" 
                               class="block w-full px-6 py-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all text-center font-bold">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>

                        {{-- Quick Tips --}}
                        <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border-2 border-blue-200">
                            <h3 class="text-sm font-bold text-blue-900 mb-3 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>Quick Tips
                            </h3>
                            <ul class="text-xs text-blue-800 space-y-2">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                                    <span>Select warehouse first before sales order</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                                    <span>Items will auto-load with inventory</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                                    <span>Verify locations carefully</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                                    <span>Assign picker for immediate start</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

{{-- Item Template --}}
<template id="itemTemplate">
    <div class="border-2 border-gray-200 rounded-xl p-4 item-row bg-gradient-to-r from-white to-gray-50 hover:from-blue-50 hover:to-indigo-50 transition-all shadow-sm hover:shadow-md">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center flex-1">
                <span class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-xl flex items-center justify-center font-bold text-sm mr-3 item-sequence shadow-md">1</span>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 item-product-name">Product Name</h4>
                    <p class="text-xs text-gray-500 item-product-code mt-0.5">SKU-000</p>
                    <p class="text-xs text-blue-600 mt-1 item-ordered-info">Ordered: <span class="font-semibold">0</span> | Remaining: <span class="font-semibold">0</span></p>
                </div>
            </div>
            <button type="button" onclick="removeItem(this)" class="w-8 h-8 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-all flex items-center justify-center">
                <i class="fas fa-trash text-sm"></i>
            </button>
        </div>

        <input type="hidden" class="item-sales-order-item-id" name="items[INDEX][sales_order_item_id]" value="">
        <input type="hidden" class="item-product-id" name="items[INDEX][product_id]" value="">
        
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                    Location <span class="text-red-500">*</span>
                </label>
                <select name="items[INDEX][location_id]" class="w-full text-sm px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all item-location" required>
                    <option value="">Select Location...</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                    Quantity <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" name="items[INDEX][quantity_requested]" class="w-full text-sm px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all item-quantity" min="0.01" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Batch Number</label>
                <input type="text" name="items[INDEX][batch_number]" class="w-full text-sm px-3 py-2 rounded-lg border-2 border-gray-200 bg-gray-50 item-batch" placeholder="Auto-filled" readonly>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Lot Number</label>
                <input type="text" name="items[INDEX][lot_number]" class="w-full text-sm px-3 py-2 rounded-lg border-2 border-gray-200 bg-gray-50 item-lot" placeholder="Auto-filled" readonly>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Serial Number</label>
                <input type="text" name="items[INDEX][serial_number]" class="w-full text-sm px-3 py-2 rounded-lg border-2 border-gray-200 bg-gray-50 item-serial" placeholder="Auto-filled" readonly>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Expiry Date</label>
                <input type="date" name="items[INDEX][expiry_date]" class="w-full text-sm px-3 py-2 rounded-lg border-2 border-gray-200 bg-gray-50 item-expiry" readonly>
            </div>

            <div class="col-span-2">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                    UOM <span class="text-red-500">*</span>
                </label>
                <input type="text" name="items[INDEX][unit_of_measure]" class="w-full text-sm px-3 py-2 rounded-lg border-2 border-gray-200 bg-gray-50 item-uom" required readonly>
            </div>
        </div>
    </div>
</template>

<script>
let itemIndex = 0;
let currentItems = [];

// Warehouse Change Event - Enable/Disable Sales Order
document.getElementById('warehouseSelect').addEventListener('change', function() {
    const salesOrderSelect = document.getElementById('salesOrderSelect');
    const warehouseId = this.value;
    
    if (warehouseId) {
        salesOrderSelect.disabled = false;
        salesOrderSelect.innerHTML = '<option value="">Select Sales Order...</option>' + 
            salesOrderSelect.innerHTML.replace('<option value="">Select Warehouse First...</option>', '');
    } else {
        salesOrderSelect.disabled = true;
        salesOrderSelect.value = '';
        salesOrderSelect.innerHTML = '<option value="">Select Warehouse First...</option>';
        resetItems();
    }
});

// Sales Order Change Event
document.getElementById('salesOrderSelect').addEventListener('change', function() {
    const salesOrderId = this.value;
    const warehouseId = document.getElementById('warehouseSelect').value;
    
    if (salesOrderId) {
        if (!warehouseId) {
            showAlert('warning', 'Please select a warehouse first');
            this.value = '';
            return;
        }
        loadSalesOrderItems(salesOrderId, warehouseId);
    } else {
        resetItems();
    }
});

// Load Sales Order Items via AJAX
function loadSalesOrderItems(salesOrderId, warehouseId) {
    showLoading();
    
    const url = `/outbound/picking-orders/sales-order/${salesOrderId}/items?warehouse_id=${warehouseId}`;
    console.log('📡 Loading from:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        console.log('📊 Response status:', response.status);
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Failed to load items');
            });
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        console.log('✅ Response data:', data);
        console.log('🔍 Debug info:', data.debug);
        
        if (data.success) {
            if (data.data && data.data.length > 0) {
                currentItems = data.data;
                renderItems(data.data);
                
                // Check if any items have no inventory
                const noInventoryCount = data.data.filter(item => !item.inventories || item.inventories.length === 0).length;
                if (noInventoryCount > 0) {
                    showAlert('warning', `Loaded ${data.data.length} items. ${noInventoryCount} item(s) have no inventory available.`);
                } else {
                    showAlert('success', `Loaded ${data.data.length} items successfully!`);
                }
            } else {
                showAlert('warning', 'No items found for this sales order or no inventory available in selected warehouse');
                resetItems();
            }
        } else {
            showAlert('error', data.message || 'Failed to load items');
            resetItems();
        }
    })
    .catch(error => {
        hideLoading();
        console.error('❌ Error:', error);
        showAlert('error', 'Failed to load sales order items: ' + error.message);
        resetItems();
    });
}

// Render Items
function renderItems(items) {
    const container = document.getElementById('itemsContainer');
    container.innerHTML = '';
    itemIndex = 0;
    
    if (!items || items.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-4xl text-gray-400"></i>
                </div>
                <p class="font-semibold text-gray-700">No items found for this sales order</p>
            </div>`;
        updateSummary();
        return;
    }

    items.forEach((item, index) => {
        addItemRow(item, index);
    });
    
    updateSummary();
}

// Add Item Row - FIXED
function addItemRow(soItem, index) {
    console.log('Adding item row:', soItem);
    
    const template = document.getElementById('itemTemplate');
    const clone = template.content.cloneNode(true);
    
    // Set basic info
    clone.querySelector('.item-sequence').textContent = index + 1;
    clone.querySelector('.item-product-name').textContent = soItem.product_name || 'Unknown Product';
    clone.querySelector('.item-product-code').textContent = soItem.product_code || 'N/A';
    
    // Update ordered info
    const orderedInfo = clone.querySelector('.item-ordered-info');
    if (orderedInfo) {
        orderedInfo.innerHTML = `Ordered: <span class="font-semibold">${soItem.quantity_ordered || 0}</span> | Remaining: <span class="font-semibold">${soItem.remaining_quantity || 0}</span>`;
    }
    
    // Set hidden fields
    clone.querySelector('.item-sales-order-item-id').value = soItem.id;
    clone.querySelector('.item-product-id').value = soItem.product_id;
    clone.querySelector('.item-quantity').value = soItem.remaining_quantity || soItem.quantity_ordered;
    clone.querySelector('.item-uom').value = soItem.unit_of_measure || 'PCS';
    
    // Update name attributes with index
    clone.querySelectorAll('[name*="INDEX"]').forEach(input => {
        input.name = input.name.replace('INDEX', itemIndex);
    });
    
    // Populate locations - FIXED
    const locationSelect = clone.querySelector('.item-location');
    locationSelect.innerHTML = '<option value="">Select Location...</option>';
    
    console.log('📦 Item inventories:', soItem.inventories);
    
    if (soItem.inventories && soItem.inventories.length > 0) {
        soItem.inventories.forEach(inv => {
            console.log('📍 Inventory location_id:', inv.location_id, 'Available:', inv.quantity_available);
            
            const option = document.createElement('option');
            option.value = inv.location_id;
            option.textContent = `${inv.storage_bin_name || 'Unknown'} (Avail: ${inv.quantity_available || 0})`;
            option.dataset.batch = inv.batch_number || '';
            option.dataset.lot = inv.lot_number || '';
            option.dataset.serial = inv.serial_number || '';
            option.dataset.expiry = inv.expiry_date || '';
            option.dataset.maxQty = inv.quantity_available || 0;
            locationSelect.appendChild(option);
        });
        
        // Auto-select first location
        if (soItem.inventories.length > 0) {
            const firstInv = soItem.inventories[0];
            locationSelect.value = firstInv.location_id;
            
            // Auto-fill batch/lot/serial/expiry
            const batchInput = clone.querySelector('.item-batch');
            const lotInput = clone.querySelector('.item-lot');
            const serialInput = clone.querySelector('.item-serial');
            const expiryInput = clone.querySelector('.item-expiry');
            
            if (batchInput) batchInput.value = firstInv.batch_number || '';
            if (lotInput) lotInput.value = firstInv.lot_number || '';
            if (serialInput) serialInput.value = firstInv.serial_number || '';
            if (expiryInput) expiryInput.value = firstInv.expiry_date || '';
        }
    } else {
        // FIXED: Jangan disabled, biarkan user bisa pilih manual
        console.warn('⚠️ No inventory available for product:', soItem.product_name);
        const option = document.createElement('option');
        option.value = '';
        option.textContent = '⚠️ No inventory - Please check stock';
        option.className = 'text-orange-600';
        locationSelect.appendChild(option);
        // locationSelect.disabled = true; // REMOVED: Jangan disabled
        
        // Show warning di item row
        const row = clone.querySelector('.item-row');
        if (row) {
            row.classList.add('border-orange-300', 'bg-orange-50');
        }
    }
    
    // Add event listener for location selection change
    locationSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const row = this.closest('.item-row');
        
        if (selectedOption.value) {
            const batchInput = row.querySelector('.item-batch');
            const lotInput = row.querySelector('.item-lot');
            const serialInput = row.querySelector('.item-serial');
            const expiryInput = row.querySelector('.item-expiry');
            const qtyInput = row.querySelector('.item-quantity');
            
            if (batchInput) batchInput.value = selectedOption.dataset.batch || '';
            if (lotInput) lotInput.value = selectedOption.dataset.lot || '';
            if (serialInput) serialInput.value = selectedOption.dataset.serial || '';
            if (expiryInput) expiryInput.value = selectedOption.dataset.expiry || '';
            
            // Update max quantity
            if (qtyInput) {
                qtyInput.max = selectedOption.dataset.maxQty || 999999;
            }
        }
    });
    
    document.getElementById('itemsContainer').appendChild(clone);
    itemIndex++;
}

// Remove Item
function removeItem(button) {
    if (confirm('Are you sure you want to remove this item?')) {
        button.closest('.item-row').remove();
        updateSequence();
        updateSummary();
    }
}

// Update Sequence Numbers
function updateSequence() {
    document.querySelectorAll('.item-sequence').forEach((el, index) => {
        el.textContent = index + 1;
    });
}

// Update Summary
function updateSummary() {
    const items = document.querySelectorAll('.item-row');
    const totalItems = items.length;
    let totalQuantity = 0;
    
    items.forEach(item => {
        const qtyInput = item.querySelector('.item-quantity');
        const qty = parseFloat(qtyInput?.value || 0);
        totalQuantity += qty;
    });
    
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('totalQuantity').textContent = totalQuantity.toFixed(2);
    
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = totalItems === 0;
    }
}

function showLoading() {
    document.getElementById('loadingState')?.classList.remove('hidden');
    document.getElementById('itemsContainer').innerHTML = '';
}

function hideLoading() {
    document.getElementById('loadingState')?.classList.add('hidden');
}

function resetItems() {
    const container = document.getElementById('itemsContainer');
    container.innerHTML = `
        <div class="text-center py-12 text-gray-500" id="emptyState">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box-open text-4xl text-gray-400"></i>
            </div>
            <p class="font-semibold text-gray-700">Select a sales order to load items</p>
            <p class="text-sm text-gray-500 mt-1">Choose a warehouse first, then select a sales order</p>
        </div>`;
    currentItems = [];
    updateSummary();
}

function showAlert(type, message) {
    const colors = {
        error: { bg: 'from-red-50 to-rose-50', border: 'border-red-500', icon: 'bg-red-500', text: 'text-red-800', iconClass: 'fas fa-exclamation-circle' },
        warning: { bg: 'from-yellow-50 to-amber-50', border: 'border-yellow-500', icon: 'bg-yellow-500', text: 'text-yellow-800', iconClass: 'fas fa-exclamation-triangle' },
        success: { bg: 'from-green-50 to-emerald-50', border: 'border-green-500', icon: 'bg-green-500', text: 'text-green-800', iconClass: 'fas fa-check-circle' }
    };
    
    const color = colors[type] || colors.error;
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `mb-6 bg-gradient-to-r ${color.bg} border-l-4 ${color.border} rounded-xl p-4 shadow-lg animate-fade-in`;
    alertDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 ${color.icon} rounded-lg flex items-center justify-center mr-3">
                    <i class="${color.iconClass} text-white"></i>
                </div>
                <span class="${color.text} font-medium">${message}</span>
            </div>
            <button onclick="this.closest('div.mb-6').remove()" class="${color.text} hover:opacity-75 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    const form = document.getElementById('pickingOrderForm');
    if (form) {
        form.parentElement.insertBefore(alertDiv, form);
        setTimeout(() => alertDiv.remove(), 5000);
    }
}

// Listen to quantity changes
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('item-quantity')) {
        updateSummary();
        
        // Validate against max quantity
        const locationSelect = e.target.closest('.item-row').querySelector('.item-location');
        const selectedOption = locationSelect?.options[locationSelect.selectedIndex];
        
        if (selectedOption && selectedOption.dataset.maxQty) {
            const maxQty = parseFloat(selectedOption.dataset.maxQty);
            const currentQty = parseFloat(e.target.value || 0);
            
            if (currentQty > maxQty) {
                e.target.value = maxQty;
                showAlert('warning', `Quantity adjusted to available stock: ${maxQty}`);
                updateSummary();
            }
        }
    }
});

// Form submission validation
document.getElementById('pickingOrderForm').addEventListener('submit', function(e) {
    const items = document.querySelectorAll('.item-row');
    
    if (items.length === 0) {
        e.preventDefault();
        showAlert('error', 'Please add at least one item to the picking order');
        return false;
    }
    
    let hasError = false;
    let errorMessage = '';
    
    items.forEach((item, index) => {
        const locationSelect = item.querySelector('.item-location');
        if (!locationSelect || !locationSelect.value) {
            hasError = true;
            errorMessage = 'Please select a location for all items';
        }
        
        const qtyInput = item.querySelector('.item-quantity');
        const qty = parseFloat(qtyInput?.value || 0);
        if (qty <= 0) {
            hasError = true;
            errorMessage = 'Please ensure all items have quantities greater than 0';
        }
    });
    
    if (hasError) {
        e.preventDefault();
        showAlert('error', errorMessage);
        return false;
    }
    
    // Show loading on submit
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
    }
    
    return true;
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
    
    // Check if warehouse already selected on page load
    const warehouseSelect = document.getElementById('warehouseSelect');
    const salesOrderSelect = document.getElementById('salesOrderSelect');
    
    if (warehouseSelect.value) {
        salesOrderSelect.disabled = false;
    }
});
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection