{{-- resources/views/equipment/vehicles/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Vehicles Management')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-truck text-blue-600 mr-2"></i>
                Vehicles Management
            </h1>
            <p class="text-sm text-gray-600 mt-1">Manage your fleet vehicles and equipment</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('equipment.vehicles.create') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                <span>Add Vehicle</span>
            </a>
            <button onclick="window.print()" 
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                    class="text-green-500 hover:text-green-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <span class="text-red-800">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                    class="text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                <span class="text-blue-800">{{ session('info') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                    class="text-blue-500 hover:text-blue-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Vehicles</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $statistics['total'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Available</p>
                    <h3 class="text-2xl font-bold text-green-600">{{ $statistics['available'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">In Use</p>
                    <h3 class="text-2xl font-bold text-blue-600">{{ $statistics['in_use'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck-moving text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Maintenance</p>
                    <h3 class="text-2xl font-bold text-yellow-600">{{ $statistics['maintenance'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tools text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Inactive</p>
                    <h3 class="text-2xl font-bold text-gray-600">{{ $statistics['inactive'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('equipment.vehicles.index') }}" id="filterForm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Vehicle number, plate, brand..."
                               class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                    <select name="vehicle_type" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('vehicle_type') === $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Ownership Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ownership</label>
                    <select name="ownership" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All</option>
                        @foreach($ownerships as $ownership)
                            <option value="{{ $ownership }}" {{ request('ownership') === $ownership ? 'selected' : '' }}>
                                {{ ucfirst($ownership) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('equipment.vehicles.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>

            {{-- Advanced Filters (Collapsible) --}}
            <div class="mt-4 pt-4 border-t border-gray-200" id="advancedFilters" style="display: none;">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Status</label>
                        <select name="maintenance_status" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All</option>
                            @foreach($maintenanceStatuses as $maintenanceStatus)
                                <option value="{{ $maintenanceStatus }}" 
                                        {{ request('maintenance_status') === $maintenanceStatus ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $maintenanceStatus)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort_by" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Created Date</option>
                            <option value="vehicle_number" {{ request('sort_by') === 'vehicle_number' ? 'selected' : '' }}>Vehicle Number</option>
                            <option value="license_plate" {{ request('sort_by') === 'license_plate' ? 'selected' : '' }}>License Plate</option>
                            <option value="brand" {{ request('sort_by') === 'brand' ? 'selected' : '' }}>Brand</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                        <select name="sort_order" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                        <select name="per_page" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="button" 
                    onclick="toggleAdvancedFilters()" 
                    class="mt-3 text-sm text-blue-600 hover:text-blue-700 flex items-center">
                <i class="fas fa-chevron-down mr-1" id="advancedIcon"></i>
                <span id="advancedText">Show Advanced Filters</span>
            </button>
        </form>
    </div>

    {{-- Vehicles Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License Plate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ownership</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maintenance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vehicles as $vehicle)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-truck text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $vehicle->vehicle_number }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $vehicle->brand ? $vehicle->brand . ' ' . $vehicle->model : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-semibold text-gray-900">{{ $vehicle->license_plate }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $vehicle->type_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    @if($vehicle->capacity_kg)
                                        <div class="text-gray-900 font-medium">{{ number_format($vehicle->capacity_kg, 0) }} kg</div>
                                    @endif
                                    @if($vehicle->capacity_cbm)
                                        <div class="text-xs text-gray-500">{{ number_format($vehicle->capacity_cbm, 2) }} m³</div>
                                    @endif
                                    @if(!$vehicle->capacity_kg && !$vehicle->capacity_cbm)
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $vehicle->ownership_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($vehicle->next_maintenance_date)
                                    <div class="text-sm">
                                        @php
                                            $maintenanceStatus = $vehicle->maintenance_status;
                                        @endphp
                                        @if($maintenanceStatus === 'overdue')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle"></i> Overdue
                                            </span>
                                        @elseif($maintenanceStatus === 'due_soon')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock"></i> Due Soon
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check"></i> Scheduled
                                            </span>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">{{ $vehicle->next_maintenance_date->format('d M Y') }}</div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Not scheduled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $vehicle->status_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('equipment.vehicles.show', $vehicle) }}" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('equipment.vehicles.edit', $vehicle) }}" 
                                       class="text-yellow-600 hover:text-yellow-900" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('equipment.vehicles.destroy', $vehicle) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete vehicle {{ $vehicle->vehicle_number }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Delete">
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
                                        <i class="fas fa-truck text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Vehicles Found</h3>
                                    <p class="text-gray-600 mb-4">
                                        @if(request()->hasAny(['search', 'status', 'vehicle_type', 'ownership']))
                                            Try adjusting your filters
                                        @else
                                            Get started by adding your first vehicle
                                        @endif
                                    </p>
                                    @if(!request()->hasAny(['search', 'status', 'vehicle_type', 'ownership']))
                                        <a href="{{ route('equipment.vehicles.create') }}" 
                                           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                            <i class="fas fa-plus mr-2"></i>Add Vehicle
                                        </a>
                                    @else
                                        <a href="{{ route('equipment.vehicles.index') }}" 
                                           class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                            <i class="fas fa-redo mr-2"></i>Clear Filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($vehicles->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $vehicles->firstItem() }}</span> 
                        to <span class="font-medium">{{ $vehicles->lastItem() }}</span> 
                        of <span class="font-medium">{{ $vehicles->total() }}</span> results
                    </div>
                    <div>
                        {{ $vehicles->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>

<script>
function toggleAdvancedFilters() {
    const filters = document.getElementById('advancedFilters');
    const icon = document.getElementById('advancedIcon');
    const text = document.getElementById('advancedText');
    
    if (filters.style.display === 'none') {
        filters.style.display = 'block';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        text.textContent = 'Hide Advanced Filters';
    } else {
        filters.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        text.textContent = 'Show Advanced Filters';
    }
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"], [class*="bg-blue-50"]');
    alerts.forEach(alert => {
        if (alert.parentElement) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    });
}, 5000);
</script>

@endsection