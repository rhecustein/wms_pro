{{-- resources/views/master/warehouses/layout.blade.php --}}
@extends('layouts.app')

@section('title', 'Warehouse Layout - ' . $warehouse->name)

@section('content')
<div class="px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('master.warehouses.show', $warehouse) }}" class="text-gray-500 hover:text-gray-700 transition">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">
                    <i class="fas fa-warehouse text-blue-600 mr-2"></i>
                    {{ $warehouse->name }} - Layout
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    {{ $warehouse->city ?? 'N/A' }}@if($warehouse->province), {{ $warehouse->province }}@endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="printLayout()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                <i class="fas fa-print mr-2"></i><span class="hidden sm:inline">Print</span>
            </button>
            <a href="{{ route('master.warehouses.edit', $warehouse) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                <i class="fas fa-edit mr-2"></i><span class="hidden sm:inline">Edit</span>
            </a>
        </div>
    </div>

    <div class="space-y-6">

        {{-- Layout Controls --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h3 class="text-base md:text-lg font-semibold text-gray-800">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    Layout Filters
                </h3>
                <button onclick="resetFilters()" class="text-sm text-blue-600 hover:text-blue-700 transition self-start sm:self-auto">
                    <i class="fas fa-redo mr-1"></i>Reset Filters
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4">
                {{-- Status Filter --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5">Status</label>
                    <select id="statusFilter" onchange="filterBins()" class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="reserved">Reserved</option>
                        <option value="blocked">Blocked</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>

                {{-- Bin Type Filter --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5">Bin Type</label>
                    <select id="typeFilter" onchange="filterBins()" class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="pick_face">Pick Face</option>
                        <option value="high_rack">High Rack</option>
                        <option value="staging">Staging</option>
                        <option value="quarantine">Quarantine</option>
                    </select>
                </div>

                {{-- Aisle Filter --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5">Aisle</label>
                    <select id="aisleFilter" onchange="filterBins()" class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Aisles</option>
                        @if($aisles->isNotEmpty())
                            @foreach($aisles->flatten()->unique('aisle')->sortBy('aisle') as $bin)
                                <option value="{{ $bin->aisle }}">{{ $bin->aisle }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Level Filter --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5">Level</label>
                    <select id="levelFilter" onchange="filterBins()" class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Levels</option>
                        <option value="A">Level A</option>
                        <option value="B">Level B</option>
                        <option value="C">Level C</option>
                        <option value="D">Level D</option>
                        <option value="E">Level E</option>
                    </select>
                </div>

                {{-- Search --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5">Search Bin</label>
                    <input type="text" id="searchBin" onkeyup="filterBins()" placeholder="Enter bin code..." class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Legend
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-green-500 rounded border-2 border-green-600 flex-shrink-0"></div>
                    <span class="text-xs md:text-sm text-gray-700">Available</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-blue-500 rounded border-2 border-blue-600 flex-shrink-0"></div>
                    <span class="text-xs md:text-sm text-gray-700">Occupied</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-yellow-500 rounded border-2 border-yellow-600 flex-shrink-0"></div>
                    <span class="text-xs md:text-sm text-gray-700">Reserved</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-red-500 rounded border-2 border-red-600 flex-shrink-0"></div>
                    <span class="text-xs md:text-sm text-gray-700">Blocked</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-gray-500 rounded border-2 border-gray-600 flex-shrink-0"></div>
                    <span class="text-xs md:text-sm text-gray-700">Maintenance</span>
                </div>
            </div>
        </div>

        {{-- Statistics Summary --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">
            @php
                $allBins = $aisles->flatten();
                $totalBins = $allBins->count();
                $availableBins = $allBins->where('status', 'available')->count();
                $occupiedBins = $allBins->where('status', 'occupied')->count();
                $reservedBins = $allBins->where('status', 'reserved')->count();
                $blockedBins = $allBins->where('status', 'blocked')->count();
            @endphp

            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 md:p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-gray-600 mb-1">Total Bins</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $totalBins }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-boxes text-gray-600 text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 md:p-6 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-green-700 mb-1">Available</p>
                        <p class="text-2xl md:text-3xl font-bold text-green-800">{{ $availableBins }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-green-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 md:p-6 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-blue-700 mb-1">Occupied</p>
                        <p class="text-2xl md:text-3xl font-bold text-blue-800">{{ $occupiedBins }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-box text-blue-600 text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4 md:p-6 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-yellow-700 mb-1">Reserved</p>
                        <p class="text-2xl md:text-3xl font-bold text-yellow-800">{{ $reservedBins }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-lock text-yellow-600 text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-4 md:p-6 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-red-700 mb-1">Blocked</p>
                        <p class="text-2xl md:text-3xl font-bold text-red-800">{{ $blockedBins }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-red-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-ban text-red-600 text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Warehouse Layout by Storage Area --}}
        @forelse($aisles as $areaType => $areas)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                    <h3 class="text-lg md:text-xl font-bold text-gray-800">
                        <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                        {{ ucfirst($areaType) }} Area
                    </h3>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium self-start sm:self-auto">
                        {{ $areas->sum(function($area) { return $area->storageBins->count(); }) }} Bins
                    </span>
                </div>

                @foreach($areas as $area)
                    <div class="mb-8 last:mb-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4 pb-3 border-b border-gray-200">
                            <h4 class="text-base md:text-lg font-semibold text-gray-700">
                                <i class="fas fa-map-marked-alt text-gray-500 mr-2"></i>
                                {{ $area->name }} ({{ $area->code }})
                            </h4>
                            <span class="text-sm text-gray-600">
                                {{ $area->storageBins->count() }} bins
                            </span>
                        </div>

                        {{-- Group bins by aisle --}}
                        @php
                            $binsByAisle = $area->storageBins->groupBy('aisle');
                        @endphp

                        <div class="space-y-6">
                            @foreach($binsByAisle as $aisle => $bins)
                                <div class="border border-gray-200 rounded-lg p-3 md:p-4 bg-gray-50">
                                    <div class="flex items-center justify-between mb-3">
                                        <h5 class="text-sm md:text-base font-semibold text-gray-700">
                                            <i class="fas fa-road text-gray-500 mr-2"></i>
                                            Aisle {{ $aisle }}
                                        </h5>
                                        <span class="text-xs text-gray-500">{{ $bins->count() }} bins</span>
                                    </div>

                                    {{-- Group by level (vertical display) --}}
                                    @php
                                        $binsByLevel = $bins->groupBy('level')->sortKeysDesc();
                                    @endphp

                                    <div class="space-y-2">
                                        @foreach($binsByLevel as $level => $levelBins)
                                            <div class="flex items-start space-x-2">
                                                <div class="w-10 md:w-12 text-center flex-shrink-0">
                                                    <span class="text-xs font-semibold text-gray-600 bg-gray-200 px-2 py-1 rounded inline-block">
                                                        L-{{ $level }}
                                                    </span>
                                                </div>
                                                <div class="flex-1 flex flex-wrap gap-1.5 md:gap-2">
                                                    @foreach($levelBins->sortBy('row')->sortBy('column') as $bin)
                                                        <div 
                                                            class="bin-item relative group cursor-pointer transition-all duration-200 hover:scale-110"
                                                            data-status="{{ $bin->status }}"
                                                            data-type="{{ $bin->bin_type }}"
                                                            data-aisle="{{ $bin->aisle }}"
                                                            data-level="{{ $bin->level }}"
                                                            data-code="{{ $bin->code }}"
                                                            onclick="showBinDetails('{{ $bin->id }}')"
                                                        >
                                                            <div class="w-12 h-12 md:w-16 md:h-16 rounded-lg border-2 flex items-center justify-center
                                                                @if($bin->status === 'available') bg-green-500 border-green-600 @endif
                                                                @if($bin->status === 'occupied') bg-blue-500 border-blue-600 @endif
                                                                @if($bin->status === 'reserved') bg-yellow-500 border-yellow-600 @endif
                                                                @if($bin->status === 'blocked') bg-red-500 border-red-600 @endif
                                                                @if($bin->status === 'maintenance') bg-gray-500 border-gray-600 @endif
                                                            ">
                                                                <div class="text-center">
                                                                    <div class="text-[8px] md:text-[10px] font-bold text-white leading-tight">
                                                                        {{ $bin->row }}{{ $bin->column }}
                                                                    </div>
                                                                    @if($bin->bin_type === 'pick_face')
                                                                        <div class="text-[6px] md:text-[8px] text-white">PF</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                            {{-- Tooltip --}}
                                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-10 pointer-events-none">
                                                                <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 whitespace-nowrap shadow-lg">
                                                                    <div class="font-bold mb-1">{{ $bin->code }}</div>
                                                                    <div>Status: <span class="font-semibold">{{ ucfirst($bin->status) }}</span></div>
                                                                    <div>Type: {{ ucfirst(str_replace('_', ' ', $bin->bin_type)) }}</div>
                                                                    @if($bin->current_quantity > 0)
                                                                        <div class="mt-1 pt-1 border-t border-gray-700">
                                                                            Qty: {{ number_format($bin->current_quantity, 2) }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 md:p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-warehouse text-3xl md:text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-2">No Storage Layout Found</h3>
                    <p class="text-sm md:text-base text-gray-600 mb-6">
                        This warehouse doesn't have any storage areas or bins configured yet.
                    </p>
                    <a href="{{ route('master.warehouses.edit', $warehouse) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm md:text-base">
                        <i class="fas fa-plus mr-2"></i>
                        Configure Storage Layout
                    </a>
                </div>
            </div>
        @endforelse

    </div>
</div>

{{-- Bin Details Modal --}}
<div id="binModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg md:rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-4 md:p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg md:text-xl font-bold text-gray-800">
                <i class="fas fa-box text-blue-600 mr-2"></i>
                Storage Bin Details
            </h3>
            <button onclick="closeBinModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="binModalContent" class="p-4 md:p-6">
            <div class="flex items-center justify-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Filter bins
    function filterBins() {
        const status = document.getElementById('statusFilter').value.toLowerCase();
        const type = document.getElementById('typeFilter').value.toLowerCase();
        const aisle = document.getElementById('aisleFilter').value.toLowerCase();
        const level = document.getElementById('levelFilter').value.toLowerCase();
        const search = document.getElementById('searchBin').value.toLowerCase();

        document.querySelectorAll('.bin-item').forEach(bin => {
            const binStatus = bin.dataset.status.toLowerCase();
            const binType = bin.dataset.type.toLowerCase();
            const binAisle = bin.dataset.aisle.toLowerCase();
            const binLevel = bin.dataset.level.toLowerCase();
            const binCode = bin.dataset.code.toLowerCase();

            const matchStatus = !status || binStatus === status;
            const matchType = !type || binType === type;
            const matchAisle = !aisle || binAisle === aisle;
            const matchLevel = !level || binLevel === level;
            const matchSearch = !search || binCode.includes(search);

            if (matchStatus && matchType && matchAisle && matchLevel && matchSearch) {
                bin.style.display = 'block';
            } else {
                bin.style.display = 'none';
            }
        });
    }

    // Reset filters
    function resetFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('typeFilter').value = '';
        document.getElementById('aisleFilter').value = '';
        document.getElementById('levelFilter').value = '';
        document.getElementById('searchBin').value = '';
        filterBins();
    }

    // Show bin details modal
    function showBinDetails(binId) {
        const modal = document.getElementById('binModal');
        modal.classList.remove('hidden');
        
        // Fetch bin details via AJAX
        fetch(`/api/storage-bins/${binId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('binModalContent').innerHTML = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Bin Code</p>
                                <p class="text-lg font-bold text-gray-800">${data.code}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Status</p>
                                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                                    ${data.status === 'available' ? 'bg-green-100 text-green-800' : ''}
                                    ${data.status === 'occupied' ? 'bg-blue-100 text-blue-800' : ''}
                                    ${data.status === 'reserved' ? 'bg-yellow-100 text-yellow-800' : ''}
                                    ${data.status === 'blocked' ? 'bg-red-100 text-red-800' : ''}
                                ">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Location Details</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div><span class="text-gray-600">Aisle:</span> <span class="font-semibold">${data.aisle}</span></div>
                                <div><span class="text-gray-600">Row:</span> <span class="font-semibold">${data.row}</span></div>
                                <div><span class="text-gray-600">Column:</span> <span class="font-semibold">${data.column}</span></div>
                                <div><span class="text-gray-600">Level:</span> <span class="font-semibold">${data.level}</span></div>
                            </div>
                        </div>
                        ${data.current_quantity > 0 ? `
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Inventory</h4>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-blue-700 mb-1">Current Quantity</p>
                                <p class="text-2xl font-bold text-blue-800">${parseFloat(data.current_quantity).toFixed(2)}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                `;
            })
            .catch(error => {
                document.getElementById('binModalContent').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-3"></i>
                        <p class="text-gray-600">Failed to load bin details</p>
                    </div>
                `;
            });
    }

    // Close bin modal
    function closeBinModal() {
        document.getElementById('binModal').classList.add('hidden');
    }

    // Print layout
    function printLayout() {
        window.print();
    }

    // Close modal on outside click
    document.getElementById('binModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeBinModal();
        }
    });
</script>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush