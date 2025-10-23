@extends('layouts.app')

@section('title', 'Vendor Performance Report')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-chart-line text-green-600 mr-2"></i>
                Vendor Performance Report
            </h1>
            <p class="text-sm text-gray-600 mt-1">Analyze supplier delivery performance and reliability</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('reports.inbound.receiving-report') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-truck-loading mr-2"></i>Receiving Report
            </a>
            <a href="{{ route('reports.inbound.receiving-accuracy') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-bullseye mr-2"></i>Accuracy Report
            </a>
        </div>
    </div>

    {{-- Overall Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Suppliers</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_suppliers']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Active Suppliers</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['active_suppliers']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Deliveries</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_deliveries']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">On-Time Rate</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($stats['overall_on_time_rate'], 1) }}%</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('reports.inbound.vendor-performance') }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Supplier</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Supplier Name, Code..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Date Range --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('reports.inbound.vendor-performance') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Vendor Performance Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Deliveries</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">On-Time Rate</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cancelled</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Items</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Items/Delivery</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-truck text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $supplier->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $supplier->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($supplier->performance['total_deliveries']) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ number_format($supplier->performance['on_time_rate'], 1) }}%
                                        </span>
                                        @if($supplier->performance['on_time_rate'] >= 90)
                                            <i class="fas fa-arrow-up text-green-600"></i>
                                        @elseif($supplier->performance['on_time_rate'] >= 70)
                                            <i class="fas fa-minus text-yellow-600"></i>
                                        @else
                                            <i class="fas fa-arrow-down text-red-600"></i>
                                        @endif
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $supplier->performance['on_time_rate'] >= 90 ? 'bg-green-600' : ($supplier->performance['on_time_rate'] >= 70 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                             style="width: {{ $supplier->performance['on_time_rate'] }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $supplier->performance['on_time_deliveries'] }}/{{ $supplier->performance['total_deliveries'] }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ number_format($supplier->performance['completion_rate'], 1) }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $supplier->performance['completion_rate'] }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $supplier->performance['completed_deliveries'] }}/{{ $supplier->performance['total_deliveries'] }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm">
                                    <div class="font-semibold text-red-600">{{ $supplier->performance['cancelled_deliveries'] }}</div>
                                    <div class="text-xs text-gray-500">{{ number_format($supplier->performance['cancellation_rate'], 1) }}%</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($supplier->performance['total_items']) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($supplier->performance['avg_items_per_delivery'], 1) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $overallScore = ($supplier->performance['on_time_rate'] + $supplier->performance['completion_rate']) / 2;
                                @endphp
                                @if($overallScore >= 90)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-star mr-1"></i>
                                        Excellent
                                    </span>
                                @elseif($overallScore >= 80)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-thumbs-up mr-1"></i>
                                        Good
                                    </span>
                                @elseif($overallScore >= 70)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Fair
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Poor
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-chart-line text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Vendor Performance Data</h3>
                                    <p class="text-gray-600">Try adjusting your date range</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($suppliers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

    {{-- Performance Legend --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Performance Rating Guide</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-star mr-1"></i>Excellent
                </span>
                <span class="text-sm text-gray-600">≥ 90%</span>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-thumbs-up mr-1"></i>Good
                </span>
                <span class="text-sm text-gray-600">80-89%</span>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-exclamation-circle mr-1"></i>Fair
                </span>
                <span class="text-sm text-gray-600">70-79%</span>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <i class="fas fa-times-circle mr-1"></i>Poor
                </span>
                <span class="text-sm text-gray-600">< 70%</span>
            </div>
        </div>
    </div>

</div>
@endsection