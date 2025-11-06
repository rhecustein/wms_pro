{{-- resources/views/inventory/movements/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock Movement Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <a href="{{ route('inventory.movements.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i>Back to Movements
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
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
                $typeColor = $typeColors[$stockMovement->movement_type] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <div class="mt-4 md:mt-0 flex gap-2">
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-lg font-medium {{ $typeColor }}">
                    <i class="fas fa-tag mr-2"></i>{{ ucfirst($stockMovement->movement_type) }}
                </span>
                <button onclick="window.print()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>
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
                        <p class="text-gray-900 font-semibold">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>
                            {{ $stockMovement->movement_date->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Movement Type</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $typeColor }}">
                            {{ ucfirst($stockMovement->movement_type) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Quantity</label>
                        <p class="text-gray-900 font-bold text-2xl">
                            {{ number_format($stockMovement->quantity) }} 
                            <span class="text-lg text-gray-600">{{ $stockMovement->unit_of_measure }}</span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Performed By</label>
                        @if($stockMovement->performedBy)
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-gray-900 font-semibold">{{ $stockMovement->performedBy->name }}</p>
                                    @if(isset($stockMovement->performedBy->email))
                                        <p class="text-xs text-gray-500">{{ $stockMovement->performedBy->email }}</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-robot text-gray-400"></i>
                                </div>
                                <span class="text-gray-600">System Generated</span>
                            </div>
                        @endif
                    </div>

                    @if($stockMovement->batch_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Batch Number</label>
                            <div class="bg-gray-50 px-3 py-2 rounded-lg">
                                <p class="text-gray-900 font-mono font-semibold">
                                    <i class="fas fa-boxes text-gray-400 mr-1"></i>
                                    {{ $stockMovement->batch_number }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($stockMovement->serial_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Serial Number</label>
                            <div class="bg-gray-50 px-3 py-2 rounded-lg">
                                <p class="text-gray-900 font-mono font-semibold">
                                    <i class="fas fa-barcode text-gray-400 mr-1"></i>
                                    {{ $stockMovement->serial_number }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                @if($stockMovement->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-500 mb-2">
                            <i class="fas fa-sticky-note mr-1"></i>Notes
                        </label>
                        <p class="text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $stockMovement->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Product Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-box text-blue-600 mr-2"></i>
                    Product Information
                </h2>

                <div class="flex items-start mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="w-16 h-16 bg-blue-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-box text-2xl text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900">{{ $stockMovement->product->name }}</h3>
                        <p class="text-sm text-gray-600 mb-2">SKU: <span class="font-mono font-semibold">{{ $stockMovement->product->sku }}</span></p>
                        <a href="{{ route('inventory.movements.by-product', $stockMovement->product) }}" 
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-history mr-1"></i>View Product Movement History
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @php
                        $productCategory = $stockMovement->product->category ?? null;
                        $categoryName = null;
                        
                        if ($productCategory) {
                            if (is_object($productCategory)) {
                                $categoryName = $productCategory->name ?? null;
                            } elseif (is_array($productCategory)) {
                                $categoryName = $productCategory['name'] ?? null;
                            } else {
                                $categoryName = $productCategory;
                            }
                        }
                    @endphp
                    
                    @if($categoryName)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                            <p class="text-gray-900 font-semibold">
                                <i class="fas fa-tag text-gray-400 mr-1"></i>
                                {{ $categoryName }}
                            </p>
                        </div>
                    @endif

                    @if(!empty($stockMovement->product->barcode))
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Barcode</label>
                            <p class="text-gray-900 font-mono font-semibold">
                                <i class="fas fa-qrcode text-gray-400 mr-1"></i>
                                {{ $stockMovement->product->barcode }}
                            </p>
                        </div>
                    @endif

                    @if(isset($stockMovement->product->unit_price))
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Unit Price</label>
                            <p class="text-gray-900 font-bold">
                                ${{ number_format($stockMovement->product->unit_price, 2) }}
                            </p>
                        </div>
                    @endif

                    @if(isset($stockMovement->product->current_stock))
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Current Stock</label>
                            <p class="text-gray-900 font-bold">
                                {{ number_format($stockMovement->product->current_stock) }} units
                            </p>
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
                        <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-900 font-bold text-lg">{{ $stockMovement->warehouse->name }}</p>
                                    <p class="text-sm text-gray-600">Code: <span class="font-mono">{{ $stockMovement->warehouse->code }}</span></p>
                                </div>
                                <a href="{{ route('inventory.movements.by-warehouse', $stockMovement->warehouse) }}" 
                                   class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                    <i class="fas fa-history mr-1"></i>View History
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($stockMovement->fromBin || $stockMovement->toBin)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($stockMovement->fromBin)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">
                                        <i class="fas fa-sign-out-alt mr-1"></i>From Bin Location
                                    </label>
                                    <div class="bg-red-50 border-2 border-red-300 p-4 rounded-lg">
                                        <p class="text-red-900 font-bold text-lg mb-1">{{ $stockMovement->fromBin->code }}</p>
                                        <p class="text-sm text-red-700">{{ $stockMovement->fromBin->location }}</p>
                                        @if(isset($stockMovement->fromBin->zone))
                                            <p class="text-xs text-red-600 mt-1">Zone: {{ $stockMovement->fromBin->zone }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($stockMovement->toBin)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">
                                        <i class="fas fa-sign-in-alt mr-1"></i>To Bin Location
                                    </label>
                                    <div class="bg-green-50 border-2 border-green-300 p-4 rounded-lg">
                                        <p class="text-green-900 font-bold text-lg mb-1">{{ $stockMovement->toBin->code }}</p>
                                        <p class="text-sm text-green-700">{{ $stockMovement->toBin->location }}</p>
                                        @if(isset($stockMovement->toBin->zone))
                                            <p class="text-xs text-green-600 mt-1">Zone: {{ $stockMovement->toBin->zone }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($stockMovement->fromBin && $stockMovement->toBin)
                            <div class="flex items-center justify-center py-4">
                                <div class="flex items-center gap-3 text-gray-400">
                                    <span class="text-sm font-medium">{{ $stockMovement->fromBin->code }}</span>
                                    <i class="fas fa-arrow-right text-2xl"></i>
                                    <span class="text-sm font-medium">{{ $stockMovement->toBin->code }}</span>
                                </div>
                            </div>
                        @endif
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                {{ ucwords(str_replace('_', ' ', $stockMovement->reference_type)) }}
                            </span>
                        </div>
                    @endif

                    @if($stockMovement->reference_number)
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Number</label>
                            <div class="bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                                <p class="text-gray-900 font-mono font-bold">{{ $stockMovement->reference_number }}</p>
                            </div>
                        </div>
                    @endif

                    @if($stockMovement->reference_id)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Reference ID</label>
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

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-3 h-3 bg-blue-600 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 mb-1">Movement Date</p>
                            <p class="text-gray-900 font-semibold">{{ $stockMovement->movement_date->format('d M Y, H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $stockMovement->movement_date->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-3 h-3 bg-green-600 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 mb-1">Created At</p>
                            <p class="text-gray-900 font-semibold">{{ $stockMovement->created_at->format('d M Y, H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $stockMovement->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if($stockMovement->updated_at && $stockMovement->updated_at->ne($stockMovement->created_at))
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-yellow-600 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-1">Last Updated</p>
                                <p class="text-gray-900 font-semibold">{{ $stockMovement->updated_at->format('d M Y, H:i') }}</p>
                                <p class="text-xs text-gray-400">{{ $stockMovement->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt text-blue-600 mr-2"></i>
                    Quick Actions
                </h2>

                <div class="space-y-2">
                    <a href="{{ route('inventory.movements.by-product', $stockMovement->product) }}" 
                       class="flex items-center w-full px-4 py-3 text-left bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition border border-blue-200">
                        <i class="fas fa-box mr-3 text-lg"></i>
                        <div>
                            <p class="font-semibold">View Product History</p>
                            <p class="text-xs text-blue-600">All movements for this product</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('inventory.movements.by-warehouse', $stockMovement->warehouse) }}" 
                       class="flex items-center w-full px-4 py-3 text-left bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition border border-purple-200">
                        <i class="fas fa-warehouse mr-3 text-lg"></i>
                        <div>
                            <p class="font-semibold">View Warehouse History</p>
                            <p class="text-xs text-purple-600">All movements in this warehouse</p>
                        </div>
                    </a>

                    <button onclick="window.print()" 
                            class="flex items-center w-full px-4 py-3 text-left bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition border border-gray-200">
                        <i class="fas fa-print mr-3 text-lg"></i>
                        <div>
                            <p class="font-semibold">Print Details</p>
                            <p class="text-xs text-gray-600">Print this movement record</p>
                        </div>
                    </button>
                </div>
            </div>

            {{-- Movement Statistics --}}
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    Movement Stats
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm opacity-90">Movement ID</span>
                        <span class="font-bold">#{{ $stockMovement->id }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm opacity-90">Type</span>
                        <span class="font-bold">{{ ucfirst($stockMovement->movement_type) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm opacity-90">Quantity Moved</span>
                        <span class="font-bold text-xl">{{ number_format($stockMovement->quantity) }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

{{-- Print Styles --}}
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background: white !important;
        }
    }
</style>
@endsection