{{-- resources/views/inbound/good-receivings/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Good Receivings')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-box-open text-white"></i>
                    </div>
                    Good Receivings
                </h1>
                <p class="text-gray-600 mt-2">Manage and track incoming goods receiving</p>
            </div>
            <a href="{{ route('inbound.good-receivings.create') }}" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all shadow-lg shadow-green-500/50 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                New Good Receiving
            </a>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl mb-6 shadow-sm animate-fade-in">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 shadow-sm animate-fade-in">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-circle text-white"></i>
                    </div>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        @php
            // Hitung statistik dari data yang ada
            $allGoodReceivings = \App\Models\GoodReceiving::all();
            
            $statusCounts = [
                'total' => $allGoodReceivings->count(),
                'draft' => $allGoodReceivings->where('status', 'draft')->count(),
                'in_progress' => $allGoodReceivings->where('status', 'in_progress')->count(),
                'quality_check' => $allGoodReceivings->where('status', 'quality_check')->count(),
                'completed' => $allGoodReceivings->where('status', 'completed')->count(),
                'total_items' => $allGoodReceivings->sum('total_items'),
            ];
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total GRs</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($statusCounts['total']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/50">
                    <i class="fas fa-box-open text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Draft</p>
                    <p class="text-3xl font-bold text-gray-600 mt-1">{{ number_format($statusCounts['draft']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">In Progress</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($statusCounts['in_progress']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-spinner text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Quality Check</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ number_format($statusCounts['quality_check']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($statusCounts['completed']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Items</p>
                    <p class="text-3xl font-bold text-indigo-600 mt-1">{{ number_format($statusCounts['total_items']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-boxes text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter text-green-600 mr-2"></i>
                Filters
            </h2>
            <button type="button" onclick="document.getElementById('filterForm').classList.toggle('hidden')" class="text-sm text-green-600 hover:text-green-700">
                <i class="fas fa-chevron-down mr-1"></i>Toggle Filters
            </button>
        </div>
        
        <form method="GET" action="{{ route('inbound.good-receivings.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search text-gray-400 mr-1"></i>Search
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="GR Number, Supplier, Notes..." class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag text-gray-400 mr-1"></i>Status
                    </label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Warehouse Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-warehouse text-gray-400 mr-1"></i>Warehouse
                    </label>
                    <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Supplier Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building text-gray-400 mr-1"></i>Supplier
                    </label>
                    <select name="supplier_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Date Range & Quality Status --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>Date From
                    </label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>Date To
                    </label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-check-circle text-gray-400 mr-1"></i>Quality Status
                    </label>
                    <select name="quality_status" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                        <option value="">All Quality</option>
                        @foreach($qualityStatuses as $qStatus)
                            <option value="{{ $qStatus }}" {{ request('quality_status') === $qStatus ? 'selected' : '' }}>
                                {{ ucfirst($qStatus) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('inbound.good-receivings.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all flex items-center justify-center" title="Reset Filters">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Good Receivings Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">GR Info</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Receiving Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Warehouse</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Items/Qty</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Quality</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($goodReceivings as $gr)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-3 shadow-md">
                                        <i class="fas fa-box text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-mono font-bold text-gray-900">{{ $gr->gr_number }}</div>
                                        @if($gr->purchase_order_id)
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                <i class="fas fa-file-invoice text-gray-400 mr-1"></i>
                                                {{ $gr->purchaseOrder->po_number ?? '-' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <i class="fas fa-calendar text-green-500 mr-1"></i>
                                    {{ \Carbon\Carbon::parse($gr->receiving_date)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    <i class="fas fa-clock text-gray-400 mr-1"></i>
                                    {{ \Carbon\Carbon::parse($gr->receiving_date)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-building text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $gr->supplier->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $gr->supplier->code ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-warehouse text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $gr->warehouse->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $gr->warehouse->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 mb-1">
                                    <i class="fas fa-boxes text-gray-400 mr-1"></i>
                                    <span class="font-bold text-green-600">{{ $gr->total_items }}</span>
                                    <span class="text-xs text-gray-500 ml-1">items</span>
                                </div>
                                <div class="text-sm text-gray-900">
                                    <i class="fas fa-cubes text-gray-400 mr-1"></i>
                                    <span class="font-bold text-blue-600">{{ number_format($gr->total_quantity) }}</span>
                                    <span class="text-xs text-gray-500 ml-1">qty</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'gray',
                                        'in_progress' => 'blue',
                                        'quality_check' => 'yellow',
                                        'completed' => 'green',
                                        'partial' => 'orange',
                                        'cancelled' => 'red',
                                    ];
                                    $color = $statusColors[$gr->status] ?? 'gray';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ ucfirst(str_replace('_', ' ', $gr->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $qualityColors = [
                                        'pending' => 'gray',
                                        'passed' => 'green',
                                        'failed' => 'red',
                                        'partial' => 'yellow',
                                    ];
                                    $qColor = $qualityColors[$gr->quality_status] ?? 'gray';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $qColor }}-100 text-{{ $qColor }}-800">
                                    {{ ucfirst($gr->quality_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('inbound.good-receivings.show', $gr) }}" class="w-8 h-8 bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg flex items-center justify-center transition-all" title="View Details">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    
                                    @if(in_array($gr->status, ['draft', 'in_progress']))
                                        <a href="{{ route('inbound.good-receivings.edit', $gr) }}" class="w-8 h-8 bg-yellow-100 text-yellow-600 hover:bg-yellow-600 hover:text-white rounded-lg flex items-center justify-center transition-all" title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('inbound.good-receivings.print', $gr) }}" class="w-8 h-8 bg-gray-100 text-gray-600 hover:bg-gray-600 hover:text-white rounded-lg flex items-center justify-center transition-all" title="Print" target="_blank">
                                        <i class="fas fa-print text-sm"></i>
                                    </a>
                                    
                                    @if($gr->status === 'draft')
                                        <form action="{{ route('inbound.good-receivings.destroy', $gr) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this good receiving?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-lg flex items-center justify-center transition-all" title="Delete">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-4">
                                        <i class="fas fa-box-open text-5xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Good Receivings Found</h3>
                                    <p class="text-gray-600 mb-6 max-w-md">
                                        @if(request()->hasAny(['search', 'status', 'quality_status', 'warehouse_id', 'supplier_id', 'date_from', 'date_to']))
                                            Try adjusting your filters to see more results
                                        @else
                                            Get started by creating your first good receiving
                                        @endif
                                    </p>
                                    <div class="flex space-x-3">
                                        @if(request()->hasAny(['search', 'status', 'quality_status', 'warehouse_id', 'supplier_id', 'date_from', 'date_to']))
                                            <a href="{{ route('inbound.good-receivings.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all flex items-center">
                                                <i class="fas fa-redo mr-2"></i>Clear Filters
                                            </a>
                                        @endif
                                        <a href="{{ route('inbound.good-receivings.create') }}" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all shadow-lg flex items-center">
                                            <i class="fas fa-plus mr-2"></i>New Good Receiving
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($goodReceivings->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $goodReceivings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection