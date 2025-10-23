{{-- resources/views/inventory/adjustments/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Stock Adjustment')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-blue-600 mr-2"></i>
                Edit Stock Adjustment
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $adjustment->adjustment_number }}</p>
        </div>
        <a href="{{ route('inventory.adjustments.show', $adjustment) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Details
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Please correct the following errors:</span>
            </div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventory.adjustments.update', $adjustment) }}" method="POST" id="adjustmentForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adjustment Number *</label>
                            <input type="text" value="{{ $adjustment->adjustment_number }}" readonly class="w-full rounded-lg border-gray-300 bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adjustment Date *</label>
                            <input type="datetime-local" name="adjustment_date" value="{{ old('adjustment_date', $adjustment->adjustment_date->format('Y-m-d\TH:i')) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse *</label>
                            <select name="warehouse_id" id="warehouse_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $adjustment->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adjustment Type *</label>
                            <select name="adjustment_type" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Type</option>
                                <option value="addition" {{ old('adjustment_type', $adjustment->adjustment_type) === 'addition' ? 'selected' : '' }}>Addition</option>
                                <option value="reduction" {{ old('adjustment_type', $adjustment->adjustment_type) === 'reduction' ? 'selected' : '' }}>Reduction</option>
                                <option value="correction" {{ old('adjustment_type', $adjustment->adjustment_type) === 'correction' ? 'selected' : '' }}>Correction</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason *</label>
                            <select name="reason" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Reason</option>
                                <option value="damaged" {{ old('reason', $adjustment->reason) === 'damaged' ? 'selected' : '' }}>Damaged</option>
                                <option value="expired" {{ old('reason', $adjustment->reason) === 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="lost" {{ old('reason', $adjustment->reason) === 'lost' ? 'selected' : '' }}>Lost</option>
                                <option value="found" {{ old('reason', $adjustment->reason) === 'found' ? 'selected' : '' }}>Found</option>
                                <option value="count_correction" {{ old('reason', $adjustment->reason) === 'count_correction' ? 'selected' : '' }}>Count Correction</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $adjustment->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Adjustment Items --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-box text-blue-600 mr-2"></i>
                            Adjustment Items
                        </h3>
                        <button type="button" onclick="addItem()" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                            <i class="fas fa-plus mr-1"></i>Add Item
                        </button>
                    </div>

                    <div id="items-container">
                        @foreach($adjustment->items as $index => $item)
                            <div class="item-row bg-gray-50 rounded-lg p-4 mb-3 border border-gray-200">
                                <div class="flex items-start justify-between mb-3">
                                    <h4 class="font-semibold text-gray-800">Item {{ $index + 1 }}</h4>
                                    <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                        <select name="items[{{ $index }}][product_id]" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }} ({{ $product->sku }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Storage Bin *</label>
                                        <select name="items[{{ $index }}][storage_bin_id]" required class="storage-bin-select w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">Select Storage Bin</option>
                                            {{-- Will be populated via JavaScript --}}
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                                        <input type="text" name="items[{{ $index }}][batch_number]" value="{{ $item->batch_number }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                                        <input type="text" name="items[{{ $index }}][serial_number]" value="{{ $item->serial_number }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Quantity *</label>
                                        <input type="number" name="items[{{ $index }}][current_quantity]" value="{{ $item->current_quantity }}" min="0" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="calculateDifference(this)">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adjusted Quantity *</label>
                                        <input type="number" name="items[{{ $index }}][adjusted_quantity]" value="{{ $item->adjusted_quantity }}" min="0" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="calculateDifference(this)">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit of Measure *</label>
                                        <input type="text" name="items[{{ $index }}][unit_of_measure]" value="{{ $item->unit_of_measure }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="PCS, KG, etc">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Difference</label>
                                        <input type="text" class="difference-display w-full rounded-lg border-gray-300 bg-gray-100 text-sm font-semibold" readonly value="{{ $item->adjusted_quantity - $item->current_quantity }}">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Item Reason</label>
                                        <input type="text" name="items[{{ $index }}][reason]" value="{{ $item->reason }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Item Notes</label>
                                        <textarea name="items[{{ $index }}][notes]" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $item->notes }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="empty-state" style="display: none;" class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-box text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-600 mb-3">No items added yet</p>
                        <button type="button" onclick="addItem()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-2"></i>Add First Item
                        </button>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Actions --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-cog text-blue-600 mr-2"></i>
                        Actions
                    </h3>
                    
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Update Adjustment
                        </button>
                        
                        <a href="{{ route('inventory.adjustments.show', $adjustment) }}" class="block w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                        Summary
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b">
                            <span class="text-gray-600">Total Items:</span>
                            <span class="font-semibold text-gray-900" id="total-items">{{ $adjustment->items->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <span class="w-2 h-2 bg-gray-500 rounded-full mr-1"></span>
                                Draft
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>

{{-- Item Template --}}
<template id="item-template">
    <div class="item-row bg-gray-50 rounded-lg p-4 mb-3 border border-gray-200">
        <div class="flex items-start justify-between mb-3">
            <h4 class="font-semibold text-gray-800">Item <span class="item-number"></span></h4>
            <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                <select name="items[INDEX][product_id]" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Storage Bin *</label>
                <select name="items[INDEX][storage_bin_id]" required class="storage-bin-select w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" disabled>
                    <option value="">Select Storage Bin</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                <input type="text" name="items[INDEX][batch_number]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                <input type="text" name="items[INDEX][serial_number]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Quantity *</label>
                <input type="number" name="items[INDEX][current_quantity]" min="0" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="calculateDifference(this)">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adjusted Quantity *</label>
                <input type="number" name="items[INDEX][adjusted_quantity]" min="0" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="calculateDifference(this)">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit of Measure *</label>
                <input type="text" name="items[INDEX][unit_of_measure]" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="PCS, KG, etc">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Difference</label>
                <input type="text" class="difference-display w-full rounded-lg border-gray-300 bg-gray-100 text-sm font-semibold" readonly value="0">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Reason</label>
                <input type="text" name="items[INDEX][reason]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Notes</label>
                <textarea name="items[INDEX][notes]" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
let itemIndex = {{ $adjustment->items->count() }};

// Load storage bins for existing items on page load
document.addEventListener('DOMContentLoaded', function() {
    const warehouseId = document.getElementById('warehouse_id').value;
    if (warehouseId) {
        loadStorageBinsForExistingItems(warehouseId);
    }
});

function loadStorageBinsForExistingItems(warehouseId) {
    const storageBinSelects = document.querySelectorAll('.storage-bin-select');
    
    fetch(`/inventory/warehouses/${warehouseId}/storage-bins`)
        .then(response => response.json())
        .then(data => {
            storageBinSelects.forEach((select, index) => {
                const selectedValue = {{ json_encode($adjustment->items->pluck('storage_bin_id')->toArray()) }}[index];
                select.innerHTML = '<option value="">Select Storage Bin</option>';
                data.forEach(bin => {
                    const selected = bin.id == selectedValue ? 'selected' : '';
                    select.innerHTML += `<option value="${bin.id}" ${selected}>${bin.bin_code} - ${bin.bin_name}</option>`;
                });
                select.disabled = false;
            });
        });
}

// Warehouse change handler
document.getElementById('warehouse_id').addEventListener('change', function() {
    const warehouseId = this.value;
    const storageBinSelects = document.querySelectorAll('.storage-bin-select');
    
    storageBinSelects.forEach(select => {
        select.innerHTML = '<option value="">Loading...</option>';
        select.disabled = true;
        
        if (warehouseId) {
            fetch(`/inventory/warehouses/${warehouseId}/storage-bins`)
                .then(response => response.json())
                .then(data => {
                    select.innerHTML = '<option value="">Select Storage Bin</option>';
                    data.forEach(bin => {
                        select.innerHTML += `<option value="${bin.id}">${bin.bin_code} - ${bin.bin_name}</option>`;
                    });
                    select.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    select.innerHTML = '<option value="">Error loading bins</option>';
                });
        } else {
            select.innerHTML = '<option value="">Select Storage Bin</option>';
            select.disabled = true;
        }
    });
});

function addItem() {
    const warehouseId = document.getElementById('warehouse_id').value;
    if (!warehouseId) {
        alert('Please select a warehouse first');
        return;
    }

    const template = document.getElementById('item-template');
    const clone = template.content.cloneNode(true);
    
    // Replace INDEX with actual index
    clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
        el.name = el.name.replace('INDEX', itemIndex);
    });
    
    clone.querySelector('.item-number').textContent = itemIndex + 1;
    
    document.getElementById('items-container').appendChild(clone);
    document.getElementById('empty-state').style.display = 'none';
    
    // Load storage bins for new item
    const lastStorageBinSelect = document.querySelectorAll('.storage-bin-select');
    const newSelect = lastStorageBinSelect[lastStorageBinSelect.length - 1];
    
    fetch(`/inventory/warehouses/${warehouseId}/storage-bins`)
        .then(response => response.json())
        .then(data => {
            newSelect.innerHTML = '<option value="">Select Storage Bin</option>';
            data.forEach(bin => {
                newSelect.innerHTML += `<option value="${bin.id}">${bin.bin_code} - ${bin.bin_name}</option>`;
            });
            newSelect.disabled = false;
        });
    
    itemIndex++;
    updateSummary();
}

function removeItem(button) {
    button.closest('.item-row').remove();
    updateSummary();
    
    if (document.querySelectorAll('.item-row').length === 0) {
        document.getElementById('empty-state').style.display = 'block';
    }
}

function calculateDifference(input) {
    const row = input.closest('.item-row');
    const currentQty = parseInt(row.querySelector('[name*="current_quantity"]').value) || 0;
    const adjustedQty = parseInt(row.querySelector('[name*="adjusted_quantity"]').value) || 0;
    const difference = adjustedQty - currentQty;
    
    const diffDisplay = row.querySelector('.difference-display');
    diffDisplay.value = difference;
    
    if (difference > 0) {
        diffDisplay.classList.add('text-green-600');
        diffDisplay.classList.remove('text-red-600');
    } else if (difference < 0) {
        diffDisplay.classList.add('text-red-600');
        diffDisplay.classList.remove('text-green-600');
    } else {
        diffDisplay.classList.remove('text-green-600', 'text-red-600');
    }
}

function updateSummary() {
    const itemCount = document.querySelectorAll('.item-row').length;
    document.getElementById('total-items').textContent = itemCount;
}

// Form validation
document.getElementById('adjustmentForm').addEventListener('submit', function(e) {
    const itemCount = document.querySelectorAll('.item-row').length;
    if (itemCount === 0) {
        e.preventDefault();
        alert('Please add at least one item');
        return false;
    }
});

// Calculate differences for existing items on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.item-row').forEach(row => {
        const currentQtyInput = row.querySelector('[name*="current_quantity"]');
        if (currentQtyInput) {
            calculateDifference(currentQtyInput);
        }
    });
});
</script>
@endpush
@endsection