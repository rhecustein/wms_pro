{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard WMS')

@section('content')
    {{-- FILTERS --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Warehouse Filter --}}
                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-warehouse text-gray-400 mr-1"></i> Warehouse
                    </label>
                    <select name="warehouse_id" id="warehouse_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $selectedWarehouseId == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Start Date --}}
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-gray-400 mr-1"></i> Start Date
                    </label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                </div>

                {{-- End Date --}}
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-gray-400 mr-1"></i> End Date
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                </div>

                {{-- Submit Button --}}
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter mr-2"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Refresh Button --}}
    <div class="flex justify-end mb-4">
        <button onclick="refreshDashboard()" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-sync-alt mr-2"></i> Refresh Dashboard
        </button>
    </div>

    {{-- KEY METRICS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- Total Products --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-box text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Total Products</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['total_products']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Stock Value --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-dollar-sign text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Total Stock Value</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($metrics['total_stock_value'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total SKUs --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-barcode text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Active SKUs</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['total_skus']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Stock Quantity --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-cubes text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Total Stock Qty</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['total_stock_qty']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inbound Today --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-arrow-down text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Inbound Today</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $metrics['inbound_today'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Outbound Today --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-arrow-up text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Outbound Today</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $metrics['outbound_today'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Pickings --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-hand-paper text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Pending Pickings</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $metrics['pending_pickings'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Deliveries --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-truck text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Pending Deliveries</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $metrics['pending_deliveries'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTS SECTION --}}
    @if(count($alerts) > 0)
    <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-4 flex items-center text-gray-900">
                <i class="fas fa-bell text-yellow-500 mr-3"></i> Alerts & Notifications
            </h3>
            <div class="space-y-3">
                @foreach($alerts as $alert)
                <div class="border-l-4 border-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-500 bg-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-50 p-4 rounded-r-lg hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="flex-shrink-0">
                                <i class="fas fa-{{ $alert['icon'] }} text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-800">
                                    {{ $alert['title'] }}
                                </p>
                                <p class="text-sm text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-700">
                                    {{ $alert['message'] }}
                                </p>
                            </div>
                        </div>
                        <div class="ml-4">
                            <a href="{{ $alert['link'] }}" class="text-sm font-medium text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-600 hover:text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-700 whitespace-nowrap">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- INVENTORY SUMMARY & TODAY'S ACTIVITIES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Inventory Summary --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4 flex items-center text-gray-900">
                    <i class="fas fa-warehouse text-blue-500 mr-3"></i> Inventory Summary
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-check-circle text-green-500 mr-2"></i> Available</span>
                        <span class="font-bold text-green-600 text-lg">{{ number_format($inventorySummary['available']) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-lock text-blue-500 mr-2"></i> Reserved</span>
                        <span class="font-bold text-blue-600 text-lg">{{ number_format($inventorySummary['reserved']) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i> Quarantine</span>
                        <span class="font-bold text-yellow-600 text-lg">{{ number_format($inventorySummary['quarantine']) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-tools text-orange-500 mr-2"></i> Damaged</span>
                        <span class="font-bold text-orange-600 text-lg">{{ number_format($inventorySummary['damaged']) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-times-circle text-red-500 mr-2"></i> Expired</span>
                        <span class="font-bold text-red-600 text-lg">{{ number_format($inventorySummary['expired']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's Activities --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4 flex items-center text-gray-900">
                    <i class="fas fa-chart-line text-green-500 mr-3"></i> Today's Activities
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-download text-blue-500 mr-2"></i> Goods Received</span>
                        <span class="font-bold text-gray-900 text-lg">{{ $todayActivities['goods_received'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-box-open text-purple-500 mr-2"></i> Putaways Completed</span>
                        <span class="font-bold text-gray-900 text-lg">{{ $todayActivities['putaways_completed'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-hand-paper text-orange-500 mr-2"></i> Orders Picked</span>
                        <span class="font-bold text-gray-900 text-lg">{{ $todayActivities['orders_picked'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-box text-indigo-500 mr-2"></i> Orders Packed</span>
                        <span class="font-bold text-gray-900 text-lg">{{ $todayActivities['orders_packed'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-shipping-fast text-green-500 mr-2"></i> Orders Shipped</span>
                        <span class="font-bold text-gray-900 text-lg">{{ $todayActivities['orders_shipped'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-gray-700 font-medium"><i class="fas fa-sync text-teal-500 mr-2"></i> Replenishments</span>
                        <span class="font-bold text-gray-900 text-lg">{{ $todayActivities['replenishments'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PENDING TASKS --}}
    <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-6 flex items-center text-gray-900">
                <i class="fas fa-tasks text-purple-500 mr-3"></i> Pending Tasks
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="text-center transform hover:scale-105 transition-transform duration-200">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl p-6 shadow-md hover:shadow-lg">
                        <i class="fas fa-dolly text-4xl text-purple-600 mb-3"></i>
                        <p class="text-3xl font-bold text-purple-700 mb-1">{{ $pendingTasks['putaway'] }}</p>
                        <p class="text-sm font-medium text-gray-700">Putaway</p>
                    </div>
                </div>
                <div class="text-center transform hover:scale-105 transition-transform duration-200">
                    <div class="bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl p-6 shadow-md hover:shadow-lg">
                        <i class="fas fa-hand-paper text-4xl text-orange-600 mb-3"></i>
                        <p class="text-3xl font-bold text-orange-700 mb-1">{{ $pendingTasks['picking'] }}</p>
                        <p class="text-sm font-medium text-gray-700">Picking</p>
                    </div>
                </div>
                <div class="text-center transform hover:scale-105 transition-transform duration-200">
                    <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl p-6 shadow-md hover:shadow-lg">
                        <i class="fas fa-box text-4xl text-blue-600 mb-3"></i>
                        <p class="text-3xl font-bold text-blue-700 mb-1">{{ $pendingTasks['packing'] }}</p>
                        <p class="text-sm font-medium text-gray-700">Packing</p>
                    </div>
                </div>
                <div class="text-center transform hover:scale-105 transition-transform duration-200">
                    <div class="bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl p-6 shadow-md hover:shadow-lg">
                        <i class="fas fa-sync-alt text-4xl text-teal-600 mb-3"></i>
                        <p class="text-3xl font-bold text-teal-700 mb-1">{{ $pendingTasks['replenishment'] }}</p>
                        <p class="text-sm font-medium text-gray-700">Replenishment</p>
                    </div>
                </div>
                <div class="text-center transform hover:scale-105 transition-transform duration-200">
                    <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl p-6 shadow-md hover:shadow-lg">
                        <i class="fas fa-check-circle text-4xl text-yellow-600 mb-3"></i>
                        <p class="text-3xl font-bold text-yellow-700 mb-1">{{ $pendingTasks['quality_check'] }}</p>
                        <p class="text-sm font-medium text-gray-700">Quality Check</p>
                    </div>
                </div>
                <div class="text-center transform hover:scale-105 transition-transform duration-200">
                    <div class="bg-gradient-to-br from-red-100 to-red-200 rounded-xl p-6 shadow-md hover:shadow-lg">
                        <i class="fas fa-edit text-4xl text-red-600 mb-3"></i>
                        <p class="text-3xl font-bold text-red-700 mb-1">{{ $pendingTasks['stock_adjustment'] }}</p>
                        <p class="text-sm font-medium text-gray-700">Adjustments</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Inbound vs Outbound Chart --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4 flex items-center text-gray-900">
                    <i class="fas fa-chart-bar text-blue-500 mr-3"></i> Inbound vs Outbound (Last 7 Days)
                </h3>
                <div style="height: 300px;">
                    <canvas id="inboundOutboundChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Order Status Distribution --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4 flex items-center text-gray-900">
                    <i class="fas fa-chart-pie text-green-500 mr-3"></i> Order Status Distribution
                </h3>
                <div style="height: 300px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock by Category Chart --}}
    <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-4 flex items-center text-gray-900">
                <i class="fas fa-chart-area text-purple-500 mr-3"></i> Stock by Category (Top 5)
            </h3>
            <div style="height: 250px;">
                <canvas id="stockByCategoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- RECENT ORDERS & TOP PRODUCTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Recent Sales Orders --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4 flex items-center text-gray-900">
                    <i class="fas fa-shopping-cart text-blue-500 mr-3"></i> Recent Sales Orders
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">SO Number</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentOrders['sales_orders'] as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $order->so_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $order->customer_name }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-{{ $order->status === 'completed' ? 'green' : 'yellow' }}-100 text-{{ $order->status === 'completed' ? 'green' : 'yellow' }}-800">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-sm text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                    <p>No recent orders</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4 flex items-center text-gray-900">
                    <i class="fas fa-star text-yellow-500 mr-3"></i> Top Moving Products (Last 7 Days)
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total Moved</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($topProducts as $product)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $product->sku }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="font-bold text-blue-600">{{ number_format($product->total_moved) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-sm text-center text-gray-500">
                                    <i class="fas fa-chart-line text-4xl text-gray-300 mb-2"></i>
                                    <p>No movement data</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- WAREHOUSE UTILIZATION --}}
    <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-6 flex items-center text-gray-900">
                <i class="fas fa-warehouse text-indigo-500 mr-3"></i> Warehouse Utilization
            </h3>
            @if($selectedWarehouseId && isset($warehouseUtilization['warehouse_name']))
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="flex justify-between mb-3">
                        <span class="text-lg font-semibold text-gray-700">{{ $warehouseUtilization['warehouse_name'] }}</span>
                        <span class="text-lg font-bold text-blue-600">{{ $warehouseUtilization['utilization_percent'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden shadow-inner">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-6 rounded-full transition-all duration-500 flex items-center justify-center" style="width: {{ $warehouseUtilization['utilization_percent'] }}%">
                            <span class="text-xs font-semibold text-white">{{ $warehouseUtilization['utilization_percent'] }}%</span>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-white rounded-lg shadow">
                            <p class="text-sm text-gray-600 mb-1">Total Bins</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($warehouseUtilization['total_bins']) }}</p>
                        </div>
                        <div class="text-center p-4 bg-white rounded-lg shadow">
                            <p class="text-sm text-gray-600 mb-1">Occupied</p>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($warehouseUtilization['occupied_bins']) }}</p>
                        </div>
                        <div class="text-center p-4 bg-white rounded-lg shadow">
                            <p class="text-sm text-gray-600 mb-1">Available</p>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($warehouseUtilization['available_bins']) }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($warehouseUtilization as $wh)
                        @php
                            $utilization = $wh->total_bins > 0 ? round(($wh->occupied_bins / $wh->total_bins) * 100, 2) : 0;
                        @endphp
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">{{ $wh->name }}</span>
                                <span class="text-sm font-bold text-blue-600">{{ $utilization }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $utilization }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- RECENT ACTIVITIES LOG --}}
    <div class="bg-white overflow-hidden shadow-lg rounded-xl">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-4 flex items-center text-gray-900">
                <i class="fas fa-history text-gray-500 mr-3"></i> Recent Activities
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Time</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentActivities as $activity)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                <i class="fas fa-clock text-gray-400 mr-2"></i>
                                {{ \Carbon\Carbon::parse($activity->created_at)->format('H:i:s') }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $activity->user_name }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $activity->log_name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $activity->description }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-sm text-center text-gray-500">
                                <i class="fas fa-history text-4xl text-gray-300 mb-2"></i>
                                <p>No recent activities</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Inbound vs Outbound Chart
    const inboundOutboundCtx = document.getElementById('inboundOutboundChart').getContext('2d');
    new Chart(inboundOutboundCtx, {
        type: 'line',
        data: {
            labels: @json($charts['inbound_outbound']['labels']),
            datasets: [
                {
                    label: 'Inbound',
                    data: @json($charts['inbound_outbound']['inbound']),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Outbound',
                    data: @json($charts['inbound_outbound']['outbound']),
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusData = @json($charts['order_status']);
    new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: orderStatusData.map(item => item.status),
            datasets: [{
                data: orderStatusData.map(item => item.count),
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(34, 197, 94)',
                    'rgb(251, 191, 36)',
                    'rgb(239, 68, 68)',
                    'rgb(168, 85, 247)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });

    // Stock by Category Chart
    const stockByCategoryCtx = document.getElementById('stockByCategoryChart').getContext('2d');
    const stockByCategoryData = @json($charts['stock_by_category']);
    new Chart(stockByCategoryCtx, {
        type: 'bar',
        data: {
            labels: stockByCategoryData.map(item => item.name),
            datasets: [{
                label: 'Stock Quantity',
                data: stockByCategoryData.map(item => item.total),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Refresh Dashboard Function
    function refreshDashboard() {
        const warehouseId = document.getElementById('warehouse_id').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        Swal.fire({
            title: 'Refreshing...',
            text: 'Please wait while we update the dashboard',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route("dashboard") }}?refresh=1&warehouse_id=' + warehouseId + '&start_date=' + startDate + '&end_date=' + endDate, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Dashboard Updated',
                text: 'Dashboard has been refreshed successfully',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to refresh dashboard'
            });
        });
    }

    // Auto refresh every 5 minutes
    setInterval(() => {
        refreshDashboard();
    }, 300000);
</script>
@endpush