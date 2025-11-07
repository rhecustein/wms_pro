@extends('layouts.app')

@section('title', 'Edit Return Order')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-yellow-600 mr-2"></i>
                Edit Return Order
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $return->return_number }}</p>
        </div>
        <a href="{{ route('outbound.returns.show', $return) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back
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

    <form action="{{ route('outbound.returns.update', $return) }}" method="POST" id="returnForm">
        @csrf
        @method('PUT')

        {{-- Basic Information --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Basic Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Return Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Return Date <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="return_date" value="{{ old('return_date', $return->return_date->format('Y-m-d\TH:i')) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Return Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Return Type <span class="text-red-500">*</span>
                    </label>
                    <select name="return_type" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Return Type</option>
                        <option value="customer_return" {{ old('return_type', $return->return_type) === 'customer_return' ? 'selected' : '' }}>Customer Return</option>
                        <option value="damaged" {{ old('return_type', $return->return_type) === 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="expired" {{ old('return_type', $return->return_type) === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="wrong_item" {{ old('return_type', $return->return_type) === 'wrong_item' ? 'selected' : '' }}>Wrong Item</option>
                    </select>
                </div>

                {{-- Notes --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Additional notes...">{{ old('notes', $return->notes) }}</textarea>
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
                @foreach($return->items as $index => $item)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 item-row" data-index="{{ $index }}">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-800">Item #{{ $index + 1 }}</h3>
                            <button type="button" onclick="removeItem({{ $index }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Product <span class="text-red-500">*</span>
                                </label>
                                <select name="items[{{ $index }}][product_id]" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 product-select" data-index="{{ $index }}">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price ?? 0 }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}{{ $product->sku ? ' - ' . $product->sku : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantity Returned <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="items[{{ $index }}][quantity_returned]" value="{{ $item->quantity_returned }}" min="1" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Condition <span class="text-red-500">*</span>
                                </label>
                                <select name="items[{{ $index }}][condition]" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Condition</option>
                                    <option value="good" {{ $item->condition === 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="damaged" {{ $item->condition === 'damaged' ? 'selected' : '' }}>Damaged</option>
                                    <option value="expired" {{ $item->condition === 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="defective" {{ $item->condition === 'defective' ? 'selected' : '' }}>Defective</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                                <input type="text" name="items[{{ $index }}][batch_number]" value="{{ $item->batch_number }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                                <input type="text" name="items[{{ $index }}][serial_number]" value="{{ $item->serial_number }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                                <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" id="price-{{ $index }}">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Return Reason</label>
                                <textarea name="items[{{ $index }}][return_reason]" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ $item->return_reason }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="emptyState" class="text-center py-8" style="display: none;">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-600">No items added yet. Click "Add Item" to start.</p>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('outbound.returns.show', $return) }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Update Return Order
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
let itemIndex = {{ count($return->items) }};
const products = @json($products);

// Add event listeners to existing product selects
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.product-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const index = this.getAttribute('data-index');
            const priceInput = document.getElementById(`price-${index}`);
            if (priceInput && !priceInput.value) {
                priceInput.value = price;
            }
        });
    });
});

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
                    <select name="items[${itemIndex}][product_id]" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 product-select" data-index="${itemIndex}">
                        <option value="">Select Product</option>
                        ${products.map(p => `<option value="${p.id}" data-price="${p.price || 0}">${p.name}${p.sku ? ' - ' + p.sku : ''}</option>`).join('')}
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity Returned <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="items[${itemIndex}][quantity_returned]" min="1" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                        <option value="defective">Defective</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                    <input type="text" name="items[${itemIndex}][batch_number]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                    <input type="text" name="items[${itemIndex}][serial_number]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                    <input type="number" name="items[${itemIndex}][unit_price]" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" id="price-${itemIndex}">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Return Reason</label>
                    <textarea name="items[${itemIndex}][return_reason]" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    
    // Add event listener for new product select
    const newSelect = document.querySelector(`select[name="items[${itemIndex}][product_id]"]`);
    newSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        const index = this.getAttribute('data-index');
        document.getElementById(`price-${index}`).value = price;
    });
    
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
</script>
@endpush

@endsection