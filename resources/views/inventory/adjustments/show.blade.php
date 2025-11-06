{{-- resources/views/inventory/adjustments/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock Adjustment Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                Adjustment Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $adjustment->adjustment_number }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('inventory.adjustments.print', $adjustment) }}" 
               target="_blank"
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition inline-flex items-center">
                <i class="fas fa-print mr-2"></i>Print
            </a>
            @if($adjustment->status === 'draft')
                <a href="{{ route('inventory.adjustments.edit', $adjustment) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('inventory.adjustments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Adjustment Number</label>
                        <p class="text-gray-900 font-mono font-semibold">{{ $adjustment->adjustment_number }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Adjustment Date</label>
                        <p class="text-gray-900">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>
                            {{ $adjustment->adjustment_date->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Warehouse</label>
                        <p class="text-gray-900 font-semibold">{{ $adjustment->warehouse->name }}</p>
                        <p class="text-xs text-gray-500">{{ $adjustment->warehouse->code }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                        <p>{!! $adjustment->type_badge !!}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Reason</label>
                        <p class="text-gray-900 capitalize">{{ str_replace('_', ' ', $adjustment->reason) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <p>{!! $adjustment->status_badge !!}</p>
                    </div>

                    @if($adjustment->notes)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Notes</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $adjustment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Adjustment Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-box text-blue-600 mr-2"></i>
                    Adjustment Items ({{ $adjustment->items->count() }})
                </h3>

                <div class="space-y-4">
                    @foreach($adjustment->items as $item)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-cube text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $item->product->name }}</h4>
                                        <p class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @php
                                        $difference = $item->adjusted_quantity - $item->current_quantity;
                                        $diffClass = $difference > 0 ? 'text-green-600' : ($difference < 0 ? 'text-red-600' : 'text-gray-600');
                                        $diffIcon = $difference > 0 ? 'fa-arrow-up' : ($difference < 0 ? 'fa-arrow-down' : 'fa-equals');
                                    @endphp
                                    <span class="text-2xl font-bold {{ $diffClass }}">
                                        <i class="fas {{ $diffIcon }} mr-1"></i>
                                        {{ abs($difference) }}
                                    </span>
                                    <p class="text-xs text-gray-500">{{ $item->unit_of_measure }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-500">Storage Bin:</span>
                                    <p class="font-semibold text-gray-900">{{ $item->storageBin->bin_code }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Current Qty:</span>
                                    <p class="font-semibold text-gray-900">{{ $item->current_quantity }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Adjusted Qty:</span>
                                    <p class="font-semibold text-gray-900">{{ $item->adjusted_quantity }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Difference:</span>
                                    <p class="font-semibold {{ $diffClass }}">
                                        {{ $difference > 0 ? '+' : '' }}{{ $difference }}
                                    </p>
                                </div>
                            </div>

                            @if($item->batch_number || $item->serial_number)
                                <div class="grid grid-cols-2 gap-3 text-sm mt-3 pt-3 border-t">
                                    @if($item->batch_number)
                                        <div>
                                            <span class="text-gray-500">Batch Number:</span>
                                            <p class="font-semibold text-gray-900">{{ $item->batch_number }}</p>
                                        </div>
                                    @endif
                                    @if($item->serial_number)
                                        <div>
                                            <span class="text-gray-500">Serial Number:</span>
                                            <p class="font-semibold text-gray-900">{{ $item->serial_number }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if($item->reason || $item->notes)
                                <div class="mt-3 pt-3 border-t">
                                    @if($item->reason)
                                        <p class="text-sm text-gray-600"><strong>Reason:</strong> {{ $item->reason }}</p>
                                    @endif
                                    @if($item->notes)
                                        <p class="text-sm text-gray-600 mt-1"><strong>Notes:</strong> {{ $item->notes }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Audit Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Audit Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block text-gray-500 mb-1">Created By</label>
                        <p class="text-gray-900 font-semibold">
                            <i class="fas fa-user text-gray-400 mr-1"></i>
                            {{ $adjustment->createdBy->name ?? 'System' }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $adjustment->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    @if($adjustment->updatedBy)
                        <div>
                            <label class="block text-gray-500 mb-1">Last Updated By</label>
                            <p class="text-gray-900 font-semibold">
                                <i class="fas fa-user text-gray-400 mr-1"></i>
                                {{ $adjustment->updatedBy->name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $adjustment->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif

                    @if($adjustment->approvedBy)
                        <div>
                            <label class="block text-gray-500 mb-1">Approved By</label>
                            <p class="text-gray-900 font-semibold">
                                <i class="fas fa-user-check text-green-500 mr-1"></i>
                                {{ $adjustment->approvedBy->name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $adjustment->approved_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-cog text-blue-600 mr-2"></i>
                    Actions
                </h3>
                
                <div class="space-y-2">
                    @if($adjustment->status === 'draft')
                        <form action="{{ route('inventory.adjustments.approve', $adjustment) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold" onclick="return confirm('Are you sure you want to approve this adjustment?')">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                        </form>

                        <a href="{{ route('inventory.adjustments.edit', $adjustment) }}" class="block w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-center text-sm font-semibold">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>

                        <form action="{{ route('inventory.adjustments.cancel', $adjustment) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold" onclick="return confirm('Are you sure you want to cancel this adjustment?')">
                                <i class="fas fa-ban mr-2"></i>Cancel
                            </button>
                        </form>
                    @endif

                    @if($adjustment->status === 'approved')
                        <form action="{{ route('inventory.adjustments.post', $adjustment) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold" onclick="return confirm('This will update inventory levels. Continue?')">
                                <i class="fas fa-paper-plane mr-2"></i>Post to Inventory
                            </button>
                        </form>

                        <form action="{{ route('inventory.adjustments.cancel', $adjustment) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold" onclick="return confirm('Are you sure you want to cancel this adjustment?')">
                                <i class="fas fa-ban mr-2"></i>Cancel
                            </button>
                        </form>
                    @endif

                    @if($adjustment->status === 'posted')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                            <p class="text-sm text-green-800 font-semibold">Posted to Inventory</p>
                        </div>
                    @endif

                    @if($adjustment->status === 'cancelled')
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                            <i class="fas fa-ban text-red-600 text-2xl mb-2"></i>
                            <p class="text-sm text-red-800 font-semibold">Cancelled</p>
                        </div>
                    @endif

                    <hr class="my-3">

                    <a href="{{ route('inventory.adjustments.print', $adjustment) }}" 
                       target="_blank"
                       class="block w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm font-semibold text-center">
                        <i class="fas fa-print mr-2"></i>Print
                    </a>

                    @if($adjustment->status === 'draft')
                        <form action="{{ route('inventory.adjustments.destroy', $adjustment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this adjustment?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-semibold">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                    Summary
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-gray-600">Total Items:</span>
                        <span class="font-semibold text-gray-900">{{ $adjustment->total_items }}</span>
                    </div>
                    
                    @php
                        $totalAdditions = 0;
                        $totalReductions = 0;
                        foreach($adjustment->items as $item) {
                            $diff = $item->adjusted_quantity - $item->current_quantity;
                            if($diff > 0) $totalAdditions += $diff;
                            if($diff < 0) $totalReductions += abs($diff);
                        }
                    @endphp

                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-gray-600">Total Additions:</span>
                        <span class="font-semibold text-green-600">+{{ $totalAdditions }}</span>
                    </div>

                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-gray-600">Total Reductions:</span>
                        <span class="font-semibold text-red-600">-{{ $totalReductions }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Status:</span>
                        <span>{!! $adjustment->status_badge !!}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection