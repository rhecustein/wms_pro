{{-- resources/views/inventory/pallets/history.blade.php --}}
@extends('layouts.app')

@section('title', 'Pallet History')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <a href="{{ route('inventory.pallets.show', $pallet) }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i>Back to Pallet Details
        </a>
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-history text-2xl text-purple-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $pallet->pallet_number }}</h1>
                    <p class="text-sm text-gray-600 mt-1">Movement & Activity History</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pallet Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Current Status</p>
                    <p class="text-lg font-bold text-gray-800">{{ ucfirst(str_replace('_', ' ', $pallet->status)) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-info-circle text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Condition</p>
                    <p class="text-lg font-bold text-green-800">{{ ucfirst($pallet->condition) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Current Weight</p>
                    <p class="text-lg font-bold text-purple-800">{{ $pallet->current_weight_kg }} kg</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-weight-hanging text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Last Used</p>
                    <p class="text-sm font-bold text-gray-800">
                        {{ $pallet->last_used_date ? $pallet->last_used_date->diffForHumans() : 'Never' }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-stream text-blue-600 mr-2"></i>
            Activity Timeline
        </h2>

        <div class="space-y-6">
            
            {{-- Current State --}}
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-circle text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-blue-900">Current State</h3>
                            <span class="text-sm text-blue-600">{{ $pallet->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-600">Status:</span>
                                <span class="font-medium text-blue-900">{{ ucfirst(str_replace('_', ' ', $pallet->status)) }}</span>
                            </div>
                            <div>
                                <span class="text-blue-600">Condition:</span>
                                <span class="font-medium text-blue-900">{{ ucfirst($pallet->condition) }}</span>
                            </div>
                            @if($pallet->storageBin)
                                <div class="col-span-2">
                                    <span class="text-blue-600">Location:</span>
                                    <span class="font-medium text-blue-900">{{ $pallet->storageBin->code }} - {{ $pallet->storageBin->warehouse->name }}</span>
                                </div>
                            @endif
                        </div>
                        @if($pallet->updatedBy)
                            <p class="text-xs text-blue-600 mt-2">
                                <i class="fas fa-user mr-1"></i>Updated by {{ $pallet->updatedBy->name }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Created Event --}}
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-plus-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-green-900">Pallet Created</h3>
                            <span class="text-sm text-green-600">{{ $pallet->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="text-sm text-green-700">
                            Pallet {{ $pallet->pallet_number }} was added to the inventory system
                        </p>
                        @if($pallet->createdBy)
                            <p class="text-xs text-green-600 mt-2">
                                <i class="fas fa-user mr-1"></i>Created by {{ $pallet->createdBy->name }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Message --}}
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-info-circle text-gray-400"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            Detailed movement history will appear here when the pallet is used in warehouse operations such as receiving, picking, or transfers.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Additional Information --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        
        {{-- Pallet Specifications --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-ruler-combined text-blue-600 mr-2"></i>
                Specifications
            </h2>

            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Type</span>
                    <span class="font-semibold text-gray-900">{{ ucfirst($pallet->pallet_type) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Dimensions</span>
                    <span class="font-semibold text-gray-900">{{ $pallet->width_cm }}×{{ $pallet->depth_cm }}×{{ $pallet->height_cm }} cm</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Volume</span>
                    <span class="font-semibold text-gray-900">{{ number_format($pallet->volume, 2) }} m³</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Max Weight</span>
                    <span class="font-semibold text-gray-900">{{ $pallet->max_weight_kg }} kg</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Current Weight</span>
                    <span class="font-semibold text-gray-900">{{ $pallet->current_weight_kg }} kg</span>
                </div>
            </div>
        </div>

        {{-- Tracking Information --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-barcode text-blue-600 mr-2"></i>
                Tracking Information
            </h2>

            <div class="space-y-3">
                <div class="py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600 block mb-1">Pallet Number</span>
                    <span class="font-mono font-semibold text-gray-900">{{ $pallet->pallet_number }}</span>
                </div>
                @if($pallet->barcode)
                    <div class="py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600 block mb-1">Barcode</span>
                        <span class="font-mono font-semibold text-gray-900">{{ $pallet->barcode }}</span>
                    </div>
                @endif
                @if($pallet->qr_code)
                    <div class="py-2">
                        <span class="text-sm text-gray-600 block mb-1">QR Code</span>
                        <span class="font-mono font-semibold text-gray-900">{{ $pallet->qr_code }}</span>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection