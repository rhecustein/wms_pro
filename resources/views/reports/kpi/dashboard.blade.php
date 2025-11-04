{{-- resources/views/reports/kpi/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'KPI Dashboard')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                KPI Dashboard
            </h1>
            <p class="text-sm text-gray-600 mt-1">Monitor key performance indicators and warehouse metrics</p>
        </div>
        <div class="flex flex-wrap gap-2 mt-4 md:mt-0">
            <a href="{{ route('reports.kpi.accuracy') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition inline-flex items-center">
                <i class="fas fa-bullseye mr-2"></i>Accuracy Details
            </a>
            <a href="{{ route('reports.kpi.efficiency') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition inline-flex items-center">
                <i class="fas fa-bolt mr-2"></i>Efficiency Details
            </a>
            <a href="{{ route('reports.kpi.order-fulfillment') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition inline-flex items-center">
                <i class="fas fa-truck mr-2"></i>Order Fulfillment
            </a>
            <a href="{{ route('reports.kpi.inventory-turnover') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition inline-flex items-center">
                <i class="fas fa-sync-alt mr-2"></i>Inventory Turnover
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('reports.kpi.dashboard') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>Date From
                    </label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>Date To
                    </label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-warehouse text-gray-400 mr-1"></i>Warehouse
                    </label>
                    <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $warehouseId == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('reports.kpi.dashboard') }}" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition inline-flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Date Range Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
            <span class="text-sm text-blue-800">
                Showing data from <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
                @if($warehouseId)
                    for warehouse <strong>{{ $warehouses->find($warehouseId)->name ?? 'N/A' }}</strong>
                @else
                    for <strong>all warehouses</strong>
                @endif
            </span>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- Accuracy Card --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bullseye text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Accuracy</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($overallAccuracy, 1) }}%</div>
            <p class="text-blue-100 text-sm">Overall Accuracy Rate</p>
            <div class="mt-4 pt-4 border-t border-blue-400 border-opacity-30">
                <a href="{{ route('reports.kpi.accuracy') }}" class="text-sm hover:underline inline-flex items-center">
                    View Details <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        {{-- Fulfillment Card --}}
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Fulfillment</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($fulfillmentRate, 1) }}%</div>
            <p class="text-green-100 text-sm">Order Fulfillment Rate</p>
            <div class="mt-4 pt-4 border-t border-green-400 border-opacity-30">
                <a href="{{ route('reports.kpi.order-fulfillment') }}" class="text-sm hover:underline inline-flex items-center">
                    View Details <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        {{-- Inventory Turnover Card --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sync-alt text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Turnover</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($inventoryTurnover, 2) }}x</div>
            <p class="text-purple-100 text-sm">Inventory Turnover Ratio</p>
            <div class="mt-4 pt-4 border-t border-purple-400 border-opacity-30">
                <a href="{{ route('reports.kpi.inventory-turnover') }}" class="text-sm hover:underline inline-flex items-center">
                    View Details <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        {{-- Efficiency Card --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bolt text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Efficiency</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($efficiencyScore, 0) }}%</div>
            <p class="text-orange-100 text-sm">Process Efficiency Score</p>
            <div class="mt-4 pt-4 border-t border-orange-400 border-opacity-30">
                <a href="{{ route('reports.kpi.efficiency') }}" class="text-sm hover:underline inline-flex items-center">
                    View Details <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Accuracy Trend Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-chart-area text-blue-600 mr-2"></i>
                    Accuracy Trend
                </h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Last 6 Months</span>
            </div>
            <div class="relative" style="height: 250px;">
                <canvas id="accuracyChart"></canvas>
            </div>
        </div>

        {{-- Performance Summary --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                Performance Summary
            </h3>
            <div class="space-y-4">
                {{-- Accuracy --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Accuracy Rate</span>
                        <span class="text-sm font-bold text-blue-600">{{ number_format($overallAccuracy, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min($overallAccuracy, 100) }}%"></div>
                    </div>
                </div>

                {{-- Fulfillment --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Fulfillment Rate</span>
                        <span class="text-sm font-bold text-green-600">{{ number_format($fulfillmentRate, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ min($fulfillmentRate, 100) }}%"></div>
                    </div>
                </div>

                {{-- Efficiency --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Efficiency Score</span>
                        <span class="text-sm font-bold text-orange-600">{{ number_format($efficiencyScore, 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-orange-600 h-2.5 rounded-full" style="width: {{ min($efficiencyScore, 100) }}%"></div>
                    </div>
                </div>

                {{-- Turnover Indicator --}}
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Inventory Turnover</span>
                        <span class="text-sm font-bold text-purple-600">{{ number_format($inventoryTurnover, 2) }}x</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        @if($inventoryTurnover >= 4)
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>Excellent turnover rate
                        @elseif($inventoryTurnover >= 2)
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>Good turnover rate
                        @else
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>Low turnover rate
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Opnames --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-clipboard-check text-blue-600 mr-2"></i>
                    Recent Stock Opnames
                </h3>
                <a href="{{ route('inventory.opnames.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentOpnames as $opname)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-warehouse text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $opname->opname_number }}</div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $opname->warehouse->name }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center">
                                @if($opname->accuracy_percentage >= 95)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>{{ number_format($opname->accuracy_percentage, 1) }}%
                                    </span>
                                @elseif($opname->accuracy_percentage >= 80)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ number_format($opname->accuracy_percentage, 1) }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>{{ number_format($opname->accuracy_percentage, 1) }}%
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-calendar mr-1"></i>{{ $opname->opname_date->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-clipboard-check text-5xl mb-3 text-gray-300"></i>
                        <p class="text-sm">No recent stock opnames found</p>
                        <a href="{{ route('inventory.opnames.create') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium mt-2 inline-block">
                            <i class="fas fa-plus mr-1"></i>Create New Opname
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-tachometer-alt text-blue-600 mr-2"></i>
                Quick Statistics
            </h3>
            <div class="grid grid-cols-2 gap-4">
                {{-- Total Warehouses --}}
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-warehouse text-2xl text-blue-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $warehouses->count() }}</div>
                    <div class="text-xs text-gray-600">Total Warehouses</div>
                </div>

                {{-- Total Opnames --}}
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-clipboard-check text-2xl text-green-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $recentOpnames->count() }}</div>
                    <div class="text-xs text-gray-600">Recent Opnames</div>
                </div>

                {{-- Avg Accuracy --}}
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-bullseye text-2xl text-purple-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($overallAccuracy, 1) }}%</div>
                    <div class="text-xs text-gray-600">Avg Accuracy</div>
                </div>

                {{-- Period --}}
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-calendar-alt text-2xl text-orange-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo)) + 1 }}</div>
                    <div class="text-xs text-gray-600">Days Period</div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Accuracy Trend Chart
    const accuracyCtx = document.getElementById('accuracyChart').getContext('2d');
    const accuracyData = @json($monthlyAccuracy);
    
    new Chart(accuracyCtx, {
        type: 'line',
        data: {
            labels: accuracyData.map(item => {
                const date = new Date(item.month + '-01');
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Accuracy Rate',
                data: accuracyData.map(item => parseFloat(item.accuracy || 0)),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return 'Accuracy: ' + context.parsed.y.toFixed(1) + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection