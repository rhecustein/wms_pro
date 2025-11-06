@extends('layouts.app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-gray-50">
    <div class="container-fluid px-4 py-8 max-w-[1800px] mx-auto">
        
        {{-- Page Header with Breadcrumb --}}
        <div class="mb-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <i class="fas fa-home mr-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <a href="{{ route('inbound.purchase-orders.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                                Purchase Orders
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <span class="text-sm font-medium text-gray-900">{{ $purchaseOrder->po_number }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-blue-500/30">
                            <i class="fas fa-file-invoice text-white text-xl"></i>
                        </div>
                        Purchase Order Details
                    </h1>
                    <p class="text-sm text-gray-600 mt-2 ml-16 font-mono font-bold">{{ $purchaseOrder->po_number }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($purchaseOrder->isEditable())
                        <a href="{{ route('inbound.purchase-orders.edit', $purchaseOrder) }}" class="px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 shadow-lg shadow-yellow-500/30 flex items-center gap-2 font-medium">
                            <i class="fas fa-edit"></i>
                            <span>Edit</span>
                        </a>
                    @endif
                    <a href="{{ route('inbound.purchase-orders.index') }}" class="px-4 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2 font-medium">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-xl shadow-sm animate-fade-in">
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <span class="text-green-800 font-medium">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl shadow-sm animate-fade-in">
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-white"></i>
                        </div>
                        <span class="text-red-800 font-medium">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            {{-- Main Content --}}
            <div class="xl:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Basic Information</h2>
                                <p class="text-xs text-gray-500">General purchase order details</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                                <label class="text-xs font-semibold text-blue-700 uppercase mb-1 block">PO Number</label>
                                <p class="text-lg font-bold text-gray-900 font-mono">{{ $purchaseOrder->po_number }}</p>
                            </div>
                            <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                                <label class="text-xs font-semibold text-purple-700 uppercase mb-1 block">Status</label>
                                <div class="mt-1">{!! $purchaseOrder->status_badge !!}</div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-gray-600 uppercase mb-1 block">
                                    <i class="fas fa-calendar mr-1"></i>PO Date
                                </label>
                                <p class="text-base font-bold text-gray-900">{{ $purchaseOrder->po_date->format('d M Y') }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-gray-600 uppercase mb-1 block">
                                    <i class="fas fa-truck mr-1"></i>Expected Delivery
                                </label>
                                <p class="text-base font-bold text-gray-900">
                                    @if($purchaseOrder->expected_delivery_date)
                                        {{ $purchaseOrder->expected_delivery_date->format('d M Y') }}
                                    @else
                                        <span class="text-gray-400 text-sm">Not set</span>
                                    @endif
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-gray-600 uppercase mb-1 block">
                                    <i class="fas fa-credit-card mr-1"></i>Payment Terms
                                </label>
                                <p class="text-base font-bold text-gray-900">{{ $purchaseOrder->payment_terms ?? '-' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-gray-600 uppercase mb-1 block">
                                    <i class="fas fa-dollar-sign mr-1"></i>Currency
                                </label>
                                <p class="text-base font-bold text-gray-900">{{ $purchaseOrder->currency }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Supplier & Warehouse Information --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-purple-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Supplier & Warehouse</h2>
                                <p class="text-xs text-gray-500">Supplier and destination information</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Supplier --}}
                            <div class="p-5 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border-2 border-blue-200">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-3 shadow-lg shadow-blue-500/30">
                                        <i class="fas fa-building text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-blue-700 uppercase">Supplier</label>
                                        <h3 class="text-base font-bold text-gray-900">{{ $purchaseOrder->supplier->name }}</h3>
                                    </div>
                                </div>
                                @if($purchaseOrder->supplier->code)
                                    <p class="text-sm text-gray-700 mb-1">
                                        <span class="font-semibold">Code:</span> {{ $purchaseOrder->supplier->code }}
                                    </p>
                                @endif
                                @if($purchaseOrder->supplier->email)
                                    <p class="text-sm text-gray-700 mb-1">
                                        <i class="fas fa-envelope mr-1 text-blue-600"></i> {{ $purchaseOrder->supplier->email }}
                                    </p>
                                @endif
                                @if($purchaseOrder->supplier->phone)
                                    <p class="text-sm text-gray-700">
                                        <i class="fas fa-phone mr-1 text-blue-600"></i> {{ $purchaseOrder->supplier->phone }}
                                    </p>
                                @endif
                            </div>

                            {{-- Warehouse --}}
                            <div class="p-5 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border-2 border-purple-200">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center mr-3 shadow-lg shadow-purple-500/30">
                                        <i class="fas fa-warehouse text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-purple-700 uppercase">Warehouse</label>
                                        <h3 class="text-base font-bold text-gray-900">{{ $purchaseOrder->warehouse->name }}</h3>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 mb-1">
                                    <span class="font-semibold">Code:</span> {{ $purchaseOrder->warehouse->code }}
                                </p>
                                @if($purchaseOrder->warehouse->address)
                                    <p class="text-sm text-gray-700">
                                        <i class="fas fa-map-marker-alt mr-1 text-purple-600"></i> {{ $purchaseOrder->warehouse->address }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-orange-50 to-white px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-boxes text-orange-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Order Items</h2>
                                    <p class="text-xs text-gray-500">Products in this purchase order</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">
                                {{ $purchaseOrder->items->count() }} items
                            </span>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Qty</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Unit Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Tax</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Disc</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">Line Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($purchaseOrder->items as $item)
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm font-bold">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-lg shadow-blue-500/30">
                                                    <i class="fas fa-box text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900">{{ $item->product_name }}</div>
                                                    @if($item->product_sku)
                                                        <div class="text-xs text-gray-500 font-mono">SKU: {{ $item->product_sku }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ number_format($item->quantity_ordered, 2) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $item->unit->name ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ $purchaseOrder->currency }} {{ number_format($item->unit_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->tax_rate > 0)
                                                <div class="text-sm font-semibold text-gray-900">{{ $item->tax_rate }}%</div>
                                                <div class="text-xs text-gray-500">{{ $purchaseOrder->currency }} {{ number_format($item->tax_amount, 2) }}</div>
                                            @else
                                                <span class="text-sm text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->discount_rate > 0)
                                                <div class="text-sm font-semibold text-red-600">{{ $item->discount_rate }}%</div>
                                                <div class="text-xs text-red-500">{{ $purchaseOrder->currency }} {{ number_format($item->discount_amount, 2) }}</div>
                                            @else
                                                <span class="text-sm text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-base font-bold text-blue-600">
                                                {{ $purchaseOrder->currency }} {{ number_format($item->line_total, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                                            <p>No items found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Financial Summary --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-green-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-invoice-dollar text-green-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Financial Summary</h2>
                                <p class="text-xs text-gray-500">Total costs and payment details</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                <span class="text-gray-700 font-medium">Subtotal</span>
                                <span class="text-lg font-bold text-gray-900">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->subtotal, 2) }}</span>
                            </div>
                            
                            @if($purchaseOrder->tax_amount > 0)
                                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                    <span class="text-gray-700 font-medium">
                                        Tax 
                                        @if($purchaseOrder->tax_rate > 0)
                                            <span class="text-sm text-gray-500">({{ $purchaseOrder->tax_rate }}%)</span>
                                        @endif
                                    </span>
                                    <span class="text-lg font-bold text-gray-900">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->tax_amount, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($purchaseOrder->discount_amount > 0)
                                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                    <span class="text-gray-700 font-medium">
                                        Discount
                                        @if($purchaseOrder->discount_rate > 0)
                                            <span class="text-sm text-gray-500">({{ $purchaseOrder->discount_rate }}%)</span>
                                        @endif
                                    </span>
                                    <span class="text-lg font-bold text-red-600">- {{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($purchaseOrder->shipping_cost > 0)
                                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                    <span class="text-gray-700 font-medium">Shipping Cost</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->shipping_cost, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($purchaseOrder->other_cost > 0)
                                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                    <span class="text-gray-700 font-medium">Other Cost</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->other_cost, 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between items-center pt-4 bg-gradient-to-r from-blue-50 to-blue-100 -mx-6 px-6 py-5 rounded-b-xl mt-4">
                                <span class="text-xl font-bold text-gray-800">Grand Total</span>
                                <span class="text-3xl font-bold text-blue-600">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->total_amount, 2) }}</span>
                            </div>
                            
                            {{-- Payment Status --}}
                            <div class="flex justify-between items-center pt-4 mt-4 border-t border-gray-200">
                                <span class="text-gray-700 font-medium">Payment Status</span>
                                <div>{!! $purchaseOrder->payment_status_badge !!}</div>
                            </div>
                            
                            @if(isset($purchaseOrder->paid_amount) && $purchaseOrder->paid_amount > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-medium">Paid Amount</span>
                                    <span class="text-lg font-bold text-green-600">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->paid_amount, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-medium">Outstanding</span>
                                    <span class="text-lg font-bold text-orange-600">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->remaining_amount ?? 0, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                @if($purchaseOrder->notes)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-white px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-sticky-note text-yellow-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Notes</h2>
                                    <p class="text-xs text-gray-500">Additional information</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $purchaseOrder->notes }}</p>
                        </div>
                    </div>
                @endif

                {{-- Inbound Shipments --}}
                @if($purchaseOrder->inboundShipments && $purchaseOrder->inboundShipments->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-truck-loading text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-gray-900">Inbound Shipments</h2>
                                        <p class="text-xs text-gray-500">Receiving history</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">
                                    {{ $purchaseOrder->inboundShipments->count() }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($purchaseOrder->inboundShipments as $shipment)
                                    <a href="{{ route('inbound.shipments.show', $shipment) }}" class="block p-4 bg-gradient-to-r from-gray-50 to-white hover:from-blue-50 hover:to-blue-100 rounded-xl border border-gray-200 hover:border-blue-300 transition-all duration-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-lg shadow-blue-500/30">
                                                    <i class="fas fa-truck text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900">{{ $shipment->shipment_number }}</div>
                                                    <div class="text-xs text-gray-500">{{ $shipment->arrival_date ? $shipment->arrival_date->format('d M Y H:i') : '-' }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                {!! $shipment->status_badge !!}
                                                <i class="fas fa-chevron-right text-gray-400"></i>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="xl:col-span-1 space-y-6">
                
                {{-- Action Buttons --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tasks text-blue-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Quick Actions</h2>
                                <p class="text-xs text-gray-500">Manage this PO</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-3">
                            @if($purchaseOrder->status === 'draft')
                                <form action="{{ route('inbound.purchase-orders.update-status', $purchaseOrder) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="submitted">
                                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg shadow-blue-500/30 font-semibold flex items-center justify-center gap-2">
                                        <i class="fas fa-paper-plane"></i>Submit PO
                                    </button>
                                </form>
                            @endif

                            @if($purchaseOrder->status === 'submitted')
                                <form action="{{ route('inbound.purchase-orders.approve', $purchaseOrder) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg shadow-green-500/30 font-semibold flex items-center justify-center gap-2">
                                        <i class="fas fa-check-circle"></i>Approve PO
                                    </button>
                                </form>
                            @endif

                            @if($purchaseOrder->status === 'approved')
                                <form action="{{ route('inbound.purchase-orders.update-status', $purchaseOrder) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-lg shadow-purple-500/30 font-semibold flex items-center justify-center gap-2">
                                        <i class="fas fa-handshake"></i>Confirm PO
                                    </button>
                                </form>
                            @endif

                            @if(in_array($purchaseOrder->status, ['confirmed', 'partial_received', 'received']))
                                <form action="{{ route('inbound.purchase-orders.update-status', $purchaseOrder) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg shadow-green-500/30 font-semibold flex items-center justify-center gap-2">
                                        <i class="fas fa-check-double"></i>Complete PO
                                    </button>
                                </form>
                            @endif

                            @if($purchaseOrder->canBeCancelled())
                                <button onclick="showCancelModal()" class="w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg shadow-red-500/30 font-semibold flex items-center justify-center gap-2">
                                    <i class="fas fa-times-circle"></i>Cancel PO
                                </button>
                            @endif

                            <div class="pt-3 border-t border-gray-200"></div>

                            <a href="{{ route('inbound.purchase-orders.print', $purchaseOrder) }}" target="_blank" class="w-full px-4 py-3 bg-gradient-to-r from-gray-700 to-gray-800 text-white rounded-xl hover:from-gray-800 hover:to-gray-900 transition-all duration-200 shadow-lg shadow-gray-500/30 font-semibold flex items-center justify-center gap-2">
                                <i class="fas fa-print"></i>Print PO
                            </a>
                            
                            <a href="{{ route('inbound.purchase-orders.duplicate', $purchaseOrder) }}" class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 shadow-lg shadow-indigo-500/30 font-semibold flex items-center justify-center gap-2">
                                <i class="fas fa-copy"></i>Duplicate PO
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-history text-purple-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Timeline</h2>
                                <p class="text-xs text-gray-500">Activity history</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex">
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg shadow-green-500/30">
                                        <i class="fas fa-plus text-white text-xs"></i>
                                    </div>
                                    <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
                                </div>
                                <div class="flex-1 pb-4">
                                    <p class="text-sm font-bold text-gray-900">Created</p>
                                    <p class="text-xs text-gray-500">{{ $purchaseOrder->created_at->format('d M Y H:i') }}</p>
                                    @if($purchaseOrder->creator)
                                        <p class="text-xs text-gray-500">by {{ $purchaseOrder->creator->name }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($purchaseOrder->approved_at)
                                <div class="flex">
                                    <div class="flex flex-col items-center mr-4">
                                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg shadow-green-500/30">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </div>
                                        <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
                                    </div>
                                    <div class="flex-1 pb-4">
                                        <p class="text-sm font-bold text-gray-900">Approved</p>
                                        <p class="text-xs text-gray-500">{{ $purchaseOrder->approved_at->format('d M Y H:i') }}</p>
                                        @if($purchaseOrder->approver)
                                            <p class="text-xs text-gray-500">by {{ $purchaseOrder->approver->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($purchaseOrder->updated_at != $purchaseOrder->created_at)
                                <div class="flex">
                                    <div class="flex flex-col items-center mr-4">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-500/30">
                                            <i class="fas fa-edit text-white text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900">Last Updated</p>
                                        <p class="text-xs text-gray-500">{{ $purchaseOrder->updated_at->format('d M Y H:i') }}</p>
                                        @if($purchaseOrder->updater)
                                            <p class="text-xs text-gray-500">by {{ $purchaseOrder->updater->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Status Guide --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-orange-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-question-circle text-orange-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Status Guide</h2>
                                <p class="text-xs text-gray-500">Understanding PO status</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-3 text-xs">
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full font-semibold whitespace-nowrap">Draft</span>
                                <span class="text-gray-600">Initial state, can be edited</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold whitespace-nowrap">Submitted</span>
                                <span class="text-gray-600">Sent for approval</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full font-semibold whitespace-nowrap">Approved</span>
                                <span class="text-gray-600">Approved by manager</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full font-semibold whitespace-nowrap">Confirmed</span>
                                <span class="text-gray-600">Supplier confirmed</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full font-semibold whitespace-nowrap">Partial</span>
                                <span class="text-gray-600">Partially received</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-1 bg-teal-100 text-teal-800 rounded-full font-semibold whitespace-nowrap">Received</span>
                                <span class="text-gray-600">Fully received</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full font-semibold whitespace-nowrap">Completed</span>
                                <span class="text-gray-600">PO closed successfully</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full font-semibold whitespace-nowrap">Cancelled</span>
                                <span class="text-gray-600">PO cancelled</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 animate-scale-up">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Cancel Purchase Order</h3>
            </div>
            <form action="{{ route('inbound.purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Cancellation Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="notes" rows="4" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all duration-200" placeholder="Please provide a reason for cancellation (min 10 characters)..." required minlength="10"></textarea>
                    <p class="text-xs text-gray-500 mt-1.5">Minimum 10 characters required</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="hideCancelModal()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg shadow-red-500/30 font-semibold">
                        Confirm Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
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
    
    @keyframes scale-up {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
    
    .animate-scale-up {
        animation: scale-up 0.2s ease-out;
    }
</style>
@endpush

@push('scripts')
<script>
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('cancelModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideCancelModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideCancelModal();
    }
});
</script>
@endpush
@endsection