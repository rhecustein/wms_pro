{{-- resources/views/outbound/picking-orders/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Picking Order Detail - ' . $pickingOrder->picking_number)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
        
        {{-- Modern Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('outbound.picking-orders.index') }}" 
                       class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-box-open text-green-600 mr-3"></i>
                            {{ $pickingOrder->picking_number }}
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">Picking Order Details</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if(in_array($pickingOrder->status, ['pending', 'assigned']))
                        <form action="{{ route('outbound.picking-orders.start', $pickingOrder) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all font-bold shadow-lg hover:shadow-xl">
                                <i class="fas fa-play mr-2"></i>Start Picking
                            </button>
                        </form>
                        <a href="{{ route('outbound.picking-orders.edit', $pickingOrder) }}" 
                           class="px-6 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all font-bold shadow-lg hover:shadow-xl">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    @endif

                    @if($pickingOrder->status === 'in_progress')
                        <a href="{{ route('outbound.picking-orders.execute', $pickingOrder) }}" 
                           class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all font-bold shadow-lg hover:shadow-xl">
                            <i class="fas fa-tasks mr-2"></i>Execute Picking
                        </a>
                        <form action="{{ route('outbound.picking-orders.complete', $pickingOrder) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to complete this picking order?')">
                            @csrf
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all font-bold shadow-lg hover:shadow-xl">
                                <i class="fas fa-check-circle mr-2"></i>Complete
                            </button>
                        </form>
                    @endif

                    @if(!in_array($pickingOrder->status, ['completed', 'cancelled']))
                        <form action="{{ route('outbound.picking-orders.cancel', $pickingOrder) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this picking order?')">
                            @csrf
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl hover:from-red-700 hover:to-rose-700 transition-all font-bold shadow-lg hover:shadow-xl">
                                <i class="fas fa-times-circle mr-2"></i>Cancel
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('outbound.picking-orders.print', $pickingOrder) }}" 
                       class="px-6 py-3 bg-white text-gray-700 border-2 border-gray-300 rounded-xl hover:bg-gray-50 transition-all font-bold shadow-md hover:shadow-lg" 
                       target="_blank">
                        <i class="fas fa-print mr-2"></i>Print
                    </a>
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-xl p-5 shadow-lg animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <span class="text-green-800 font-medium">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.closest('.mb-6').remove()" class="text-green-800 hover:text-green-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl p-5 shadow-lg animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-circle text-white"></i>
                        </div>
                        <span class="text-red-800 font-medium">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.closest('.mb-6').remove()" class="text-red-800 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-500 rounded-xl p-5 shadow-lg animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        <span class="text-yellow-800 font-medium">{{ session('warning') }}</span>
                    </div>
                    <button onclick="this.closest('.mb-6').remove()" class="text-yellow-800 hover:text-yellow-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- Main Information --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Picking Information --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Picking Information</h2>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Picking Number</label>
                            <p class="text-lg font-mono font-bold text-gray-900 mt-1">{{ $pickingOrder->picking_number }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Status</label>
                            <div class="mt-2">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'assigned' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'in_progress' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'completed' => 'bg-green-100 text-green-800 border-green-200',
                                        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                    $statusIcons = [
                                        'pending' => 'fa-clock',
                                        'assigned' => 'fa-user-check',
                                        'in_progress' => 'fa-spinner',
                                        'completed' => 'fa-check-circle',
                                        'cancelled' => 'fa-times-circle',
                                    ];
                                @endphp
                                <span class="px-4 py-2 inline-flex text-sm font-bold rounded-full border-2 {{ $statusClasses[$pickingOrder->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                    <i class="fas {{ $statusIcons[$pickingOrder->status] ?? 'fa-question' }} mr-2"></i>
                                    {{ ucfirst(str_replace('_', ' ', $pickingOrder->status)) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Picking Date</label>
                            <p class="text-base text-gray-900 mt-1 flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                {{ \Carbon\Carbon::parse($pickingOrder->picking_date)->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Priority</label>
                            <div class="mt-2">
                                @php
                                    $priorityClasses = [
                                        'urgent' => 'bg-red-100 text-red-800 border-red-200',
                                        'high' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'medium' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'low' => 'bg-green-100 text-green-800 border-green-200',
                                    ];
                                @endphp
                                <span class="px-4 py-2 inline-flex text-sm font-bold rounded-full border-2 {{ $priorityClasses[$pickingOrder->priority] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                    {{ ucfirst($pickingOrder->priority) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Picking Type</label>
                            <p class="text-base text-gray-900 mt-1">{{ ucfirst(str_replace('_', ' ', $pickingOrder->picking_type)) }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total Items</label>
                            <p class="text-base font-bold text-gray-900 mt-1">
                                {{ $pickingOrder->total_items }} items 
                                <span class="text-gray-500 font-normal">({{ number_format($pickingOrder->total_quantity) }} qty)</span>
                            </p>
                        </div>
                    </div>

                    @if($pickingOrder->notes)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Notes</label>
                            <p class="text-base text-gray-900 mt-2 bg-gray-50 p-4 rounded-lg">{{ $pickingOrder->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Sales Order Information --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Sales Order Information</h2>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">SO Number</label>
                            <p class="text-base font-bold text-gray-900 mt-1">{{ $pickingOrder->salesOrder->so_number }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Customer</label>
                            <p class="text-base text-gray-900 mt-1">{{ $pickingOrder->salesOrder->customer->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Order Date</label>
                            <p class="text-base text-gray-900 mt-1">
                                {{ \Carbon\Carbon::parse($pickingOrder->salesOrder->order_date)->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Delivery Date</label>
                            <p class="text-base text-gray-900 mt-1">
                                {{ $pickingOrder->salesOrder->requested_delivery_date ? \Carbon\Carbon::parse($pickingOrder->salesOrder->requested_delivery_date)->format('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Warehouse Information --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-warehouse text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Warehouse Information</h2>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Warehouse Name</label>
                            <p class="text-base font-bold text-gray-900 mt-1">{{ $pickingOrder->warehouse->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Warehouse Code</label>
                            <p class="text-base text-gray-900 mt-1">{{ $pickingOrder->warehouse->code ?? '-' }}</p>
                        </div>
                        @if($pickingOrder->warehouse->address)
                            <div class="col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Location</label>
                                <p class="text-base text-gray-900 mt-1">{{ $pickingOrder->warehouse->address }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Assignment & Timeline --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-user-clock text-white"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Assignment</h2>
                    </div>
                    
                    {{-- Assigned To --}}
                    <div class="mb-6">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 block">Assigned To</label>
                        @if($pickingOrder->assignedUser)
                            <div class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-gray-900">{{ $pickingOrder->assignedUser->name }}</p>
                                    <p class="text-xs text-gray-600 mt-0.5">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $pickingOrder->assigned_at ? \Carbon\Carbon::parse($pickingOrder->assigned_at)->format('d M Y, H:i') : '-' }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 rounded-xl border-2 border-gray-200">
                                <p class="text-sm text-gray-500 text-center mb-3">Not assigned yet</p>
                                @if($pickingOrder->status === 'pending')
                                    <button onclick="document.getElementById('assignModal').classList.remove('hidden')" 
                                            class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all font-bold shadow-md">
                                        <i class="fas fa-user-plus mr-2"></i>Assign Picker
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Timeline --}}
                    <div class="pt-6 border-t border-gray-200">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4 block">Timeline</label>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-plus text-white text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-900">Created</p>
                                    <p class="text-xs text-gray-600 mt-0.5">{{ $pickingOrder->created_at->format('d M Y, H:i') }}</p>
                                    @if($pickingOrder->createdBy)
                                        <p class="text-xs text-gray-500">by {{ $pickingOrder->createdBy->name }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($pickingOrder->assigned_at)
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-user-check text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900">Assigned</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ \Carbon\Carbon::parse($pickingOrder->assigned_at)->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($pickingOrder->started_at)
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-play text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900">Started</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ \Carbon\Carbon::parse($pickingOrder->started_at)->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($pickingOrder->completed_at)
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-check-circle text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900">Completed</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ \Carbon\Carbon::parse($pickingOrder->completed_at)->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Progress Summary --}}
                @if($pickingOrder->status === 'in_progress')
                    @php
                        $pickedCount = $pickingOrder->items->where('status', 'picked')->count();
                        $pendingCount = $pickingOrder->items->where('status', 'pending')->count();
                        $totalCount = $pickingOrder->items->count();
                        $progressPercentage = $totalCount > 0 ? round(($pickedCount / $totalCount) * 100) : 0;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-xl border-2 border-green-200 p-6">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800">Progress</h2>
                        </div>
                        <div class="text-center mb-6">
                            <div class="text-5xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                {{ $progressPercentage }}%
                            </div>
                            <p class="text-sm text-gray-600 mt-2 font-semibold">Completion</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 mb-6">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-4 rounded-full transition-all duration-500" 
                                 style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 border-2 border-green-200 text-center">
                                <p class="text-3xl font-bold text-green-600">{{ $pickedCount }}</p>
                                <p class="text-xs text-gray-600 mt-1 font-semibold">Picked</p>
                            </div>
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border-2 border-gray-200 text-center">
                                <p class="text-3xl font-bold text-gray-600">{{ $pendingCount }}</p>
                                <p class="text-xs text-gray-600 mt-1 font-semibold">Pending</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Picking Items --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-list text-white"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">
                        Picking Items ({{ $pickingOrder->items->count() }})
                    </h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Seq</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Batch/Serial</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Requested</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Picked</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Picked By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pickingOrder->items->sortBy('pick_sequence') as $item)
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-xl flex items-center justify-center font-bold text-sm inline-flex">
                                        {{ $item->pick_sequence }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center mr-3 flex-shrink-0">
                                            <i class="fas fa-box text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $item->product->name }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5">{{ $item->product->sku ?? $item->product->barcode ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->storageBin)
                                        <div class="text-sm font-bold text-gray-900">{{ $item->storageBin->code ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $item->storageBin->name ?? '' }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->batch_number || $item->serial_number)
                                        <div class="text-xs text-gray-900 space-y-1">
                                            @if($item->batch_number)
                                                <div><span class="font-bold">Batch:</span> {{ $item->batch_number }}</div>
                                            @endif
                                            @if($item->serial_number)
                                                <div><span class="font-bold">Serial:</span> {{ $item->serial_number }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                    @if($item->expiry_date)
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            Exp: {{ \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-base font-bold text-gray-900">{{ number_format($item->quantity_requested, 2) }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->unit_of_measure }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-base font-bold {{ $item->quantity_picked >= $item->quantity_requested ? 'text-green-600' : 'text-yellow-600' }}">
                                        {{ number_format($item->quantity_picked, 2) }}
                                    </div>
                                    @if($item->quantity_picked > 0 && $item->quantity_picked < $item->quantity_requested)
                                        <div class="text-xs text-yellow-600 font-semibold">Short pick</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $itemStatusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'picked' => 'bg-green-100 text-green-800 border-green-200',
                                            'short' => 'bg-orange-100 text-orange-800 border-orange-200',
                                            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                        ];
                                        $itemStatusIcons = [
                                            'pending' => 'fa-clock',
                                            'picked' => 'fa-check-circle',
                                            'short' => 'fa-exclamation-triangle',
                                            'cancelled' => 'fa-times-circle',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full border-2 {{ $itemStatusClasses[$item->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                        <i class="fas {{ $itemStatusIcons[$item->status] ?? 'fa-question' }} mr-1"></i>
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->pickedBy)
                                        <div class="text-sm font-semibold text-gray-900">{{ $item->pickedBy->name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $item->picked_at ? \Carbon\Carbon::parse($item->picked_at)->format('d M, H:i') : '-' }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-inbox text-4xl text-gray-400"></i>
                                    </div>
                                    <p class="font-semibold text-gray-700">No items found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Assignment Modal --}}
@if($pickingOrder->status === 'pending')
<div id="assignModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-6 border w-full max-w-md shadow-2xl rounded-2xl bg-white m-4 animate-fade-in">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                Assign Picker
            </h3>
            <button onclick="document.getElementById('assignModal').classList.add('hidden')" 
                    class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                <i class="fas fa-times text-gray-600"></i>
            </button>
        </div>
        <form action="{{ route('outbound.picking-orders.assign', $pickingOrder) }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Select User</label>
                <select name="assigned_to" 
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all" 
                        required>
                    <option value="">Choose a picker...</option>
                    @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-3">
                <button type="button" 
                        onclick="document.getElementById('assignModal').classList.add('hidden')" 
                        class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-bold shadow-lg">
                    <i class="fas fa-user-check mr-2"></i>Assign
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.animate-fade-in');
    alerts.forEach(alert => {
        if (alert.classList.contains('mb-6')) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        }
    });
});
</script>

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