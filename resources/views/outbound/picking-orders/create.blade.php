{{-- resources/views/outbound/picking-orders/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Create Picking Order')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('outbound.picking-orders.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                    Create Picking Order
                </h1>
                <p class="text-sm text-gray-600 mt-1">Generate a new picking order</p>
            </div>
        </div>
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

    <form action="{{ route('outbound.picking-orders.store') }}" method="POST" id="pickingOrderForm" novalidate>
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2">
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-green-600 mr-2"></i>
                        Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sales Order <span class="text-red-500">*</span>
                            </label>
                            <select name="sales_order_id" id="salesOrderSelect" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                                <option value="">Select Sales Order...</option>
                                @foreach($salesOrders as $so)
                                    <option value="{{ $so->id }}" {{ old('sales_order_id') == $so->id ? 'selected' : '' }}>
                                        {{ $so->so_number }} - {{ $so->customer->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sales_order_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" id="warehouseSelect" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                                <option value="">Select Warehouse...</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Picking Date <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="picking_date" value="{{ old('picking_date', now()->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                            @error('picking_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Picking Type <span class="text-red-500">*</span>
                            </label>
                            <select name="picking_type" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                                <option value="single_order" {{ old('picking_type', 'single_order') == 'single_order' ? 'selected' : '' }}>Single Order</option>
                                <option value="batch" {{ old('picking_type') == 'batch' ? 'selected' : '' }}>Batch</option>
                                <option value="wave" {{ old('picking_type') == 'wave' ? 'selected' : '' }}>Wave</option>
                                <option value="zone" {{ old('picking_type') == 'zone' ? 'selected' : '' }}>Zone</option>
                            </select>
                            @error('picking_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <select name="priority" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Assign To (Optional)
                            </label>
                            <select name="assigned_to" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="Add any special instructions or notes...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Picking Items --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-list text-green-600 mr-2"></i>
                            Picking Items
                        </h2>
                    </div>

                    {{-- Loading State --}}
                    <div id="loadingState" class="hidden text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
                        <p class="text-gray-600 mt-3">Loading items...</p>
                    </div>

                    {{-- Items Container --}}
                    <div id="itemsContainer" class="space-y-4">
                        <div class="text-center py-8 text-gray-500" id="emptyState">
                            <i class="fas fa-box-open text-4xl mb-2"></i>
                            <p>Select a sales order to load items</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-calculator text-green-600 mr-2"></i>
                        Summary
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Items:</span>
                            <span class="text-lg font-semibold text-gray-900" id="totalItems">0</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Quantity:</span>
                            <span class="text-lg font-semibold text-gray-900" id="totalQuantity">0</span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <button type="submit" id="submitBtn" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <i class="fas fa-save mr-2"></i>Create Picking Order
                        </button>
                        <a href="{{ route('outbound.picking-orders.index') }}" class="block w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">
                            <i class="fas fa-info-circle mr-1"></i>Quick Tips
                        </h3>
                        <ul class="text-xs text-blue-800 space-y-1">
                            <li>• Select a sales order first</li>
                            <li>• Items will be auto-loaded</li>
                            <li>• Choose warehouse for inventory</li>
                            <li>• Verify storage bins carefully</li>
                            <li>• Assign picker for immediate start</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

{{-- Item Template --}}
<template id="itemTemplate">
    <div class="border border-gray-300 rounded-lg p-4 item-row bg-gray-50 hover:bg-gray-100 transition">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center">
                <span class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-semibold text-sm mr-3 item-sequence">1</span>
                <div>
                    <h4 class="font-semibold text-gray-900 item-product-name">Product Name</h4>
                    <p class="text-xs text-gray-500 item-product-code">SKU-000</p>
                </div>
            </div>
            <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800 transition">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <input type="hidden" class="item-sales-order-item-id" name="items[INDEX][sales_order_item_id]" value="">
            <input type="hidden" class="item-product-id" name="items[INDEX][product_id]" value="">
            
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Storage Bin <span class="text-red-500">*</span></label>
                <select name="items[INDEX][storage_bin_id]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 item-storage-bin" required>
                    <option value="">Select Bin...</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                <input type="number" name="items[INDEX][quantity_requested]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 item-quantity" min="1" required>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Batch Number</label>
                <input type="text" name="items[INDEX][batch_number]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 item-batch" placeholder="Optional">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Serial Number</label>
                <input type="text" name="items[INDEX][serial_number]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 item-serial" placeholder="Optional">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="items[INDEX][expiry_date]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 item-expiry">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">UOM <span class="text-red-500">*</span></label>
                <input type="text" name="items[INDEX][unit_of_measure]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 item-uom" required>
            </div>
        </div>
    </div>
</template>

<script>
let itemIndex = 0;
let currentItems = [];

// Sales Order Change Event
document.getElementById('salesOrderSelect').addEventListener('change', function() {
    const salesOrderId = this.value;
    const warehouseId = document.getElementById('warehouseSelect').value;
    
    console.log('Sales Order Changed:', { salesOrderId, warehouseId });
    
    if (salesOrderId) {
        if (!warehouseId) {
            alert('Please select a warehouse first');
            this.value = '';
            return;
        }
        loadSalesOrderItems(salesOrderId, warehouseId);
    } else {
        resetItems();
    }
});

// Warehouse Change Event
document.getElementById('warehouseSelect').addEventListener('change', function() {
    const salesOrderId = document.getElementById('salesOrderSelect').value;
    const warehouseId = this.value;
    
    console.log('Warehouse Changed:', { salesOrderId, warehouseId });
    
    if (salesOrderId && warehouseId) {
        loadSalesOrderItems(salesOrderId, warehouseId);
    }
});

// Load Sales Order Items via AJAX
function loadSalesOrderItems(salesOrderId, warehouseId) {
    showLoading();
    
    const url = `/picking-orders/sales-order/${salesOrderId}/items?warehouse_id=${warehouseId}`;
    console.log('Fetching from URL:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
        .then(response => {
            console.log('Response Status:', response.status);
            console.log('Response Headers:', response.headers);
            
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Failed to load items');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response Data:', data);
            hideLoading();
            
            if (data.success) {
                if (data.data && data.data.length > 0) {
                    currentItems = data.data;
                    renderItems(data.data);
                } else {
                    showWarning('No items found for this sales order or no inventory available in selected warehouse');
                    resetItems();
                }
            } else {
                showError(data.message || 'Failed to load items');
                resetItems();
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Fetch Error:', error);
            showError('Failed to load sales order items: ' + error.message);
            resetItems();
        });
}

// Render Items
function renderItems(items) {
    const container = document.getElementById('itemsContainer');
    container.innerHTML = '';
    itemIndex = 0;
    
    console.log('Rendering items:', items.length);
    
    if (!items || items.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-2"></i>
                <p>No items found for this sales order</p>
            </div>`;
        updateSummary();
        return;
    }

    items.forEach((item, index) => {
        console.log('Adding item:', item);
        addItemRow(item, index);
    });
    
    updateSummary();
}

// Add Item Row
function addItemRow(soItem, index) {
    const template = document.getElementById('itemTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update sequence
    clone.querySelector('.item-sequence').textContent = index + 1;
    
    // Update product info
    clone.querySelector('.item-product-name').textContent = soItem.product_name || 'Unknown Product';
    clone.querySelector('.item-product-code').textContent = soItem.product_code || 'N/A';
    
    // Set hidden values
    clone.querySelector('.item-sales-order-item-id').value = soItem.id;
    clone.querySelector('.item-product-id').value = soItem.product_id;
    clone.querySelector('.item-quantity').value = soItem.quantity_ordered;
    clone.querySelector('.item-uom').value = soItem.unit_of_measure;
    
    // Update name attributes with index
    clone.querySelectorAll('[name*="INDEX"]').forEach(input => {
        input.name = input.name.replace('INDEX', itemIndex);
    });
    
    // Populate storage bins
    const binSelect = clone.querySelector('.item-storage-bin');
    binSelect.innerHTML = '<option value="">Select Bin...</option>';
    
    console.log('Item inventories:', soItem.inventories);
    
    if (soItem.inventories && soItem.inventories.length > 0) {
        soItem.inventories.forEach(inv => {
            const option = document.createElement('option');
            option.value = inv.storage_bin_id;
            option.textContent = `${inv.storage_bin_name} (Avail: ${inv.quantity_available})`;
            option.dataset.batch = inv.batch_number || '';
            option.dataset.serial = inv.serial_number || '';
            option.dataset.expiry = inv.expiry_date || '';
            option.dataset.maxQty = inv.quantity_available || 0;
            binSelect.appendChild(option);
        });
        
        // Auto-select first bin and populate related fields
        if (soItem.inventories.length > 0) {
            const firstInv = soItem.inventories[0];
            binSelect.value = firstInv.storage_bin_id;
            
            const batchInput = clone.querySelector('.item-batch');
            const serialInput = clone.querySelector('.item-serial');
            const expiryInput = clone.querySelector('.item-expiry');
            
            if (batchInput) batchInput.value = firstInv.batch_number || '';
            if (serialInput) serialInput.value = firstInv.serial_number || '';
            if (expiryInput) expiryInput.value = firstInv.expiry_date || '';
        }
    } else {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'No inventory available';
        option.disabled = true;
        binSelect.appendChild(option);
        binSelect.disabled = true;
        
        // Show warning on the row
        const row = clone.querySelector('.item-row');
        const warningDiv = document.createElement('div');
        warningDiv.className = 'col-span-2 mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-800';
        warningDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> No inventory available in selected warehouse';
        row.querySelector('.grid').appendChild(warningDiv);
    }
    
    // Add event listener for bin selection change
    binSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const row = this.closest('.item-row');
        
        if (selectedOption.value) {
            const batchInput = row.querySelector('.item-batch');
            const serialInput = row.querySelector('.item-serial');
            const expiryInput = row.querySelector('.item-expiry');
            
            if (batchInput) batchInput.value = selectedOption.dataset.batch || '';
            if (serialInput) serialInput.value = selectedOption.dataset.serial || '';
            if (expiryInput) expiryInput.value = selectedOption.dataset.expiry || '';
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
        const qty = parseInt(qtyInput?.value || 0);
        totalQuantity += qty;
    });
    
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('totalQuantity').textContent = totalQuantity.toLocaleString();
    
    // Enable/disable submit button
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = totalItems === 0;
    }
}

// Show Loading
function showLoading() {
    const loadingState = document.getElementById('loadingState');
    if (loadingState) {
        loadingState.classList.remove('hidden');
    }
    document.getElementById('itemsContainer').innerHTML = '';
}

// Hide Loading
function hideLoading() {
    const loadingState = document.getElementById('loadingState');
    if (loadingState) {
        loadingState.classList.add('hidden');
    }
}

// Reset Items
function resetItems() {
    const container = document.getElementById('itemsContainer');
    container.innerHTML = `
        <div class="text-center py-8 text-gray-500" id="emptyState">
            <i class="fas fa-box-open text-4xl mb-2"></i>
            <p>Select a sales order to load items</p>
        </div>`;
    currentItems = [];
    updateSummary();
}

// Show Error
function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center justify-between';
    errorDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>${message}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    const form = document.getElementById('pickingOrderForm');
    if (form) {
        form.insertBefore(errorDiv, form.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
}

// Show Warning
function showWarning(message) {
    const warningDiv = document.createElement('div');
    warningDiv.className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-4 flex items-center justify-between';
    warningDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span>${message}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-yellow-700 hover:text-yellow-900">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    const form = document.getElementById('pickingOrderForm');
    if (form) {
        form.insertBefore(warningDiv, form.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            warningDiv.remove();
        }, 5000);
    }
}

// Listen to quantity changes
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('item-quantity')) {
        updateSummary();
    }
});

// Form submission validation
document.getElementById('pickingOrderForm').addEventListener('submit', function(e) {
    const items = document.querySelectorAll('.item-row');
    
    console.log('Form submitting, items count:', items.length);
    
    if (items.length === 0) {
        e.preventDefault();
        alert('Please add at least one item to the picking order');
        return false;
    }
    
    // Check if all items have valid storage bins
    let hasInvalidBin = false;
    let invalidItems = [];
    
    items.forEach((item, index) => {
        const binSelect = item.querySelector('.item-storage-bin');
        if (!binSelect || !binSelect.value) {
            hasInvalidBin = true;
            invalidItems.push(index + 1);
        }
    });
    
    if (hasInvalidBin) {
        e.preventDefault();
        alert(`Please select a storage bin for all items. Missing bins on item(s): ${invalidItems.join(', ')}`);
        return false;
    }
    
    // Validate quantities
    let hasInvalidQty = false;
    items.forEach((item, index) => {
        const qtyInput = item.querySelector('.item-quantity');
        const qty = parseInt(qtyInput?.value || 0);
        if (qty <= 0) {
            hasInvalidQty = true;
        }
    });
    
    if (hasInvalidQty) {
        e.preventDefault();
        alert('Please ensure all quantities are greater than 0');
        return false;
    }
    
    console.log('Form validation passed');
    return true;
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing...');
    updateSummary();
    
    // Check if CSRF token exists
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        console.log('CSRF Token found');
    } else {
        console.warn('CSRF Token not found in meta tag');
    }
});
</script>

@endsection