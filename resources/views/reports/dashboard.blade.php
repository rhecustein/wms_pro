{{-- resources/views/dashboard/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard WMS') }}
            </h2>
            <div class="flex space-x-2">
                <button onclick="refreshDashboard()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- FILTERS --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Warehouse Filter --}}
                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Warehouse</label>
                            <select name="warehouse_id" id="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- End Date --}}
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- KEY METRICS CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Total Products --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <i class="fas fa-box text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Products</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_products']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Stock Value --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <i class="fas fa-dollar-sign text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Stock Value</p>
                                <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($metrics['total_stock_value'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total SKUs --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <i class="fas fa-barcode text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Active SKUs</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_skus']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Stock Quantity --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <i class="fas fa-cubes text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Stock Qty</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_stock_qty']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Inbound Today --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <i class="fas fa-arrow-down text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Inbound Today</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $metrics['inbound_today'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Outbound Today --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <i class="fas fa-arrow-up text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Outbound Today</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $metrics['outbound_today'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pending Pickings --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                                <i class="fas fa-hand-paper text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Pending Pickings</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $metrics['pending_pickings'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pending Deliveries --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-teal-500 rounded-md p-3">
                                <i class="fas fa-truck text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Pending Deliveries</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $metrics['pending_deliveries'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ALERTS SECTION --}}
            @if(count($alerts) > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-bell"></i> Alerts & Notifications
                    </h3>
                    <div class="space-y-3">
                        @foreach($alerts as $alert)
                        <div class="border-l-4 border-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-500 bg-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-50 p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-{{ $alert['icon'] }} text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-800">
                                        {{ $alert['title'] }}
                                    </p>
                                    <p class="text-sm text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-700">
                                        {{ $alert['message'] }}
                                    </p>
                                </div>
                                <div class="ml-auto">
                                    <a href="{{ $alert['link'] }}" class="text-sm font-medium text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-600 hover:text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-500">
                                        View Details →
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-warehouse"></i> Inventory Summary
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Available</span>
                                <span class="font-semibold text-green-600">{{ number_format($inventorySummary['available']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Reserved</span>
                                <span class="font-semibold text-blue-600">{{ number_format($inventorySummary['reserved']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Quarantine</span>
                                <span class="font-semibold text-yellow-600">{{ number_format($inventorySummary['quarantine']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Damaged</span>
                                <span class="font-semibold text-orange-600">{{ number_format($inventorySummary['damaged']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Expired</span>
                                <span class="font-semibold text-red-600">{{ number_format($inventorySummary['expired']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Today's Activities --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-chart-line"></i> Today's Activities
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600"><i class="fas fa-download text-blue-500"></i> Goods Received</span>
                                <span class="font-semibold">{{ $todayActivities['goods_received'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600"><i class="fas fa-box-open text-purple-500"></i> Putaways Completed</span>
                                <span class="font-semibold">{{ $todayActivities['putaways_completed'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600"><i class="fas fa-hand-paper text-orange-500"></i> Orders Picked</span>
                                <span class="font-semibold">{{ $todayActivities['orders_picked'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600"><i class="fas fa-box text-indigo-500"></i> Orders Packed</span>
                                <span class="font-semibold">{{ $todayActivities['orders_packed'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600"><i class="fas fa-shipping-fast text-green-500"></i> Orders Shipped</span>
                                <span class="font-semibold">{{ $todayActivities['orders_shipped'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600"><i class="fas fa-sync text-teal-500"></i> Replenishments</span>
                                <span class="font-semibold">{{ $todayActivities['replenishments'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PENDING TASKS --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-tasks"></i> Pending Tasks
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        <div class="text-center">
                            <div class="bg-purple-100 rounded-lg p-4">
                                <i class="fas fa-dolly text-3xl text-purple-600 mb-2"></i>
                                <p class="text-2xl font-bold text-purple-600">{{ $pendingTasks['putaway'] }}</p>
                                <p class="text-sm text-gray-600">Putaway</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-orange-100 rounded-lg p-4">
                                <i class="fas fa-hand-paper text-3xl text-orange-600 mb-2"></i>
                                <p class="text-2xl font-bold text-orange-600">{{ $pendingTasks['picking'] }}</p>
                                <p class="text-sm text-gray-600">Picking</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-blue-100 rounded-lg p-4">
                                <i class="fas fa-box text-3xl text-blue-600 mb-2"></i>
                                <p class="text-2xl font-bold text-blue-600">{{ $pendingTasks['packing'] }}</p>
                                <p class="text-sm text-gray-600">Packing</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-teal-100 rounded-lg p-4">
                                <i class="fas fa-sync-alt text-3xl text-teal-600 mb-2"></i>
                                <p class="text-2xl font-bold text-teal-600">{{ $pendingTasks['replenishment'] }}</p>
                                <p class="text-sm text-gray-600">Replenishment</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-yellow-100 rounded-lg p-4">
                                <i class="fas fa-check-circle text-3xl text-yellow-600 mb-2"></i>
                                <p class="text-2xl font-bold text-yellow-600">{{ $pendingTasks['quality_check'] }}</p>
                                <p class="text-sm text-gray-600">Quality Check</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-red-100 rounded-lg p-4">
                                <i class="fas fa-edit text-3xl text-red-600 mb-2"></i>
                                <p class="text-2xl font-bold text-red-600">{{ $pendingTasks['stock_adjustment'] }}</p>
                                <p class="text-sm text-gray-600">Adjustments</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CHARTS SECTION --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Inbound vs Outbound Chart --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-chart-bar"></i> Inbound vs Outbound (Last 7 Days)
                        </h3>
                        <canvas id="inboundOutboundChart" height="250"></canvas>
                    </div>
                </div>

                {{-- Order Status Distribution --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-chart-pie"></i> Order Status Distribution
                        </h3>
                        <canvas id="orderStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            {{-- Stock by Category Chart --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-chart-area"></i> Stock by Category (Top 5)
                    </h3>
                    <canvas id="stockByCategoryChart" height="100"></canvas>
                </div>
            </div>

            {{-- RECENT ORDERS & TOP PRODUCTS --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Recent Sales Orders --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-shopping-cart"></i> Recent Sales Orders
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SO Number</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recentOrders['sales_orders'] as $order)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">{{ $order->so_number }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $order->customer_name }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->status === 'completed' ? 'green' : 'yellow' }}-100 text-{{ $order->status === 'completed' ? 'green' : 'yellow' }}-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-sm text-center text-gray-500">No recent orders</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Top Products --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-star"></i> Top Moving Products (Last 7 Days)
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Moved</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($topProducts as $product)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">{{ $product->sku }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $product->name }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold">{{ number_format($product->total_moved) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-sm text-center text-gray-500">No movement data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WAREHOUSE UTILIZATION --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-warehouse"></i> Warehouse Utilization
                    </h3>
                    @if($selectedWarehouseId && isset($warehouseUtilization['warehouse_name']))
                        <div class="mb-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">{{ $warehouseUtilization['warehouse_name'] }}</span>
                                <span class="font-semibold">{{ $warehouseUtilization['utilization_percent'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $warehouseUtilization['utilization_percent'] }}%"></div>
                            </div>
                            <div class="mt-2 grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Total Bins:</span>
                                    <span class="font-semibold">{{ number_format($warehouseUtilization['total_bins']) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Occupied:</span>
                                    <span class="font-semibold text-blue-600">{{ number_format($warehouseUtilization['occupied_bins']) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Available:</span>
                                    <span class="font-semibold text-green-600">{{ number_format($warehouseUtilization['available_bins']) }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($warehouseUtilization as $wh)
                                @php
                                    $utilization = $wh->total_bins > 0 ? round(($wh->occupied_bins / $wh->total_bins) * 100, 2) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm text-gray-600">{{ $wh->name }}</span>
                                        <span class="text-sm font-semibold">{{ $utilization }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $utilization }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- RECENT ACTIVITIES LOG --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-history"></i> Recent Activities
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentActivities as $activity)
                                <tr>
                                    <td class="px-4 py-3 text-sm whitespace-nowrap">{{ \Carbon\Carbon::parse($activity->created_at)->format('H:i:s') }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $activity->user_name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $activity->log_name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $activity->description }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm text-center text-gray-500">No recent activities</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

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
                        tension: 0.4
                    },
                    {
                        label: 'Outbound',
                        data: @json($charts['inbound_outbound']['outbound']),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
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

            // Show loading indicator
            Swal.fire({
                title: 'Refreshing...',
                text: 'Please wait while we update the dashboard',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX request
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
</x-app-layout>