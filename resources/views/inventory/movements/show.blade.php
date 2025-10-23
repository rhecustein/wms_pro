{{-- resources/views/inventory/movements/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock Movement Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('inventory.movements.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i>Back to Movements
            </a>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-exchange-alt text-blue-600 mr-2"></i>
                Stock Movement Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">Movement ID: #{{ $stockMovement->id }}</p>
        </div>
        @php
            $typeColors = [
                'inbound' => 'bg-green-100 text-green-800',
                'outbound' => 'bg-red-100 text-red-800',
                'transfer' => 'bg-blue-100 text-blue-800',
                'adjustment' => 'bg-yellow-100 text-yellow-800',
                'putaway' => 'bg-purple-100 text-purple-800',
                'picking' => 'bg-orange-100 text-orange-800',
                'replenishment' => 'bg-indigo-100 text-indigo-800'
            ];
        @endphp
        <span class="inline-flex items-center px-4 py-2 rounded-lg text-lg font-medium {{ $typeColors[$stockMovement->movement_type] ?? 'bg-gray-100 text-gray-800' }}">
            {{ ucfirst($stockMovement->movement_type) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Information --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Movement Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Movement Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Movement Date</label>
                        <p class="text-gray-900 font-semibold">{{ $stockMovement->movement_date->format('d M Y, H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Movement Type</label>
                        <p class="text-gray-900 font-semibold">{{ ucfirst($stockMovement->movement_type) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Quantity</label>
                        <p class="text-gray-900 font-semibold text-lg">
                            {{ $stockMovement->quantity }} {{ $stockMovement->unit_of_measure }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Performed By</label>
                        <p class="text-gray-900 font-semibold">
                            @if($stockMovement->performedBy)
                                <i class="fas fa-user text-gray-400 mr-1"></i>
                                {{ $stockMovement->performedBy->name }}
                            @else
                                <span class="text-gray-400">System</span>
                            @endif
                        </p>
                    </div>

                    @if($stockMovement->batch_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Batch Number</label>
                            <p class="text-gray-900 font-mono">{{ $stockMovement->batch_number }}</p>
                        </div>
                    @endif

                    @if($stockMovement->serial_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Serial Number</label>
                            <p class="text-gray-900 font-mono">{{ $stockMovement->serial_number }}</p>
                        </div>
                    @endif
                </div>

                @if($stockMovement->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Notes</label>
                        <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $stockMovement->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Product Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-box text-blue-600 mr-2"></i>
                    Product Information
                </h2>

                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-box text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $stockMovement->product->name }}</h3>
                        <p class="text-sm text-gray-500">SKU: {{ $stockMovement->product->sku }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @if(isset($stockMovement->product->category))
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                            <p class="text-gray-900">{{ $stockMovement->product->category }}</p>
                        </div>
                    @endif

                    @if(isset($stockMovement->product->barcode))
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Barcode</label>
                            <p class="text-gray-900 font-mono">{{ $stockMovement->product->barcode }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Location Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                    Location Information
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Warehouse</label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-900 font-semibold">{{ $stockMovement->warehouse->name }}</p>
                            <p class="text-sm text-gray-500">{{ $stockMovement->warehouse->code }}</p>
                        </div>
                    </div>

                    @if($stockMovement->fromBin || $stockMovement->toBin)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($stockMovement->fromBin)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">From Bin</label>
                                    <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                                        <p class="text-red-900 font-semibold">{{ $stockMovement->fromBin->code }}</p>
                                        <p class="text-sm text-red-600">{{ $stockMovement->fromBin->location }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($stockMovement->toBin)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">To Bin</label>
                                    <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                        <p class="text-green-900 font-semibold">{{ $stockMovement->toBin->code }}</p>
                                        <p class="text-sm text-green-600">{{ $stockMovement->toBin->location }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Reference Information --}}
            @if($stockMovement->reference_type || $stockMovement->reference_number)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-link text-blue-600 mr-2"></i>
                        Reference
                    </h2>

                    @if($stockMovement->reference_type)
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                            <p class="text-gray-900 font-semibold">{{ ucwords(str_replace('_', ' ', $stockMovement->reference_type)) }}</p>
                        </div>
                    @endif

                    @if($stockMovement->reference_number)
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Number</label>
                            <p class="text-gray-900 font-mono font-semibold">{{ $stockMovement->reference_number }}</p>
                        </div>
                    @endif

                    @if($stockMovement->reference_id)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">ID</label>
                            <p class="text-gray-900 font-mono">#{{ $stockMovement->reference_id }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clock text-blue-600 mr-2"></i>
                    Timeline
                </h2>

                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3"></div>
                        <div>
                            <p class="text-sm text-gray-500">Created At</p>
                            <p class="text-gray-900 font-medium">{{ $stockMovement->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt text-blue-600 mr-2"></i>
                    Quick Actions
                </h2>

                <div class="space-y-2">
                    <a href="{{ route('inventory.movements.by-product', $stockMovement->product) }}" class="block w-full px-4 py-2 text-center bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        <i class="fas fa-box mr-2"></i>View Product History
                    </a>
                    <a href="{{ route('inventory.movements.by-warehouse', $stockMovement->warehouse) }}" class="block w-full px-4 py-2 text-center bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition">
                        <i class="fas fa-warehouse mr-2"></i>View Warehouse History
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection