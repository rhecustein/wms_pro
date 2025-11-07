{{-- resources/views/outbound/picking-orders/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Picking Order - ' . $pickingOrder->picking_number)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
        
        {{-- Modern Header --}}
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('outbound.picking-orders.show', $pickingOrder) }}" 
                   class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent flex items-center">
                        <i class="fas fa-edit text-yellow-600 mr-3"></i>
                        Edit Picking Order
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $pickingOrder->picking_number }}</p>
                </div>
            </div>
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl p-5 shadow-lg animate-fade-in">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-800 mb-2">Please fix the following errors:</h3>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm text-red-700">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('outbound.picking-orders.update', $pickingOrder) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Form --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Basic Information Card --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Basic Information</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Picking Number --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Picking Number
                                </label>
                                <input type="text" 
                                       value="{{ $pickingOrder->picking_number }}" 
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 cursor-not-allowed" 
                                       readonly>
                            </div>

                            {{-- Sales Order --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Sales Order
                                </label>
                                <input type="text" 
                                       value="{{ $pickingOrder->salesOrder->so_number }} - {{ $pickingOrder->salesOrder->customer->name ?? 'N/A' }}" 
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 cursor-not-allowed" 
                                       readonly>
                            </div>

                            {{-- Picking Date --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Picking Date <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="datetime-local" 
                                           name="picking_date" 
                                           value="{{ old('picking_date', $pickingOrder->picking_date->format('Y-m-d\TH:i')) }}" 
                                           class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all" 
                                           required>
                                    <i class="fas fa-calendar-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('picking_date')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Picking Type --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Picking Type <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="picking_type" 
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all" 
                                            required>
                                        <option value="single_order" {{ old('picking_type', $pickingOrder->picking_type) == 'single_order' ? 'selected' : '' }}>Single Order</option>
                                        <option value="batch" {{ old('picking_type', $pickingOrder->picking_type) == 'batch' ? 'selected' : '' }}>Batch</option>
                                        <option value="wave" {{ old('picking_type', $pickingOrder->picking_type) == 'wave' ? 'selected' : '' }}>Wave</option>
                                        <option value="zone" {{ old('picking_type', $pickingOrder->picking_type) == 'zone' ? 'selected' : '' }}>Zone</option>
                                    </select>
                                    <i class="fas fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('picking_type')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Priority --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="priority" 
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all" 
                                            required>
                                        <option value="low" {{ old('priority', $pickingOrder->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $pickingOrder->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $pickingOrder->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority', $pickingOrder->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    <i class="fas fa-flag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('priority')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Assign To --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Assign To (Optional)
                                </label>
                                <div class="relative">
                                    <select name="assigned_to" 
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all">
                                        <option value="">Unassigned</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to', $pickingOrder->assigned_to) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('assigned_to')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="mt-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" 
                                      rows="3" 
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all" 
                                      placeholder="Add any special instructions or notes...">{{ old('notes', $pickingOrder->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Picking Items Card (Read-only) --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-list text-white"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Picking Items (Read-only)</h2>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Seq</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Location</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Qty</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Batch/Lot</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($pickingOrder->items->sortBy('pick_sequence') as $item)
                                        <tr class="hover:bg-blue-50 transition-colors">
                                            <td class="px-4 py-3 text-center">
                                                <span class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-lg flex items-center justify-center font-bold text-sm inline-flex">
                                                    {{ $item->pick_sequence }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-gray-900">{{ $item->product->name }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $item->product->sku ?? $item->product->barcode ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($item->storageBin)
                                                    <div class="font-semibold text-gray-900">
                                                        {{ $item->storageBin->code ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $item->storageBin->name ?? '' }}
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-sm">No location</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="font-bold text-gray-900">{{ number_format($item->quantity_requested, 2) }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->unit_of_measure }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($item->batch_number || $item->lot_number)
                                                    @if($item->batch_number)
                                                        <div class="text-xs text-gray-700">
                                                            <span class="font-semibold">Batch:</span> {{ $item->batch_number }}
                                                        </div>
                                                    @endif
                                                    @if($item->lot_number)
                                                        <div class="text-xs text-gray-700">
                                                            <span class="font-semibold">Lot:</span> {{ $item->lot_number }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                        'picked' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                        'completed' => 'bg-green-100 text-green-800 border-green-200',
                                                        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                                    ];
                                                    $statusIcons = [
                                                        'pending' => 'fa-clock',
                                                        'picked' => 'fa-box',
                                                        'completed' => 'fa-check-circle',
                                                        'cancelled' => 'fa-times-circle',
                                                    ];
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-bold border-2 {{ $statusClasses[$item->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                                    <i class="fas {{ $statusIcons[$item->status] ?? 'fa-question' }} mr-1"></i>
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                                <p>No items found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl">
                            <p class="text-sm text-blue-800 flex items-start">
                                <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                                <span>Items cannot be modified after the picking order is created. You can only update the basic information above.</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Summary Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 sticky top-6">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Order Info</h2>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="pb-4 border-b border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Status</p>
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'assigned' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'in_progress' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'completed' => 'bg-green-100 text-green-800 border-green-200',
                                        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                @endphp
                                <div class="inline-block px-3 py-1 rounded-lg text-sm font-bold border-2 {{ $statusClasses[$pickingOrder->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $pickingOrder->status)) }}
                                </div>
                            </div>

                            <div class="pb-4 border-b border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Warehouse</p>
                                <p class="text-sm font-bold text-gray-900">{{ $pickingOrder->warehouse->name }}</p>
                            </div>

                            <div class="pb-4 border-b border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Total Items</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $pickingOrder->total_items }}</p>
                            </div>

                            <div class="pb-4 border-b border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Total Quantity</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($pickingOrder->total_quantity, 2) }}</p>
                            </div>

                            <div class="pb-4 border-b border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Created</p>
                                <p class="text-sm text-gray-900">{{ $pickingOrder->created_at->format('d M Y, H:i') }}</p>
                                @if($pickingOrder->createdBy)
                                    <p class="text-xs text-gray-500 mt-1">by {{ $pickingOrder->createdBy->name }}</p>
                                @endif
                            </div>

                            @if($pickingOrder->updated_at != $pickingOrder->created_at)
                                <div class="pb-4 border-b border-gray-200">
                                    <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Last Updated</p>
                                    <p class="text-sm text-gray-900">{{ $pickingOrder->updated_at->format('d M Y, H:i') }}</p>
                                    @if($pickingOrder->updatedBy)
                                        <p class="text-xs text-gray-500 mt-1">by {{ $pickingOrder->updatedBy->name }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 space-y-3">
                            <button type="submit" 
                                    class="w-full px-6 py-4 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all font-bold shadow-lg hover:shadow-xl">
                                <i class="fas fa-save mr-2"></i>Update Picking Order
                            </button>
                            <a href="{{ route('outbound.picking-orders.show', $pickingOrder) }}" 
                               class="block w-full px-6 py-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all text-center font-bold">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>

                        {{-- Warning Note --}}
                        <div class="mt-6 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl p-4 border-2 border-yellow-200">
                            <h3 class="text-sm font-bold text-yellow-900 mb-2 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Important Note
                            </h3>
                            <p class="text-xs text-yellow-800 leading-relaxed">
                                Only basic information can be updated. Items, quantities, and locations cannot be modified after creation.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection