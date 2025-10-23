@extends('layouts.app')

@section('title', 'Return Order Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-undo-alt text-red-600 mr-2"></i>
                Return Order Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $return->return_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('outbound.returns.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            @if($return->status === 'pending')
                <a href="{{ route('outbound.returns.edit', $return) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('outbound.returns.print', $return) }}" target="_blank" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition">
                <i class="fas fa-print mr-2"></i>Print
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
            
            {{-- Return Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Return Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Return Number</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">{{ $return->return_number }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Return Date</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">
                            {{ $return->return_date->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Return Type</label>
                        <div class="mt-1">
                            {!! $return->return_type_badge !!}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1">
                            {!! $return->status_badge !!}
                        </div>
                    </div>

                    @if($return->deliveryOrder)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Delivery Order</label>
                            <p class="text-base font-semibold text-gray-900 mt-1">
                                <a href="{{ route('outbound.delivery-orders.show', $return->deliveryOrder) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $return->deliveryOrder->do_number }}
                                </a>
                            </p>
                        </div>
                    @endif

                    @if($return->salesOrder)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Sales Order</label>
                            <p class="text-base font-semibold text-gray-900 mt-1">
                                <a href="{{ route('sales.orders.show', $return->salesOrder) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $return->salesOrder->order_number }}
                                </a>
                            </p>
                        </div>
                    @endif

                    @if($return->disposition)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Disposition</label>
                            <p class="text-base font-semibold text-gray-900 mt-1 capitalize">
                                {{ $return->disposition }}
                            </p>
                        </div>
                    @endif

                    @if($return->inspectedBy)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Inspected By</label>
                            <p class="text-base font-semibold text-gray-900 mt-1">
                                {{ $return->inspectedBy->name }}
                                <span class="text-sm text-gray-500">
                                    ({{ $return->inspected_at->format('d M Y, H:i') }})
                                </span>
                            </p>
                        </div>
                    @endif

                    @if($return->notes)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Notes</label>
                            <p class="text-base text-gray-900 mt-1">{{ $return->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Customer & Warehouse Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Customer --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user text-pink-600 mr-2"></i>
                        Customer
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Name</label>
                            <p class="text-base font-semibold text-gray-900 mt-1">{{ $return->customer->name }}</p>
                        </div>
                        @if($return->customer->code)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Code</label>
                                <p class="text-base text-gray-900 mt-1">{{ $return->customer->code }}</p>
                            </div>
                        @endif
                        @if($return->customer->email)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email</label>
                                <p class="text-base text-gray-900 mt-1">{{ $return->customer->email }}</p>
                            </div>
                        @endif
                        @if($return->customer->phone)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Phone</label>
                                <p class="text-base text-gray-900 mt-1">{{ $return->customer->phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Warehouse --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-warehouse text-purple-600 mr-2"></i>
                        Warehouse
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Name</label>
                            <p class="text-base font-semibold text-gray-900 mt-1">{{ $return->warehouse->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Code</label>
                            <p class="text-base text-gray-900 mt-1">{{ $return->warehouse->code }}</p>
                        </div>
                        @if($return->warehouse->address)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Address</label>
                                <p class="text-base text-gray-900 mt-1">{{ $return->warehouse->address }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Return Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-box text-red-600 mr-2"></i>
                        Return Items
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Serial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disposition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restocked To</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($return->items as $item)
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
                                        @if($item->batch_number)
                                            <div class="text-sm text-gray-900">
                                                <i class="fas fa-hashtag text-gray-400 mr-1"></i>{{ $item->batch_number }}
                                            </div>
                                        @endif
                                        @if($item->serial_number)
                                            <div class="text-xs text-gray-500">
                                                <i class="fas fa-barcode text-gray-400 mr-1"></i>{{ $item->serial_number }}
                                            </div>
                                        @endif
                                        @if(!$item->batch_number && !$item->serial_number)
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ number_format($item->quantity_returned) }}
                                        </div>
                                        @if($item->quantity_restocked > 0)
                                            <div class="text-xs text-green-600">
                                                <i class="fas fa-check mr-1"></i>{{ number_format($item->quantity_restocked) }} restocked
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->condition === 'good')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Good
                                            </span>
                                        @elseif($item->condition === 'damaged')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Damaged
                                            </span>
                                        @elseif($item->condition === 'expired')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                                <i class="fas fa-calendar-times mr-1"></i>Expired
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->disposition)
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 capitalize">
                                                {{ $item->disposition }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->restockedToBin)
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $item->restockedToBin->bin_code }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($item->return_reason || $item->notes)
                                    <tr class="bg-gray-50">
                                        <td colspan="6" class="px-6 py-3">
                                            @if($item->return_reason)
                                                <div class="text-sm text-gray-700">
                                                    <span class="font-semibold">Reason:</span> {{ $item->return_reason }}
                                                </div>
                                            @endif
                                            @if($item->notes)
                                                <div class="text-sm text-gray-700 mt-1">
                                                    <span class="font-semibold">Notes:</span> {{ $item->notes }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Summary Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                    Summary
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-boxes text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Total Items</p>
                                <p class="text-lg font-bold text-gray-900">{{ $return->total_items }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-cubes text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Total Quantity</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($return->total_quantity) }}</p>
                            </div>
                        </div>
                    </div>

                    @if($return->refund_amount > 0)
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-dollar-sign text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Refund Amount</p>
                                    <p class="text-lg font-bold text-gray-900">${{ number_format($return->refund_amount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-tasks text-green-600 mr-2"></i>
                    Actions
                </h3>

                <div class="space-y-3">
                    @if($return->status === 'pending')
                        <form action="{{ route('outbound.returns.receive', $return) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                                <i class="fas fa-check mr-2"></i>Mark as Received
                            </button>
                        </form>
                    @endif

                    @if($return->status === 'received')
                        <button type="button" onclick="openInspectModal()" class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-semibold">
                            <i class="fas fa-search mr-2"></i>Inspect Items
                        </button>
                    @endif

                    @if($return->status === 'inspected')
                        <button type="button" onclick="openRestockModal()" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                            <i class="fas fa-layer-group mr-2"></i>Restock Items
                        </button>
                    @endif

                    @if(in_array($return->status, ['pending', 'received']))
                        <form action="{{ route('outbound.returns.cancel', $return) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this return order?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold">
                                <i class="fas fa-times mr-2"></i>Cancel Return
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('outbound.returns.print', $return) }}" target="_blank" class="block w-full px-4 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition text-sm font-semibold text-center">
                        <i class="fas fa-print mr-2"></i>Print Return
                    </a>
                </div>
            </div>

            {{-- Timeline Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-history text-orange-600 mr-2"></i>
                    Timeline
                </h3>

                <div class="space-y-4">
                    <div class="flex">
                        <div class="flex flex-col items-center mr-4">
                            <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                            <div class="w-0.5 h-full bg-gray-300 mt-2"></div>
                        </div>
                        <div class="pb-4">
                            <p class="text-sm font-semibold text-gray-900">Created</p>
                            <p class="text-xs text-gray-500">{{ $return->created_at->format('d M Y, H:i') }}</p>
                            @if($return->createdBy)
                                <p class="text-xs text-gray-500">by {{ $return->createdBy->name }}</p>
                            @endif
                        </div>
                    </div>

                    @if($return->inspected_at)
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 bg-purple-600 rounded-full"></div>
                                @if($return->status !== 'inspected')
                                    <div class="w-0.5 h-full bg-gray-300 mt-2"></div>
                                @endif
                            </div>
                            <div class="pb-4">
                                <p class="text-sm font-semibold text-gray-900">Inspected</p>
                                <p class="text-xs text-gray-500">{{ $return->inspected_at->format('d M Y, H:i') }}</p>
                                @if($return->inspectedBy)
                                    <p class="text-xs text-gray-500">by {{ $return->inspectedBy->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($return->status === 'restocked')
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Restocked</p>
                                <p class="text-xs text-gray-500">{{ $return->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($return->status === 'cancelled')
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 bg-red-600 rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Cancelled</p>
                                <p class="text-xs text-gray-500">{{ $return->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>

{{-- Inspect Modal (placeholder - you can implement this based on your needs) --}}
{{-- Restock Modal (placeholder - you can implement this based on your needs) --}}

@push('scripts')
<script>
function openInspectModal() {
    alert('Inspect modal will be implemented here');
}

function openRestockModal() {
    alert('Restock modal will be implemented here');
}
</script>
@endpush

@endsection