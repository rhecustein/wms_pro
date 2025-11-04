@extends('layouts.app')

@section('title', 'Storage Areas Management')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-boxes text-purple-600 mr-3"></i>
                Storage Areas Management
            </h1>
            <p class="text-sm text-gray-600 mt-2">Manage warehouse storage areas and zones</p>
        </div>
        <a href="{{ route('master.storage-areas.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-purple-800 shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-plus mr-2"></i>Add Storage Area
        </a>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        {{-- Total Areas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Total Areas</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['total_areas'] ?? 0 }}</h3>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-boxes text-white text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Active Areas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Active</p>
                    <h3 class="text-3xl font-bold text-green-600">{{ $stats['active_areas'] ?? 0 }}</h3>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Inactive Areas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Inactive</p>
                    <h3 class="text-3xl font-bold text-red-600">{{ $stats['inactive_areas'] ?? 0 }}</h3>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-times-circle text-white text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Capacity --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Capacity</p>
                    <h3 class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_capacity'] ?? 0) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">pallets</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-pallet text-white text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Area --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Total Area</p>
                    <h3 class="text-2xl font-bold text-orange-600">{{ number_format($stats['total_area_sqm'] ?? 0, 0) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">m²</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-ruler-combined text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Type Distribution Cards --}}
    @if(isset($stats['by_type']))
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-6">
        {{-- SPR --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-boxes-stacked text-indigo-600"></i>
                </div>
                <p class="text-xs text-gray-600 mb-1">SPR</p>
                <h4 class="text-lg font-bold text-gray-800">{{ $stats['by_type']['spr'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- Bulky --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-box text-yellow-600"></i>
                </div>
                <p class="text-xs text-gray-600 mb-1">Bulky</p>
                <h4 class="text-lg font-bold text-gray-800">{{ $stats['by_type']['bulky'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- Quarantine --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-shield-virus text-red-600"></i>
                </div>
                <p class="text-xs text-gray-600 mb-1">Quarantine</p>
                <h4 class="text-lg font-bold text-gray-800">{{ $stats['by_type']['quarantine'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- Staging 1 --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-dolly text-teal-600"></i>
                </div>
                <p class="text-xs text-gray-600 mb-1">Staging 1</p>
                <h4 class="text-lg font-bold text-gray-800">{{ $stats['by_type']['staging_1'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- Staging 2 --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-truck-loading text-cyan-600"></i>
                </div>
                <p class="text-xs text-gray-600 mb-1">Staging 2</p>
                <h4 class="text-lg font-bold text-gray-800">{{ $stats['by_type']['staging_2'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- Virtual --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-cloud text-pink-600"></i>
                </div>
                <p class="text-xs text-gray-600 mb-1">Virtual</p>
                <h4 class="text-lg font-bold text-gray-800">{{ $stats['by_type']['virtual'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-filter text-purple-600 mr-2"></i>Filter & Search
            </h3>
            @if(request()->hasAny(['search', 'warehouse', 'type', 'status']))
                <a href="{{ route('master.storage-areas.index') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">
                    <i class="fas fa-times-circle mr-1"></i>Clear Filters
                </a>
            @endif
        </div>
        
        <form method="GET" action="{{ route('master.storage-areas.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search text-gray-400 mr-1"></i>Search
                    </label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Code, Name..." 
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 shadow-sm"
                    >
                </div>

                {{-- Warehouse Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-warehouse text-gray-400 mr-1"></i>Warehouse
                    </label>
                    <select name="warehouse" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 shadow-sm">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags text-gray-400 mr-1"></i>Type
                    </label>
                    <select name="type" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 shadow-sm">
                        <option value="">All Types</option>
                        @if(isset($types) && is_array($types))
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                    {{ strtoupper(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        @else
                            <option value="spr" {{ request('type') === 'spr' ? 'selected' : '' }}>SPR</option>
                            <option value="bulky" {{ request('type') === 'bulky' ? 'selected' : '' }}>BULKY</option>
                            <option value="quarantine" {{ request('type') === 'quarantine' ? 'selected' : '' }}>QUARANTINE</option>
                            <option value="staging_1" {{ request('type') === 'staging_1' ? 'selected' : '' }}>STAGING 1</option>
                            <option value="staging_2" {{ request('type') === 'staging_2' ? 'selected' : '' }}>STAGING 2</option>
                            <option value="virtual" {{ request('type') === 'virtual' ? 'selected' : '' }}>VIRTUAL</option>
                        @endif
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-toggle-on text-gray-400 mr-1"></i>Status
                    </label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 shadow-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-filter mr-2"></i>Apply
                    </button>
                    <a href="{{ route('master.storage-areas.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200" title="Reset">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Storage Areas Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Table Header Info --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">
                    Showing {{ $storageAreas->firstItem() ?? 0 }} to {{ $storageAreas->lastItem() ?? 0 }} of {{ $storageAreas->total() }} storage areas
                </h3>
                @if(request()->hasAny(['search', 'warehouse', 'type', 'status']))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-filter mr-1"></i>Filtered
                    </span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Code</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Storage Area</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Warehouse</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Capacity</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($storageAreas as $area)
                        <tr class="hover:bg-purple-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-mono font-bold text-gray-900 bg-gray-100 border border-gray-300">
                                    {{ $area->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                        <i class="fas fa-layer-group text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $area->name }}</div>
                                        @if($area->area_sqm)
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                <i class="fas fa-expand-arrows-alt mr-1"></i>{{ number_format($area->area_sqm, 2) }} m²
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if(isset($area->warehouse) && $area->warehouse)
                                    <div class="flex items-center">
                                        <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-2 shadow-md">
                                            <i class="fas fa-warehouse text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">{{ $area->warehouse->name }}</span>
                                            @if(isset($area->warehouse->city) && $area->warehouse->city)
                                                <div class="text-xs text-gray-500">{{ $area->warehouse->city }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $typeColors = [
                                        'spr' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'bulky' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        'quarantine' => 'bg-red-100 text-red-800 border-red-300',
                                        'staging_1' => 'bg-green-100 text-green-800 border-green-300',
                                        'staging_2' => 'bg-teal-100 text-teal-800 border-teal-300',
                                        'virtual' => 'bg-purple-100 text-purple-800 border-purple-300',
                                    ];
                                    $color = $typeColors[$area->type] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $color }} border">
                                    {{ strtoupper($area->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full text-sm font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                    <i class="fas fa-pallet mr-1.5"></i>
                                    {{ number_format($area->capacity_pallets ?? 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($area->is_active)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-300">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('master.storage-areas.show', $area) }}" class="inline-flex items-center justify-center w-9 h-9 text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors duration-200" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('master.storage-areas.edit', $area) }}" class="inline-flex items-center justify-center w-9 h-9 text-yellow-600 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors duration-200" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('master.storage-areas.destroy', $area) }}" method="POST" class="inline" onsubmit="return confirm('Delete storage area {{ $area->code }}?\n\nThis cannot be undone!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 text-red-600 bg-red-100 hover:bg-red-200 rounded-lg transition-colors duration-200" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                        <i class="fas fa-boxes text-5xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Storage Areas Found</h3>
                                    <p class="text-gray-600 mb-6 max-w-md">
                                        @if(request()->hasAny(['search', 'warehouse', 'type', 'status']))
                                            No storage areas match your filters. Try adjusting your criteria.
                                        @else
                                            Get started by creating your first storage area.
                                        @endif
                                    </p>
                                    <div class="flex space-x-3">
                                        @if(request()->hasAny(['search', 'warehouse', 'type', 'status']))
                                            <a href="{{ route('master.storage-areas.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200">
                                                <i class="fas fa-redo mr-2"></i>Clear Filters
                                            </a>
                                        @endif
                                        <a href="{{ route('master.storage-areas.create') }}" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-purple-800 shadow-md hover:shadow-lg transition-all duration-200">
                                            <i class="fas fa-plus mr-2"></i>Add Storage Area
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
        @if($storageAreas->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $storageAreas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection