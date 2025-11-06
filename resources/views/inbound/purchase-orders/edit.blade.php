@extends('layouts.app')

@section('title', 'Edit Purchase Order')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-gray-50">
    <div class="container-fluid px-4 py-8 max-w-[1800px] mx-auto">
        
        {{-- Page Header with Breadcrumb --}}
        <div class="mb-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <i class="fas fa-home mr-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <a href="{{ route('inbound.purchase-orders.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                                Purchase Orders
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <a href="{{ route('inbound.purchase-orders.show', $purchaseOrder) }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                                {{ $purchaseOrder->po_number }}
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <span class="text-sm font-medium text-gray-900">Edit</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-yellow-500/30">
                            <i class="fas fa-edit text-white text-xl"></i>
                        </div>
                        Edit Purchase Order
                    </h1>
                    <p class="text-sm text-gray-600 mt-2 ml-16">Update purchase order: <span class="font-mono font-bold">{{ $purchaseOrder->po_number }}</span></p>
                </div>
                <a href="{{ route('inbound.purchase-orders.show', $purchaseOrder) }}" class="px-6 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2 font-medium">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Details</span>
                </a>
            </div>
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl shadow-sm">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-red-900 mb-2">Please correct the following errors:</p>
                            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('inbound.purchase-orders.update', $purchaseOrder) }}" method="POST" id="poForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                
                {{-- Main Form --}}
                <div class="xl:col-span-2 space-y-6">
                    
                    {{-- Basic Information --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Basic Information</h2>
                                    <p class="text-xs text-gray-500">General purchase order details</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                {{-- PO Number --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-hashtag text-gray-400 mr-1"></i>
                                        PO Number <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" value="{{ $purchaseOrder->po_number }}" class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-gray-200 bg-gray-50 cursor-not-allowed font-mono font-bold text-gray-600" readonly>
                                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i>
                                        Cannot be changed
                                    </p>
                                </div>

                                {{-- PO Date --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                        PO Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="po_date" value="{{ old('po_date', $purchaseOrder->po_date->format('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('po_date') border-red-500 @enderror" required>
                                    @error('po_date')
                                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Warehouse --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-warehouse text-gray-400 mr-1"></i>
                                        Warehouse <span class="text-red-500">*</span>
                                    </label>
                                    <select name="warehouse_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('warehouse_id') border-red-500 @enderror" required>
                                        <option value="">Select Warehouse</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $purchaseOrder->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }} ({{ $warehouse->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id')
                                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Supplier --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-building text-gray-400 mr-1"></i>
                                        Supplier <span class="text-red-500">*</span>
                                    </label>
                                    <select name="supplier_id" id="supplier_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('supplier_id') border-red-500 @enderror" required>
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }} ({{ $supplier->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Expected Delivery Date --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-truck text-gray-400 mr-1"></i>
                                        Expected Delivery Date
                                    </label>
                                    <input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date', $purchaseOrder->expected_delivery_date?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('expected_delivery_date') border-red-500 @enderror">
                                    @error('expected_delivery_date')
                                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Payment Terms --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-credit-card text-gray-400 mr-1"></i>
                                        Payment Terms
                                    </label>
                                    <input type="text" name="payment_terms" value="{{ old('payment_terms', $purchaseOrder->payment_terms) }}" placeholder="e.g., Net 30, COD" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('payment_terms') border-red-500 @enderror">
                                    @error('payment_terms')
                                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Payment Due Days --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-days text-gray-400 mr-1"></i>
                                        Payment Due (Days)
                                    </label>
                                    <input type="number" name="payment_due_days" value="{{ old('payment_due_days', $purchaseOrder->payment_due_days) }}" min="0" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                </div>

                                {{-- Reference Number --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-file-alt text-gray-400 mr-1"></i>
                                        Reference Number
                                    </label>
                                    <input type="text" name="reference_number" value="{{ old('reference_number', $purchaseOrder->reference_number) }}" placeholder="Optional reference" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                </div>
                            </div>

                            {{-- Shipping Info --}}
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-shipping-fast text-blue-600"></i>
                                    Shipping Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                            Shipping Address
                                        </label>
                                        <textarea name="shipping_address" rows="2" placeholder="Enter shipping address..." class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">{{ old('shipping_address', $purchaseOrder->shipping_address) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-truck text-gray-400 mr-1"></i>
                                            Shipping Method
                                        </label>
                                        <input type="text" name="shipping_method" value="{{ old('shipping_method', $purchaseOrder->shipping_method) }}" placeholder="e.g., Courier, Pickup" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-boxes text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-gray-900">Order Items</h2>
                                        <p class="text-xs text-gray-500">Add or modify products in this purchase order</p>
                                    </div>
                                </div>
                                <button type="button" onclick="addItem()" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-lg shadow-purple-500/30 flex items-center gap-2 font-medium text-sm">
                                    <i class="fas fa-plus"></i>
                                    Add Item
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <div id="items-container" class="space-y-4">
                                @foreach($purchaseOrder->items as $index => $item)
                                    <div class="item-row bg-gradient-to-r from-gray-50 to-white p-5 rounded-xl border-2 border-gray-200 hover:border-blue-300 transition-all duration-200" data-item-index="{{ $index }}">
                                        <div class="flex items-start gap-4">
                                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <span class="item-number font-bold text-purple-600 text-sm">{{ $index + 1 }}</span>
                                            </div>
                                            
                                            <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-4">
                                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                
                                                {{-- Product --}}
                                                <div class="md:col-span-4">
                                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Product *</label>
                                                    <select name="items[{{ $index }}][product_id]" class="product-select w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm" required onchange="updateProductInfo(this)">
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" 
                                                                data-sku="{{ $product->sku }}"
                                                                data-unit="{{ $product->unit->id ?? '' }}"
                                                                data-price="{{ $product->purchase_price ?? 0 }}"
                                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }} @if($product->sku)({{ $product->sku }})@endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Quantity --}}
                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Quantity *</label>
                                                    <input type="number" name="items[{{ $index }}][quantity_ordered]" class="quantity-input w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm font-semibold" min="0.01" step="0.01" value="{{ $item->quantity_ordered }}" required onchange="calculateItemTotal(this)">
                                                </div>

                                                {{-- Unit --}}
                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit *</label>
                                                    <select name="items[{{ $index }}][unit_id]" class="unit-select w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm" required>
                                                        @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}" {{ $item->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Unit Price --}}
                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit Price *</label>
                                                    <input type="number" name="items[{{ $index }}][unit_price]" class="unit-price-input w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm font-semibold" min="0" step="0.01" value="{{ $item->unit_price }}" required onchange="calculateItemTotal(this)">
                                                </div>

                                                {{-- Line Total --}}
                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Line Total</label>
                                                    <input type="text" class="line-total w-full px-3 py-2 rounded-lg border-2 border-gray-200 bg-gray-50 text-sm font-bold text-blue-600" readonly value="{{ number_format($item->line_total, 2) }}">
                                                </div>

                                                {{-- Tax & Discount --}}
                                                <div class="md:col-span-6 grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tax Rate (%)</label>
                                                        <input type="number" name="items[{{ $index }}][tax_rate]" class="tax-rate-input w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm" min="0" max="100" step="0.01" value="{{ $item->tax_rate }}" onchange="calculateItemTotal(this)">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Discount Rate (%)</label>
                                                        <input type="number" name="items[{{ $index }}][discount_rate]" class="discount-rate-input w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm" min="0" max="100" step="0.01" value="{{ $item->discount_rate }}" onchange="calculateItemTotal(this)">
                                                    </div>
                                                </div>

                                                {{-- Notes --}}
                                                <div class="md:col-span-6">
                                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Notes</label>
                                                    <input type="text" name="items[{{ $index }}][notes]" value="{{ $item->notes }}" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm" placeholder="Optional notes...">
                                                </div>
                                            </div>

                                            {{-- Remove Button --}}
                                            <button type="button" onclick="removeItem(this)" class="w-9 h-9 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center flex-shrink-0 transition-all duration-200 hover:scale-110" title="Remove Item">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Notes & Terms --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-white px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-sticky-note text-yellow-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Additional Information</h2>
                                    <p class="text-xs text-gray-500">Notes and terms & conditions</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-comment text-gray-400 mr-1"></i>
                                    Notes
                                </label>
                                <textarea name="notes" rows="3" placeholder="Add any additional notes or comments..." class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-file-contract text-gray-400 mr-1"></i>
                                    Terms & Conditions
                                </label>
                                <textarea name="terms_conditions" rows="3" placeholder="Enter terms and conditions..." class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">{{ old('terms_conditions', $purchaseOrder->terms_conditions) }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Sidebar Summary --}}
                <div class="xl:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-green-50 to-white px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-invoice-dollar text-green-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Order Summary</h2>
                                    <p class="text-xs text-gray-500">Financial details</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            {{-- Currency --}}
                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-dollar-sign text-gray-400 mr-1"></i>
                                    Currency <span class="text-red-500">*</span>
                                </label>
                                <select name="currency" id="currency" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200" required>
                                    <option value="IDR" {{ old('currency', $purchaseOrder->currency) == 'IDR' ? 'selected' : '' }}>IDR - Indonesian Rupiah</option>
                                    <option value="USD" {{ old('currency', $purchaseOrder->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ old('currency', $purchaseOrder->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                </select>
                            </div>

                            {{-- Financial Summary --}}
                            <div class="space-y-4 py-4 border-y border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Subtotal:</span>
                                    <span class="text-lg font-bold text-gray-900" id="summary_subtotal">0.00</span>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="text-sm text-gray-600">Tax Rate (%):</label>
                                        <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', $purchaseOrder->tax_rate) }}" min="0" max="100" step="0.01" class="w-24 px-3 py-1.5 text-right rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 text-sm font-semibold" onchange="calculateTotal()">
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 pl-4">Tax Amount:</span>
                                        <span class="text-base font-semibold text-gray-900" id="summary_tax">0.00</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="text-sm text-gray-600">Discount Rate (%):</label>
                                        <input type="number" name="discount_rate" id="discount_rate" value="{{ old('discount_rate', $purchaseOrder->discount_rate) }}" min="0" max="100" step="0.01" class="w-24 px-3 py-1.5 text-right rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 text-sm font-semibold" onchange="calculateTotal()">
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 pl-4">Discount Amount:</span>
                                        <span class="text-base font-semibold text-red-600" id="summary_discount">0.00</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="text-sm text-gray-600">Shipping Cost:</label>
                                        <input type="number" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', $purchaseOrder->shipping_cost) }}" min="0" step="0.01" class="w-32 px-3 py-1.5 text-right rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 text-sm font-semibold" onchange="calculateTotal()">
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="text-sm text-gray-600">Other Cost:</label>
                                        <input type="number" name="other_cost" id="other_cost" value="{{ old('other_cost', $purchaseOrder->other_cost) }}" min="0" step="0.01" class="w-32 px-3 py-1.5 text-right rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 text-sm font-semibold" onchange="calculateTotal()">
                                    </div>
                                </div>
                            </div>

                            {{-- Total --}}
                            <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl">
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-bold text-gray-800">Grand Total:</span>
                                    <span class="text-2xl font-bold text-blue-600" id="summary_total">0.00</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-2 text-right">
                                    <span id="items_count">0</span> item(s)
                                </p>
                            </div>

                            {{-- Hidden Inputs --}}
                            <input type="hidden" name="subtotal" id="subtotal" value="0">
                            <input type="hidden" name="tax_amount" id="tax_amount" value="0">
                            <input type="hidden" name="discount_amount" id="discount_amount" value="0">
                            <input type="hidden" name="total_amount" id="total_amount" value="0">

                            {{-- Action Buttons --}}
                            <div class="mt-6 space-y-3">
                                <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg shadow-blue-500/30 font-semibold flex items-center justify-center gap-2">
                                    <i class="fas fa-save"></i>
                                    Update Purchase Order
                                </button>
                                <a href="{{ route('inbound.purchase-orders.show', $purchaseOrder) }}" class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-semibold text-center flex items-center justify-center gap-2">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                            </div>

                            {{-- Info Box --}}
                            <div class="mt-6 p-4 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl border border-yellow-100">
                                <h3 class="text-sm font-bold text-yellow-900 mb-2 flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Notice
                                </h3>
                                <ul class="text-xs text-yellow-800 space-y-1.5">
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check-circle mt-0.5 text-yellow-600"></i>
                                        <span>Changes will be saved immediately</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check-circle mt-0.5 text-yellow-600"></i>
                                        <span>Status: {{ ucfirst($purchaseOrder->status) }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check-circle mt-0.5 text-yellow-600"></i>
                                        <span>Last update: {{ $purchaseOrder->updated_at->diffForHumans() }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

{{-- Item Row Template --}}
<template id="item-template">
    <div class="item-row bg-gradient-to-r from-gray-50 to-white p-5 rounded-xl border-2 border-gray-200 hover:border-blue-300 transition-all duration-200">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="item-number font-bold text-purple-600 text-sm"></span>
            </div>
            
            <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-4">
                {{-- Product --}}
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Product *</label>
                    <select name="items[INDEX][product_id]" class="product-select w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm" required onchange="updateProductInfo(this)">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                data-sku="{{ $product->sku }}"
                                data-unit="{{ $product->unit->id ?? '' }}"
                                data-price="{{ $product->purchase_price ?? 0 }}">
                                {{ $product->name }} @if($product->sku)({{ $product->sku }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Quantity --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Quantity *</label>
                    <input type="number" name="items[INDEX][quantity_ordered]" class="quantity-input w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm font-semibold" min="0.01" step="0.01" value="1" required onchange="calculateItemTotal(this)">
                </div>

                {{-- Unit --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit *</label>
                    <select name="items[INDEX][unit_id]" class="unit-select w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm" required>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Unit Price --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit Price *</label>
                    <input type="number" name="items[INDEX][unit_price]" class="unit-price-input w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm font-semibold" min="0" step="0.01" value="0" required onchange="calculateItemTotal(this)">
                </div>

                {{-- Line Total --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Line Total</label>
                    <input type="text" class="line-total w-full px-3 py-2 rounded-lg border-2 border-gray-200 bg-gray-50 text-sm font-bold text-blue-600" readonly value="0.00">
                </div>

                {{-- Tax & Discount --}}
                <div class="md:col-span-6 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tax Rate (%)</label>
                        <input type="number" name="items[INDEX][tax_rate]" class="tax-rate-input w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm" min="0" max="100" step="0.01" value="0" onchange="calculateItemTotal(this)">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Discount Rate (%)</label>
                        <input type="number" name="items[INDEX][discount_rate]" class="discount-rate-input w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm" min="0" max="100" step="0.01" value="0" onchange="calculateItemTotal(this)">
                    </div>
                </div>

                {{-- Notes --}}
                <div class="md:col-span-6">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Notes</label>
                    <input type="text" name="items[INDEX][notes]" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/10 text-sm" placeholder="Optional notes...">
                </div>
            </div>

            {{-- Remove Button --}}
            <button type="button" onclick="removeItem(this)" class="w-9 h-9 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center flex-shrink-0 transition-all duration-200 hover:scale-110" title="Remove Item">
                <i class="fas fa-trash text-sm"></i>
            </button>
        </div>
    </div>
</template>

@push('scripts')
<script>
let itemIndex = {{ $purchaseOrder->items->count() }};

// Add new item row
function addItem() {
    const template = document.getElementById('item-template');
    const clone = template.content.cloneNode(true);
    const container = document.getElementById('items-container');
    
    // Replace INDEX with actual index
    const html = clone.querySelector('.item-row').outerHTML.replace(/INDEX/g, itemIndex);
    container.insertAdjacentHTML('beforeend', html);
    
    // Update item number
    const newRow = container.lastElementChild;
    newRow.querySelector('.item-number').textContent = itemIndex + 1;
    
    itemIndex++;
    
    calculateTotal();
}

// Remove item row
function removeItem(button) {
    const row = button.closest('.item-row');
    
    if (document.querySelectorAll('.item-row').length === 1) {
        alert('You must have at least one item in the purchase order!');
        return;
    }
    
    row.remove();
    
    // Renumber items
    document.querySelectorAll('.item-number').forEach((num, index) => {
        num.textContent = index + 1;
    });
    
    calculateTotal();
}

// Update product info when selected
function updateProductInfo(select) {
    const option = select.options[select.selectedIndex];
    const row = select.closest('.item-row');
    
    if (option.value) {
        const unitId = option.getAttribute('data-unit');
        const price = option.getAttribute('data-price');
        
        if (unitId) {
            row.querySelector('.unit-select').value = unitId;
        }
        
        if (price) {
            row.querySelector('.unit-price-input').value = price;
        }
        
        calculateItemTotal(select);
    }
}

// Calculate item line total
function calculateItemTotal(input) {
    const row = input.closest('.item-row');
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
    const taxRate = parseFloat(row.querySelector('.tax-rate-input').value) || 0;
    const discountRate = parseFloat(row.querySelector('.discount-rate-input').value) || 0;
    
    const subtotal = quantity * unitPrice;
    const taxAmount = subtotal * (taxRate / 100);
    const discountAmount = subtotal * (discountRate / 100);
    const lineTotal = subtotal + taxAmount - discountAmount;
    
    row.querySelector('.line-total').value = lineTotal.toFixed(2);
    
    calculateTotal();
}

// Calculate grand total
function calculateTotal() {
    let subtotal = 0;
    
    // Sum all line totals
    document.querySelectorAll('.line-total').forEach(input => {
        const value = parseFloat(input.value) || 0;
        subtotal += value;
    });
    
    const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
    const discountRate = parseFloat(document.getElementById('discount_rate').value) || 0;
    const shippingCost = parseFloat(document.getElementById('shipping_cost').value) || 0;
    const otherCost = parseFloat(document.getElementById('other_cost').value) || 0;
    
    const taxAmount = subtotal * (taxRate / 100);
    const discountAmount = subtotal * (discountRate / 100);
    const total = subtotal + taxAmount - discountAmount + shippingCost + otherCost;
    
    // Update hidden inputs
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('tax_amount').value = taxAmount.toFixed(2);
    document.getElementById('discount_amount').value = discountAmount.toFixed(2);
    document.getElementById('total_amount').value = total.toFixed(2);
    
    // Update summary
    document.getElementById('summary_subtotal').textContent = formatMoney(subtotal);
    document.getElementById('summary_tax').textContent = formatMoney(taxAmount);
    document.getElementById('summary_discount').textContent = formatMoney(discountAmount);
    document.getElementById('summary_total').textContent = formatMoney(total);
    document.getElementById('items_count').textContent = document.querySelectorAll('.item-row').length;
}

// Format money
function formatMoney(amount) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

// Form validation
document.getElementById('poForm').addEventListener('submit', function(e) {
    const itemsCount = document.querySelectorAll('.item-row').length;
    
    if (itemsCount === 0) {
        e.preventDefault();
        alert('Please add at least one item to the purchase order!');
        return false;
    }
});

// Initialize calculation on page load
document.addEventListener('DOMContentLoaded', function() {
    // Calculate totals for existing items
    document.querySelectorAll('.item-row').forEach(row => {
        calculateItemTotal(row.querySelector('.quantity-input'));
    });
});
</script>
@endpush
@endsection