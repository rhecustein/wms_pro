@extends('layouts.app')

@section('title', 'Edit Good Receiving')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-yellow-600 mr-2"></i>
                Edit Good Receiving
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $goodReceiving->gr_number }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('inbound.good-receivings.show', $goodReceiving) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Details
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <form action="{{ route('inbound.good-receivings.update', $goodReceiving) }}" method="POST" id="grEditForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-green-600 mr-2"></i>
                        Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse <span class="text-red-500">*</span></label>
                            <select name="warehouse_id" id="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                                <option value="">Select Warehouse...</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $goodReceiving->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Supplier <span class="text-red-500">*</span></label>
                            <select name="supplier_id" id="supplier_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                                <option value="">Select Supplier...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $goodReceiving->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Receiving Date <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="receiving_date" value="{{ old('receiving_date', $goodReceiving->receiving_date->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                            @error('receiving_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="Additional notes...">{{ old('notes', $goodReceiving->notes) }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Items --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-boxes text-green-600 mr-2"></i>
                            Items
                        </h3>
                        <button type="button" onclick="addItem()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full" id="itemsTable">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expected</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Received</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pallets</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                @forelse($goodReceiving->items as $index => $item)
                                    <tr class="border-b border-gray-200">
                                        <td class="px-4 py-3">
                                            <select name="items[{{ $index }}][product_id]" class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                                                <option value="">Select Product...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" {{ old("items.$index.product_id", $item->product_id) == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }} ({{ $product->sku }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="items[{{ $index }}][quantity_expected]" value="{{ old("items.$index.quantity_expected", $item->quantity_expected) }}" class="w-24 rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500" min="0" required>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="items[{{ $index }}][quantity_received]" value="{{ old("items.$index.quantity_received", $item->quantity_received) }}" class="w-24 rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500" min="0" required>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="items[{{ $index }}][pallets]" value="{{ old("items.$index.pallets", $item->pallets) }}" class="w-20 rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500" min="0">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="items[{{ $index }}][notes]" value="{{ old("items.$index.notes", $item->notes) }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500" placeholder="Notes...">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-3xl mb-2"></i>
                                            <p>No items added yet. Click "Add Item" to start.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-clipboard-list text-green-600 mr-2"></i>
                        Summary
                    </h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Items</span>
                            <span class="text-lg font-bold text-gray-900" id="totalItems">0</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Quantity</span>
                            <span class="text-lg font-bold text-gray-900" id="totalQuantity">0</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Pallets</span>
                            <span class="text-lg font-bold text-gray-900" id="totalPallets">0</span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Update Good Receiving
                        </button>
                        <a href="{{ route('inbound.good-receivings.show', $goodReceiving) }}" class="w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold text-center block">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>

                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-xs text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Note:</strong> Changes will be saved and the good receiving status will remain {{ $goodReceiving->status }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>

@push('scripts')
<script>
let itemIndex = {{ $goodReceiving->items->count() }};

function addItem() {
    const tbody = document.getElementById('itemsBody');
    
    // Remove empty state if exists
    const emptyRow = tbody.querySelector('td[colspan="6"]');
    if (emptyRow) {
        emptyRow.parentElement.remove();
    }
    
    const row = `
        <tr class="border-b border-gray-200">
            <td class="px-4 py-3">
                <select name="items[${itemIndex}][product_id]" class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                    <option value="">Select Product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-3">
                <input type="number" name="items[${itemIndex}][quantity_expected]" value="0" class="w-24 rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500" min="0" required>
            </td>
            <td class="px-4 py-3">
                <input type="number" name="items[${itemIndex}][quantity_received]" value="0" class="w-24 rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500" min="0" required>
            </td>
            <td class="px-4 py-3">
                <input type="number" name="items[${itemIndex}][pallets]" value="0" class="w-20 rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500" min="0">
            </td>
            <td class="px-4 py-3">
                <input type="text" name="items[${itemIndex}][notes]" class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500" placeholder="Notes...">
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    tbody.insertAdjacentHTML('beforeend', row);
    itemIndex++;
    updateSummary();
}

function removeItem(button) {
    const row = button.closest('tr');
    row.remove();
    
    const tbody = document.getElementById('itemsBody');
    if (tbody.children.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p>No items added yet. Click "Add Item" to start.</p>
                </td>
            </tr>
        `;
    }
    
    updateSummary();
}

function updateSummary() {
    const rows = document.querySelectorAll('#itemsBody tr');
    let totalItems = 0;
    let totalQuantity = 0;
    let totalPallets = 0;
    
    rows.forEach(row => {
        const qtyInput = row.querySelector('input[name*="[quantity_received]"]');
        const palletInput = row.querySelector('input[name*="[pallets]"]');
        
        if (qtyInput && palletInput) {
            totalItems++;
            totalQuantity += parseInt(qtyInput.value) || 0;
            totalPallets += parseInt(palletInput.value) || 0;
        }
    });
    
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('totalQuantity').textContent = totalQuantity.toLocaleString();
    document.getElementById('totalPallets').textContent = totalPallets;
}

// Update summary on input change
document.addEventListener('input', function(e) {
    if (e.target.name && (e.target.name.includes('quantity_received') || e.target.name.includes('pallets'))) {
        updateSummary();
    }
});

// Initialize summary on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
});
</script>
@endpush
@endsection