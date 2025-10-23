{{-- resources/views/outbound/picking-orders/execute.blade.php --}}

@extends('layouts.app')

@section('title', 'Execute Picking - ' . $pickingOrder->picking_number)

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
                    <i class="fas fa-tasks text-purple-600 mr-2"></i>
                    Execute Picking
                </h1>
                <p class="text-sm text-gray-600 mt-1">{{ $pickingOrder->picking_number }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="px-4 py-2 bg-purple-100 rounded-lg">
                <span class="text-sm font-medium text-purple-800">Progress:</span>
                <span class="text-lg font-bold text-purple-600 ml-2" id="progressPercentage">{{ $pickingOrder->progress_percentage }}%</span>
            </div>
            <form action="{{ route('outbound.picking-orders.complete', $pickingOrder) }}" method="POST" id="completeForm">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" id="completeBtn" disabled>
                    <i class="fas fa-check-circle mr-2"></i>Complete Picking
                </button>
            </form>
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

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Main Picking Area --}}
        <div class="lg:col-span-3">
            {{-- Progress Bar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                    <span class="text-sm font-semibold text-gray-900">
                        <span id="pickedCount">{{ $pickingOrder->items->where('status', 'picked')->count() }}</span> / 
                        {{ $pickingOrder->total_items }} Items
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div id="progressBar" class="bg-gradient-to-r from-purple-500 to-green-500 h-4 rounded-full transition-all duration-500" style="width: {{ $pickingOrder->progress_percentage }}%"></div>
                </div>
            </div>

            {{-- Picking Items List --}}
            <div class="space-y-4">
                @foreach($pickingOrder->items->sortBy('pick_sequence') as $item)
                    <div class="bg-white rounded-xl shadow-sm border-2 {{ $item->status === 'picked' ? 'border-green-300 bg-green-50' : 'border-gray-200' }} p-6 picking-item" data-item-id="{{ $item->id }}">
                        <div class="flex items-start">
                            {{-- Sequence Number --}}
                            <div class="flex-shrink-0 mr-4">
                                <div class="w-12 h-12 {{ $item->status === 'picked' ? 'bg-green-500' : 'bg-purple-500' }} rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    @if($item->status === 'picked')
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $item->pick_sequence }}
                                    @endif
                                </div>
                            </div>

                            {{-- Item Details --}}
                            <div class="flex-grow">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $item->product->name }}</h3>
                                        <p class="text-sm text-gray-600">SKU: {{ $item->product->sku }}</p>
                                    </div>
                                    <div class="text-right">
                                        {!! $item->status_badge !!}
                                    </div>
                                </div>

                                <div class="grid grid-cols-4 gap-4 mb-4">
                                    {{-- Storage Location --}}
                                    <div class="bg-blue-50 rounded-lg p-3">
                                        <p class="text-xs text-blue-600 font-medium mb-1">LOCATION</p>
                                        <p class="text-sm font-bold text-blue-900">{{ $item->storageBin->bin_code }}</p>
                                        <p class="text-xs text-blue-700">{{ $item->storageBin->location ?? 'N/A' }}</p>
                                    </div>

                                    {{-- Quantity --}}
                                    <div class="bg-purple-50 rounded-lg p-3">
                                        <p class="text-xs text-purple-600 font-medium mb-1">QUANTITY</p>
                                        <p class="text-xl font-bold text-purple-900">{{ number_format($item->quantity_requested) }}</p>
                                        <p class="text-xs text-purple-700">{{ $item->unit_of_measure }}</p>
                                    </div>

                                    {{-- Batch/Serial --}}
                                    <div class="bg-yellow-50 rounded-lg p-3">
                                        <p class="text-xs text-yellow-600 font-medium mb-1">BATCH/SERIAL</p>
                                        @if($item->batch_number)
                                            <p class="text-sm font-bold text-yellow-900">{{ $item->batch_number }}</p>
                                            <p class="text-xs text-yellow-700">Batch</p>
                                        @elseif($item->serial_number)
                                            <p class="text-sm font-bold text-yellow-900">{{ $item->serial_number }}</p>
                                            <p class="text-xs text-yellow-700">Serial</p>
                                        @else
                                            <p class="text-sm text-yellow-700">N/A</p>
                                        @endif
                                    </div>

                                    {{-- Expiry --}}
                                    <div class="bg-red-50 rounded-lg p-3">
                                        <p class="text-xs text-red-600 font-medium mb-1">EXPIRY DATE</p>
                                        @if($item->expiry_date)
                                            <p class="text-sm font-bold text-red-900">{{ $item->expiry_date->format('d M Y') }}</p>
                                            <p class="text-xs text-red-700">
                                                @if($item->expiry_date->isPast())
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>Expired
                                                @elseif($item->expiry_date->diffInDays(now()) <= 30)
                                                    <i class="fas fa-exclamation-circle mr-1"></i>Expiring Soon
                                                @else
                                                    Valid
                                                @endif
                                            </p>
                                        @else
                                            <p class="text-sm text-red-700">N/A</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Picking Actions --}}
                                @if($item->status !== 'picked')
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-grow">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Quantity Picked</label>
                                            <input type="number" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 picked-quantity" data-item-id="{{ $item->id }}" min="0" max="{{ $item->quantity_requested }}" value="{{ $item->quantity_requested }}" placeholder="Enter quantity">
                                        </div>
                                        <div class="flex-shrink-0 pt-5">
                                            <button type="button" onclick="confirmPick({{ $item->id }})" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                                <i class="fas fa-check mr-2"></i>Confirm Pick
                                            </button>
                                        </div>
                                    </div>

                                    @if($item->notes)
                                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <p class="text-xs font-medium text-yellow-800">
                                                <i class="fas fa-sticky-note mr-1"></i>Notes: {{ $item->notes }}
                                            </p>
                                        </div>
                                    @endif
                                @else
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-semibold text-green-900">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Picked: {{ number_format($item->quantity_picked) }} {{ $item->unit_of_measure }}
                                                </p>
                                                @if($item->pickedBy)
                                                    <p class="text-xs text-green-700 mt-1">
                                                        By {{ $item->pickedBy->name }} at {{ $item->picked_at->format('d M Y, H:i') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <button type="button" onclick="unpickItem({{ $item->id }})" class="text-red-600 hover:text-red-800 text-sm">
                                                <i class="fas fa-undo mr-1"></i>Undo
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="lg:col-span-1">
            {{-- Order Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                    Order Summary
                </h2>
                
                <div class="space-y-3">
                    <div class="pb-3 border-b border-gray-200">
                        <p class="text-xs text-gray-500 mb-1">Sales Order</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $pickingOrder->salesOrder->so_number }}</p>
                    </div>

                    <div class="pb-3 border-b border-gray-200">
                        <p class="text-xs text-gray-500 mb-1">Customer</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $pickingOrder->salesOrder->customer->name ?? 'N/A' }}</p>
                    </div>

                    <div class="pb-3 border-b border-gray-200">
                        <p class="text-xs text-gray-500 mb-1">Warehouse</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $pickingOrder->warehouse->name }}</p>
                    </div>

                    <div class="pb-3 border-b border-gray-200">
                        <p class="text-xs text-gray-500 mb-1">Priority</p>
                        <div class="mt-1">{!! $pickingOrder->priority_badge !!}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $pickingOrder->total_items }}</p>
                            <p class="text-xs text-blue-700">Total Items</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-purple-600">{{ number_format($pickingOrder->total_quantity) }}</p>
                            <p class="text-xs text-purple-700">Total Qty</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <h3 class="text-sm font-semibold text-yellow-900 mb-2">
                        <i class="fas fa-lightbulb mr-1"></i>Picking Tips
                    </h3>
                    <ul class="text-xs text-yellow-800 space-y-1">
                        <li>• Follow the sequence order</li>
                        <li>• Verify bin location carefully</li>
                        <li>• Check expiry dates</li>
                        <li>• Confirm batch/serial numbers</li>
                        <li>• Report any discrepancies</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function confirmPick(itemId) {
    const quantityInput = document.querySelector(`.picked-quantity[data-item-id="${itemId}"]`);
    const quantity = parseInt(quantityInput.value);
    
    if (!quantity || quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }
    
    if (confirm('Confirm picking this item?')) {
        // Send AJAX request to update item status
        fetch(`/api/picking-items/${itemId}/pick`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                quantity_picked: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                location.reload(); // Simple reload for now
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to confirm pick');
        });
    }
}

function unpickItem(itemId) {
    if (confirm('Are you sure you want to undo this pick?')) {
        fetch(`/api/picking-items/${itemId}/unpick`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to undo pick');
        });
    }
}

// Check if all items are picked to enable complete button
function checkCompletion() {
    const totalItems = {{ $pickingOrder->total_items }};
    const pickedItems = {{ $pickingOrder->items->where('status', 'picked')->count() }};
    
    if (pickedItems === totalItems) {
        document.getElementById('completeBtn').disabled = false;
        document.getElementById('completeBtn').classList.remove('opacity-50');
    }
}

// Auto-check on page load
checkCompletion();

// Confirm before completing
document.getElementById('completeForm').addEventListener('submit', function(e) {
    if (!confirm('Are you sure you want to complete this picking order? This action cannot be undone.')) {
        e.preventDefault();
    }
});
</script>

@endsection