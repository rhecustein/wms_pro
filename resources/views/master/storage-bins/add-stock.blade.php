@extends('layouts.app')

@section('title', 'Add Stock - ' . $storageBin->code)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                Add Stock to Bin
            </h1>
            <p class="text-sm text-gray-600 mt-1">Add inventory to storage bin {{ $storageBin->code }}</p>
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
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-warehouse mr-2"></i>
                        Bin Information
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Bin Code --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Bin Code</label>
                        <div class="px-4 py-3 bg-blue-50 border-2 border-blue-200 rounded-lg">
                            <p class="text-xl font-bold text-blue-800 font-mono text-center">
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

                    {{-- Capacity Info --}}
                    <div class="pt-4 border-t border-gray-200">
                        <label class="block text-xs font-medium text-gray-500 mb-2">Capacity Limits</label>
                        <div class="space-y-2">
                            @if($storageBin->max_weight_kg)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-weight-hanging text-yellow-500 mr-1"></i>
                                        Max Weight:
                                    </span>
                                    <span class="font-semibold text-gray-900">{{ number_format($storageBin->max_weight_kg, 2) }} kg</span>
                                </div>
                            @endif
                            @if($storageBin->max_volume_cbm)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-cube text-purple-500 mr-1"></i>
                                        Max Volume:
                                    </span>
                                    <span class="font-semibold text-gray-900">{{ number_format($storageBin->max_volume_cbm, 3) }} m³</span>
                                </div>
                            @endif
                            @if($storageBin->packaging_restriction && $storageBin->packaging_restriction !== 'none')
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-box text-orange-500 mr-1"></i>
                                        Packaging:
                                    </span>
                                    <span class="font-semibold text-gray-900 capitalize">{{ $storageBin->packaging_restriction }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Special Flags --}}
                    @if($storageBin->is_hazmat || $storageBin->customer)
                        <div class="pt-4 border-t border-gray-200">
                            <label class="block text-xs font-medium text-gray-500 mb-2">Special Information</label>
                            <div class="space-y-2">
                                @if($storageBin->is_hazmat)
                                    <div class="flex items-center px-3 py-2 bg-orange-50 border border-orange-200 rounded-lg">
                                        <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>
                                        <span class="text-sm text-orange-800 font-medium">Hazmat Storage</span>
                                    </div>
                                @endif
                                @if($storageBin->customer)
                                    <div class="flex items-center px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                                        <i class="fas fa-user text-blue-600 mr-2"></i>
                                        <div>
                                            <p class="text-xs text-blue-600">Dedicated Customer</p>
                                            <p class="text-sm text-blue-900 font-medium">{{ $storageBin->customer->name }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column - Add Stock Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-box mr-2"></i>
                        Stock Information
                    </h2>
                </div>

                <form action="{{ route('master.storage-bins.add-stock.store', $storageBin) }}" method="POST" class="p-6">
                    @csrf

                    <div class="space-y-6">
                        {{-- Product Selection --}}
                        <div>
                            <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Product <span class="text-red-500">*</span>
                            </label>
                            <select name="product_id" id="product_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('product_id') border-red-500 @enderror">
                                <option value="">-- Select Product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-sku="{{ $product->sku }}"
                                            data-weight="{{ $product->weight_kg ?? 0 }}"
                                            data-volume="{{ $product->volume_cbm ?? 0 }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            {{-- Product Info Display --}}
                            <div id="productInfo" class="mt-3 hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600">SKU:</p>
                                            <p class="font-semibold text-gray-900" id="displaySku">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Weight per unit:</p>
                                            <p class="font-semibold text-gray-900" id="displayWeight">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Volume per unit:</p>
                                            <p class="font-semibold text-gray-900" id="displayVolume">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Quantity and Unit --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="quantity" id="quantity" step="0.01" min="0.01" required
                                    value="{{ old('quantity') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quantity') border-red-500 @enderror"
                                    placeholder="Enter quantity">
                                @error('quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="unit_of_measure" class="block text-sm font-medium text-gray-700 mb-2">
                                    Unit of Measure <span class="text-red-500">*</span>
                                </label>
                                <select name="unit_of_measure" id="unit_of_measure" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('unit_of_measure') border-red-500 @enderror">
                                    <option value="">-- Select Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->short_code }}" {{ old('unit_of_measure') == $unit->short_code ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->short_code }}) - {{ ucfirst($unit->type) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_of_measure')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Batch and Serial Number --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Batch Number
                                    <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input type="text" name="batch_number" id="batch_number" maxlength="100"
                                    value="{{ old('batch_number') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('batch_number') border-red-500 @enderror"
                                    placeholder="e.g., BATCH-2025-001">
                                @error('batch_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Serial Number
                                    <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input type="text" name="serial_number" id="serial_number" maxlength="100"
                                    value="{{ old('serial_number') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('serial_number') border-red-500 @enderror"
                                    placeholder="e.g., SN-123456789">
                                @error('serial_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Dates --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="manufacturing_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Manufacturing Date
                                    <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input type="date" name="manufacturing_date" id="manufacturing_date"
                                    value="{{ old('manufacturing_date') }}"
                                    max="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('manufacturing_date') border-red-500 @enderror">
                                @error('manufacturing_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Expiry Date
                                    <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input type="date" name="expiry_date" id="expiry_date"
                                    value="{{ old('expiry_date') }}"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expiry_date') border-red-500 @enderror">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                                <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <textarea name="notes" id="notes" rows="4" maxlength="500"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                                placeholder="Enter any additional notes or comments...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <span id="notesCount">0</span>/500 characters
                            </p>
                        </div>

                        {{-- Calculated Summary --}}
                        <div id="calculatedSummary" class="hidden">
                            <div class="bg-gradient-to-r from-purple-50 to-purple-100 border-2 border-purple-300 rounded-lg p-6">
                                <h3 class="text-sm font-semibold text-purple-900 mb-4 flex items-center">
                                    <i class="fas fa-calculator mr-2"></i>
                                    Calculated Summary
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-purple-700 mb-1">Total Weight</p>
                                        <p class="text-lg font-bold text-purple-900" id="totalWeight">0 kg</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-purple-700 mb-1">Total Volume</p>
                                        <p class="text-lg font-bold text-purple-900" id="totalVolume">0 m³</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                            <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold inline-flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i>
                                Add Stock to Bin
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

            {{-- Help Card --}}
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Important Information
                </h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Make sure the product matches the bin's capacity limits</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Batch number is recommended for traceability</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Expiry date will be monitored automatically</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Stock movement will be recorded in history</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const productInfo = document.getElementById('productInfo');
    const calculatedSummary = document.getElementById('calculatedSummary');
    const notesTextarea = document.getElementById('notes');
    const notesCount = document.getElementById('notesCount');

    // Product change handler
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            // Show product info
            productInfo.classList.remove('hidden');
            
            // Display product details
            document.getElementById('displaySku').textContent = selectedOption.dataset.sku || '-';
            document.getElementById('displayWeight').textContent = selectedOption.dataset.weight 
                ? parseFloat(selectedOption.dataset.weight).toFixed(2) + ' kg' 
                : '-';
            document.getElementById('displayVolume').textContent = selectedOption.dataset.volume 
                ? parseFloat(selectedOption.dataset.volume).toFixed(3) + ' m³' 
                : '-';
            
            // Calculate totals if quantity is entered
            calculateTotals();
        } else {
            productInfo.classList.add('hidden');
            calculatedSummary.classList.add('hidden');
        }
    });

    // Quantity change handler
    quantityInput.addEventListener('input', function() {
        calculateTotals();
    });

    // Calculate totals function
    function calculateTotals() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const quantity = parseFloat(quantityInput.value) || 0;
        
        if (productSelect.value && quantity > 0) {
            const weightPerUnit = parseFloat(selectedOption.dataset.weight) || 0;
            const volumePerUnit = parseFloat(selectedOption.dataset.volume) || 0;
            
            const totalWeight = weightPerUnit * quantity;
            const totalVolume = volumePerUnit * quantity;
            
            document.getElementById('totalWeight').textContent = totalWeight.toFixed(2) + ' kg';
            document.getElementById('totalVolume').textContent = totalVolume.toFixed(3) + ' m³';
            
            calculatedSummary.classList.remove('hidden');
        } else {
            calculatedSummary.classList.add('hidden');
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
    document.querySelector('form').addEventListener('submit', function(e) {
        const quantity = parseFloat(quantityInput.value);
        
        if (quantity <= 0) {
            e.preventDefault();
            quantityInput.focus();
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    });

    // Initialize on page load if old values exist
    if (productSelect.value) {
        productSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush

@endsection