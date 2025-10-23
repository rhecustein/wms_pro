{{-- resources/views/master/storage-bins/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Storage Bins Management')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-boxes text-blue-600 mr-2"></i>
                Storage Bins Management
            </h1>
            <p class="text-sm text-gray-600 mt-1">Manage all storage bin locations and capacity</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="document.getElementById('generateModal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-layer-group mr-2"></i>Generate Bins
            </button>
            <a href="{{ route('master.storage-bins.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Add New Bin
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Bins</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $storageBins->total() }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Available</p>
                    <h3 class="text-2xl font-bold text-green-600">{{ $storageBins->where('status', 'available')->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Occupied</p>
                    <h3 class="text-2xl font-bold text-orange-600">{{ $storageBins->where('status', 'occupied')->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Reserved</p>
                    <h3 class="text-2xl font-bold text-purple-600">{{ $storageBins->where('status', 'reserved')->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Blocked</p>
                    <h3 class="text-2xl font-bold text-red-600">{{ $storageBins->where('status', 'blocked')->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('master.storage-bins.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Code, Aisle..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

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

                {{-- Storage Area Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Storage Area</label>
                    <select name="storage_area_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Areas</option>
                        @foreach($storageAreas as $area)
                            <option value="{{ $area->id }}" {{ request('storage_area_id') == $area->id ? 'selected' : '' }}>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bin Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bin Type</label>
                    <select name="bin_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach($binTypes as $type)
                            <option value="{{ $type }}" {{ request('bin_type') === $type ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('master.storage-bins.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Storage Bins Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($storageBins as $bin)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-mono font-bold text-gray-900">{{ $bin->code }}</span>
                                        @if($bin->is_hazmat)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>HAZMAT
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">
                                        A:{{ $bin->aisle }} R:{{ $bin->row }} C:{{ $bin->column }} L:{{ $bin->level }}
                                    </div>
                                    @if($bin->customer)
                                        <div class="text-xs text-purple-600">
                                            <i class="fas fa-user mr-1"></i>{{ $bin->customer->name }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $bin->warehouse->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($bin->storageArea)
                                    <span class="text-sm text-gray-900">{{ $bin->storageArea->name }}</span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($bin->bin_type === 'pick_face') bg-green-100 text-green-800
                                    @elseif($bin->bin_type === 'high_rack') bg-blue-100 text-blue-800
                                    @elseif($bin->bin_type === 'staging') bg-yellow-100 text-yellow-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    {{ ucwords(str_replace('_', ' ', $bin->bin_type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    @if($bin->max_weight_kg)
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-weight-hanging w-4 mr-1"></i>
                                            <span>{{ number_format($bin->current_weight_kg, 2) }}/{{ number_format($bin->max_weight_kg, 2) }} kg</span>
                                        </div>
                                    @endif
                                    @if($bin->max_volume_cbm)
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-cube w-4 mr-1"></i>
                                            <span>{{ number_format($bin->current_volume_cbm, 2) }}/{{ number_format($bin->max_volume_cbm, 2) }} m³</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-boxes w-4 mr-1"></i>
                                        <span>Qty: {{ number_format($bin->current_quantity, 2) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($bin->status === 'available')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>Available
                                    </span>
                                @elseif($bin->status === 'occupied')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>Occupied
                                    </span>
                                @elseif($bin->status === 'reserved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>Reserved
                                    </span>
                                @elseif($bin->status === 'blocked')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>Blocked
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>Maintenance
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('master.storage-bins.show', $bin) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('master.storage-bins.current-stock', $bin) }}" class="text-purple-600 hover:text-purple-900" title="Current Stock">
                                        <i class="fas fa-box-open"></i>
                                    </a>
                                    <a href="{{ route('master.storage-bins.edit', $bin) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($bin->is_active)
                                        <form action="{{ route('master.storage-bins.deactivate', $bin) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-gray-900" title="Deactivate">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('master.storage-bins.activate', $bin) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Activate">
                                                <i class="fas fa-toggle-off"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('master.storage-bins.destroy', $bin) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this storage bin?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-boxes text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Storage Bins Found</h3>
                                    <p class="text-gray-600 mb-4">Get started by creating your first storage bin or generate bulk bins</p>
                                    <div class="flex space-x-2">
                                        <button onclick="document.getElementById('generateModal').classList.remove('hidden')" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                            <i class="fas fa-layer-group mr-2"></i>Generate Bins
                                        </button>
                                        <a href="{{ route('master.storage-bins.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                            <i class="fas fa-plus mr-2"></i>Add Bin
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
        @if($storageBins->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $storageBins->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Generate Bins Modal --}}
<div id="generateModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4 max-h-screen overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-layer-group text-green-600 mr-2"></i>
                    Generate Storage Bins
                </h2>
                <button onclick="document.getElementById('generateModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('master.storage-bins.generate') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    {{-- Warehouse --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse *</label>
                        <select name="warehouse_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Warehouse</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Storage Area --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Storage Area (Optional)</label>
                        <select name="storage_area_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Storage Area</option>
                            @foreach($storageAreas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Aisle Range --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aisle Start *</label>
                        <input type="text" name="aisle_start" required placeholder="AA" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aisle End *</label>
                        <input type="text" name="aisle_end" required placeholder="AZ" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    {{-- Row Range --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Row Start *</label>
                        <input type="number" name="row_start" required min="1" placeholder="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Row End *</label>
                        <input type="number" name="row_end" required min="1" placeholder="10" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    {{-- Column Range --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Column Start *</label>
                        <input type="number" name="column_start" required min="1" placeholder="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Column End *</label>
                        <input type="number" name="column_end" required min="1" placeholder="5" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    {{-- Level Range --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Level Start *</label>
                        <input type="text" name="level_start" required placeholder="A" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Level End *</label>
                        <input type="text" name="level_end" required placeholder="D" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    {{-- Bin Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bin Type *</label>
                        <select name="bin_type" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="high_rack">High Rack</option>
                            <option value="pick_face">Pick Face</option>
                            <option value="staging">Staging</option>
                            <option value="quarantine">Quarantine</option>
                        </select>
                    </div>

                    {{-- Max Weight --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Weight (kg)</label>
                        <input type="number" step="0.01" name="max_weight_kg" placeholder="1000.00" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    {{-- Max Volume --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Volume (m³)</label>
                        <input type="number" step="0.01" name="max_volume_cbm" placeholder="2.50" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-layer-group mr-2"></i>Generate Bins
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection