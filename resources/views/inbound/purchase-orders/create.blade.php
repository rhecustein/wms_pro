@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                Create Purchase Order
            </h1>
            <p class="text-sm text-gray-600 mt-1">Create a new purchase order</p>
        </div>
        <a href="{{ route('inbound.purchase-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('inbound.purchase-orders.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- PO Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                PO Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" value="{{ $poNumber }}" class="w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed" readonly>
                            <p class="text-xs text-gray-500 mt-1">Auto-generated</p>
                        </div>

                        {{-- PO Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                PO Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="po_date" value="{{ old('po_date', date('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('po_date') border-red-500 @enderror" required>
                            @error('po_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Warehouse --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('warehouse_id') border-red-500 @enderror" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Vendor --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vendor <span class="text-red-500">*</span>
                            </label>
                            <select name="vendor_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('vendor_id') border-red-500 @enderror" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vendor_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Expected Delivery Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Expected Delivery Date
                            </label>
                            <input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('expected_delivery_date') border-red-500 @enderror">
                            @error('expected_delivery_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Payment Terms --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Terms
                            </label>
                            <input type="text" name="payment_terms" value="{{ old('payment_terms') }}" placeholder="e.g., Net 30, COD" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('payment_terms') border-red-500 @enderror">
                            @error('payment_terms')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Financial Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                        Financial Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Currency --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Currency <span class="text-red-500">*</span>
                            </label>
                            <select name="currency" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('currency') border-red-500 @enderror" required>
                                <option value="IDR" {{ old('currency', 'IDR') == 'IDR' ? 'selected' : '' }}>IDR</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                            </select>
                            @error('currency')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Subtotal --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Subtotal <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="subtotal" id="subtotal" value="{{ old('subtotal', 0) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('subtotal') border-red-500 @enderror" required>
                            @error('subtotal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tax Amount --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tax Amount
                            </label>
                            <input type="number" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', 0) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('tax_amount') border-red-500 @enderror">
                            @error('tax_amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Discount Amount --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Discount Amount
                            </label>
                            <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" step="0.01" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('discount_amount') border-red-500 @enderror">
                            @error('discount_amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Total Amount --}}
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <label class="text-lg font-semibold text-gray-800">
                                Total Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="text-right">
                                <input type="number" name="total_amount" id="total_amount" value="{{ old('total_amount', 0) }}" step="0.01" min="0" class="text-right text-2xl font-bold text-blue-600 border-0 focus:ring-0 w-64" readonly>
                                @error('total_amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                        Notes
                    </h2>
                    
                    <textarea name="notes" rows="4" placeholder="Add any additional notes or comments..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Sidebar Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-file-invoice-dollar text-purple-600 mr-2"></i>
                        Order Summary
                    </h2>

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold text-gray-900" id="summary_subtotal">0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax:</span>
                            <span class="font-semibold text-gray-900" id="summary_tax">0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount:</span>
                            <span class="font-semibold text-red-600" id="summary_discount">0.00</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between">
                                <span class="text-base font-semibold text-gray-800">Total:</span>
                                <span class="text-xl font-bold text-blue-600" id="summary_total">0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Create Purchase Order
                        </button>
                        <a href="{{ route('inbound.purchase-orders.index') }}" class="w-full mt-2 px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold text-center block">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-sm font-semibold text-blue-800 mb-2">
                            <i class="fas fa-info-circle mr-1"></i>Information
                        </h3>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li>• PO will be created as draft</li>
                            <li>• Can be edited before submission</li>
                            <li>• All fields marked with * are required</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>

@push('scripts')
<script>
    // Auto-calculate total amount
    function calculateTotal() {
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        
        const total = subtotal + tax - discount;
        
        document.getElementById('total_amount').value = total.toFixed(2);
        
        // Update summary
        document.getElementById('summary_subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('summary_tax').textContent = tax.toFixed(2);
        document.getElementById('summary_discount').textContent = discount.toFixed(2);
        document.getElementById('summary_total').textContent = total.toFixed(2);
    }

    // Add event listeners
    document.getElementById('subtotal').addEventListener('input', calculateTotal);
    document.getElementById('tax_amount').addEventListener('input', calculateTotal);
    document.getElementById('discount_amount').addEventListener('input', calculateTotal);

    // Initial calculation
    calculateTotal();
</script>
@endpush
@endsection