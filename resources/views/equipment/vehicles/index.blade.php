{{-- resources/views/equipment/vehicles/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Vehicles')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-truck text-blue-600 mr-2"></i>
                Vehicles Management
            </h1>
            <p class="text-sm text-gray-600 mt-1">Manage company vehicles, trucks, and equipment</p>
        </div>
        <a href="{{ route('equipment.vehicles.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>New Vehicle
        </a>
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

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Vehicles</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $vehicles->total() }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Available</p>
                    <h3 class="text-2xl font-bold text-green-600">
                        {{ \App\Models\Vehicle::where('status', 'available')->count() }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">In Use</p>
                    <h3 class="text-2xl font-bold text-blue-600">
                        {{ \App\Models\Vehicle::where('status', 'in_use')->count() }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck-moving text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Maintenance</p>
                    <h3 class="text-2xl font-bold text-yellow-600">
                        {{ \App\Models\Vehicle::where('status', 'maintenance')->count() }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tools text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('equipment.vehicles.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Vehicle #, License Plate, Brand..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="vehicle_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                    <select name="ownership" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Ownership</option>
                        @foreach($ownerships as $ownership)
                            <option value="{{ $ownership }}" {{ request('ownership') === $ownership ? 'selected' : '' }}>
                                {{ ucfirst($ownership) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('equipment.vehicles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
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
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-truck text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $vehicle->vehicle_number }}</div>
                                        <div class="text-xs text-gray-500">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
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
                                        <div class="text-gray-900">{{ number_format($vehicle->capacity_kg, 0) }} kg</div>
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
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                                            </span>
                                        @elseif($maintenanceStatus === 'due_soon')
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>Due Soon
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Scheduled
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
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('equipment.vehicles.show', $vehicle) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('equipment.vehicles.edit', $vehicle) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('equipment.vehicles.destroy', $vehicle) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
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
                                        <i class="fas fa-truck text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Vehicles Found</h3>
                                    <p class="text-gray-600 mb-4">Get started by adding your first vehicle</p>
                                    <a href="{{ route('equipment.vehicles.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-plus mr-2"></i>New Vehicle
                                    </a>
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
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>

</div>
@endsection