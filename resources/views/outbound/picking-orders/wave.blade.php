{{-- resources/views/outbound/picking-orders/wave.blade.php --}}

@extends('layouts.app')

@section('title', 'Wave Picking Management')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-wave-square text-purple-600 mr-2"></i>
                Wave Picking Management
            </h1>
            <p class="text-sm text-gray-600 mt-1">Organize and optimize picking operations in waves</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('outbound.picking-orders.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-list mr-2"></i>All Orders
            </a>
            <button onclick="scrollToWaveForm()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-arrow-down mr-2"></i>Go to Wave Form
            </button>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm border border-purple-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 font-medium">Pending Wave Orders</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2">{{ $pendingOrders->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-wave-square text-2xl text-purple-700"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Total Items</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $pendingOrders->sum('total_items') }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-boxes text-2xl text-blue-700"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm border border-green-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium">Total Quantity</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ number_format($pendingOrders->sum('total_quantity')) }}</p>
                </div>
                <div class="w-14 h-14 bg-green-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-cubes text-2xl text-green-700"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-sm border border-orange-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-orange-600 font-medium">Urgent Orders</p>
                    <p class="text-3xl font-bold text-orange-900 mt-2">{{ $pendingOrders->where('priority', 'urgent')->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-orange-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-2xl text-orange-700"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Instructions --}}
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-blue-800">How to Create a Wave:</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Select orders from the list below by clicking on them or using checkboxes</li>
                        <li>Review the wave summary on the right panel</li>
                        <li>Fill in the wave configuration (warehouse, date, optional: picker assignment)</li>
                        <li>Click "Create Wave" to batch process selected orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Available Orders for Wave Picking --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                        Available Orders for Wave Picking
                    </h2>
                    <div class="flex space-x-2">
                        <button onclick="selectAll()" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm">
                            <i class="fas fa-check-double mr-1"></i>Select All
                        </button>
                        <button onclick="clearSelection()" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                            <i class="fas fa-times mr-1"></i>Clear
                        </button>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <select id="warehouseFilter" class="rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm" onchange="filterOrders()">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                    <select id="priorityFilter" class="rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm" onchange="filterOrders()">
                        <option value="">All Priorities</option>
                        <option value="urgent">Urgent</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                    <button onclick="sortOrders()" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                        <i class="fas fa-sort mr-1"></i>Sort by Priority
                    </button>
                </div>

                {{-- Orders List --}}
                <div class="space-y-3 max-h-[600px] overflow-y-auto" id="ordersList">
                    @forelse($pendingOrders as $order)
                        <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-purple-300 transition cursor-pointer order-item" 
                             data-order-id="{{ $order->id }}"
                             data-warehouse="{{ $order->warehouse_id }}"
                             data-priority="{{ $order->priority }}"
                             onclick="toggleOrder({{ $order->id }})">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <input type="checkbox" 
                                           class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 order-checkbox" 
                                           id="order_{{ $order->id }}"
                                           value="{{ $order->id }}"
                                           onclick="event.stopPropagation()">
                                </div>
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <h4 class="text-base font-bold text-gray-900">{{ $order->picking_number }}</h4>
                                            <p class="text-xs text-gray-600">SO: {{ $order->salesOrder->so_number }}</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            {!! $order->priority_badge !!}
                                            {!! $order->status_badge !!}
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-3 text-xs">
                                        <div>
                                            <span class="text-gray-500">Customer:</span>
                                            <p class="font-semibold text-gray-900">{{ $order->salesOrder->customer->name ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Warehouse:</span>
                                            <p class="font-semibold text-gray-900">{{ $order->warehouse->name }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Items:</span>
                                            <p class="font-semibold text-gray-900">{{ $order->total_items }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Quantity:</span>
                                            <p class="font-semibold text-gray-900">{{ number_format($order->total_quantity) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-inbox text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Pending Wave Orders</h3>
                            <p class="text-gray-600">All orders have been processed or assigned to waves</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Wave Creation Panel --}}
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl shadow-sm border-2 border-purple-200 p-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-magic text-purple-600 mr-2"></i>
                    Wave Configuration
                </h2>

                <form action="{{ route('outbound.picking-orders.batch-generate') }}" method="POST" id="waveForm">
                    @csrf
                    <div id="hiddenInputsContainer"></div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Selected Orders
                        </label>
                        <div class="px-4 py-3 bg-white border-2 border-purple-300 rounded-lg">
                            <div class="text-center">
                                <span class="text-3xl font-bold text-purple-600" id="selectedCount">0</span>
                                <p class="text-xs text-gray-600 mt-1">orders selected</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Wave Name
                        </label>
                        <input type="text" 
                               name="wave_name"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" 
                               placeholder="e.g., Morning Wave"
                               id="waveName">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Warehouse <span class="text-red-500">*</span>
                        </label>
                        <select name="warehouse_id" 
                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" 
                                required>
                            <option value="">Select Warehouse...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Picking Date <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="picking_date" 
                               value="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" 
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Assign To (Optional)
                        </label>
                        <select name="assigned_to" 
                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Unassigned</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Summary --}}
                    <div class="mb-4 p-4 bg-white rounded-lg border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Wave Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Orders:</span>
                                <span class="font-semibold text-gray-900" id="summaryOrders">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Items:</span>
                                <span class="font-semibold text-gray-900" id="summaryItems">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Quantity:</span>
                                <span class="font-semibold text-gray-900" id="summaryQuantity">0</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed" 
                            id="createWaveBtn"
                            disabled>
                        <i class="fas fa-magic mr-2"></i>Create Wave
                    </button>

                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="text-xs font-semibold text-yellow-900 mb-1">
                            <i class="fas fa-lightbulb mr-1"></i>Wave Picking Tips
                        </h4>
                        <ul class="text-xs text-yellow-800 space-y-1">
                            <li>• Group orders by warehouse</li>
                            <li>• Prioritize urgent orders</li>
                            <li>• Balance workload per wave</li>
                            <li>• Consider pick path optimization</li>
                        </ul>
                    </div>
                </form>
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
    waveForm.parentElement.classList.add('ring-4', 'ring-purple-400');
    setTimeout(() => {
        waveForm.parentElement.classList.remove('ring-4', 'ring-purple-400');
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
            totalQuantity += order.total_quantity;
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
    document.querySelectorAll('.order-checkbox:not(:checked)').forEach(checkbox => {
        checkbox.checked = true;
        updateSelection(parseInt(checkbox.value), true);
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
    console.log('Form data:', new FormData(this));
    
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
    
    if (!confirm(`Create wave with ${selectedOrders.size} orders?`)) {
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
        hint.className = 'fixed bottom-4 right-4 bg-purple-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce';
        hint.innerHTML = '<i class="fas fa-hand-point-left mr-2"></i>Select orders from the left to create a wave';
        document.body.appendChild(hint);
        
        setTimeout(() => {
            hint.remove();
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

@endsection