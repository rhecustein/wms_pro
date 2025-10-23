@extends('layouts.app')

@section('title', 'Execute Packing')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-box-open text-purple-600 mr-2"></i>
                Execute Packing
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $packingOrder->packing_number }}</p>
        </div>
        <div class="flex space-x-3">
            <form action="{{ route('outbound.packing-orders.complete', $packingOrder) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to complete this packing order?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-check mr-2"></i>Complete Packing
                </button>
            </form>
            <a href="{{ route('outbound.packing-orders.show', $packingOrder) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
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
        {{-- Scanning Section --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Scan Box Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-barcode text-purple-600 mr-2"></i>
                    Scan and Pack Items
                </h3>

                <form id="packingForm" class="space-y-4">
                    @csrf
                    
                    {{-- Box Number --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Box Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="box_number" name="box_number" placeholder="Scan or enter box number..." class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required autofocus>
                    </div>

                    {{-- Product Barcode --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Product Barcode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="product_barcode" name="product_barcode" placeholder="Scan product barcode..." class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required>
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="quantity" name="quantity" min="1" value="1" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required>
                    </div>

                    {{-- Box Weight --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Box Weight (kg)
                        </label>
                        <input type="number" id="box_weight" name="box_weight" step="0.01" min="0" placeholder="0.00" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>

                    {{-- Batch/Serial (Optional) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Batch Number (Optional)
                            </label>
                            <input type="text" id="batch_number" name="batch_number" placeholder="Batch..." class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Serial Number (Optional)
                            </label>
                            <input type="text" id="serial_number" name="serial_number" placeholder="Serial..." class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>

                    <button type="submit" class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                        <i class="fas fa-box mr-2"></i>Pack Item
                    </button>
                </form>
            </div>

            {{-- Items to Pack (From Picking Order) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-list text-blue-600 mr-2"></i>
                        Items to Pack
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Picked Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Packed Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pickingItems as $item)
                                @php
                                    $packedQty = $packingOrder->items->where('picking_order_item_id', $item->id)->sum('quantity_packed');
                                    $remaining = $item->quantity_picked - $packedQty;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-box text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->product->sku ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($item->quantity_picked) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-blue-600">{{ number_format($packedQty) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold {{ $remaining > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                            {{ number_format($remaining) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($remaining == 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Complete
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="fas fa-clock mr-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar - Packed Items Summary --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Order Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Order Info
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-gray-500">Sales Order</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $packingOrder->salesOrder->order_number }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500">Customer</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $packingOrder->salesOrder->customer->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500">Warehouse</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $packingOrder->warehouse->name }}</p>
                    </div>
                </div>
            </div>

            {{-- Packing Progress --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-pie text-green-600 mr-2"></i>
                    Packing Progress
                </h3>
                <div class="space-y-4">
                    @php
                        $totalItems = $pickingItems->sum('quantity_picked');
                        $packedItems = $packingOrder->items->sum('quantity_packed');
                        $percentage = $totalItems > 0 ? round(($packedItems / $totalItems) * 100) : 0;
                    @endphp
                    
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Progress</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <label class="text-xs font-medium text-gray-500">Total Items</label>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($totalItems) }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500">Packed</label>
                            <p class="text-lg font-bold text-green-600">{{ number_format($packedItems) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Current Boxes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-boxes text-orange-600 mr-2"></i>
                    Current Boxes
                </h3>
                <div class="space-y-2">
                    @php
                        $boxes = $packingOrder->items->groupBy('box_number');
                    @endphp
                    @forelse($boxes as $boxNumber => $items)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-box text-orange-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $boxNumber }}</p>
                                    <p class="text-xs text-gray-500">{{ $items->count() }} items</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($items->sum('box_weight_kg'), 2) }} kg</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-box text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-sm text-gray-500">No boxes packed yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Instructions --}}
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
                <h4 class="font-semibold text-purple-900 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>Instructions
                </h4>
                <ul class="text-sm text-purple-800 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mt-1 mr-2"></i>
                        <span>Scan box number first</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mt-1 mr-2"></i>
                        <span>Scan product barcode</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mt-1 mr-2"></i>
                        <span>Enter quantity and weight</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mt-1 mr-2"></i>
                        <span>Click "Complete Packing" when done</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Auto-focus next input after scanning
    document.getElementById('box_number').addEventListener('input', function(e) {
        if(e.target.value.length > 5) {
            document.getElementById('product_barcode').focus();
        }
    });

    document.getElementById('product_barcode').addEventListener('input', function(e) {
        if(e.target.value.length > 5) {
            document.getElementById('quantity').focus();
        }
    });

    // Handle form submission (would typically be AJAX)
    document.getElementById('packingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // This is a placeholder - in real implementation, this would be an AJAX call
        alert('In production, this would submit the packing data via AJAX and update the page dynamically.');
        
        // Reset form for next item
        this.reset();
        document.getElementById('box_number').focus();
    });
</script>
@endpush
@endsection