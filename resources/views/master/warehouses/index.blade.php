@extends('layouts.app')

@section('title', 'Warehouses Management')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-warehouse text-blue-600 mr-3"></i>
                Warehouses Management
            </h1>
            <p class="text-sm text-gray-600 mt-2">Manage all warehouse locations and facilities</p>
        </div>
        <a href="{{ route('master.warehouses.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-plus mr-2"></i>Add New Warehouse
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fade-in">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                    <i class="fas fa-times text-green-600 hover:text-green-800"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fade-in">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                    <i class="fas fa-times text-red-600 hover:text-red-800"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        {{-- Total Warehouses --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Total Warehouses</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['total_warehouses'] }}</h3>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-warehouse text-white text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Active Warehouses --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Active</p>
                    <h3 class="text-3xl font-bold text-green-600">{{ $stats['active_warehouses'] }}</h3>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Inactive Warehouses --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Inactive</p>
                    <h3 class="text-3xl font-bold text-red-600">{{ $stats['inactive_warehouses'] }}</h3>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-times-circle text-white text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Storage Bins --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Storage Bins</p>
                    <h3 class="text-3xl font-bold text-purple-600">{{ number_format($stats['total_storage_bins']) }}</h3>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-boxes text-white text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Area --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Total Area</p>
                    <h3 class="text-2xl font-bold text-orange-600">{{ number_format($stats['total_area_sqm'], 0) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">square meters</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-ruler-combined text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-filter text-blue-600 mr-2"></i>Filter & Search
            </h3>
            @if(request()->hasAny(['search', 'status', 'city']))
                <a href="{{ route('master.warehouses.index') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">
                    <i class="fas fa-times-circle mr-1"></i>Clear All Filters
                </a>
            @endif
        </div>
        
        <form method="GET" action="{{ route('master.warehouses.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search text-gray-400 mr-1"></i>Search
                    </label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Code, Name, City..." 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                    >
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-toggle-on text-gray-400 mr-1"></i>Status
                    </label>
                    <select 
                        name="status" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                    >
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                </div>

                {{-- City Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-city text-gray-400 mr-1"></i>City
                    </label>
                    <select 
                        name="city" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                    >
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-end space-x-2">
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm hover:shadow-md"
                    >
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                    <a 
                        href="{{ route('master.warehouses.index') }}" 
                        class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200"
                        title="Reset Filters"
                    >
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Warehouses Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Table Header Info --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h3 class="text-sm font-semibold text-gray-700">
                        Showing {{ $warehouses->firstItem() ?? 0 }} to {{ $warehouses->lastItem() ?? 0 }} of {{ $warehouses->total() }} warehouses
                    </h3>
                    @if(request()->hasAny(['search', 'status', 'city']))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-filter mr-1"></i>Filtered
                        </span>
                    @endif
                </div>
                <div class="text-xs text-gray-500">
                    <i class="fas fa-clock mr-1"></i>Updated {{ now()->format('d M Y H:i') }}
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Code
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Warehouse
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Manager
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Storage Bins
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($warehouses as $warehouse)
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-mono font-bold text-gray-900 bg-gray-100 border border-gray-300">
                                    {{ $warehouse->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                        <i class="fas fa-warehouse text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $warehouse->name }}</div>
                                        @if($warehouse->phone)
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                <i class="fas fa-phone mr-1"></i>{{ $warehouse->phone }}
                                            </div>
                                        @endif
                                        @if($warehouse->email)
                                            <div class="text-xs text-gray-500">
                                                <i class="fas fa-envelope mr-1"></i>{{ $warehouse->email }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                    {{ $warehouse->city ?? 'Not specified' }}
                                </div>
                                @if($warehouse->province)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $warehouse->province }}</div>
                                @endif
                                @if($warehouse->total_area_sqm)
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        <i class="fas fa-expand-arrows-alt mr-1"></i>{{ number_format($warehouse->total_area_sqm, 0) }} m²
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($warehouse->manager)
                                    <div class="flex items-center">
                                        <div class="w-9 h-9 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mr-2 shadow-md">
                                            <i class="fas fa-user-tie text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">{{ $warehouse->manager->name }}</span>
                                            @if($warehouse->manager->email)
                                                <div class="text-xs text-gray-500">{{ $warehouse->manager->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full text-sm font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                    <i class="fas fa-boxes mr-1.5"></i>
                                    {{ $warehouse->storage_bins_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($warehouse->is_active)
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
                                    <a 
                                        href="{{ route('master.warehouses.show', $warehouse) }}" 
                                        class="inline-flex items-center justify-center w-9 h-9 text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors duration-200" 
                                        title="View Details"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a 
                                        href="{{ route('master.warehouses.layout', $warehouse) }}" 
                                        class="inline-flex items-center justify-center w-9 h-9 text-purple-600 bg-purple-100 hover:bg-purple-200 rounded-lg transition-colors duration-200" 
                                        title="View Layout"
                                    >
                                        <i class="fas fa-map"></i>
                                    </a>
                                    <a 
                                        href="{{ route('master.warehouses.edit', $warehouse) }}" 
                                        class="inline-flex items-center justify-center w-9 h-9 text-yellow-600 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors duration-200" 
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form 
                                        action="{{ route('master.warehouses.destroy', $warehouse) }}" 
                                        method="POST" 
                                        class="inline" 
                                        onsubmit="return confirm('Are you sure you want to delete warehouse {{ $warehouse->code }}?\n\nThis action cannot be undone!')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center justify-center w-9 h-9 text-red-600 bg-red-100 hover:bg-red-200 rounded-lg transition-colors duration-200" 
                                            title="Delete"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                        <i class="fas fa-warehouse text-5xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Warehouses Found</h3>
                                    <p class="text-gray-600 mb-6 max-w-md">
                                        @if(request()->hasAny(['search', 'status', 'city']))
                                            No warehouses match your current filters. Try adjusting your search criteria.
                                        @else
                                            Get started by creating your first warehouse location.
                                        @endif
                                    </p>
                                    <div class="flex space-x-3">
                                        @if(request()->hasAny(['search', 'status', 'city']))
                                            <a 
                                                href="{{ route('master.warehouses.index') }}" 
                                                class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200"
                                            >
                                                <i class="fas fa-redo mr-2"></i>Clear Filters
                                            </a>
                                        @endif
                                        <a 
                                            href="{{ route('master.warehouses.create') }}" 
                                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-md hover:shadow-lg transition-all duration-200"
                                        >
                                            <i class="fas fa-plus mr-2"></i>Add Warehouse
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
        @if($warehouses->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing 
                        <span class="font-semibold">{{ $warehouses->firstItem() ?? 0 }}</span> 
                        to 
                        <span class="font-semibold">{{ $warehouses->lastItem() ?? 0 }}</span> 
                        of 
                        <span class="font-semibold">{{ $warehouses->total() }}</span> 
                        results
                    </div>
                    <div>
                        {{ $warehouses->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Auto-dismiss alerts after 5 seconds
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

    // Confirm before delete with warehouse details
    function confirmDelete(warehouseCode, warehouseName) {
        return confirm(`Are you sure you want to delete warehouse ${warehouseCode} - ${warehouseName}?\n\nThis action cannot be undone!`);
    }

    // Filter form auto-submit on change (optional)
    const filterSelects = document.querySelectorAll('#filterForm select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Optionally auto-submit form when filter changes
            // document.getElementById('filterForm').submit();
        });
    });
</script>
@endpush

@push('styles')
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

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Smooth transitions for all interactive elements */
    table tbody tr:hover {
        transform: scale(1.002);
    }

    /* Custom scrollbar for table */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
</style>
@endpush