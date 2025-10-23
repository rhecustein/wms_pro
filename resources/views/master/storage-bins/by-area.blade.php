{{-- resources/views/master/storage-bins/by-area.blade.php --}}
@extends('layouts.app')

@section('title', 'Storage Bins by Area')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-layer-group text-purple-600 mr-2"></i>
                Storage Bins in: {{ $storageArea->name }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                <span class="font-semibold">{{ $storageArea->warehouse->name }}</span> • 
                {{ $bins->count() }} bins found
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.storage-areas.show', $storageArea) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-eye mr-2"></i>View Area Details
            </a>
            <a href="{{ route('master.storage-bins.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to All Bins
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Bins</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $bins->count() }}</h3>
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
                    <h3 class="text-2xl font-bold text-green-600">{{ $bins->where('status', 'available')->count() }}</h3>
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
                    <h3 class="text-2xl font-bold text-orange-600">{{ $bins->where('status', 'occupied')->count() }}</h3>
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
                    <h3 class="text-2xl font-bold text-purple-600">{{ $bins->where('status', 'reserved')->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active</p>
                    <h3 class="text-2xl font-bold text-blue-600">{{ $bins->where('is_active', true)->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-toggle-on text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Storage Area Information --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
            Storage Area Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="text-sm text-gray-600">Area Code</label>
                <div class="mt-1 font-mono font-bold text-gray-900">{{ $storageArea->code }}</div>
            </div>

            <div>
                <label class="text-sm text-gray-600">Area Name</label>
                <div class="mt-1 font-semibold text-gray-900">{{ $storageArea->name }}</div>
            </div>

            <div>
                <label class="text-sm text-gray-600">Warehouse</label>
                <div class="mt-1 font-semibold text-gray-900">{{ $storageArea->warehouse->name }}</div>
            </div>

            <div>
                <label class="text-sm text-gray-600">Area Type</label>
                <div class="mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ ucwords(str_replace('_', ' ', $storageArea->area_type ?? 'General')) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Bins Grid View --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-th text-orange-600 mr-2"></i>
                Storage Bins
            </h2>
            <div class="flex space-x-2">
                <button onclick="toggleView('grid')" id="gridViewBtn" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-th"></i>
                </button>
                <button onclick="toggleView('list')" id="listViewBtn" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        {{-- Grid View --}}
        <div id="gridView" class="p-6">
            @if($bins->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-3">
                    @foreach($bins as $bin)
                        <a href="{{ route('master.storage-bins.show', $bin) }}" 
                           class="group relative p-4 border-2 rounded-lg transition-all hover:shadow-lg
                                  @if($bin->status === 'available') border-green-300 bg-green-50 hover:border-green-500
                                  @elseif($bin->status === 'occupied') border-orange-300 bg-orange-50 hover:border-orange-500
                                  @elseif($bin->status === 'reserved') border-purple-300 bg-purple-50 hover:border-purple-500
                                  @elseif($bin->status === 'blocked') border-red-300 bg-red-50 hover:border-red-500
                                  @else border-gray-300 bg-gray-50 hover:border-gray-500
                                  @endif">
                            
                            {{-- Status Indicator --}}
                            <div class="absolute top-2 right-2">
                                <span class="w-3 h-3 rounded-full inline-block
                                             @if($bin->status === 'available') bg-green-500
                                             @elseif($bin->status === 'occupied') bg-orange-500
                                             @elseif($bin->status === 'reserved') bg-purple-500
                                             @elseif($bin->status === 'blocked') bg-red-500
                                             @else bg-gray-500
                                             @endif"></span>
                            </div>

                            {{-- Bin Icon --}}
                            <div class="flex justify-center mb-2">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                           @if($bin->status === 'available') bg-green-100
                                           @elseif($bin->status === 'occupied') bg-orange-100
                                           @elseif($bin->status === 'reserved') bg-purple-100
                                           @elseif($bin->status === 'blocked') bg-red-100
                                           @else bg-gray-100
                                           @endif">
                                    <i class="fas fa-box
                                             @if($bin->status === 'available') text-green-600
                                             @elseif($bin->status === 'occupied') text-orange-600
                                             @elseif($bin->status === 'reserved') text-purple-600
                                             @elseif($bin->status === 'blocked') text-red-600
                                             @else text-gray-600
                                             @endif"></i>
                                </div>
                            </div>

                            {{-- Bin Code --}}
                            <div class="text-center">
                                <div class="font-mono font-bold text-sm text-gray-900 mb-1">{{ $bin->code }}</div>
                                <div class="text-xs text-gray-600">
                                    {{ ucfirst($bin->status) }}
                                </div>
                            </div>

                            {{-- Quantity Badge --}}
                            @if($bin->current_quantity > 0)
                                <div class="absolute bottom-2 left-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ number_format($bin->current_quantity, 0) }}
                                    </span>
                                </div>
                            @endif

                            {{-- Hazmat Badge --}}
                            @if($bin->is_hazmat)
                                <div class="absolute bottom-2 right-2">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-xs"></i>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-boxes text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Bins in This Area</h3>
                    <p class="text-gray-600">This storage area doesn't have any bins assigned yet</p>
                </div>
            @endif
        </div>

        {{-- List View --}}
        <div id="listView" class="hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($bins as $bin)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-box text-blue-600"></i>
                                        </div>
                                        <span class="text-sm font-mono font-bold text-gray-900">{{ $bin->code }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        A:{{ $bin->aisle }} R:{{ $bin->row }} C:{{ $bin->column }} L:{{ $bin->level }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucwords(str_replace('_', ' ', $bin->bin_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs space-y-1">
                                        <div class="text-gray-600">Qty: {{ number_format($bin->current_quantity, 2) }}</div>
                                        @if($bin->max_weight_kg)
                                            <div class="text-gray-600">{{ number_format($bin->current_weight_kg, 2) }}/{{ number_format($bin->max_weight_kg, 2) }} kg</div>
                                        @endif
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
                                        <a href="{{ route('master.storage-bins.edit', $bin) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">No bins found in this storage area</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
function toggleView(view) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');

    if (view === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridBtn.classList.remove('bg-gray-200', 'text-gray-700');
        gridBtn.classList.add('bg-blue-600', 'text-white');
        listBtn.classList.remove('bg-blue-600', 'text-white');
        listBtn.classList.add('bg-gray-200', 'text-gray-700');
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        listBtn.classList.remove('bg-gray-200', 'text-gray-700');
        listBtn.classList.add('bg-blue-600', 'text-white');
        gridBtn.classList.remove('bg-blue-600', 'text-white');
        gridBtn.classList.add('bg-gray-200', 'text-gray-700');
    }
}
</script>

@endsection