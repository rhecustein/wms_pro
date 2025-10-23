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

    <form action="{{ route('outbound.picking-orders.store') }}" method="POST" id="pickingOrderForm">
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
                                    <option value="{{ $so->id }}" data-items='@json($so->items)' {{ old('sales_order_id') == $so->id ? 'selected' : '' }}>
                                        {{ $so->so_number }} - {{ $so->customer->name ?? '' }}
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
                            <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
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
                                <option value="single_order" {{ old('picking_type') == 'single_order' ? 'selected' : '' }}>Single Order</option>
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
                        <button type="button" onclick="addItem()" class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                            <i class="fas fa-plus mr-1"></i>Add Item
                        </button>
                    </div>

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
                        <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
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
                            <li>• Assign picker for immediate start</li>
                            <li>• Verify storage bins carefully</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

{{-- Item Template --}}
<template id="itemTemplate">
    <div class="border border-gray-300 rounded-lg p-4 item-row">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center">
                <span class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-semibold text-sm mr-3 item-sequence">1</span>
                <div>
                    <h4 class="font-semibold text-gray-900 item-product-name">Product Name</h4>
                    <p class="text-xs text-gray-500 item-product-sku">SKU</p>
                </div>
            </div>
            <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-900">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <input type="hidden" class="item-sales-order-item-id" name="items[INDEX][sales_order_item_id]" value="">
            <input type="hidden" class="item-product-id" name="items[INDEX][product_id]" value="">
            
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Storage Bin *</label>
                <select name="items[INDEX][storage_bin_id]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                    <option value="">Select Bin...</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Quantity *</label>
                <input type="number" name="items[INDEX][quantity_requested]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 item-quantity" min="1" required>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Batch Number</label>
                <input type="text" name="items[INDEX][batch_number]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Serial Number</label>
                <input type="text" name="items[INDEX][serial_number]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="items[INDEX][expiry_date]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">UOM *</label>
                <input type="text" name="items[INDEX][unit_of_measure]" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 item-uom" required>
            </div>
        </div>
    </div>
</template>

<script>
let itemIndex = 0;

document.getElementById('salesOrderSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        const items = JSON.parse(selectedOption.dataset.items || '[]');
        loadItems(items);
    } else {
        document.getElementById('itemsContainer').innerHTML = '<div class="text-center py-8 text-gray-500" id="emptyState"><i class="fas fa-box-open text-4xl mb-2"></i><p>Select a sales order to load items</p></div>';
    }
});

function loadItems(items) {
    const container = document.getElementById('itemsContainer');
    container.innerHTML = '';
    
    if (items.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500"><p>No items found for this sales order</p></div>';
        return;
    }

    items.forEach((item, index) => {
        addItemFromSO(item, index);
    });
    
    updateSummary();
}

function addItemFromSO(soItem, index) {
    const template = document.getElementById('itemTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update sequence
    clone.querySelector('.item-sequence').textContent = index + 1;
    
    // Update product info
    clone.querySelector('.item-product-name').textContent = soItem.product.name;
    clone.querySelector('.item-product-sku').textContent = soItem.product.sku;
    
    // Set hidden values
    clone.querySelector('.item-sales-order-item-id').value = soItem.id;
    clone.querySelector('.item-product-id').value = soItem.product_id;
    clone.querySelector('.item-quantity').value = soItem.quantity;
    clone.querySelector('.item-uom').value = soItem.unit_of_measure;
    
    // Update name attributes with index
    clone.querySelectorAll('[name*="INDEX"]').forEach(input => {
        input.name = input.name.replace('INDEX', itemIndex);
    });
    
    // Add storage bins (you should fetch these from your backend)
    const binSelect = clone.querySelector('select[name*="storage_bin_id"]');
    // For now, adding a placeholder - replace with actual bin data
    binSelect.innerHTML = '<option value="1">BIN-A-001</option><option value="2">BIN-A-002</option>';
    
    document.getElementById('itemsContainer').appendChild(clone);
    itemIndex++;
}

function addItem() {
    // Manual add item function - can be used for custom items
    alert('Please select a sales order first to load items');
}

function removeItem(button) {
    button.closest('.item-row').remove();
    updateSequence();
    updateSummary();
}

function updateSequence() {
    document.querySelectorAll('.item-sequence').forEach((el, index) => {
        el.textContent = index + 1;
    });
}

function updateSummary() {
    const items = document.querySelectorAll('.item-row');
    const totalItems = items.length;
    let totalQuantity = 0;
    
    items.forEach(item => {
        const qty = parseInt(item.querySelector('.item-quantity').value) || 0;
        totalQuantity += qty;
    });
    
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('totalQuantity').textContent = totalQuantity.toLocaleString();
}

// Listen to quantity changes
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('item-quantity')) {
        updateSummary();
    }
});
</script>

@endsection