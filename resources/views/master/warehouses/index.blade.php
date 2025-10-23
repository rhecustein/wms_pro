@extends('layouts.app')

@section('title', 'Warehouses Management')

@section('content')
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-warehouse text-blue-600 mr-3"></i>Warehouses Management
            </h1>
            <p class="text-gray-600 mt-1">Manage your warehouse locations and information</p>
        </div>
        <a href="{{ route('master.warehouses.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-plus mr-2"></i>Add New Warehouse
        </a>
    </div>

    {{-- FILTERS --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('master.warehouses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Search --}}
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search text-gray-400 mr-1"></i> Search
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Code, name, city..." class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-toggle-on text-gray-400 mr-1"></i> Status
                    </label>
                    <select name="status" id="status" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- City Filter --}}
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-city text-gray-400 mr-1"></i> City
                    </label>
                    <select name="city" id="city" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- WAREHOUSES TABLE --}}
    <div class="bg-white overflow-hidden shadow-lg rounded-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Warehouse Info</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Manager</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($warehouses as $warehouse)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-blue-600">{{ $warehouse->code }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $warehouse->name }}</div>
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-cubes mr-1"></i>{{ number_format($warehouse->storage_bins_count) }} bins
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>{{ $warehouse->city }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $warehouse->province }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <i class="fas fa-user text-purple-500 mr-1"></i>{{ $warehouse->manager->name ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <i class="fas fa-cube text-blue-500 mr-1"></i>{{ number_format($warehouse->total_area_sqm, 0) }} m²
                            </div>
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-arrows-alt-v text-green-500 mr-1"></i>{{ number_format($warehouse->height_meters, 1) }} m
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($warehouse->is_active)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('master.warehouses.show', $warehouse) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                    <i class="fas fa-eye text-lg"></i>
                                </a>
                                <a href="{{ route('master.warehouses.layout', $warehouse) }}" class="text-purple-600 hover:text-purple-900" title="Layout">
                                    <i class="fas fa-th text-lg"></i>
                                </a>
                                <a href="{{ route('master.warehouses.edit', $warehouse) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <i class="fas fa-edit text-lg"></i>
                                </a>
                                @if($warehouse->is_active)
                                    <form action="{{ route('master.warehouses.deactivate', $warehouse) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-orange-600 hover:text-orange-900" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this warehouse?')">
                                            <i class="fas fa-ban text-lg"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('master.warehouses.activate', $warehouse) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Activate">
                                            <i class="fas fa-check text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('master.warehouses.destroy', $warehouse) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete" onclick="return confirm('Are you sure you want to delete this warehouse?')">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-warehouse text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">No warehouses found</p>
                                <a href="{{ route('master.warehouses.create') }}" class="mt-4 text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-plus mr-1"></i>Create your first warehouse
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($warehouses->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $warehouses->links() }}
        </div>
        @endif
    </div>
@endsection