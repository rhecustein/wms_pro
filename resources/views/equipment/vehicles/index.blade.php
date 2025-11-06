{{-- resources/views/equipment/vehicles/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Vehicle Management')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-truck text-white text-xl"></i>
                    </div>
                    Vehicle Management
                </h1>
                <p class="text-gray-600 mt-2">Manage your fleet vehicles and maintenance schedules</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('equipment.vehicles.print') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                   target="_blank"
                   class="px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-print"></i>
                    <span>Print</span>
                </a>
                <a href="{{ route('equipment.vehicles.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                   class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-excel"></i>
                    <span>Export CSV</span>
                </a>
                <a href="{{ route('equipment.vehicles.create') }}" 
                   class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Vehicle</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                    <div class="flex-1 pt-1">
                        <p class="font-medium text-green-900">Success!</p>
                        <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-green-600 hover:text-green-800 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                    </div>
                    <div class="flex-1 pt-1">
                        <p class="font-medium text-red-900">Error!</p>
                        <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-red-600 hover:text-red-800 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                    </div>
                    <div class="flex-1 pt-1">
                        <p class="font-medium text-blue-900">Information</p>
                        <p class="text-sm text-blue-700 mt-1">{{ session('info') }}</p>
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-blue-600 hover:text-blue-800 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        {{-- Total --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Vehicles</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($statistics['total']) }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Available --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Available</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($statistics['available']) }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- In Use --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">In Use</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($statistics['in_use']) }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Maintenance --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Maintenance</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ number_format($statistics['maintenance']) }}</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wrench text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Inactive --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Inactive</p>
                    <p class="text-3xl font-bold text-gray-600">{{ number_format($statistics['inactive']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-gray-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-filter text-blue-600"></i>
                    Filters & Search
                </h3>
                <button type="button" onclick="toggleFilters()" class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                    <span id="filterText">Show Filters</span>
                    <i class="fas fa-chevron-down" id="filterIcon"></i>
                </button>
            </div>
        </div>

        <div id="filterSection" class="hidden border-t border-gray-200">
            <form method="GET" action="{{ route('equipment.vehicles.index') }}" id="filterForm">
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        {{-- Search --}}
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Vehicle #, License Plate, Brand..." 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select name="vehicle_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Types</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ request('vehicle_type') === $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ownership --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ownership</label>
                            <select name="ownership" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Types</option>
                                @foreach($ownerships as $ownership)
                                    <option value="{{ $ownership }}" {{ request('ownership') === $ownership ? 'selected' : '' }}>
                                        {{ ucfirst($ownership) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Maintenance Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance</label>
                            <select name="maintenance_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                @foreach($maintenanceStatuses as $mStatus)
                                    <option value="{{ $mStatus }}" {{ request('maintenance_status') === $mStatus ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $mStatus)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-5 pt-5 border-t border-gray-200">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            <span>Apply Filters</span>
                        </button>
                        <a href="{{ route('equipment.vehicles.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
                            <i class="fas fa-redo"></i>
                            <span>Reset</span>
                        </a>
                        <div class="ml-auto text-sm text-gray-600">
                            Showing <span class="font-semibold">{{ $vehicles->total() }}</span> vehicles
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        {{-- Table Header --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-lg font-semibold text-gray-900">Vehicle List</h3>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Show:</label>
                    <select onchange="changePerPage(this.value)" 
                            class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Vehicle Info</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Maintenance</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Odometer</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vehicles as $vehicle)
                        <tr class="hover:bg-blue-50 transition-colors">
                            {{-- Vehicle Info --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-truck text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 font-mono">{{ $vehicle->vehicle_number }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5 font-mono">{{ $vehicle->license_plate }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Details --}}
                            <td class="px-6 py-4">
                                <div>
                                    {!! $vehicle->type_badge !!}
                                    <div class="mt-2 text-sm font-medium text-gray-900">{{ $vehicle->brand ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-600">{{ $vehicle->model ?? 'N/A' }}</div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    {!! $vehicle->status_badge !!}
                                    {!! $vehicle->ownership_badge !!}
                                </div>
                            </td>

                            {{-- Maintenance --}}
                            <td class="px-6 py-4">
                                @php
                                    $maintenanceStatus = $vehicle->maintenance_status;
                                    $badges = [
                                        'overdue' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-exclamation-triangle mr-1"></i>Overdue</span>',
                                        'due_soon' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-clock mr-1"></i>Due Soon</span>',
                                        'scheduled' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Scheduled</span>',
                                        'not_scheduled' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-minus mr-1"></i>Not Set</span>',
                                    ];
                                @endphp
                                <div class="space-y-1">
                                    {!! $badges[$maintenanceStatus] ?? $badges['not_scheduled'] !!}
                                    @if($vehicle->next_maintenance_date)
                                        <div class="text-xs text-gray-500">{{ $vehicle->next_maintenance_date->format('d M Y') }}</div>
                                    @endif
                                </div>
                            </td>

                            {{-- Odometer --}}
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="text-base font-bold text-gray-900">{{ number_format($vehicle->odometer_km ?? 0) }}</div>
                                <div class="text-xs text-gray-500">km</div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('equipment.vehicles.show', $vehicle) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('equipment.vehicles.edit', $vehicle) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" 
                                       title="Edit Vehicle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('equipment.vehicles.destroy', $vehicle) }}" 
                                          method="POST" 
                                          class="inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete vehicle {{ $vehicle->vehicle_number }}? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                                title="Delete Vehicle">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-truck text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Vehicles Found</h3>
                                    <p class="text-gray-600 mb-6 max-w-md">
                                        @if(request()->hasAny(['search', 'status', 'vehicle_type', 'ownership', 'maintenance_status']))
                                            No vehicles match your current filters. Try adjusting your search criteria.
                                        @else
                                            Get started by adding your first vehicle to the system.
                                        @endif
                                    </p>
                                    @if(!request()->hasAny(['search', 'status', 'vehicle_type', 'ownership', 'maintenance_status']))
                                        <a href="{{ route('equipment.vehicles.create') }}" 
                                           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                                            <i class="fas fa-plus-circle"></i>
                                            <span>Add Your First Vehicle</span>
                                        </a>
                                    @else
                                        <a href="{{ route('equipment.vehicles.index') }}" 
                                           class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
                                            <i class="fas fa-redo"></i>
                                            <span>Clear Filters</span>
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
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-semibold text-gray-900">{{ $vehicles->firstItem() }}</span> 
                        to <span class="font-semibold text-gray-900">{{ $vehicles->lastItem() }}</span> 
                        of <span class="font-semibold text-gray-900">{{ $vehicles->total() }}</span> vehicles
                    </div>
                    <div>
                        {{ $vehicles->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function toggleFilters() {
    const section = document.getElementById('filterSection');
    const icon = document.getElementById('filterIcon');
    const text = document.getElementById('filterText');
    
    if (section.classList.contains('hidden')) {
        section.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        text.textContent = 'Hide Filters';
    } else {
        section.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        text.textContent = 'Show Filters';
    }
}

function changePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page'); // Reset to page 1
    window.location.href = url.toString();
}
</script>
@endpush
@endsection