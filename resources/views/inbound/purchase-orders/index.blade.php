@extends('layouts.app')
@section('title', 'Purchase Orders')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-gray-50">
    <div class="container-fluid px-4 py-8 max-w-[1600px] mx-auto">
        
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
                            <span class="text-sm font-medium text-gray-900">Purchase Orders</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-blue-500/30">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                        Purchase Orders
                    </h1>
                    <p class="text-sm text-gray-600 mt-2 ml-16">Manage and track your purchase orders efficiently</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="exportData()" class="px-4 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2 font-medium">
                        <i class="fas fa-download"></i>
                        <span class="hidden sm:inline">Export</span>
                    </button>
                    <a href="{{ route('inbound.purchase-orders.create') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 flex items-center gap-2 font-medium">
                        <i class="fas fa-plus"></i>
                        <span>New Purchase Order</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total POs --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-file-invoice text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total POs</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">All purchase orders</p>
                        </div>
                        <div class="flex items-center text-green-600 text-sm font-medium">
                            <i class="fas fa-arrow-up text-xs mr-1"></i>
                            <span>12%</span>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
            </div>

            {{-- Pending --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-yellow-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Awaiting confirmation</p>
                        </div>
                        <div class="flex items-center text-yellow-600 text-sm font-medium">
                            <i class="fas fa-minus text-xs mr-1"></i>
                            <span>0%</span>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-yellow-500 to-orange-500"></div>
            </div>

            {{-- Partial Received --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-box-open text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Partial</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['partial'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Partially received</p>
                        </div>
                        <div class="flex items-center text-purple-600 text-sm font-medium">
                            <i class="fas fa-arrow-down text-xs mr-1"></i>
                            <span>5%</span>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-purple-500 to-purple-600"></div>
            </div>

            {{-- Total Amount --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-dollar-sign text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Value</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">IDR {{ number_format($stats['total_amount'] ?? 0, 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Outstanding value</p>
                        </div>
                        <div class="flex items-center text-green-600 text-sm font-medium">
                            <i class="fas fa-arrow-up text-xs mr-1"></i>
                            <span>8%</span>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-xl shadow-sm animate-slideInRight">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-green-900">Success!</p>
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button onclick="this.closest('.animate-slideInRight').remove()" class="text-green-600 hover:text-green-800 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl shadow-sm animate-slideInRight">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-circle text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-red-900">Error!</p>
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button onclick="this.closest('.animate-slideInRight').remove()" class="text-red-600 hover:text-red-800 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- Filters Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-filter text-blue-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Filters</h2>
                            <p class="text-xs text-gray-500">Refine your search results</p>
                        </div>
                    </div>
                    <button onclick="toggleFilters()" class="lg:hidden px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-chevron-down" id="filterIcon"></i>
                    </button>
                </div>
            </div>
            
            <div id="filterSection" class="p-6">
                <form method="GET" action="{{ route('inbound.purchase-orders.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        {{-- Search --}}
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>
                                Search
                            </label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="PO Number, Supplier, Notes..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        {{-- Status Filter --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-flag text-gray-400 mr-1"></i>
                                Status
                            </label>
                            <select name="status" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                <option value="">All Status</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Payment Status --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-credit-card text-gray-400 mr-1"></i>
                                Payment
                            </label>
                            <select name="payment_status" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                <option value="">All Payment</option>
                                @foreach($paymentStatuses as $key => $label)
                                    <option value="{{ $key }}" {{ request('payment_status') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Warehouse Filter --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-warehouse text-gray-400 mr-1"></i>
                                Warehouse
                            </label>
                            <select name="warehouse_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                <option value="">All Warehouses</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Supplier Filter - SUDAH BENAR ✅ --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-building text-gray-400 mr-1"></i>
                                Supplier
                            </label>
                            <select name="supplier_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                Date From
                            </label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-check text-gray-400 mr-1"></i>
                                Date To
                            </label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg shadow-blue-500/30 flex items-center gap-2 font-medium">
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('inbound.purchase-orders.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 flex items-center gap-2 font-medium">
                            <i class="fas fa-redo"></i>
                            Reset
                        </a>
                        <div class="ml-auto text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Showing <span class="font-semibold text-gray-900">{{ $purchaseOrders->count() }}</span> of <span class="font-semibold text-gray-900">{{ $purchaseOrders->total() }}</span> results
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Purchase Orders Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                    PO Number
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                    Date
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-building text-gray-400"></i>
                                    Supplier
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-warehouse text-gray-400"></i>
                                    Warehouse
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-box text-gray-400"></i>
                                    Items
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-dollar-sign text-gray-400"></i>
                                    Amount
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-flag text-gray-400"></i>
                                    Status
                                </div>
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center justify-end gap-2">
                                    <i class="fas fa-cog text-gray-400"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($purchaseOrders as $po)
                            <tr class="hover:bg-blue-50/50 transition-all duration-200 group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/30">
                                            <i class="fas fa-file-invoice text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold font-mono text-gray-900">{{ $po->po_number }}</span>
                                            @if($po->reference_number)
                                                <p class="text-xs text-gray-500">Ref: {{ $po->reference_number }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                            <i class="fas fa-calendar-alt text-blue-500 text-xs"></i>
                                            {{ $po->po_date->format('d M Y') }}
                                        </div>
                                        @if($po->expected_delivery_date)
                                            <div class="text-xs text-gray-500 flex items-center gap-2 mt-1">
                                                <i class="fas fa-truck text-gray-400"></i>
                                                {{ $po->expected_delivery_date->format('d M Y') }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-200">
                                            <i class="fas fa-building text-purple-600"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-bold text-gray-900 truncate">{{ $po->supplier->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $po->supplier->code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-200">
                                            <i class="fas fa-warehouse text-teal-600"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-bold text-gray-900 truncate">{{ $po->warehouse->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $po->warehouse->code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <span class="text-sm font-bold text-blue-600">{{ $po->items->count() }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">items</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ $po->currency }} {{ number_format($po->total_amount, 0) }}
                                        </div>
                                        @if($po->payment_status !== 'paid')
                                            <div class="flex items-center gap-1 mt-1">
                                                {!! $po->payment_status_badge !!}
                                            </div>
                                        @else
                                            <span class="text-xs text-green-600 font-medium flex items-center gap-1 mt-1">
                                                <i class="fas fa-check-circle"></i>
                                                Paid
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $po->status_badge !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('inbound.purchase-orders.show', $po) }}" class="w-9 h-9 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110" title="View Details">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        
                                        @if(in_array($po->status, ['draft', 'submitted']))
                                            <a href="{{ route('inbound.purchase-orders.edit', $po) }}" class="w-9 h-9 bg-yellow-100 hover:bg-yellow-200 text-yellow-600 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110" title="Edit">
                                                <i class="fas fa-edit text-sm"></i>
                                            </a>
                                        @endif
                                        
                                        <div class="relative group/menu">
                                            <button class="w-9 h-9 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110">
                                                <i class="fas fa-ellipsis-v text-sm"></i>
                                            </button>
                                            
                                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 py-2 invisible group-hover/menu:visible opacity-0 group-hover/menu:opacity-100 transition-all duration-200 z-10">
                                                <a href="{{ route('inbound.purchase-orders.print', $po) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                    <i class="fas fa-print w-4"></i>
                                                    Print PO
                                                </a>
                                                <a href="{{ route('inbound.purchase-orders.duplicate', $po) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                    <i class="fas fa-copy w-4"></i>
                                                    Duplicate
                                                </a>
                                                @if($po->status === 'draft')
                                                    <hr class="my-2">
                                                    <form action="{{ route('inbound.purchase-orders.destroy', $po) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this purchase order?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                            <i class="fas fa-trash w-4"></i>
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20">
                                    <div class="flex flex-col items-center">
                                        <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                            <i class="fas fa-shopping-cart text-5xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800 mb-2">No Purchase Orders Found</h3>
                                        <p class="text-gray-600 mb-6 text-center max-w-md">Get started by creating your first purchase order to manage your inventory</p>
                                        <a href="{{ route('inbound.purchase-orders.create') }}" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg shadow-blue-500/30 flex items-center gap-2 font-medium">
                                            <i class="fas fa-plus"></i>
                                            Create Your First PO
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($purchaseOrders->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $purchaseOrders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slideInRight {
    animation: slideInRight 0.5s ease-out;
}
</style>

<script>
function toggleFilters() {
    const section = document.getElementById('filterSection');
    const icon = document.getElementById('filterIcon');
    
    section.classList.toggle('hidden');
    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
}

function exportData() {
    alert('Export functionality will be implemented');
}

// Auto-submit form on select change
document.querySelectorAll('#filterForm select').forEach(select => {
    select.addEventListener('change', function() {
        if (window.innerWidth >= 1024) {
            document.getElementById('filterForm').submit();
        }
    });
});
</script>
@endsection