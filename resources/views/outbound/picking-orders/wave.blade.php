{{-- resources/views/outbound/picking-orders/wave.blade.php --}}

@extends('layouts.app')

@section('title', 'Wave Picking Management')

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
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent flex items-center">
                            <i class="fas fa-wave-square text-purple-600 mr-3"></i>
                            Wave Picking Management
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">Organize and optimize picking operations in waves</p>
                    </div>
                </div>
                <button onclick="scrollToWaveForm()" 
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all font-bold shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-down mr-2"></i>Go to Wave Form
                </button>
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

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 hover:shadow-2xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Pending Orders</p>
                        <p class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                            {{ $pendingOrders->count() }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-wave-square text-2xl text-white"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 hover:shadow-2xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Total Items</p>
                        <p class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                            {{ $pendingOrders->sum('total_items') }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-boxes text-2xl text-white"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 hover:shadow-2xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Total Quantity</p>
                        <p class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                            {{ number_format($pendingOrders->sum('total_quantity')) }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-cubes text-2xl text-white"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 hover:shadow-2xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Urgent Orders</p>
                        <p class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                            {{ $pendingOrders->where('priority', 'urgent')->count() }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-exclamation-triangle text-2xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Instructions --}}
        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-xl p-6 shadow-md">
            <div class="flex items-start">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                    <i class="fas fa-info-circle text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-blue-900 mb-3">How to Create a Wave</h3>
                    <ol class="space-y-2 text-sm text-blue-800">
                        <li class="flex items-start">
                            <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center mr-3 flex-shrink-0 text-xs font-bold">1</span>
                            <span>Select orders from the list below by clicking on them or using checkboxes</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center mr-3 flex-shrink-0 text-xs font-bold">2</span>
                            <span>Review the wave summary on the right panel</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center mr-3 flex-shrink-0 text-xs font-bold">3</span>
                            <span>Fill in the wave configuration (warehouse, date, optional: picker assignment)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center mr-3 flex-shrink-0 text-xs font-bold">4</span>
                            <span>Click "Create Wave" to batch process selected orders</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Available Orders for Wave Picking --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-clipboard-list text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Available Orders</h2>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="selectAll()" 
                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg hover:from-blue-600 hover:to-cyan-600 transition font-semibold text-sm shadow-md">
                                <i class="fas fa-check-double mr-1"></i>Select All
                            </button>
                            <button onclick="clearSelection()" 
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold text-sm">
                                <i class="fas fa-times mr-1"></i>Clear
                            </button>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <div class="relative">
                            <select id="warehouseFilter" 
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all text-sm" 
                                    onchange="filterOrders()">
                                <option value="">All Warehouses</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-warehouse absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <div class="relative">
                            <select id="priorityFilter" 
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all text-sm" 
                                    onchange="filterOrders()">
                                <option value="">All Priorities</option>
                                <option value="urgent">Urgent</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                            <i class="fas fa-flag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <button onclick="sortOrders()" 
                                class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition text-sm font-semibold">
                            <i class="fas fa-sort mr-2"></i>Sort by Priority
                        </button>
                    </div>

                    {{-- Orders List --}}
                    <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2" id="ordersList">
                        @forelse($pendingOrders as $order)
                            <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-purple-400 hover:shadow-lg transition-all cursor-pointer order-item bg-gradient-to-r from-white to-gray-50" 
                                 data-order-id="{{ $order->id }}"
                                 data-warehouse="{{ $order->warehouse_id }}"
                                 data-priority="{{ $order->priority }}"
                                 onclick="toggleOrder({{ $order->id }})">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-4 mt-1">
                                        <input type="checkbox" 
                                               class="w-5 h-5 text-purple-600 border-2 border-gray-300 rounded focus:ring-purple-500 order-checkbox" 
                                               id="order_{{ $order->id }}"
                                               value="{{ $order->id }}"
                                               onclick="event.stopPropagation()">
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <h4 class="text-lg font-bold text-gray-900">{{ $order->picking_number }}</h4>
                                                <p class="text-xs text-gray-600 mt-0.5">SO: {{ $order->salesOrder->so_number }}</p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                @php
                                                    $priorityClasses = [
                                                        'urgent' => 'bg-red-100 text-red-800 border-red-200',
                                                        'high' => 'bg-orange-100 text-orange-800 border-orange-200',
                                                        'medium' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                        'low' => 'bg-green-100 text-green-800 border-green-200',
                                                    ];
                                                    $statusClasses = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                    ];
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-bold border-2 {{ $priorityClasses[$order->priority] ?? '' }}">
                                                    {{ ucfirst($order->priority) }}
                                                </span>
                                                <span class="px-3 py-1 rounded-full text-xs font-bold border-2 {{ $statusClasses[$order->status] ?? '' }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-4 text-xs">
                                            <div>
                                                <span class="text-gray-500 font-semibold">Customer:</span>
                                                <p class="font-bold text-gray-900 mt-1">{{ $order->salesOrder->customer->name ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 font-semibold">Warehouse:</span>
                                                <p class="font-bold text-gray-900 mt-1">{{ $order->warehouse->name }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 font-semibold">Items:</span>
                                                <p class="font-bold text-gray-900 mt-1">{{ $order->total_items }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 font-semibold">Quantity:</span>
                                                <p class="font-bold text-gray-900 mt-1">{{ number_format($order->total_quantity) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-inbox text-5xl text-gray-400"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">No Pending Wave Orders</h3>
                                <p class="text-gray-600">All orders have been processed or assigned to waves</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Wave Creation Panel --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border-2 border-purple-200 p-6 sticky top-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-magic text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Wave Config</h2>
                    </div>

                    <form action="{{ route('outbound.picking-orders.batch-generate') }}" method="POST" id="waveForm">
                        @csrf
                        <div id="hiddenInputsContainer"></div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Selected Orders
                            </label>
                            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-2 border-purple-300 rounded-xl">
                                <div class="text-center">
                                    <span class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent" id="selectedCount">0</span>
                                    <p class="text-xs text-gray-600 mt-1 font-semibold">orders selected</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-tag text-purple-600 mr-1"></i>
                                Wave Name
                            </label>
                            <input type="text" 
                                   name="wave_name"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all" 
                                   placeholder="e.g., Morning Wave"
                                   id="waveName">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-warehouse text-purple-600 mr-1"></i>
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" 
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all" 
                                    required>
                                <option value="">Select Warehouse...</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt text-purple-600 mr-1"></i>
                                Picking Date <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" 
                                   name="picking_date" 
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all" 
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-user text-purple-600 mr-1"></i>
                                Assign To (Optional)
                            </label>
                            <select name="assigned_to" 
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Summary --}}
                        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                            <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                                Wave Summary
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-semibold">Total Orders:</span>
                                    <span class="font-bold text-gray-900 text-lg" id="summaryOrders">0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-semibold">Total Items:</span>
                                    <span class="font-bold text-gray-900 text-lg" id="summaryItems">0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-semibold">Total Quantity:</span>
                                    <span class="font-bold text-gray-900 text-lg" id="summaryQuantity">0</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all font-bold shadow-lg hover:shadow-xl disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed disabled:shadow-none" 
                                id="createWaveBtn"
                                disabled>
                            <i class="fas fa-magic mr-2"></i>Create Wave
                        </button>

                        <div class="mt-6 bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-xl p-4">
                            <h4 class="text-sm font-bold text-yellow-900 mb-2 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>Wave Picking Tips
                            </h4>
                            <ul class="text-xs text-yellow-800 space-y-1.5">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                                    <span>Group orders by warehouse</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                                    <span>Prioritize urgent orders</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                                    <span>Balance workload per wave</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                                    <span>Consider pick path optimization</span>
                                </li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
let selectedOrders = new Set();
let ordersData = @json($pendingOrders);

// Scroll to wave form
function scrollToWaveForm() {
    const waveForm = document.getElementById('waveForm');
    waveForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Highlight the form briefly
    const panel = waveForm.closest('.lg\\:col-span-1').querySelector('.bg-white');
    panel.classList.add('ring-4', 'ring-purple-400');
    setTimeout(() => {
        panel.classList.remove('ring-4', 'ring-purple-400');
    }, 2000);
}

function toggleOrder(orderId) {
    const checkbox = document.getElementById(`order_${orderId}`);
    checkbox.checked = !checkbox.checked;
    updateSelection(orderId, checkbox.checked);
}

function updateSelection(orderId, isChecked) {
    if (isChecked) {
        selectedOrders.add(orderId);
    } else {
        selectedOrders.delete(orderId);
    }
    updateSummary();
}

// Listen to checkbox changes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('order-checkbox')) {
        updateSelection(parseInt(e.target.value), e.target.checked);
    }
});

function updateSummary() {
    const count = selectedOrders.size;
    document.getElementById('selectedCount').textContent = count;
    
    // Calculate totals
    let totalItems = 0;
    let totalQuantity = 0;
    
    selectedOrders.forEach(orderId => {
        const order = ordersData.find(o => o.id === orderId);
        if (order) {
            totalItems += order.total_items;
            totalQuantity += parseFloat(order.total_quantity) || 0;
        }
    });
    
    document.getElementById('summaryOrders').textContent = count;
    document.getElementById('summaryItems').textContent = totalItems;
    document.getElementById('summaryQuantity').textContent = totalQuantity.toLocaleString();
    
    // Enable/disable submit button
    const submitBtn = document.getElementById('createWaveBtn');
    submitBtn.disabled = count === 0;
    
    // Update hidden input
    updateHiddenInput();
}

function updateHiddenInput() {
    const container = document.getElementById('hiddenInputsContainer');
    // Clear existing inputs
    container.innerHTML = '';
    
    // Add new hidden inputs for each selected picking order
    selectedOrders.forEach(orderId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'picking_order_ids[]';
        input.value = orderId;
        container.appendChild(input);
    });
}

function selectAll() {
    const visibleCheckboxes = Array.from(document.querySelectorAll('.order-item'))
        .filter(item => item.style.display !== 'none')
        .map(item => item.querySelector('.order-checkbox'));
    
    visibleCheckboxes.forEach(checkbox => {
        if (!checkbox.checked) {
            checkbox.checked = true;
            updateSelection(parseInt(checkbox.value), true);
        }
    });
}

function clearSelection() {
    document.querySelectorAll('.order-checkbox:checked').forEach(checkbox => {
        checkbox.checked = false;
    });
    selectedOrders.clear();
    updateSummary();
}

function filterOrders() {
    const warehouseId = document.getElementById('warehouseFilter').value;
    const priority = document.getElementById('priorityFilter').value;
    
    document.querySelectorAll('.order-item').forEach(item => {
        const itemWarehouse = item.dataset.warehouse;
        const itemPriority = item.dataset.priority;
        
        let show = true;
        if (warehouseId && itemWarehouse !== warehouseId) show = false;
        if (priority && itemPriority !== priority) show = false;
        
        item.style.display = show ? 'block' : 'none';
    });
}

function sortOrders() {
    const container = document.getElementById('ordersList');
    const items = Array.from(container.querySelectorAll('.order-item'));
    
    const priorityOrder = { 'urgent': 0, 'high': 1, 'medium': 2, 'low': 3 };
    
    items.sort((a, b) => {
        return priorityOrder[a.dataset.priority] - priorityOrder[b.dataset.priority];
    });
    
    items.forEach(item => container.appendChild(item));
}

// Validate form before submit
document.getElementById('waveForm').addEventListener('submit', function(e) {
    console.log('Form submitted');
    console.log('Selected orders:', Array.from(selectedOrders));
    
    if (selectedOrders.size === 0) {
        e.preventDefault();
        alert('Please select at least one order to create a wave');
        return false;
    }
    
    // Validate warehouse
    const warehouse = document.querySelector('select[name="warehouse_id"]').value;
    if (!warehouse) {
        e.preventDefault();
        alert('Please select a warehouse');
        return false;
    }
    
    // Validate picking date
    const pickingDate = document.querySelector('input[name="picking_date"]').value;
    if (!pickingDate) {
        e.preventDefault();
        alert('Please select a picking date');
        return false;
    }
    
    if (!confirm(`Create wave with ${selectedOrders.size} order(s)?`)) {
        e.preventDefault();
        return false;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('createWaveBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Wave...';
    
    console.log('Form validation passed, submitting...');
});

// Show helper message when no orders selected
function showSelectionHint() {
    if (selectedOrders.size === 0) {
        const hint = document.createElement('div');
        hint.className = 'fixed bottom-8 right-8 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-4 rounded-xl shadow-2xl z-50 animate-bounce';
        hint.innerHTML = '<i class="fas fa-hand-point-left mr-2"></i>Select orders from the left to create a wave';
        document.body.appendChild(hint);
        
        setTimeout(() => {
            hint.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => hint.remove(), 500);
        }, 3000);
    }
}

// Add click handler to submit button when disabled
document.getElementById('createWaveBtn').addEventListener('click', function(e) {
    if (this.disabled && selectedOrders.size === 0) {
        showSelectionHint();
    }
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

/* Custom Scrollbar */
#ordersList::-webkit-scrollbar {
    width: 8px;
}

#ordersList::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#ordersList::-webkit-scrollbar-thumb {
    background: #a855f7;
    border-radius: 10px;
}

#ordersList::-webkit-scrollbar-thumb:hover {
    background: #9333ea;
}
</style>
@endsection