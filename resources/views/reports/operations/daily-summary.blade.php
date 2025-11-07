{{-- resources/views/reports/operations/daily-summary.blade.php --}}
@extends('layouts.app')

@section('title', 'Daily Operations Summary')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-calendar-day text-blue-600 mr-2"></i>
                Daily Operations Summary
            </h1>
            <p class="text-sm text-gray-600 mt-1">Comprehensive overview of warehouse operations</p>
        </div>
        <div class="flex flex-wrap gap-2 mt-4 md:mt-0">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition inline-flex items-center">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition inline-flex items-center">
                <i class="fas fa-file-excel mr-2"></i>Export
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('reports.operations.daily-summary') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>Date
                    </label>
                    <input type="date" name="date" value="{{ $date }}" 
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
                    <a href="{{ route('reports.operations.daily-summary') }}" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition inline-flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Date Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
            <span class="text-sm text-blue-800">
                Showing operations data for <strong>{{ $selectedDate->format('l, d F Y') }}</strong>
                @if($warehouseId)
                    at warehouse <strong>{{ $warehouses->find($warehouseId)->name ?? 'N/A' }}</strong>
                @else
                    across <strong>all warehouses</strong>
                @endif
            </span>
        </div>
    </div>

    {{-- Main KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- Inbound Card --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Inbound</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($inboundData['total_receipts']) }}</div>
            <p class="text-blue-100 text-sm mb-4">Total Receipts</p>
            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-blue-400 border-opacity-30">
                <div>
                    <div class="text-xs text-blue-100">Pallets</div>
                    <div class="text-lg font-bold">{{ number_format($inboundData['pallets_received']) }}</div>
                </div>
                <div>
                    <div class="text-xs text-blue-100">Boxes</div>
                    <div class="text-lg font-bold">{{ number_format($inboundData['boxes_received']) }}</div>
                </div>
            </div>
        </div>

        {{-- Outbound Card --}}
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Outbound</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($outboundData['total_orders']) }}</div>
            <p class="text-green-100 text-sm mb-4">Total Orders</p>
            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-green-400 border-opacity-30">
                <div>
                    <div class="text-xs text-green-100">Delivered</div>
                    <div class="text-lg font-bold">{{ number_format($outboundData['delivered_orders']) }}</div>
                </div>
                <div>
                    <div class="text-xs text-green-100">Rate</div>
                    <div class="text-lg font-bold">{{ number_format($outboundData['fulfillment_rate'], 1) }}%</div>
                </div>
            </div>
        </div>

        {{-- Movements Card --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Movements</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($movementData['total_movements']) }}</div>
            <p class="text-purple-100 text-sm mb-4">Total Movements</p>
            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-purple-400 border-opacity-30">
                <div>
                    <div class="text-xs text-purple-100">In</div>
                    <div class="text-lg font-bold">{{ number_format($movementData['inbound_moves']) }}</div>
                </div>
                <div>
                    <div class="text-xs text-purple-100">Out</div>
                    <div class="text-lg font-bold">{{ number_format($movementData['outbound_moves']) }}</div>
                </div>
            </div>
        </div>

        {{-- Inventory Card --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">Inventory</span>
            </div>
            <div class="text-4xl font-bold mb-2">{{ number_format($inventoryData['total_skus']) }}</div>
            <p class="text-orange-100 text-sm mb-4">Active SKUs</p>
            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-orange-400 border-opacity-30">
                <div>
                    <div class="text-xs text-orange-100">Quarantine</div>
                    <div class="text-lg font-bold">{{ number_format($inventoryData['quarantine_items']) }}</div>
                </div>
                <div>
                    <div class="text-xs text-orange-100">Damaged</div>
                    <div class="text-lg font-bold">{{ number_format($inventoryData['damaged_items']) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Hourly Activity Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Hourly Activity
                </h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">24 Hours</span>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>

        {{-- Value Summary --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-dollar-sign text-blue-600 mr-2"></i>
                Financial Summary
            </h3>
            <div class="space-y-4">
                {{-- Inbound Value --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">
                            <i class="fas fa-arrow-down text-blue-500 mr-1"></i>Inbound Value
                        </span>
                        <span class="text-sm font-bold text-blue-600">Rp {{ number_format($inboundData['value_received'], 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>

                {{-- Outbound Value --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">
                            <i class="fas fa-arrow-up text-green-500 mr-1"></i>Outbound Value
                        </span>
                        <span class="text-sm font-bold text-green-600">Rp {{ number_format($outboundData['value_shipped'], 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-green-600 h-2.5 rounded-full" 
                             style="width: {{ $inboundData['value_received'] > 0 ? min(($outboundData['value_shipped'] / $inboundData['value_received']) * 100, 100) : 0 }}%"></div>
                    </div>
                </div>

                {{-- Total Inventory Value --}}
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">
                            <i class="fas fa-boxes text-orange-500 mr-1"></i>Total Inventory Value
                        </span>
                        <span class="text-sm font-bold text-orange-600">Rp {{ number_format($inventoryData['total_value'], 0, ',', '.') }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        <div class="bg-blue-50 rounded p-2">
                            <div class="text-xs text-gray-600">Available</div>
                            <div class="text-sm font-bold text-blue-600">{{ number_format($inventoryData['available_quantity']) }}</div>
                        </div>
                        <div class="bg-yellow-50 rounded p-2">
                            <div class="text-xs text-gray-600">Reserved</div>
                            <div class="text-sm font-bold text-yellow-600">{{ number_format($inventoryData['reserved_quantity']) }}</div>
                        </div>
                        <div class="bg-red-50 rounded p-2">
                            <div class="text-xs text-gray-600">Expired</div>
                            <div class="text-sm font-bold text-red-600">{{ number_format($inventoryData['expired_items']) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Pending Orders Summary --}}
                <div class="pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 rounded-lg p-3">
                            <div class="text-xs text-gray-600 mb-1">Pending POs</div>
                            <div class="text-xl font-bold text-blue-600">{{ number_format($pendingOrders['purchase_orders']) }}</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3">
                            <div class="text-xs text-gray-600 mb-1">Pending SOs</div>
                            <div class="text-xl font-bold text-green-600">{{ number_format($pendingOrders['sales_orders']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Products --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-fire text-blue-600 mr-2"></i>
                    Most Active Products
                </h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Today</span>
            </div>
            <div class="space-y-3">
                @forelse($topProducts as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center flex-1">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $item->product->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-barcode mr-1"></i>{{ $item->product->sku ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-blue-600">{{ number_format($item->movement_count) }} moves</div>
                            <div class="text-xs text-gray-500">{{ number_format($item->total_quantity) }} qty</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-box-open text-5xl mb-3 text-gray-300"></i>
                        <p class="text-sm">No product movements today</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Recent Activities
                </h3>
                <a href="{{ route('reports.inventory.stock-movements') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @forelse($recentActivities as $activity)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-sm">
                        <div class="flex items-center flex-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3
                                @if($activity->movement_type == 'inbound') bg-blue-100 text-blue-600
                                @elseif($activity->movement_type == 'outbound') bg-green-100 text-green-600
                                @elseif($activity->movement_type == 'transfer') bg-purple-100 text-purple-600
                                @else bg-gray-100 text-gray-600
                                @endif">
                                <i class="fas 
                                    @if($activity->movement_type == 'inbound') fa-arrow-down
                                    @elseif($activity->movement_type == 'outbound') fa-arrow-up
                                    @elseif($activity->movement_type == 'transfer') fa-exchange-alt
                                    @else fa-edit
                                    @endif text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ ucfirst($activity->movement_type) }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $activity->product->name ?? 'N/A' }} - {{ number_format($activity->quantity) }} units
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-600">{{ $activity->movement_date->format('H:i') }}</div>
                            <div class="text-xs text-gray-500">{{ $activity->user->name ?? 'System' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-clipboard-list text-5xl mb-3 text-gray-300"></i>
                        <p class="text-sm">No activities recorded today</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Status Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
        {{-- Scheduled Receipts --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-600"></i>
                </div>
                <span class="text-2xl font-bold text-blue-600">{{ number_format($inboundData['scheduled_receipts']) }}</span>
            </div>
            <div class="text-sm font-medium text-gray-700">Scheduled Receipts</div>
            <div class="text-xs text-gray-500 mt-1">Pending arrivals today</div>
        </div>

        {{-- In Transit --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-purple-600"></i>
                </div>
                <span class="text-2xl font-bold text-purple-600">{{ number_format($inboundData['in_transit']) }}</span>
            </div>
            <div class="text-sm font-medium text-gray-700">In Transit</div>
            <div class="text-xs text-gray-500 mt-1">On the way to warehouse</div>
        </div>

        {{-- Picking Orders --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-holding-box text-green-600"></i>
                </div>
                <span class="text-2xl font-bold text-green-600">{{ number_format($outboundData['picking_orders']) }}</span>
            </div>
            <div class="text-sm font-medium text-gray-700">Picking Orders</div>
            <div class="text-xs text-gray-500 mt-1">Currently being picked</div>
        </div>

        {{-- Packing Orders --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box-open text-orange-600"></i>
                </div>
                <span class="text-2xl font-bold text-orange-600">{{ number_format($outboundData['packing_orders']) }}</span>
            </div>
            <div class="text-sm font-medium text-gray-700">Packing Orders</div>
            <div class="text-xs text-gray-500 mt-1">Currently being packed</div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hourly Activity Chart
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    const hourlyData = @json($hourlyActivity);
    
    new Chart(hourlyCtx, {
        type: 'line',
        data: {
            labels: hourlyData.map(item => {
                const hour = item.hour;
                return hour.toString().padStart(2, '0') + ':00';
            }),
            datasets: [
                {
                    label: 'Inbound',
                    data: hourlyData.map(item => item.inbound),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Outbound',
                    data: hourlyData.map(item => item.outbound),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Movements',
                    data: hourlyData.map(item => item.movements),
                    borderColor: 'rgb(168, 85, 247)',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgb(168, 85, 247)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' operations';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection