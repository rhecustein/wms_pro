@extends('layouts.app')

@section('title', 'Create Return Order')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-undo-alt text-red-600 mr-2"></i>
                Create Return Order
            </h1>
            <p class="text-sm text-gray-600 mt-1">Process customer returns and damaged goods</p>
        </div>
        <a href="{{ route('outbound.returns.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
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

    <form action="{{ route('outbound.returns.store') }}" method="POST" id="returnForm">
        @csrf

        {{-- Basic Information --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Basic Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Delivery Order --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Delivery Order <span class="text-gray-400">(Optional)</span>
                    </label>
                    <select name="delivery_order_id" id="delivery_order_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Delivery Order</option>
                        @foreach($deliveryOrders as $do)
                            <option value="{{ $do->id }}" {{ old('delivery_order_id') == $do->id ? 'selected' : '' }}>
                                {{ $do->do_number }} - {{ $do->customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Warehouse --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Warehouse <span class="text-red-500">*</span>
                    </label>
                    <select name="warehouse_id" id="warehouse_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Customer --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Customer <span class="text-red-500">*</span>
                    </label>
                    <select name="customer_id" id="customer_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->code ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Return Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Return Date <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="return_date" value="{{ old('return_date', now()->format('Y-m-d\TH:i')) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Return Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Return Type <span class="text-red-500">*</span>
                    </label>
                    <select name="return_type" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Return Type</option>
                        <option value="customer_return" {{ old('return_type') === 'customer_return' ? 'selected' : '' }}>Customer Return</option>
                        <option value="damaged" {{ old('return_type') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="expired" {{ old('return_type') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="wrong_item" {{ old('return_type') === 'wrong_item' ? 'selected' : '' }}>Wrong Item</option>
                    </select>
                </div>

                {{-- Notes --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Return Items --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-box text-red-600 mr-2"></i>
                    Return Items
                </h2>
                <button type="button" onclick="addItem()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Add Item
                </button>
            </div>

            <div id="itemsContainer">
                {{-- Items will be added here dynamically --}}
            </div>

            <div id="emptyState" class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-600">No items added yet. Click "Add Item" to start.</p>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('outbound.returns.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Create Return Order
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
let itemIndex = 0;
const products = @json($products);

function addItem() {
    const container = document.getElementById('itemsContainer');
    const emptyState = document.getElementById('emptyState');
    
    emptyState.style.display = 'none';
    
    const itemHtml = `
        <div class="border border-gray-200 rounded-lg p-4 mb-4 item-row" data-index="${itemIndex}">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Item #${itemIndex + 1}</h3>
                <button type="button" onclick="removeItem(${itemIndex})" class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Product <span class="text-red-500">*</span>
                    </label>
                    <select name="items[${itemIndex}][product_id]" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Product</option>
                        ${products.map(p => `<option value="${p.id}">${p.name} - ${p.sku || ''}</option>`).join('')}
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity Returned <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="items[${itemIndex}][quantity_returned]" min="1" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="0">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Condition <span class="text-red-500">*</span>
                    </label>
                    <select name="items[${itemIndex}][condition]" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Condition</option>
                        <option value="good">Good</option>
                        <option value="damaged">Damaged</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                    <input type="text" name="items[${itemIndex}][batch_number]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Batch number">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                    <input type="text" name="items[${itemIndex}][serial_number]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Serial number">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Return Reason</label>
                    <textarea name="items[${itemIndex}][return_reason]" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Reason for return..."></textarea>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

function removeItem(index) {
    const item = document.querySelector(`[data-index="${index}"]`);
    if (item) {
        item.remove();
    }
    
    const container = document.getElementById('itemsContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (container.children.length === 0) {
        emptyState.style.display = 'block';
    }
}

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});
</script>
@endpush

@endsection