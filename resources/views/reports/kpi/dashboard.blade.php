{{-- resources/views/reports/kpi/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'KPI Dashboard')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                KPI Dashboard
            </h1>
            <p class="text-sm text-gray-600 mt-1">Key Performance Indicators overview and metrics</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('reports.kpi.accuracy') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-bullseye mr-2"></i>Accuracy
            </a>
            <a href="{{ route('reports.kpi.efficiency') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-bolt mr-2"></i>Efficiency
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('reports.kpi.dashboard') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse</label>
                    <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        {{-- Accuracy Card --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bullseye text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Accuracy</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($overallAccuracy, 1) }}%</div>
            <p class="text-blue-100 text-sm">Overall Accuracy Rate</p>
        </div>

        {{-- Fulfillment Card --}}
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Fulfillment</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($fulfillmentRate, 1) }}%</div>
            <p class="text-green-100 text-sm">Order Fulfillment Rate</p>
        </div>

        {{-- Inventory Turnover Card --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sync-alt text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Turnover</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($inventoryTurnover, 2) }}x</div>
            <p class="text-purple-100 text-sm">Inventory Turnover</p>
        </div>

        {{-- Efficiency Card --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bolt text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Efficiency</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($efficiencyScore, 0) }}%</div>
            <p class="text-orange-100 text-sm">Process Efficiency Score</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Accuracy Trend Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-area text-blue-600 mr-2"></i>
                Accuracy Trend (6 Months)
            </h3>
            <canvas id="accuracyChart" height="200"></canvas>
        </div>

        {{-- Recent Opnames --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-clipboard-check text-blue-600 mr-2"></i>
                    Recent Stock Opnames
                </h3>
                <a href="{{ route('inventory.opnames.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
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
                                <div class="text-xs text-gray-500">{{ $opname->warehouse->name }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-gray-900">{{ number_format($opname->accuracy_percentage, 1) }}%</div>
                            <div class="text-xs text-gray-500">{{ $opname->opname_date->format('d M Y') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-clipboard-check text-4xl mb-2"></i>
                        <p>No recent opnames</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Accuracy Trend Chart
    const accuracyCtx = document.getElementById('accuracyChart').getContext('2d');
    const accuracyData = @json($monthlyAccuracy);
    
    new Chart(accuracyCtx, {
        type: 'line',
        data: {
            labels: accuracyData.map(item => item.month),
            datasets: [{
                label: 'Accuracy %',
                data: accuracyData.map(item => item.accuracy),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
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
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection