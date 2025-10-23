{{-- resources/views/outbound/picking-orders/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Picking Order - ' . $pickingOrder->picking_number)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('outbound.picking-orders.show', $pickingOrder) }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-edit text-yellow-600 mr-2"></i>
                    Edit Picking Order
                </h1>
                <p class="text-sm text-gray-600 mt-1">{{ $pickingOrder->picking_number }}</p>
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
            <ul class="list-disc list-inside ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('outbound.picking-orders.update', $pickingOrder) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2">
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                        Picking Information
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Picking Number
                            </label>
                            <input type="text" value="{{ $pickingOrder->picking_number }}" class="w-full rounded-lg border-gray-300 bg-gray-100" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sales Order
                            </label>
                            <input type="text" value="{{ $pickingOrder->salesOrder->so_number }}" class="w-full rounded-lg border-gray-300 bg-gray-100" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Picking Date <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="picking_date" value="{{ old('picking_date', $pickingOrder->picking_date->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500" required>
                            @error('picking_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Picking Type <span class="text-red-500">*</span>
                            </label>
                            <select name="picking_type" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500" required>
                                <option value="single_order" {{ old('picking_type', $pickingOrder->picking_type) == 'single_order' ? 'selected' : '' }}>Single Order</option>
                                <option value="batch" {{ old('picking_type', $pickingOrder->picking_type) == 'batch' ? 'selected' : '' }}>Batch</option>
                                <option value="wave" {{ old('picking_type', $pickingOrder->picking_type) == 'wave' ? 'selected' : '' }}>Wave</option>
                                <option value="zone" {{ old('picking_type', $pickingOrder->picking_type) == 'zone' ? 'selected' : '' }}>Zone</option>
                            </select>
                            @error('picking_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <select name="priority" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500" required>
                                <option value="low" {{ old('priority', $pickingOrder->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $pickingOrder->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $pickingOrder->priority) == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority', $pickingOrder->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Assign To (Optional)
                            </label>
                            <select name="assigned_to" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $pickingOrder->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500" placeholder="Add any special instructions or notes...">{{ old('notes', $pickingOrder->notes) }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Current Items (Read-only) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-list text-yellow-600 mr-2"></i>
                        Picking Items (Read-only)
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Seq</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Storage Bin</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($pickingOrder->items->sortBy('pick_sequence') as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 bg-gray-100 rounded-full text-sm font-semibold">{{ $item->pick_sequence }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->product->sku }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">{{ $item->storageBin->bin_code }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm text-gray-900">{{ number_format($item->quantity_requested) }} {{ $item->unit_of_measure }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            {!! $item->status_badge !!}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Items cannot be modified after the picking order is created. You can only update the basic information above.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                        Order Information
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="pb-3 border-b border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Current Status</p>
                            <div class="mt-1">{!! $pickingOrder->status_badge !!}</div>
                        </div>

                        <div class="pb-3 border-b border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Warehouse</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $pickingOrder->warehouse->name }}</p>
                        </div>

                        <div class="pb-3 border-b border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Total Items</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $pickingOrder->total_items }} items</p>
                        </div>

                        <div class="pb-3 border-b border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Total Quantity</p>
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($pickingOrder->total_quantity) }}</p>
                        </div>

                        <div class="pb-3 border-b border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Created At</p>
                            <p class="text-sm text-gray-900">{{ $pickingOrder->created_at->format('d M Y, H:i') }}</p>
                            @if($pickingOrder->createdBy)
                                <p class="text-xs text-gray-500">by {{ $pickingOrder->createdBy->name }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <button type="submit" class="w-full px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Update Picking Order
                        </button>
                        <a href="{{ route('outbound.picking-orders.show', $pickingOrder) }}" class="block w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>

                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <h3 class="text-sm font-semibold text-yellow-900 mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Note
                        </h3>
                        <p class="text-xs text-yellow-800">
                            Only basic information can be updated. Items and quantities cannot be modified after creation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection