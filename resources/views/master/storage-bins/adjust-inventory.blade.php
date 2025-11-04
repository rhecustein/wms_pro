@extends('layouts.app')

@section('title', 'Adjust Inventory - ' . $storageBin->code)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-purple-600 mr-2"></i>
                Adjust Inventory
            </h1>
            <p class="text-sm text-gray-600 mt-1">Adjust stock quantity in storage bin {{ $storageBin->code }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('master.storage-bins.current-stock', $storageBin) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition inline-flex items-center">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column - Bin Info --}}
        <div class="lg:col-span-1">
            {{-- Storage Bin Info Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-warehouse mr-2"></i>
                        Bin Information
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Bin Code --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Bin Code</label>
                        <div class="px-4 py-3 bg-purple-50 border-2 border-purple-200 rounded-lg">
                            <p class="text-xl font-bold text-purple-800 font-mono text-center">
                                {{ $storageBin->code }}
                            </p>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-2">Location</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-gray-50 px-3 py-2 rounded-lg">
                                <p class="text-xs text-gray-500">Aisle</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $storageBin->aisle }}</p>
                            </div>
                            <div class="bg-gray-50 px-3 py-2 rounded-lg">
                                <p class="text-xs text-gray-500">Row</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $storageBin->row }}</p>
                            </div>
                            <div class="bg-gray-50 px-3 py-2 rounded-lg">
                                <p class="text-xs text-gray-500">Column</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $storageBin->column }}</p>
                            </div>
                            <div class="bg-gray-50 px-3 py-2 rounded-lg">
                                <p class="text-xs text-gray-500">Level</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $storageBin->level }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Warehouse --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Warehouse</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $storageBin->warehouse->name }}</p>
                        @if($storageBin->storageArea)
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-layer-group mr-1"></i>{{ $storageBin->storageArea->name }}
                            </p>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Current Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($storageBin->status === 'available') bg-green-100 text-green-800
                            @elseif($storageBin->status === 'occupied') bg-blue-100 text-blue-800
                            @elseif($storageBin->status === 'reserved') bg-yellow-100 text-yellow-800
                            @elseif($storageBin->status === 'blocked') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            <span class="w-2 h-2 rounded-full mr-2
                                @if($storageBin->status === 'available') bg-green-500
                                @elseif($storageBin->status === 'occupied') bg-blue-500
                                @elseif($storageBin->status === 'reserved') bg-yellow-500
                                @elseif($storageBin->status === 'blocked') bg-red-500
                                @else bg-gray-500
                                @endif"></span>
                            {{ ucfirst($storageBin->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Warning Card --}}
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-yellow-900 mb-2 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Important Notice
                </h3>
                <ul class="space-y-1 text-xs text-yellow-800">
                    <li class="flex items-start">
                        <i class="fas fa-circle text-yellow-600 mr-2 mt-1" style="font-size: 4px;"></i>
                        <span>Adjustments will be recorded in stock movement history</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-yellow-600 mr-2 mt-1" style="font-size: 4px;"></i>
                        <span>Provide a clear reason for audit purposes</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-yellow-600 mr-2 mt-1" style="font-size: 4px;"></i>
                        <span>Double-check quantities before submitting</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Right Column - Adjust Form --}}
        <div class="lg:col-span-2">
            @if($stockItems->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            Select Stock Item to Adjust
                        </h2>
                    </div>

                    <form action="{{ route('master.storage-bins.adjust-inventory.store', $storageBin) }}" method="POST" class="p-6">
                        @csrf

                        <div class="space-y-6">
                            {{-- Stock Item Selection --}}
                            <div>
                                <label for="inventory_stock_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Stock Item <span class="text-red-500">*</span>
                                </label>
                                <select name="inventory_stock_id" id="inventory_stock_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('inventory_stock_id') border-red-500 @enderror">
                                    <option value="">-- Select Stock Item --</option>
                                    @foreach($stockItems as $item)
                                        <option value="{{ $item->id }}" 
                                                data-product="{{ $item->product->name }}"
                                                data-sku="{{ $item->product->sku }}"
                                                data-current-qty="{{ $item->quantity }}"
                                                data-unit="{{ $item->unit_of_measure }}"
                                                data-batch="{{ $item->batch_number }}"
                                                data-serial="{{ $item->serial_number }}"
                                                {{ old('inventory_stock_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->product->name }} ({{ $item->product->sku }}) - 
                                            Current: {{ number_format($item->quantity, 2) }} {{ $item->unit_of_measure }}
                                            @if($item->batch_number) | Batch: {{ $item->batch_number }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('inventory_stock_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Stock Item Info Display --}}
                            <div id="stockItemInfo" class="hidden">
                                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-200 rounded-lg p-6">
                                    <h3 class="text-sm font-semibold text-blue-900 mb-4 flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Current Stock Information
                                    </h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-blue-700 mb-1">Product</p>
                                            <p class="text-sm font-semibold text-blue-900" id="displayProduct">-</p>
                                            <p class="text-xs text-blue-600 font-mono" id="displaySku">-</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-blue-700 mb-1">Current Quantity</p>
                                            <p class="text-2xl font-bold text-blue-900" id="displayCurrentQty">0</p>
                                            <p class="text-xs text-blue-600" id="displayUnit">-</p>
                                        </div>
                                        <div id="batchInfo" class="hidden">
                                            <p class="text-xs text-blue-700 mb-1">Batch Number</p>
                                            <p class="text-sm font-mono text-blue-900" id="displayBatch">-</p>
                                        </div>
                                        <div id="serialInfo" class="hidden">
                                            <p class="text-xs text-blue-700 mb-1">Serial Number</p>
                                            <p class="text-sm font-mono text-blue-900" id="displaySerial">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Adjustment Type --}}
                            <div>
                                <label for="adjustment_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Adjustment Type <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <label class="relative flex items-center px-4 py-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                        @error('adjustment_type') border-red-500 @else border-gray-300 @enderror">
                                        <input type="radio" name="adjustment_type" value="add" 
                                               {{ old('adjustment_type') == 'add' ? 'checked' : '' }}
                                               class="w-4 h-4 text-green-600 focus:ring-green-500" required>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center">
                                                <i class="fas fa-plus-circle text-green-600 text-xl mr-2"></i>
                                                <span class="font-semibold text-gray-900">Add</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Increase quantity</p>
                                        </div>
                                    </label>

                                    <label class="relative flex items-center px-4 py-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                        @error('adjustment_type') border-red-500 @else border-gray-300 @enderror">
                                        <input type="radio" name="adjustment_type" value="reduce" 
                                               {{ old('adjustment_type') == 'reduce' ? 'checked' : '' }}
                                               class="w-4 h-4 text-orange-600 focus:ring-orange-500" required>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center">
                                                <i class="fas fa-minus-circle text-orange-600 text-xl mr-2"></i>
                                                <span class="font-semibold text-gray-900">Reduce</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Decrease quantity</p>
                                        </div>
                                    </label>

                                    <label class="relative flex items-center px-4 py-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                        @error('adjustment_type') border-red-500 @else border-gray-300 @enderror">
                                        <input type="radio" name="adjustment_type" value="set" 
                                               {{ old('adjustment_type') == 'set' ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 focus:ring-blue-500" required>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center">
                                                <i class="fas fa-equals text-blue-600 text-xl mr-2"></i>
                                                <span class="font-semibold text-gray-900">Set</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Set exact quantity</p>
                                        </div>
                                    </label>
                                </div>
                                @error('adjustment_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Quantity --}}
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="quantity" id="quantity" step="0.01" min="0" required
                                    value="{{ old('quantity') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('quantity') border-red-500 @enderror"
                                    placeholder="Enter quantity">
                                @error('quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500" id="quantityHelp">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <span id="quantityHelpText">Select adjustment type first</span>
                                </p>
                            </div>

                            {{-- Calculation Preview --}}
                            <div id="calculationPreview" class="hidden">
                                <div class="bg-gradient-to-r from-purple-50 to-purple-100 border-2 border-purple-300 rounded-lg p-6">
                                    <h3 class="text-sm font-semibold text-purple-900 mb-4 flex items-center">
                                        <i class="fas fa-calculator mr-2"></i>
                                        Adjustment Preview
                                    </h3>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <p class="text-xs text-purple-700 mb-1">Current Quantity</p>
                                            <p class="text-xl font-bold text-purple-900" id="previewCurrent">0</p>
                                        </div>
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-arrow-right text-2xl text-purple-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-purple-700 mb-1">New Quantity</p>
                                            <p class="text-xl font-bold text-purple-900" id="previewNew">0</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-purple-300">
                                        <p class="text-xs text-purple-700">Difference</p>
                                        <p class="text-lg font-bold" id="previewDifference">0</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Reason --}}
                            <div>
                                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Reason <span class="text-red-500">*</span>
                                </label>
                                <select name="reason" id="reason" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('reason') border-red-500 @enderror">
                                    <option value="">-- Select Reason --</option>
                                    <option value="damaged" {{ old('reason') == 'damaged' ? 'selected' : '' }}>
                                        <i class="fas fa-broken"></i> Damaged / Broken
                                    </option>
                                    <option value="expired" {{ old('reason') == 'expired' ? 'selected' : '' }}>
                                        Expired
                                    </option>
                                    <option value="lost" {{ old('reason') == 'lost' ? 'selected' : '' }}>
                                        Lost / Missing
                                    </option>
                                    <option value="found" {{ old('reason') == 'found' ? 'selected' : '' }}>
                                        Found / Recovered
                                    </option>
                                    <option value="count_correction" {{ old('reason') == 'count_correction' ? 'selected' : '' }}>
                                        Count Correction / Physical Count
                                    </option>
                                    <option value="system_error" {{ old('reason') == 'system_error' ? 'selected' : '' }}>
                                        System Error / Data Correction
                                    </option>
                                    <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>
                                        Other
                                    </option>
                                </select>
                                @error('reason')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes
                                    <span class="text-gray-400 text-xs">(Optional but recommended)</span>
                                </label>
                                <textarea name="notes" id="notes" rows="4" maxlength="500"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                                    placeholder="Provide additional details about this adjustment...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    <span id="notesCount">0</span>/500 characters
                                </p>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                                <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold inline-flex items-center justify-center">
                                    <i class="fas fa-check mr-2"></i>
                                    Submit Adjustment
                                </button>
                                <a href="{{ route('master.storage-bins.current-stock', $storageBin) }}" 
                                   class="flex-1 sm:flex-none px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold inline-flex items-center justify-center">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                            <i class="fas fa-box-open text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Stock Items Found</h3>
                        <p class="text-gray-600 mb-6">This storage bin doesn't have any stock items to adjust</p>
                        <div class="flex flex-wrap gap-2 justify-center">
                            <a href="{{ route('master.storage-bins.add-stock', $storageBin) }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>Add Stock First
                            </a>
                            <a href="{{ route('master.storage-bins.current-stock', $storageBin) }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition inline-flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Stock
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stockSelect = document.getElementById('inventory_stock_id');
    const adjustmentTypeInputs = document.querySelectorAll('input[name="adjustment_type"]');
    const quantityInput = document.getElementById('quantity');
    const stockItemInfo = document.getElementById('stockItemInfo');
    const calculationPreview = document.getElementById('calculationPreview');
    const notesTextarea = document.getElementById('notes');
    const notesCount = document.getElementById('notesCount');
    
    let currentQty = 0;

    // Stock item selection handler
    stockSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            // Show stock info
            stockItemInfo.classList.remove('hidden');
            
            // Update display
            document.getElementById('displayProduct').textContent = selectedOption.dataset.product;
            document.getElementById('displaySku').textContent = selectedOption.dataset.sku;
            document.getElementById('displayCurrentQty').textContent = parseFloat(selectedOption.dataset.currentQty).toFixed(2);
            document.getElementById('displayUnit').textContent = selectedOption.dataset.unit;
            
            currentQty = parseFloat(selectedOption.dataset.currentQty);
            
            // Show/hide batch and serial
            if (selectedOption.dataset.batch) {
                document.getElementById('batchInfo').classList.remove('hidden');
                document.getElementById('displayBatch').textContent = selectedOption.dataset.batch;
            } else {
                document.getElementById('batchInfo').classList.add('hidden');
            }
            
            if (selectedOption.dataset.serial) {
                document.getElementById('serialInfo').classList.remove('hidden');
                document.getElementById('displaySerial').textContent = selectedOption.dataset.serial;
            } else {
                document.getElementById('serialInfo').classList.add('hidden');
            }
            
            updateCalculation();
        } else {
            stockItemInfo.classList.add('hidden');
            calculationPreview.classList.add('hidden');
            currentQty = 0;
        }
    });

    // Adjustment type change handler
    adjustmentTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            const type = this.value;
            let helpText = '';
            
            if (type === 'add') {
                helpText = 'Enter the amount to ADD to current quantity';
                quantityInput.placeholder = 'Amount to add';
            } else if (type === 'reduce') {
                helpText = 'Enter the amount to REDUCE from current quantity';
                quantityInput.placeholder = 'Amount to reduce';
            } else if (type === 'set') {
                helpText = 'Enter the NEW TOTAL quantity (will replace current)';
                quantityInput.placeholder = 'New total quantity';
            }
            
            document.getElementById('quantityHelpText').textContent = helpText;
            updateCalculation();
        });
    });

    // Quantity input handler
    quantityInput.addEventListener('input', function() {
        updateCalculation();
    });

    // Update calculation preview
    function updateCalculation() {
        const adjustmentType = document.querySelector('input[name="adjustment_type"]:checked');
        const quantity = parseFloat(quantityInput.value) || 0;
        
        if (stockSelect.value && adjustmentType && quantity > 0) {
            let newQty = 0;
            let difference = 0;
            let differenceColor = 'text-gray-600';
            let differenceIcon = '';
            
            if (adjustmentType.value === 'add') {
                newQty = currentQty + quantity;
                difference = '+' + quantity.toFixed(2);
                differenceColor = 'text-green-600';
                differenceIcon = '<i class="fas fa-arrow-up mr-1"></i>';
            } else if (adjustmentType.value === 'reduce') {
                newQty = Math.max(0, currentQty - quantity);
                difference = '-' + quantity.toFixed(2);
                differenceColor = 'text-red-600';
                differenceIcon = '<i class="fas fa-arrow-down mr-1"></i>';
            } else if (adjustmentType.value === 'set') {
                newQty = quantity;
                difference = (quantity - currentQty).toFixed(2);
                differenceColor = difference >= 0 ? 'text-green-600' : 'text-red-600';
                differenceIcon = difference >= 0 ? '<i class="fas fa-arrow-up mr-1"></i>' : '<i class="fas fa-arrow-down mr-1"></i>';
                difference = difference >= 0 ? '+' + difference : difference;
            }
            
            document.getElementById('previewCurrent').textContent = currentQty.toFixed(2);
            document.getElementById('previewNew').textContent = newQty.toFixed(2);
            
            const diffElement = document.getElementById('previewDifference');
            diffElement.className = 'text-lg font-bold ' + differenceColor;
            diffElement.innerHTML = differenceIcon + difference;
            
            calculationPreview.classList.remove('hidden');
        } else {
            calculationPreview.classList.add('hidden');
        }
    }

    // Notes character counter
    if (notesTextarea) {
        notesTextarea.addEventListener('input', function() {
            notesCount.textContent = this.value.length;
        });
        
        // Initialize count
        notesCount.textContent = notesTextarea.value.length;
    }

    // Form validation before submit
    document.querySelector('form')?.addEventListener('submit', function(e) {
        const adjustmentType = document.querySelector('input[name="adjustment_type"]:checked');
        const quantity = parseFloat(quantityInput.value);
        
        if (!adjustmentType) {
            e.preventDefault();
            return false;
        }
        
        if (adjustmentType.value === 'reduce' && quantity > currentQty) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    });

    // Initialize on page load if old values exist
    if (stockSelect.value) {
        stockSelect.dispatchEvent(new Event('change'));
    }
    
    const checkedAdjustment = document.querySelector('input[name="adjustment_type"]:checked');
    if (checkedAdjustment) {
        checkedAdjustment.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush

@endsection