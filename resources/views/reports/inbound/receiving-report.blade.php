@extends('layouts.app')

@section('title', 'Receiving Accuracy Report')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-bullseye text-purple-600 mr-2"></i>
                Receiving Accuracy Report
            </h1>
            <p class="text-sm text-gray-600 mt-1">Monitor accuracy of received quantities vs expected</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('reports.inbound.receiving-report') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-truck-loading mr-2"></i>Receiving Report
            </a>
            <a href="{{ route('reports.inbound.vendor-performance') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-chart-line mr-2"></i>Vendor Performance
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-7 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Items</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_items']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Accurate</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['accurate_items']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Accuracy Rate</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['accuracy_rate'], 1) }}%</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percentage text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Over Received</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['over_received']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Under Received</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['under_received']) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Expected</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_expected']) }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Variance</p>
                    <p class="text-2xl font-bold {{ $stats['total_variance'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        {{ $stats['total_variance'] >= 0 ? '+' : '' }}{{ number_format($stats['total_variance']) }}
                    </p>
                </div>
                <div class="w-12 h-12 {{ $stats['total_variance'] >= 0 ? 'bg-blue-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-xl {{ $stats['total_variance'] >= 0 ? 'text-blue-600' : 'text-red-600' }}"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('reports.inbound.receiving-accuracy') }}">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Product Name, SKU..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Date Range --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Supplier Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <select name="supplier_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('reports.inbound.receiving-accuracy') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Accuracy Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inbound</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Expected</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Variance</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Variance %</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $item->product->name ?? '-' }}</div>
                                        @if($item->product)
                                            <div class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="text-gray-900 font-mono">{{ $item->inbound->inbound_number ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->inbound->supplier->name ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($item->quantity_expected) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($item->quantity_received) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-semibold {{ $item->variance > 0 ? 'text-blue-600' : ($item->variance < 0 ? 'text-red-600' : 'text-gray-900') }}">
                                    {{ $item->variance >= 0 ? '+' : '' }}{{ number_format($item->variance) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center">
                                    <div class="text-sm font-semibold {{ abs($item->variance_percentage) <= 5 ? 'text-green-600' : (abs($item->variance_percentage) <= 10 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $item->variance_percentage >= 0 ? '+' : '' }}{{ number_format($item->variance_percentage, 1) }}%
                                    </div>
                                    @if(abs($item->variance_percentage) > 0)
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="h-2 rounded-full {{ abs($item->variance_percentage) <= 5 ? 'bg-green-600' : (abs($item->variance_percentage) <= 10 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                                 style="width: {{ min(abs($item->variance_percentage), 100) }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->is_accurate)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Accurate
                                    </span>
                                @elseif($item->variance > 0)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        Over
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i class="fas fa-arrow-down mr-1"></i>
                                        Under
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="viewDetails({{ $item->id }})" class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(!$item->is_accurate)
                                        <button onclick="investigateVariance({{ $item->id }})" class="text-purple-600 hover:text-purple-900" title="Investigate">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-bullseye text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Accuracy Records Found</h3>
                                    <p class="text-gray-600">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    {{-- Accuracy Legend --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Variance Tolerance Guide</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-4 h-4 bg-green-600 rounded"></div>
                <span class="text-sm text-gray-600">Acceptable (≤ 5%)</span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-4 h-4 bg-yellow-600 rounded"></div>
                <span class="text-sm text-gray-600">Warning (5-10%)</span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-4 h-4 bg-red-600 rounded"></div>
                <span class="text-sm text-gray-600">Critical (> 10%)</span>
            </div>
        </div>
    </div>

</div>

<script>
function viewDetails(id) {
    alert('View details for item ID: ' + id);
}

function investigateVariance(id) {
    alert('Investigate variance for item ID: ' + id);
}
</script>
@endsection