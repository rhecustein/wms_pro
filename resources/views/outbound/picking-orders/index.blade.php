{{-- resources/views/outbound/picking-orders/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Picking Orders')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="container-fluid px-4 py-6 max-w-7xl mx-auto">

        {{-- Modern Header with Gradient --}}
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-box-open text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                                Picking Orders
                            </h1>
                            <p class="text-sm text-gray-600 mt-1">Manage warehouse picking operations efficiently</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('outbound.picking-orders.pending') }}" 
                       class="inline-flex items-center px-4 py-2.5 bg-white border-2 border-amber-500 text-amber-700 rounded-xl hover:bg-amber-50 transition-all duration-200 font-semibold shadow-sm hover:shadow-md">
                        <i class="fas fa-clock mr-2"></i>
                        <span>Pending Orders</span>
                    </a>
                    <a href="{{ route('outbound.picking-orders.wave') }}" 
                       class="inline-flex items-center px-4 py-2.5 bg-white border-2 border-purple-500 text-purple-700 rounded-xl hover:bg-purple-50 transition-all duration-200 font-semibold shadow-sm hover:shadow-md">
                        <i class="fas fa-wave-square mr-2"></i>
                        <span>Wave Picking</span>
                    </a>
                    <a href="{{ route('outbound.picking-orders.create') }}" 
                       class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus mr-2"></i>
                        <span>New Picking Order</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-xl p-4 shadow-sm animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <span class="text-green-800 font-medium">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.closest('div.mb-6').remove()" class="text-green-600 hover:text-green-800 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl p-4 shadow-sm animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-circle text-white"></i>
                        </div>
                        <span class="text-red-800 font-medium">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.closest('div.mb-6').remove()" class="text-red-600 hover:text-red-800 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 bg-gradient-to-r from-yellow-50 to-amber-50 border-l-4 border-yellow-500 rounded-xl p-4 shadow-sm animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        <span class="text-yellow-800 font-medium">{{ session('warning') }}</span>
                    </div>
                    <button onclick="this.closest('div.mb-6').remove()" class="text-yellow-600 hover:text-yellow-800 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- Modern Filters Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    Filters
                </h3>
                <button type="button" onclick="toggleFilters()" 
                        class="text-gray-500 hover:text-gray-700 lg:hidden">
                    <i class="fas fa-chevron-down" id="filterIcon"></i>
                </button>
            </div>
            
            <form method="GET" action="{{ route('outbound.picking-orders.index') }}" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    {{-- Search --}}
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Search</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Picking Number, SO Number..." 
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Status</label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Priority Filter --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Priority</label>
                        <select name="priority" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                            <option value="">All Priorities</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Picking Type Filter --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Type</label>
                        <select name="picking_type" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                            <option value="">All Types</option>
                            @foreach($pickingTypes as $type)
                                <option value="{{ $type }}" {{ request('picking_type') === $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all font-semibold shadow-md hover:shadow-lg">
                            <i class="fas fa-filter mr-2"></i>Apply
                        </button>
                        <a href="{{ route('outbound.picking-orders.index') }}" 
                           class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>

                {{-- Additional Filters --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Warehouse</label>
                        <select name="warehouse_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                            <option value="">All Warehouses</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                    </div>
                </div>
            </form>
        </div>

        {{-- Modern Table Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-slate-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Picking Number</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sales Order</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Warehouse</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Assigned</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pickingOrders as $order)
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3 shadow">
                                            <i class="fas fa-barcode text-white text-sm"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900 font-mono">{{ $order->picking_number }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $order->picking_date->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $order->picking_date->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $order->salesOrder->so_number ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->salesOrder->customer->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-9 h-9 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-2 shadow">
                                            <i class="fas fa-warehouse text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $order->warehouse->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->warehouse->code ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">
                                        {{ ucfirst(str_replace('_', ' ', $order->picking_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $order->priority_badge !!}
                                </td>
                                <td class="px-6 py-4">
                                    @if($order->assignedUser)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center mr-2 shadow">
                                                <i class="fas fa-user text-white text-xs"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $order->assignedUser->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->assigned_at?->format('d M, H:i') }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-bold">{{ $order->total_items }}</span> items
                                    </div>
                                    <div class="text-xs text-gray-500">{{ number_format($order->total_quantity) }} qty</div>
                                    @if($order->status === 'in_progress' && method_exists($order, 'getProgressPercentageAttribute'))
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                            <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-1.5 rounded-full transition-all" 
                                                 style="width: {{ $order->progress_percentage }}%"></div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $order->status_badge !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                        <a href="{{ route('outbound.picking-orders.show', $order) }}" 
                                           class="w-8 h-8 inline-flex items-center justify-center text-blue-600 hover:bg-blue-100 rounded-lg transition" 
                                           title="View Details">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        
                                        @if(in_array($order->status, ['pending', 'assigned']))
                                            <a href="{{ route('outbound.picking-orders.edit', $order) }}" 
                                               class="w-8 h-8 inline-flex items-center justify-center text-amber-600 hover:bg-amber-100 rounded-lg transition" 
                                               title="Edit">
                                                <i class="fas fa-edit text-sm"></i>
                                            </a>
                                        @endif

                                        @if(in_array($order->status, ['pending', 'assigned']))
                                            <form action="{{ route('outbound.picking-orders.start', $order) }}" method="POST" class="inline" onsubmit="return confirm('Start picking process?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-8 h-8 inline-flex items-center justify-center text-green-600 hover:bg-green-100 rounded-lg transition" 
                                                        title="Start Picking">
                                                    <i class="fas fa-play text-sm"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($order->status === 'in_progress')
                                            <a href="{{ route('outbound.picking-orders.execute', $order) }}" 
                                               class="w-8 h-8 inline-flex items-center justify-center text-purple-600 hover:bg-purple-100 rounded-lg transition" 
                                               title="Execute">
                                                <i class="fas fa-tasks text-sm"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('outbound.picking-orders.print', $order) }}" 
                                           class="w-8 h-8 inline-flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-lg transition" 
                                           title="Print" target="_blank">
                                            <i class="fas fa-print text-sm"></i>
                                        </a>
                                        
                                        @if($order->status === 'pending')
                                            <form action="{{ route('outbound.picking-orders.destroy', $order) }}" method="POST" class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this picking order?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-8 h-8 inline-flex items-center justify-center text-red-600 hover:bg-red-100 rounded-lg transition" 
                                                        title="Delete">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-16">
                                    <div class="flex flex-col items-center">
                                        <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                            <i class="fas fa-box-open text-5xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800 mb-2">No Picking Orders Found</h3>
                                        <p class="text-gray-600 mb-6 text-center max-w-md">Get started by creating your first picking order to manage warehouse operations</p>
                                        <a href="{{ route('outbound.picking-orders.create') }}" 
                                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl">
                                            <i class="fas fa-plus mr-2"></i>Create New Picking Order
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modern Pagination --}}
            @if($pickingOrders->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $pickingOrders->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<script>
// Toggle filters on mobile
function toggleFilters() {
    const filterForm = document.getElementById('filterForm');
    const filterIcon = document.getElementById('filterIcon');
    
    filterForm.classList.toggle('hidden');
    
    if (filterForm.classList.contains('hidden')) {
        filterIcon.classList.remove('fa-chevron-up');
        filterIcon.classList.add('fa-chevron-down');
    } else {
        filterIcon.classList.remove('fa-chevron-down');
        filterIcon.classList.add('fa-chevron-up');
    }
}

// Auto hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.animate-fade-in');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
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
</style>
@endsection