@extends('layouts.app')

@section('title', 'Edit Packing Order')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-yellow-600 mr-2"></i>
                Edit Packing Order
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $packingOrder->packing_number }}</p>
        </div>
        <a href="{{ route('outbound.packing-orders.show', $packingOrder) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Details
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Please fix the following errors:</span>
            </div>
            @if(session('error'))
                <p class="mb-2">{{ session('error') }}</p>
            @endif
            <ul class="list-disc list-inside ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('outbound.packing-orders.update', $packingOrder) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Packing Information
                    </h3>

                    <div class="space-y-4">
                        {{-- Packing Number (Read Only) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Packing Number
                            </label>
                            <input type="text" value="{{ $packingOrder->packing_number }}" class="w-full rounded-lg border-gray-300 bg-gray-100" readonly>
                        </div>

                        {{-- Sales Order (Read Only) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sales Order
                            </label>
                            <input type="text" value="{{ $packingOrder->salesOrder->order_number }} - {{ $packingOrder->salesOrder->customer->name ?? '' }}" class="w-full rounded-lg border-gray-300 bg-gray-100" readonly>
                        </div>

                        {{-- Warehouse --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $packingOrder->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
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
                            <input type="datetime-local" name="packing_date" value="{{ old('packing_date', $packingOrder->packing_date->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
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
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $packingOrder->assigned_to) == $user->id ? 'selected' : '' }}>
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
                            <textarea name="notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Additional packing instructions or notes...">{{ old('notes', $packingOrder->notes) }}</textarea>
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
                            <i class="fas fa-save mr-2"></i>Update Packing Order
                        </button>
                        <a href="{{ route('outbound.packing-orders.show', $packingOrder) }}" class="block w-full px-4 py-2 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300 transition">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>

                {{-- Current Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Current Status
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-medium text-gray-500">Status</label>
                            <div class="mt-1">{!! $packingOrder->status_badge !!}</div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500">Created</label>
                            <p class="text-sm text-gray-900">{{ $packingOrder->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        @if($packingOrder->started_at)
                            <div>
                                <label class="text-xs font-medium text-gray-500">Started</label>
                                <p class="text-sm text-gray-900">{{ $packingOrder->started_at->format('d M Y, H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Warning --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <h4 class="font-semibold text-yellow-900 mb-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Notice
                    </h4>
                    <ul class="text-sm text-yellow-800 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-info-circle mt-1 mr-2"></i>
                            <span>Only basic information can be edited</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-info-circle mt-1 mr-2"></i>
                            <span>Picking order cannot be changed</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection