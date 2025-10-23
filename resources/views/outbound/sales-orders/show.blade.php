{{-- resources/views/outbound/sales-orders/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Sales Order Details - ' . $salesOrder->so_number)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('outbound.sales-orders.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-shopping-cart text-blue-600 mr-2"></i>
                    Sales Order Details
                </h1>
                <p class="text-sm text-gray-600 mt-1">{{ $salesOrder->so_number }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('outbound.sales-orders.print', $salesOrder) }}" target="_blank" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-print mr-2"></i>Print
            </a>
            
            @if($salesOrder->canEdit())
                <a href="{{ route('outbound.sales-orders.edit', $salesOrder) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif

            @if($salesOrder->canConfirm())
                <form action="{{ route('outbound.sales-orders.confirm', $salesOrder) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" onclick="return confirm('Confirm this sales order?')">
                        <i class="fas fa-check mr-2"></i>Confirm
                    </button>
                </form>
            @endif

            @if($salesOrder->canGeneratePicking())
                <form action="{{ route('outbound.sales-orders.generate-picking', $salesOrder) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" onclick="return confirm('Generate picking list?')">
                        <i class="fas fa-clipboard-list mr-2"></i>Generate Picking
                    </button>
                </form>
            @endif

            @if($salesOrder->canCancel())
                <form action="{{ route('outbound.sales-orders.cancel', $salesOrder) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="return confirm('Cancel this sales order?')">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </form>
            @endif
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
            
            {{-- Order Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Order Information
                    </h2>
                    <div class="flex items-center space-x-2">
                        {!! $salesOrder->status_badge !!}
                        {!! $salesOrder->payment_status_badge !!}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">SO Number</label>
                        <p class="text-sm font-mono font-semibold text-gray-900 mt-1">{{ $salesOrder->so_number }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Order Date</label>
                        <p class="text-sm text-gray-900 mt-1">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>
                            {{ $salesOrder->order_date->format('d M Y') }}
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Customer</label>
                        <div class="flex items-center mt-2">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $salesOrder->customer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $salesOrder->customer->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Warehouse</label>
                        <div class="flex items-center mt-2">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-warehouse text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $salesOrder->warehouse->name }}</p>
                                <p class="text-xs text-gray-500">{{ $salesOrder->warehouse->code }}</p>
                            </div>
                        </div>
                    </div>

                    @if($salesOrder->requested_delivery_date)
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Requested Delivery</label>
                            <p class="text-sm text-gray-900 mt-1">
                                <i class="fas fa-truck text-gray-400 mr-1"></i>
                                {{ $salesOrder->requested_delivery_date->format('d M Y') }}
                            </p>
                        </div>
                    @endif

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Currency</label>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $salesOrder->currency }}</p>
                    </div>
                </div>

                @if($salesOrder->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="text-xs font-medium text-gray-500 uppercase">Notes</label>
                        <p class="text-sm text-gray-700 mt-2">{{ $salesOrder->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Shipping Information --}}
            @if($salesOrder->shipping_address)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                        Shipping Information
                    </h2>

                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Address</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $salesOrder->shipping_address }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @if($salesOrder->shipping_city)
                                <div>
                                    <label class="text-xs font-medium text-gray-500 uppercase">City</label>
                                    <p class="text-sm text-gray-900 mt-1">{{ $salesOrder->shipping_city }}</p>
                                </div>
                            @endif

                            @if($salesOrder->shipping_province)
                                <div>
                                    <label class="text-xs font-medium text-gray-500 uppercase">Province</label>
                                    <p class="text-sm text-gray-900 mt-1">{{ $salesOrder->shipping_province }}</p>
                                </div>
                            @endif

                            @if($salesOrder->shipping_postal_code)
                                <div>
                                    <label class="text-xs font-medium text-gray-500 uppercase">Postal Code</label>
                                    <p class="text-sm text-gray-900 mt-1">{{ $salesOrder->shipping_postal_code }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Order Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-boxes text-blue-600 mr-2"></i>
                        Order Items
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Discount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($salesOrder->items as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-box text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->product->sku }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">{{ number_format($item->discount, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-calculator text-blue-600 mr-2"></i>
                    Summary
                </h2>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold text-gray-900">{{ $salesOrder->currency }} {{ number_format($salesOrder->subtotal, 2) }}</span>
                    </div>

                    @if($salesOrder->discount_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount</span>
                            <span class="font-semibold text-red-600">-{{ $salesOrder->currency }} {{ number_format($salesOrder->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    @if($salesOrder->tax_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-semibold text-gray-900">{{ $salesOrder->currency }} {{ number_format($salesOrder->tax_amount, 2) }}</span>
                        </div>
                    @endif

                    @if($salesOrder->shipping_cost > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping Cost</span>
                            <span class="font-semibold text-gray-900">{{ $salesOrder->currency }} {{ number_format($salesOrder->shipping_cost, 2) }}</span>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-gray-200">
                        <div class="flex justify-between">
                            <span class="text-base font-bold text-gray-900">Total Amount</span>
                            <span class="text-lg font-bold text-blue-600">{{ $salesOrder->currency }} {{ number_format($salesOrder->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity Log --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Activity Log
                </h2>

                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-plus text-green-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Order Created</p>
                            <p class="text-xs text-gray-500">
                                {{ $salesOrder->created_at->format('d M Y, H:i') }}
                                @if($salesOrder->createdBy)
                                    by {{ $salesOrder->createdBy->name }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($salesOrder->updated_at != $salesOrder->created_at)
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-edit text-blue-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Last Updated</p>
                                <p class="text-xs text-gray-500">
                                    {{ $salesOrder->updated_at->format('d M Y, H:i') }}
                                    @if($salesOrder->updatedBy)
                                        by {{ $salesOrder->updatedBy->name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection