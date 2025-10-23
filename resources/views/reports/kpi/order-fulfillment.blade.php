{{-- resources/views/reports/kpi/order-fulfillment.blade.php --}}
@extends('layouts.app')

@section('title', 'Order Fulfillment Report')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                Order Fulfillment Report
            </h1>
            <p class="text-sm text-gray-600 mt-1">Order completion and fulfillment metrics</p>
        </div>
        <a href="{{ route('reports.kpi.dashboard') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('reports.kpi.order-fulfillment') }}">
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

    {{-- Fulfillment Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        {{-- Total Orders --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </div>
            </div>
            <div class="text-3xl font-bold mb-2">{{ number_format($fulfillmentMetrics->total_orders) }}</div>
            <p class="text-blue-100 text-sm">Total Orders</p>
        </div>

        {{-- Completed Orders --}}
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
            <div class="text-3xl font-bold mb-2">{{ number_format($fulfillmentMetrics->completed_orders) }}</div>
            <p class="text-green-100 text-sm">Completed Orders</p>
        </div>

        {{-- Pending Orders --}}
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
            <div class="text-3xl font-bold mb-2">{{ number_format($fulfillmentMetrics->pending_orders) }}</div>
            <p class="text-yellow-100 text-sm">Pending Orders</p>
        </div>

        {{-- Fulfillment Rate --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percentage text-2xl"></i>
                </div>
            </div>
            @php
                $rate = $fulfillmentMetrics->total_orders > 0 
                    ? ($fulfillmentMetrics->completed_orders / $fulfillmentMetrics->total_orders) * 100 
                    : 0;
            @endphp
            <div class="text-3xl font-bold mb-2">{{ number_format($rate, 1) }}%</div>
            <p class="text-purple-100 text-sm">Fulfillment Rate</p>
        </div>
    </div>

    {{-- Daily Fulfillment Trend --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
            Daily Fulfillment Trend
        </h3>
        <canvas id="dailyFulfillmentChart" height="100"></canvas>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Order Status Distribution --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                Order Status Distribution
            </h3>
            <canvas id="statusDistributionChart" height="250"></canvas>
        </div>

        {{-- Performance Summary --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-list-check text-blue-600 mr-2"></i>
                Performance Summary
            </h3>
            <div class="space-y-4">
                {{-- Completed --}}
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Completed Orders</span>
                        <span class="text-lg font-bold text-green-700">{{ number_format($fulfillmentMetrics->completed_orders) }}</span>
                    </div>
                    @php
                        $completedPercent = $fulfillmentMetrics->total_orders > 0 
                            ? ($fulfillmentMetrics->completed_orders / $fulfillmentMetrics->total_orders) * 100 
                            : 0;
                    @endphp
                    <div class="w-full bg-green-200 rounded-full h-3">
                        <div class="bg-green-600 h-3 rounded-full" style="width: {{ $completedPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ number_format($completedPercent, 1) }}% of total orders</p>
                </div>

                {{-- Pending --}}
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Pending Orders</span>
                        <span class="text-lg font-bold text-yellow-700">{{ number_format($fulfillmentMetrics->pending_orders) }}</span>
                    </div>
                    @php
                        $pendingPercent = $fulfillmentMetrics->total_orders > 0 
                            ? ($fulfillmentMetrics->pending_orders / $fulfillmentMetrics->total_orders) * 100 
                            : 0;
                    @endphp
                    <div class="w-full bg-yellow-200 rounded-full h-3">
                        <div class="bg-yellow-600 h-3 rounded-full" style="width: {{ $pendingPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ number_format($pendingPercent, 1) }}% of total orders</p>
                </div>

                {{-- Cancelled --}}
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Cancelled Orders</span>
                        <span class="text-lg font-bold text-red-700">{{ number_format($fulfillmentMetrics->cancelled_orders) }}</span>
                    </div>
                    @php
                        $cancelledPercent = $fulfillmentMetrics->total_orders > 0 
                            ? ($fulfillmentMetrics->cancelled_orders / $fulfillmentMetrics->total_orders) * 100 
                            : 0;
                    @endphp
                    <div class="w-full bg-red-200 rounded-full h-3">
                        <div class="bg-red-600 h-3 rounded-full" style="width: {{ $cancelledPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ number_format($cancelledPercent, 1) }}% of total orders</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Warehouse Fulfillment Comparison --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-warehouse text-purple-600 mr-2"></i>
            Warehouse Fulfillment Comparison
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Fulfillment Rate</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($warehouseFulfillment as $data)
                        @php
                            $warehouseRate = $data->total_orders > 0 
                                ? ($data->completed_orders / $data->total_orders) * 100 
                                : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-warehouse text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $data->warehouse->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $data->warehouse->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($data->total_orders) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-semibold text-green-600">{{ number_format($data->completed_orders) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-lg font-bold text-blue-600 mb-1">{{ number_format($warehouseRate, 1) }}%</span>
                                    <div class="w-full max-w-xs bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full" style="width: {{ $warehouseRate }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($warehouseRate >= 95)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-star mr-1"></i>Excellent
                                    </span>
                                @elseif($warehouseRate >= 85)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-thumbs-up mr-1"></i>Good
                                    </span>
                                @elseif($warehouseRate >= 70)
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Fair
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-times mr-1"></i>Needs Improvement
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-chart-bar text-4xl mb-2"></i>
                                <p>No data available for the selected period</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Fulfillment Trend Chart
    const trendCtx = document.getElementById('dailyFulfillmentChart').getContext('2d');
    const dailyData = @json($dailyFulfillment);
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(item => item.date),
            datasets: [
                {
                    label: 'Total Orders',
                    data: dailyData.map(item => item.total),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Completed Orders',
                    data: dailyData.map(item => item.completed),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Status Distribution Chart
    const distCtx = document.getElementById('statusDistributionChart').getContext('2d');
    const metrics = @json($fulfillmentMetrics);
    
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending', 'Cancelled'],
            datasets: [{
                data: [
                    metrics.completed_orders,
                    metrics.pending_orders,
                    metrics.cancelled_orders
                ],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = metrics.total_orders;
                            const value = context.parsed;
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + value + ' (' + percentage + '%)';
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