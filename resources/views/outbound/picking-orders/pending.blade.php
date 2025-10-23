{{-- resources/views/outbound/picking-orders/pending.blade.php --}}

@extends('layouts.app')

@section('title', 'Pending Picking Orders')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-clock text-yellow-600 mr-2"></i>
                Pending Picking Orders
            </h1>
            <p class="text-sm text-gray-600 mt-1">Orders awaiting assignment and picking</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('outbound.picking-orders.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-list mr-2"></i>All Orders
            </a>
            <a href="{{ route('outbound.picking-orders.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-plus mr-2"></i>New Picking Order
            </a>
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

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl shadow-sm border border-yellow-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600 font-medium">Pending Orders</p>
                    <p class="text-3xl font-bold text-yellow-900 mt-2">{{ $pickingOrders->total() }}</p>
                </div>
                <div class="w-14 h-14 bg-yellow-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-yellow-700"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-sm border border-red-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-600 font-medium">Urgent Priority</p>
                    <p class="text-3xl font-bold text-red-900 mt-2">{{ $pickingOrders->where('priority', 'urgent')->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-red-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-700"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Total Items</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $pickingOrders->sum('total_items') }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-boxes text-2xl text-blue-700"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm border border-purple-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 font-medium">Total Quantity</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2">{{ number_format($pickingOrders->sum('total_quantity')) }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-cubes text-2xl text-purple-700"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-gray-700">Quick Filter:</span>
            <a href="{{ route('outbound.picking-orders.pending') }}" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                All
            </a>
            <a href="{{ route('outbound.picking-orders.pending', ['priority' => 'urgent']) }}" class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm">
                Urgent
            </a>
            <a href="{{ route('outbound.picking-orders.pending', ['priority' => 'high']) }}" class="px-3 py-1 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition text-sm">
                High
            </a>
            <a href="{{ route('outbound.picking-orders.pending', ['warehouse_id' => request('warehouse_id')]) }}" class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition text-sm">
                By Warehouse
            </a>
        </div>
    </div>

    {{-- Pending Orders List --}}
    <div class="space-y-4">
        @forelse($pickingOrders as $order)
            <div class="bg-white rounded-xl shadow-sm border-2 {{ $order->priority === 'urgent' ? 'border-red-300' : 'border-gray-200' }} p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-start flex-grow">
                        {{-- Priority Badge --}}
                        <div class="flex-shrink-0 mr-4">
                            @php
                                $priorityColors = [
                                    'urgent' => 'bg-red-500',
                                    'high' => 'bg-orange-500',
                                    'medium' => 'bg-blue-500',
                                    'low' => 'bg-gray-500'
                                ];
                                $color = $priorityColors[$order->priority] ?? 'bg-gray-500';
                            @endphp
                            <div class="w-16 h-16 {{ $color }} rounded-lg flex items-center justify-center text-white">
                                <div class="text-center">
                                    <i class="fas fa-flag text-2xl mb-1"></i>
                                    <div class="text-xs font-bold uppercase">{{ $order->priority }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Order Details --}}
                        <div class="flex-grow">
                            <div class="flex items-center mb-2">
                                <h3 class="text-xl font-bold text-gray-900 mr-3">{{ $order->picking_number }}</h3>
                                {!! $order->status_badge !!}
                            </div>

                            <div class="grid grid-cols-4 gap-4 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500">Sales Order</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $order->salesOrder->so_number }}</p>
                                    <p class="text-xs text-gray-600">{{ $order->salesOrder->customer->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Warehouse</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $order->warehouse->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Picking Date</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $order->picking_date->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-600">{{ $order->picking_date->format('H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Items / Quantity</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $order->total_items }} items</p>
                                    <p class="text-xs text-gray-600">{{ number_format($order->total_quantity) }} qty</p>
                                </div>
                            </div>

                            {{-- Assigned User --}}
                            @if($order->assignedUser)
                                <div class="flex items-center mt-3 p-2 bg-blue-50 rounded-lg">
                                    <i class="fas fa-user-check text-blue-600 mr-2"></i>
                                    <span class="text-sm text-blue-900">
                                        Assigned to <strong>{{ $order->assignedUser->name }}</strong>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col space-y-2 ml-4">
                        @if(!$order->assignedUser)
                            <button onclick="openAssignModal({{ $order->id }}, '{{ $order->picking_number }}')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm whitespace-nowrap">
                                <i class="fas fa-user-plus mr-2"></i>Assign Picker
                            </button>
                        @endif

                        <a href="{{ route('outbound.picking-orders.show', $order) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm text-center whitespace-nowrap">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </a>

                        <a href="{{ route('outbound.picking-orders.edit', $order) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm text-center whitespace-nowrap">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>

                        <form action="{{ route('outbound.picking-orders.start', $order) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm whitespace-nowrap">
                                <i class="fas fa-play mr-2"></i>Start Picking
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Pending Orders</h3>
                <p class="text-gray-600 mb-4">All picking orders have been processed!</p>
                <a href="{{ route('outbound.picking-orders.index') }}" class="inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-list mr-2"></i>View All Orders
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($pickingOrders->hasPages())
        <div class="mt-6">
            {{ $pickingOrders->links() }}
        </div>
    @endif

</div>

{{-- Assign Modal --}}
<div id="assignModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Assign Picker</h3>
            <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="assignForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Picking Order: <span id="modalPickingNumber" class="font-bold text-green-600"></span>
                </label>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Picker</label>
                <select name="assigned_to" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">Choose a picker...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeAssignModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-user-check mr-2"></i>Assign
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAssignModal(orderId, pickingNumber) {
    document.getElementById('assignModal').classList.remove('hidden');
    document.getElementById('modalPickingNumber').textContent = pickingNumber;
    document.getElementById('assignForm').action = `/outbound/picking-orders/${orderId}/assign`;
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
}
</script>

@endsection