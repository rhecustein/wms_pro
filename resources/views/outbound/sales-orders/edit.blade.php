{{-- resources/views/outbound/sales-orders/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Sales Order - ' . $salesOrder->so_number)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('outbound.sales-orders.show', $salesOrder) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                    Edit Sales Order
                </h1>
                <p class="text-sm text-gray-600 mt-1">{{ $salesOrder->so_number }}</p>
            </div>
        </div>
        <div>
            {!! $salesOrder->status_badge !!}
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('outbound.sales-orders.update', $salesOrder) }}" method="POST" id="salesOrderForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SO Number</label>
                            <input type="text" value="{{ $salesOrder->so_number }}" class="w-full rounded-lg border-gray-300 bg-gray-100" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Order Date <span class="text-red-500">*</span></label>
                            <input type="date" name="order_date" value="{{ old('order_date', $salesOrder->order_date->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer <span class="text-red-500">*</span></label>
                            <select name="customer_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $salesOrder->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse <span class="text-red-500">*</span></label>
                            <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $salesOrder->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Requested Delivery Date</label>
                            <input type="date" name="requested_delivery_date" value="{{ old('requested_delivery_date', $salesOrder->requested_delivery_date?->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Additional notes...">{{ old('notes', $salesOrder->notes) }}</textarea>
                    </div>
                </div>

                {{-- Shipping Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                        Shipping Information
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Address</label>
                            <textarea name="shipping_address" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Full shipping address">{{ old('shipping_address', $salesOrder->shipping_address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city', $salesOrder->shipping_city) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="City">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                                <input type="text" name="shipping_province" value="{{ old('shipping_province', $salesOrder->shipping_province) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Province">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code', $salesOrder->shipping_postal_code) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Postal Code">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-boxes text-blue-600 mr-2"></i>
                            Order Items <span class="text-red-500">*</span>
                        </h2>
                        <button type="button" onclick="addItem()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>

                    <div id="itemsContainer" class="space-y-4">
                        {{-- Existing items will be loaded here --}}
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Summary --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-calculator text-blue-600 mr-2"></i>
                        Summary
                    </h2>

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold text-gray-900" id="displaySubtotal">IDR 0.00</span>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Discount</label>
                            <input type="number" name="discount_amount" value="{{ old('discount_amount', $salesOrder->discount_amount) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="calculateTotal()">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Tax</label>
                            <input type="number" name="tax_amount" value="{{ old('tax_amount', $salesOrder->tax_amount) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="calculateTotal()">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Shipping Cost</label>
                            <input type="number" name="shipping_cost" value="{{ old('shipping_cost', $salesOrder->shipping_cost) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="calculateTotal()">
                        </div>

                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-base font-bold text-gray-900">Total Amount</span>
                                <span class="text-lg font-bold text-blue-600" id="displayTotal">IDR 0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Update Sales Order
                        </button>
                        <a href="{{ route('outbound.sales-orders.show', $salesOrder) }}" class="block w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

@push('scripts')
<script>
    let itemIndex = 0;
    const products = @json($products);
    const existingItems = @json($salesOrder->items);

    // Load existing items on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (existingItems && existingItems.length > 0) {
            existingItems.forEach(item => {
                addItem(item);
            });
        } else {
            addItem();
        }
    });

    function addItem(existingItem = null) {
        const container = document.getElementById('itemsContainer');
        const itemDiv = document.createElement('div');
        itemDiv.className = 'p-4 border border-gray-200 rounded-lg relative';
        itemDiv.id = `item-${itemIndex}`;
        
        const productId = existingItem ? existingItem.product_id : '';
        const quantity = existingItem ? existingItem.quantity : 1;
        const unitPrice = existingItem ? existingItem.unit_price : 0;
        const discount = existingItem ? existingItem.discount : 0;
        
        itemDiv.innerHTML = `
            <button type="button" onclick="removeItem(${itemIndex})" class="absolute top-2 right-2 text-red-600 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product <span class="text-red-500">*</span></label>
                    <select name="items[${itemIndex}][product_id]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required onchange="updatePrice(${itemIndex})">
                        <option value="">Select Product</option>
                        ${products.map(product => `<option value="${product.id}" data-price="${product.price}" ${product.id == productId ? 'selected' : ''}>${product.name} - ${product.sku}</option>`).join('')}
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="items[${itemIndex}][quantity]" value="${quantity}" step="0.01" min="0.01" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required onchange="calculateItemTotal(${itemIndex})">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price <span class="text-red-500">*</span></label>
                    <input type="number" name="items[${itemIndex}][unit_price]" value="${unitPrice}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required onchange="calculateItemTotal(${itemIndex})">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount</label>
                    <input type="number" name="items[${itemIndex}][discount]" value="${discount}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="calculateItemTotal(${itemIndex})">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Item Total</label>
                    <input type="text" id="item-total-${itemIndex}" class="w-full rounded-lg border-gray-300 bg-gray-100" readonly value="0.00">
                </div>
            </div>
        `;
        
        container.appendChild(itemDiv);
        
        // Calculate item total after adding
        if (existingItem) {
            setTimeout(() => {
                calculateItemTotal(itemIndex);
            }, 100);
        }
        
        itemIndex++;
    }

    function removeItem(index) {
        const itemDiv = document.getElementById(`item-${index}`);
        if (itemDiv) {
            itemDiv.remove();
            calculateTotal();
        }
    }

    function updatePrice(index) {
        const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
        const priceInput = document.querySelector(`input[name="items[${index}][unit_price]"]`);
        
        if (select.selectedIndex > 0) {
            const price = select.options[select.selectedIndex].dataset.price;
            priceInput.value = price;
            calculateItemTotal(index);
        }
    }

    function calculateItemTotal(index) {
        const quantity = parseFloat(document.querySelector(`input[name="items[${index}][quantity]"]`)?.value) || 0;
        const unitPrice = parseFloat(document.querySelector(`input[name="items[${index}][unit_price]"]`)?.value) || 0;
        const discount = parseFloat(document.querySelector(`input[name="items[${index}][discount]"]`)?.value) || 0;
        
        const itemTotal = (quantity * unitPrice) - discount;
        const itemTotalInput = document.getElementById(`item-total-${index}`);
        if (itemTotalInput) {
            itemTotalInput.value = itemTotal.toFixed(2);
        }
        
        calculateTotal();
    }

    function calculateTotal() {
        let subtotal = 0;
        
        // Calculate subtotal from all items
        for (let i = 0; i < itemIndex; i++) {
            const itemTotalInput = document.getElementById(`item-total-${i}`);
            if (itemTotalInput) {
                subtotal += parseFloat(itemTotalInput.value) || 0;
            }
        }
        
        const discount = parseFloat(document.querySelector('input[name="discount_amount"]')?.value) || 0;
        const tax = parseFloat(document.querySelector('input[name="tax_amount"]')?.value) || 0;
        const shipping = parseFloat(document.querySelector('input[name="shipping_cost"]')?.value) || 0;
        
        const total = subtotal - discount + tax + shipping;
        
        document.getElementById('displaySubtotal').textContent = `IDR ${subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
        document.getElementById('displayTotal').textContent = `IDR ${total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
    }
</script>
@endpush

@endsection