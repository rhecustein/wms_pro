@extends('layouts.app')

@section('title', 'Create Replenishment Task')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                Create Replenishment Task
            </h1>
            <p class="text-sm text-gray-600 mt-1">Create a new replenishment task</p>
        </div>
        <a href="{{ route('operations.replenishments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('operations.replenishments.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Warehouse --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" id="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('warehouse_id') border-red-500 @enderror" required>
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('warehouse_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Product --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product <span class="text-red-500">*</span></label>
                    <select name="product_id" id="product_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('product_id') border-red-500 @enderror" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} - {{ $product->sku }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- From Storage Bin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Storage Bin (High Rack) <span class="text-red-500">*</span></label>
                    <select name="from_storage_bin_id" id="from_storage_bin_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('from_storage_bin_id') border-red-500 @enderror" required>
                        <option value="">Select Storage Bin</option>
                    </select>
                    @error('from_storage_bin_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- To Storage Bin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Storage Bin (Pick Face) <span class="text-red-500">*</span></label>
                    <select name="to_storage_bin_id" id="to_storage_bin_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('to_storage_bin_id') border-red-500 @enderror" required>
                        <option value="">Select Storage Bin</option>
                    </select>
                    @error('to_storage_bin_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Batch Number --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                    <input type="text" name="batch_number" value="{{ old('batch_number') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('batch_number') border-red-500 @enderror" placeholder="e.g., BATCH-001">
                    @error('batch_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Serial Number --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('serial_number') border-red-500 @enderror" placeholder="e.g., SN-12345">
                    @error('serial_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Quantity Suggested --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Suggested <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity_suggested" value="{{ old('quantity_suggested') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('quantity_suggested') border-red-500 @enderror" placeholder="e.g., 100" min="1" required>
                    @error('quantity_suggested')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Unit of Measure --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit of Measure <span class="text-red-500">*</span></label>
                    <input type="text" name="unit_of_measure" value="{{ old('unit_of_measure', 'PCS') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('unit_of_measure') border-red-500 @enderror" placeholder="e.g., PCS, KG, BOX" required>
                    @error('unit_of_measure')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('priority') border-red-500 @enderror" required>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Trigger Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trigger Type <span class="text-red-500">*</span></label>
                    <select name="trigger_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('trigger_type') border-red-500 @enderror" required>
                        <option value="manual" {{ old('trigger_type', 'manual') == 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="min_level" {{ old('trigger_type') == 'min_level' ? 'selected' : '' }}>Min Level</option>
                        <option value="empty_pick_face" {{ old('trigger_type') == 'empty_pick_face' ? 'selected' : '' }}>Empty Pick Face</option>
                    </select>
                    @error('trigger_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Assigned To --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign To</label>
                    <select name="assigned_to" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('assigned_to') border-red-500 @enderror">
                        <option value="">Unassigned</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror" placeholder="Enter any additional notes...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('operations.replenishments.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Create Task
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const warehouseSelect = document.getElementById('warehouse_id');
    const productSelect = document.getElementById('product_id');
    const fromBinSelect = document.getElementById('from_storage_bin_id');
    const toBinSelect = document.getElementById('to_storage_bin_id');

    // Load storage bins when warehouse changes
    warehouseSelect.addEventListener('change', function() {
        const warehouseId = this.value;
        
        // Reset bins
        fromBinSelect.innerHTML = '<option value="">Select Storage Bin</option>';
        toBinSelect.innerHTML = '<option value="">Select Storage Bin</option>';
        
        if (warehouseId) {
            // In production, you would fetch bins via AJAX
            // For now, this is a placeholder
            console.log('Load bins for warehouse:', warehouseId);
        }
    });
});
</script>
@endpush
@endsection