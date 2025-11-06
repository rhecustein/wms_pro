@extends('layouts.app')

@section('title', 'Good Receiving Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Enhanced Page Header --}}
    <div class="relative mb-8">
        <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl opacity-10"></div>
        <div class="relative p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-box-open text-3xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $goodReceiving->gr_number }}</h1>
                        <p class="text-sm text-gray-600 mt-1">Good Receiving Details</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    @if(in_array($goodReceiving->status, ['draft', 'in_progress']))
                        <a href="{{ route('inbound.good-receivings.edit', $goodReceiving) }}" class="group px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl hover:shadow-lg transition-all duration-300">
                            <i class="fas fa-edit mr-2 group-hover:rotate-12 transition-transform"></i>Edit
                        </a>
                    @endif
                    <a href="{{ route('inbound.good-receivings.print', $goodReceiving) }}" class="group px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-xl hover:shadow-lg transition-all duration-300" target="_blank">
                        <i class="fas fa-print mr-2 group-hover:scale-110 transition-transform"></i>Print
                    </a>
                    <a href="{{ route('inbound.good-receivings.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
            </div>

            {{-- Status Timeline --}}
            <div class="mt-6 bg-white rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between relative">
                    <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200"></div>
                    <div class="absolute top-5 left-0 h-1 bg-gradient-to-r from-green-500 to-emerald-500 transition-all duration-500" style="width: {{ $goodReceiving->status === 'completed' ? '100%' : ($goodReceiving->status === 'quality_check' ? '66%' : ($goodReceiving->status === 'in_progress' ? '33%' : '0%')) }}"></div>
                    
                    @php
                        $statuses = [
                            ['key' => 'draft', 'icon' => 'fa-file', 'label' => 'Draft'],
                            ['key' => 'in_progress', 'icon' => 'fa-spinner', 'label' => 'In Progress'],
                            ['key' => 'quality_check', 'icon' => 'fa-clipboard-check', 'label' => 'Quality Check'],
                            ['key' => 'completed', 'icon' => 'fa-check-circle', 'label' => 'Completed']
                        ];
                    @endphp

                    @foreach($statuses as $index => $status)
                        <div class="relative flex flex-col items-center z-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 {{ 
                                $goodReceiving->status === $status['key'] ? 'bg-gradient-to-br from-green-500 to-emerald-500 shadow-lg scale-110' : 
                                (array_search($goodReceiving->status, array_column($statuses, 'key')) > $index ? 'bg-green-500' : 'bg-gray-300')
                            }}">
                                <i class="fas {{ $status['icon'] }} text-white"></i>
                            </div>
                            <span class="text-xs font-medium mt-2 {{ $goodReceiving->status === $status['key'] ? 'text-green-600' : 'text-gray-500' }}">
                                {{ $status['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center justify-between shadow-sm animate-slideIn">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900 hover:rotate-90 transition-transform duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center justify-between shadow-sm animate-slideIn">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900 hover:rotate-90 transition-transform duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Tabs Navigation --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100">
                    <nav class="flex space-x-1 p-2" role="tablist">
                        <button onclick="switchTab('overview')" class="tab-btn active px-6 py-3 rounded-xl font-medium transition-all duration-200" data-tab="overview">
                            <i class="fas fa-info-circle mr-2"></i>Overview
                        </button>
                        <button onclick="switchTab('items')" class="tab-btn px-6 py-3 rounded-xl font-medium transition-all duration-200" data-tab="items">
                            <i class="fas fa-boxes mr-2"></i>Items ({{ $goodReceiving->items->count() }})
                        </button>
                        <button onclick="switchTab('history')" class="tab-btn px-6 py-3 rounded-xl font-medium transition-all duration-200" data-tab="history">
                            <i class="fas fa-history mr-2"></i>History
                        </button>
                    </nav>
                </div>

                {{-- Tab Content --}}
                <div class="p-6">
                    {{-- Overview Tab --}}
                    <div id="overview-tab" class="tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl flex items-center justify-center mr-3">
                                        <i class="fas fa-hashtag text-green-600"></i>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">GR Number</label>
                                        <p class="text-base font-bold text-gray-900 font-mono mt-1">{{ $goodReceiving->gr_number }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center mr-3">
                                        <i class="fas fa-calendar text-blue-600"></i>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Receiving Date</label>
                                        <p class="text-base text-gray-900 mt-1">{{ $goodReceiving->receiving_date->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl flex items-center justify-center mr-3">
                                        <i class="fas fa-warehouse text-purple-600"></i>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Warehouse</label>
                                        <p class="text-base font-semibold text-gray-900 mt-1">{{ $goodReceiving->warehouse->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $goodReceiving->warehouse->code }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-red-100 rounded-xl flex items-center justify-center mr-3">
                                        <i class="fas fa-building text-orange-600"></i>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Supplier</label>
                                        <p class="text-base font-semibold text-gray-900 mt-1">{{ $goodReceiving->supplier->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $goodReceiving->supplier->code ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-teal-100 rounded-xl flex items-center justify-center mr-3">
                                        <i class="fas fa-tasks text-green-600"></i>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Status</label>
                                        <div class="mt-1">{!! $goodReceiving->status_badge !!}</div>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-100 to-amber-100 rounded-xl flex items-center justify-center mr-3">
                                        <i class="fas fa-check-circle text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Quality Status</label>
                                        <div class="mt-1">{!! $goodReceiving->quality_status_badge !!}</div>
                                    </div>
                                </div>

                                @if($goodReceiving->purchase_order_id)
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center mr-3">
                                            <i class="fas fa-link text-indigo-600"></i>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Reference PO</label>
                                            <p class="text-base font-mono text-gray-900 mt-1">{{ $goodReceiving->purchaseOrder->po_number }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($goodReceiving->receivedBy)
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-gradient-to-br from-pink-100 to-rose-100 rounded-xl flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-pink-600"></i>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Received By</label>
                                            <p class="text-base text-gray-900 mt-1">{{ $goodReceiving->receivedBy->name }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($goodReceiving->notes)
                            <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                <label class="text-sm font-semibold text-blue-900 flex items-center mb-2">
                                    <i class="fas fa-sticky-note mr-2"></i>Notes
                                </label>
                                <p class="text-sm text-blue-800 whitespace-pre-line">{{ $goodReceiving->notes }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Items Tab --}}
                    <div id="items-tab" class="tab-content hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Product</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Expected</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Received</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Accepted</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Rejected</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($goodReceiving->items as $index => $item)
                                        <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-colors">
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center mr-3">
                                                        <i class="fas fa-box text-gray-600"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</div>
                                                        <div class="text-xs text-gray-500 font-mono">{{ $item->product->sku }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="text-sm font-medium text-gray-900">{{ number_format($item->quantity_expected) }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="text-sm font-bold {{ $item->quantity_received < $item->quantity_expected ? 'text-yellow-600' : 'text-green-600' }}">
                                                    {{ number_format($item->quantity_received) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="text-sm font-semibold text-green-600">{{ number_format($item->quantity_accepted) }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="text-sm font-semibold text-red-600">{{ number_format($item->quantity_rejected) }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($item->quantity_received >= $item->quantity_expected)
                                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        <i class="fas fa-check mr-1"></i>Complete
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-exclamation mr-1"></i>Short
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gradient-to-r from-gray-50 to-gray-100 border-t-2 border-gray-300">
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-900">Total</td>
                                        <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">{{ number_format($goodReceiving->items->sum('quantity_expected')) }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">{{ number_format($goodReceiving->total_quantity) }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-bold text-green-600">{{ number_format($goodReceiving->items->sum('quantity_accepted')) }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-bold text-red-600">{{ number_format($goodReceiving->items->sum('quantity_rejected')) }}</td>
                                        <td class="px-4 py-3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- History Tab --}}
                    <div id="history-tab" class="tab-content hidden">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-plus-circle text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-semibold text-gray-900">Created</h4>
                                        <span class="text-xs text-gray-500">{{ $goodReceiving->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">By {{ $goodReceiving->createdBy->name ?? '-' }}</p>
                                </div>
                            </div>

                            @if($goodReceiving->receivedBy)
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-play text-green-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-semibold text-gray-900">Receiving Started</h4>
                                            <span class="text-xs text-gray-500">{{ $goodReceiving->receiving_date->format('d M Y, H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">By {{ $goodReceiving->receivedBy->name }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($goodReceiving->quality_checked_at)
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-clipboard-check text-purple-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-semibold text-gray-900">Quality Check Completed</h4>
                                            <span class="text-xs text-gray-500">{{ $goodReceiving->quality_checked_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">By {{ $goodReceiving->qualityCheckedBy->name ?? '-' }}</p>
                                        <div class="mt-2">{!! $goodReceiving->quality_status_badge !!}</div>
                                    </div>
                                </div>
                            @endif

                            @if($goodReceiving->updatedBy && $goodReceiving->updated_at != $goodReceiving->created_at)
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-edit text-yellow-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-semibold text-gray-900">Last Updated</h4>
                                            <span class="text-xs text-gray-500">{{ $goodReceiving->updated_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">By {{ $goodReceiving->updatedBy->name }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Enhanced Sidebar --}}
        <div class="lg:col-span-1">
            <div class="space-y-6 sticky top-6">
                
                {{-- Summary Card --}}
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Summary
                    </h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-white/20">
                            <span class="text-sm text-white/80">Total Items</span>
                            <span class="text-2xl font-bold">{{ $goodReceiving->total_items }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-white/20">
                            <span class="text-sm text-white/80">Total Quantity</span>
                            <span class="text-2xl font-bold">{{ number_format($goodReceiving->total_quantity) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-white/80">Total Pallets</span>
                            <span class="text-2xl font-bold">{{ $goodReceiving->total_pallets }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action Cards --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                        Quick Actions
                    </h3>

                    <div class="space-y-2">
                        @if($goodReceiving->status === 'draft')
                            <form action="{{ route('inbound.good-receivings.start', $goodReceiving) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full group px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-xl hover:shadow-lg transition-all duration-300 text-sm font-semibold">
                                    <i class="fas fa-play mr-2 group-hover:translate-x-1 transition-transform"></i>Start Receiving
                                </button>
                            </form>
                        @endif

                        @if(in_array($goodReceiving->status, ['in_progress', 'quality_check']))
                            <button type="button" onclick="openQualityCheckModal()" class="w-full group px-4 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl hover:shadow-lg transition-all duration-300 text-sm font-semibold">
                                <i class="fas fa-clipboard-check mr-2 group-hover:scale-110 transition-transform"></i>Quality Check
                            </button>

                            <form action="{{ route('inbound.good-receivings.complete', $goodReceiving) }}" method="POST" onsubmit="return confirm('Are you sure you want to complete this receiving?')">
                                @csrf
                                <button type="submit" class="w-full group px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-xl hover:shadow-lg transition-all duration-300 text-sm font-semibold">
                                    <i class="fas fa-check-circle mr-2 group-hover:rotate-12 transition-transform"></i>Complete Receiving
                                </button>
                            </form>
                        @endif

                        @if(!in_array($goodReceiving->status, ['completed', 'cancelled']))
                            <button type="button" onclick="openCancelModal()" class="w-full group px-4 py-3 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-xl hover:shadow-lg transition-all duration-300 text-sm font-semibold">
                                <i class="fas fa-ban mr-2 group-hover:rotate-180 transition-transform"></i>Cancel Receiving
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Info Card --}}
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6">
                    <h4 class="font-semibold text-blue-900 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>Information
                    </h4>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                            <span>All changes are tracked</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                            <span>Quality check is required</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                            <span>Document can be printed anytime</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Enhanced Quality Check Modal --}}
<div id="qualityCheckModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all scale-95 opacity-0" id="modalContent">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-clipboard-check text-white"></i>
                    </div>
                    Quality Check
                </h3>
                <button onclick="closeQualityCheckModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('inbound.good-receivings.quality-check', $goodReceiving) }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quality Status <span class="text-red-500">*</span></label>
                        <select name="quality_status" class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500 transition" required>
                            <option value="">Select Status...</option>
                            <option value="passed">✓ Passed</option>
                            <option value="failed">✗ Failed</option>
                            <option value="partial">⚠ Partial</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quality Notes</label>
                        <textarea name="quality_notes" rows="4" class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500 transition" placeholder="Add quality check notes..."></textarea>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold">
                        <i class="fas fa-check mr-2"></i>Submit
                    </button>
                    <button type="button" onclick="closeQualityCheckModal()" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Enhanced Cancel Modal --}}
<div id="cancelModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all scale-95 opacity-0" id="cancelModalContent">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-500 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-ban text-white"></i>
                    </div>
                    Cancel GR
                </h3>
                <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('inbound.good-receivings.cancel', $goodReceiving) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason <span class="text-red-500">*</span></label>
                    <textarea name="cancellation_reason" rows="4" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500 transition" placeholder="Please provide a reason for cancellation..." required></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold">
                        <i class="fas fa-ban mr-2"></i>Cancel GR
                    </button>
                    <button type="button" onclick="closeCancelModal()" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-semibold">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes slideIn {
        from { transform: translateX(-100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animate-slideIn { animation: slideIn 0.3s ease-out; }
    
    .tab-btn { color: #6b7280; background: transparent; }
    .tab-btn.active { color: #059669; background: #d1fae5; }
    .tab-btn:hover:not(.active) { background: #f3f4f6; }
</style>
@endpush

@push('scripts')
<script>
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
}

function openQualityCheckModal() {
    const modal = document.getElementById('qualityCheckModal');
    const content = document.getElementById('modalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeQualityCheckModal() {
    const modal = document.getElementById('qualityCheckModal');
    const content = document.getElementById('modalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

function openCancelModal() {
    const modal = document.getElementById('cancelModal');
    const content = document.getElementById('cancelModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeCancelModal() {
    const modal = document.getElementById('cancelModal');
    const content = document.getElementById('cancelModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQualityCheckModal();
        closeCancelModal();
    }
});

// Close modal on backdrop click
document.getElementById('qualityCheckModal').addEventListener('click', function(e) {
    if (e.target === this) closeQualityCheckModal();
});
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
@endpush

@endsection