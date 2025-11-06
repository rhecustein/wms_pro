{{-- resources/views/inventory/stocks/by-product.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock by Product')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-box-open text-blue-600 mr-2"></i>
                Stock by Product
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $product->name }} - {{ $product->sku }}</p>
        </div>
        <a href="{{ route('inventory.stocks.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Quantity</p>
                    <h3 class="text-2xl font-bold text-blue-600">{{ number_format($totalQuantity) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">All Locations</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cubes text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Available</p>
                    <h3 class="text-2xl font-bold text-green-600">{{ number_format($availableQuantity) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Ready to Ship</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Reserved</p>
                    <h3 class="text-2xl font-bold text-orange-600">{{ number_format($reservedQuantity) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Allocated</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Stock Locations</p>
                    <h3 class="text-2xl font-bold text-purple-600">{{ $stocks->total() }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Different Bins</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Product Information --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Product Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">SKU</label>
                <p class="text-base font-mono font-semibold text-gray-900">{{ $product->sku }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Category</label>
                <p class="text-base text-gray-900">{{ $product->category->name ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Unit of Measure</label>
                <p class="text-base text-gray-900">{{ $product->unit_of_measure ?? 'PCS' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Minimum Stock</label>
                <p class="text-base text-gray-900">{{ number_format($product->minimum_stock ?? 0) }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Reorder Level</label>
                <p class="text-base text-gray-900">{{ number_format($product->reorder_level ?? 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('inventory.stocks.by-product', $product) }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Warehouse Filter --}}
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

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="quarantine" {{ request('status') === 'quarantine' ? 'selected' : '' }}>Quarantine</option>
                        <option value="damaged" {{ request('status') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('inventory.stocks.by-product', $product) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                    <a href="{{ route('inventory.stocks.stock-card', $product) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition" title="Stock Card">
                        <i class="fas fa-file-invoice"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stock Items Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Stock Locations</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Storage Bin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Serial</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-warehouse text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $stock->warehouse->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $stock->warehouse->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-mono font-semibold text-gray-900">{{ $stock->storageBin->code }}</div>
                                <div class="text-xs text-gray-500">{{ $stock->storageBin->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($stock->batch_number)
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">Batch:</span> {{ $stock->batch_number }}
                                    </div>
                                @endif
                                @if($stock->serial_number)
                                    <div class="text-xs text-gray-500">
                                        <span class="font-medium">Serial:</span> {{ $stock->serial_number }}
                                    </div>
                                @endif
                                @if(!$stock->batch_number && !$stock->serial_number)
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($stock->expiry_date)
                                    @php
                                        $expiryDate = \Carbon\Carbon::parse($stock->expiry_date);
                                        $daysUntilExpiry = now()->diffInDays($expiryDate, false);
                                        $isExpired = $daysUntilExpiry < 0;
                                        $isExpiringSoon = $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
                                    @endphp
                                    
                                    <div class="text-sm text-gray-900">{{ $expiryDate->format('M d, Y') }}</div>
                                    @if($isExpired)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Expired
                                        </span>
                                    @elseif($isExpiringSoon)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                            <i class="fas fa-clock mr-1"></i>{{ abs($daysUntilExpiry) }} days
                                        </span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($stock->quantity) }}</span>
                                <span class="text-xs text-gray-500">{{ $stock->unit_of_measure }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-orange-600">{{ number_format($stock->reserved_quantity) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-green-600">{{ number_format($stock->available_quantity) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'available' => 'bg-green-100 text-green-800',
                                        'reserved' => 'bg-orange-100 text-orange-800',
                                        'quarantine' => 'bg-yellow-100 text-yellow-800',
                                        'damaged' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$stock->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($stock->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('inventory.stocks.show', $stock) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-boxes text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Stock Found</h3>
                                    <p class="text-gray-600">This product has no stock in any location</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($stocks->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $stocks->links() }}
            </div>
        @endif
    </div>

    {{-- Stock Analysis --}}
    @if($stocks->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        {{-- Stock by Warehouse --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-warehouse text-purple-600 mr-2"></i>
                Stock by Warehouse
            </h3>
            <div class="space-y-3">
                @php
                    $warehouseStocks = $stocks->groupBy('warehouse_id');
                @endphp
                @foreach($warehouseStocks as $warehouseId => $items)
                    @php
                        $warehouse = $items->first()->warehouse;
                        $totalQty = $items->sum('quantity');
                        $availableQty = $items->sum('available_quantity');
                        $percentage = $totalQuantity > 0 ? round(($totalQty / $totalQuantity) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-900">{{ $warehouse->name }}</div>
                            <div class="text-xs text-gray-500">{{ number_format($availableQty) }} available of {{ number_format($totalQty) }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-blue-600">{{ $percentage }}%</div>
                            <div class="text-xs text-gray-500">of total</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Stock by Status --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-green-600 mr-2"></i>
                Stock by Status
            </h3>
            <div class="space-y-3">
                @php
                    $statusStocks = $stocks->groupBy('status');
                    $statusConfig = [
                        'available' => ['color' => 'green', 'icon' => 'check-circle'],
                        'reserved' => ['color' => 'orange', 'icon' => 'lock'],
                        'quarantine' => ['color' => 'yellow', 'icon' => 'exclamation-triangle'],
                        'damaged' => ['color' => 'red', 'icon' => 'times-circle'],
                        'expired' => ['color' => 'gray', 'icon' => 'clock'],
                    ];
                @endphp
                @foreach($statusStocks as $status => $items)
                    @php
                        $totalQty = $items->sum('quantity');
                        $percentage = $totalQuantity > 0 ? round(($totalQty / $totalQuantity) * 100, 1) : 0;
                        $config = $statusConfig[$status] ?? ['color' => 'gray', 'icon' => 'box'];
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-{{ $config['color'] }}-50 rounded-lg">
                        <div class="flex items-center flex-1">
                            <div class="w-8 h-8 bg-{{ $config['color'] }}-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-{{ $config['icon'] }} text-{{ $config['color'] }}-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ ucfirst($status) }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($totalQty) }} units</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-{{ $config['color'] }}-600">{{ $percentage }}%</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
@endsection