@extends('layouts.app')

@section('title', 'Create Packing Order')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-box-open text-blue-600 mr-2"></i>
                Create Packing Order
            </h1>
            <p class="text-sm text-gray-600 mt-1">Create a new packing order from completed picking</p>
        </div>
        <a href="{{ route('outbound.packing-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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
            <ul class="list-disc list-inside ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('outbound.packing-orders.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Packing Information
                    </h3>

                    <div class="space-y-4">
                        {{-- Picking Order --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Picking Order <span class="text-red-500">*</span>
                            </label>
                            <select name="picking_order_id" id="picking_order_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Picking Order</option>
                                @foreach($pickingOrders as $picking)
                                    <option value="{{ $picking->id }}" 
                                            data-warehouse="{{ $picking->warehouse_id }}"
                                            data-order="{{ $picking->salesOrder->order_number }}"
                                            {{ old('picking_order_id') == $picking->id ? 'selected' : '' }}>
                                        {{ $picking->picking_number }} - {{ $picking->salesOrder->order_number }} ({{ $picking->warehouse->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('picking_order_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Warehouse --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" id="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Packing Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Packing Date <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="packing_date" value="{{ old('packing_date', now()->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            @error('packing_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Assigned To --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Assigned To
                            </label>
                            <select name="assigned_to" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select User (Optional)</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <textarea name="notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Additional packing instructions or notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- Actions --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-cogs text-blue-600 mr-2"></i>
                        Actions
                    </h3>
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i>Create Packing Order
                        </button>
                        <a href="{{ route('outbound.packing-orders.index') }}" class="block w-full px-4 py-2 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300 transition">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>

                {{-- Instructions --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h4 class="font-semibold text-blue-900 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>Instructions
                    </h4>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mt-1 mr-2"></i>
                            <span>Select a completed picking order</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mt-1 mr-2"></i>
                            <span>Assign to a packer if needed</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mt-1 mr-2"></i>
                            <span>After creation, you can execute the packing process</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
    // Auto-fill warehouse when picking order is selected
    document.getElementById('picking_order_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const warehouseId = selectedOption.getAttribute('data-warehouse');
        
        if (warehouseId) {
            document.getElementById('warehouse_id').value = warehouseId;
        }
    });
</script>
@endpush
@endsection