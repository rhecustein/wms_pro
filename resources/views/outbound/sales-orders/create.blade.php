{{-- resources/views/outbound/sales-orders/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Create Sales Order')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('outbound.sales-orders.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                    Create Sales Order
                </h1>
                <p class="text-sm text-gray-600 mt-1">Add a new sales order to the system</p>
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
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('outbound.sales-orders.store') }}" method="POST" id="salesOrderForm">
        @csrf

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
                            <input type="text" value="{{ $soNumber }}" class="w-full rounded-lg border-gray-300 bg-gray-100" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Order Date <span class="text-red-500">*</span></label>
                            <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" id="customerSearch" placeholder="Search customer by name, email, or phone..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-10">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                            <input type="hidden" name="customer_id" id="customerId" required>
                            
                            <div id="customerDropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <!-- Customer results will be shown here -->
                            </div>

                            <div id="selectedCustomerInfo" class="hidden mt-2 p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900" id="selectedCustomerName"></div>
                                            <div class="text-xs text-gray-600" id="selectedCustomerEmail"></div>
                                        </div>
                                    </div>
                                    <button type="button" onclick="clearCustomer()" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse <span class="text-red-500">*</span></label>
                            <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Requested Delivery Date</label>
                            <input type="date" name="requested_delivery_date" value="{{ old('requested_delivery_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Additional notes...">{{ old('notes') }}</textarea>
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
                            <textarea name="shipping_address" id="shippingAddress" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Full shipping address">{{ old('shipping_address') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" name="shipping_city" id="shippingCity" value="{{ old('shipping_city') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="City">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                                <input type="text" name="shipping_province" id="shippingProvince" value="{{ old('shipping_province') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Province">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                <input type="text" name="shipping_postal_code" id="shippingPostalCode" value="{{ old('shipping_postal_code') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Postal Code">
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
                        {{-- Items will be added here dynamically --}}
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
                            <input type="number" name="discount_amount" value="{{ old('discount_amount', 0) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="calculateTotal()">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Tax</label>
                            <input type="number" name="tax_amount" value="{{ old('tax_amount', 0) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="calculateTotal()">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Shipping Cost</label>
                            <input type="number" name="shipping_cost" value="{{ old('shipping_cost', 0) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="calculateTotal()">
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
                            <i class="fas fa-save mr-2"></i>Create Sales Order
                        </button>
                        <a href="{{ route('outbound.sales-orders.index') }}" class="block w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold">
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
    let customers = @json($customers);
    let products = @json($products);
    let customerSearchTimeout = null;

    // Customer Search
    document.getElementById('customerSearch').addEventListener('input', function(e) {
        clearTimeout(customerSearchTimeout);
        const searchTerm = e.target.value.toLowerCase();
        
        customerSearchTimeout = setTimeout(() => {
            if (searchTerm.length === 0) {
                document.getElementById('customerDropdown').classList.add('hidden');
                return;
            }

            const filtered = customers.filter(customer => {
                return customer.name.toLowerCase().includes(searchTerm) ||
                       (customer.company_name && customer.company_name.toLowerCase().includes(searchTerm)) ||
                       (customer.code && customer.code.toLowerCase().includes(searchTerm)) ||
                       (customer.email && customer.email.toLowerCase().includes(searchTerm)) ||
                       (customer.phone && customer.phone.toLowerCase().includes(searchTerm));
            });

            showCustomerDropdown(filtered);
        }, 300);
    });

    // Click outside to close dropdown
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#customerSearch') && !e.target.closest('#customerDropdown')) {
            document.getElementById('customerDropdown').classList.add('hidden');
        }
    });

    function showCustomerDropdown(filteredCustomers) {
        const dropdown = document.getElementById('customerDropdown');
        
        if (filteredCustomers.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-sm text-gray-500">No customers found</div>';
            dropdown.classList.remove('hidden');
            return;
        }

        dropdown.innerHTML = filteredCustomers.map(customer => `
            <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100" onclick="selectCustomer(${customer.id})">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm text-gray-900">${customer.name}</span>
                            ${customer.customer_type ? `<span class="px-2 py-0.5 text-xs rounded-full ${getCustomerTypeClass(customer.customer_type)}">${customer.customer_type.toUpperCase()}</span>` : ''}
                        </div>
                        ${customer.company_name ? `<div class="text-xs text-gray-600 mt-0.5">${customer.company_name}</div>` : ''}
                        <div class="text-xs text-gray-500 mt-1">
                            ${customer.code ? `Code: ${customer.code}` : ''} 
                            ${customer.email ? `• ${customer.email}` : ''} 
                            ${customer.phone ? `• ${customer.phone}` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        
        dropdown.classList.remove('hidden');
    }

    function getCustomerTypeClass(type) {
        const classes = {
            'regular': 'bg-blue-100 text-blue-700',
            'vip': 'bg-purple-100 text-purple-700',
            'wholesale': 'bg-green-100 text-green-700'
        };
        return classes[type] || 'bg-gray-100 text-gray-700';
    }

    function selectCustomer(customerId) {
        const customer = customers.find(c => c.id === customerId);
        if (!customer) return;

        // Set hidden input
        document.getElementById('customerId').value = customer.id;
        
        // Update search input
        document.getElementById('customerSearch').value = customer.name + (customer.company_name ? ` (${customer.company_name})` : '');
        
        // Show selected customer info
        document.getElementById('selectedCustomerName').textContent = customer.name + (customer.company_name ? ` - ${customer.company_name}` : '');
        document.getElementById('selectedCustomerEmail').textContent = [customer.code, customer.email, customer.phone].filter(Boolean).join(' • ');
        document.getElementById('selectedCustomerInfo').classList.remove('hidden');
        
        // Hide dropdown
        document.getElementById('customerDropdown').classList.add('hidden');

        // Auto-fill shipping information
        if (customer.address) {
            document.getElementById('shippingAddress').value = customer.address;
        }
        if (customer.city) {
            document.getElementById('shippingCity').value = customer.city;
        }
        if (customer.province) {
            document.getElementById('shippingProvince').value = customer.province;
        }
        if (customer.postal_code) {
            document.getElementById('shippingPostalCode').value = customer.postal_code;
        }
    }

    function clearCustomer() {
        document.getElementById('customerId').value = '';
        document.getElementById('customerSearch').value = '';
        document.getElementById('selectedCustomerInfo').classList.add('hidden');
    }

    // Product Search for each item
    function setupProductSearch(index) {
        const searchInput = document.getElementById(`productSearch-${index}`);
        const dropdown = document.getElementById(`productDropdown-${index}`);
        let searchTimeout = null;

        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.toLowerCase();
            
            searchTimeout = setTimeout(() => {
                if (searchTerm.length === 0) {
                    dropdown.classList.add('hidden');
                    return;
                }

                const filtered = products.filter(product => {
                    return product.name.toLowerCase().includes(searchTerm) ||
                           product.sku.toLowerCase().includes(searchTerm) ||
                           (product.barcode && product.barcode.toLowerCase().includes(searchTerm));
                });

                showProductDropdown(index, filtered);
            }, 300);
        });

        // Click outside to close dropdown
        searchInput.addEventListener('blur', function() {
            setTimeout(() => {
                dropdown.classList.add('hidden');
            }, 200);
        });
    }

    function showProductDropdown(index, filteredProducts) {
        const dropdown = document.getElementById(`productDropdown-${index}`);
        
        if (filteredProducts.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-sm text-gray-500">No products found</div>';
            dropdown.classList.remove('hidden');
            return;
        }

        dropdown.innerHTML = filteredProducts.map(product => `
            <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100" onclick="selectProduct(${index}, ${product.id})">
                <div class="flex items-center gap-3">
                    ${product.image ? 
                        `<img src="/storage/${product.image}" alt="${product.name}" class="w-12 h-12 object-cover rounded border border-gray-200">` : 
                        `<div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>`
                    }
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm text-gray-900">${product.name}</span>
                            ${getProductTypeBadge(product.type)}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            SKU: ${product.sku}
                            ${product.barcode ? ` • Barcode: ${product.barcode}` : ''}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-blue-600">Rp ${formatNumber(product.price)}</div>
                        <div class="text-xs ${getStockClass(product.stock)}">
                            Stock: ${product.stock || 0} ${product.unit}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        
        dropdown.classList.remove('hidden');
    }

    function getProductTypeBadge(type) {
        const badges = {
            'raw_material': '<span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700">Raw Material</span>',
            'finished_goods': '<span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">Finished</span>',
            'spare_parts': '<span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700">Spare Parts</span>',
            'consumable': '<span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Consumable</span>'
        };
        return badges[type] || '';
    }

    function getStockClass(stock) {
        if (stock <= 0) return 'text-red-600 font-semibold';
        if (stock < 10) return 'text-orange-600 font-semibold';
        return 'text-green-600';
    }

    function formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function selectProduct(index, productId) {
        const product = products.find(p => p.id === productId);
        if (!product) return;

        // Set hidden input
        document.querySelector(`input[name="items[${index}][product_id]"]`).value = product.id;
        
        // Update search input
        document.getElementById(`productSearch-${index}`).value = product.name + ' - ' + product.sku;
        
        // Set price
        document.querySelector(`input[name="items[${index}][unit_price]"]`).value = product.price;
        
        // Show product info
        const infoDiv = document.getElementById(`selectedProductInfo-${index}`);
        infoDiv.classList.remove('hidden');
        
        // Update info with image
        infoDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    ${product.image ? 
                        `<img src="/storage/${product.image}" alt="${product.name}" class="w-10 h-10 object-cover rounded border border-gray-200">` : 
                        `<div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                            <i class="fas fa-box text-gray-400 text-sm"></i>
                        </div>`
                    }
                    <div>
                        <div class="text-sm font-semibold text-gray-900">${product.name}</div>
                        <div class="text-xs text-gray-600">
                            <span>SKU: ${product.sku}</span>
                            ${product.barcode ? ` • Barcode: ${product.barcode}` : ''}
                             • <span class="${getStockClass(product.stock)}">Stock: ${product.stock || 0} ${product.unit}</span>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="clearProduct(${index})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // Hide dropdown
        document.getElementById(`productDropdown-${index}`).classList.add('hidden');
        
        calculateItemTotal(index);
    }

    function clearProduct(index) {
        document.querySelector(`input[name="items[${index}][product_id]"]`).value = '';
        document.getElementById(`productSearch-${index}`).value = '';
        document.getElementById(`selectedProductInfo-${index}`).classList.add('hidden');
        document.querySelector(`input[name="items[${index}][unit_price]"]`).value = 0;
        calculateItemTotal(index);
    }

    // Add first item on page load
    document.addEventListener('DOMContentLoaded', function() {
        addItem();
    });

    function addItem() {
        const container = document.getElementById('itemsContainer');
        const itemDiv = document.createElement('div');
        itemDiv.className = 'p-4 border border-gray-200 rounded-lg relative';
        itemDiv.id = `item-${itemIndex}`;
        
        itemDiv.innerHTML = `
            <button type="button" onclick="removeItem(${itemIndex})" class="absolute top-2 right-2 text-red-600 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="productSearch-${itemIndex}" placeholder="Search product by name or SKU..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-10">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <input type="hidden" name="items[${itemIndex}][product_id]" required>
                    
                    <div id="productDropdown-${itemIndex}" class="hidden absolute z-10 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto" style="width: calc(100% - 2rem);">
                        <!-- Product results will be shown here -->
                    </div>

                    <div id="selectedProductInfo-${itemIndex}" class="hidden mt-2 p-2 bg-green-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-gray-900" id="selectedProductName-${itemIndex}"></div>
                                <div class="text-xs text-gray-600">
                                    <span id="selectedProductSku-${itemIndex}"></span> • 
                                    <span id="selectedProductStock-${itemIndex}"></span>
                                </div>
                            </div>
                            <button type="button" onclick="clearProduct(${itemIndex})" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="items[${itemIndex}][quantity]" value="1" step="0.01" min="0.01" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required onchange="calculateItemTotal(${itemIndex})">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price <span class="text-red-500">*</span></label>
                    <input type="number" name="items[${itemIndex}][unit_price]" value="0" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required onchange="calculateItemTotal(${itemIndex})">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount</label>
                    <input type="number" name="items[${itemIndex}][discount]" value="0" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="calculateItemTotal(${itemIndex})">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Item Total</label>
                    <input type="text" id="item-total-${itemIndex}" class="w-full rounded-lg border-gray-300 bg-gray-100" readonly value="0.00">
                </div>
            </div>
        `;
        
        container.appendChild(itemDiv);
        
        // Setup product search for this item
        setupProductSearch(itemIndex);
        
        itemIndex++;
    }

    function removeItem(index) {
        const itemDiv = document.getElementById(`item-${index}`);
        if (itemDiv) {
            itemDiv.remove();
            calculateTotal();
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