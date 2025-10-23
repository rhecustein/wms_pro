{{-- resources/views/master/warehouses/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Warehouse Details - ' . $warehouse->name)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('master.warehouses.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-warehouse text-blue-600 mr-2"></i>
                    {{ $warehouse->name }}
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    <i class="fas fa-barcode mr-1"></i>
                    {{ $warehouse->code }}
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('master.warehouses.layout', $warehouse) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-map mr-2"></i>View Layout
            </a>
            <a href="{{ route('master.warehouses.edit', $warehouse) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            @if($warehouse->is_active)
                <form action="{{ route('master.warehouses.deactivate', $warehouse) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-pause mr-2"></i>Deactivate
                    </button>
                </form>
            @else
                <form action="{{ route('master.warehouses.activate', $warehouse) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-play mr-2"></i>Activate
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="space-y-6">

            {{-- Status Badge --}}
            <div class="flex items-center space-x-4">
                @if($warehouse->is_active)
                    <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-check-circle mr-2"></i>Active
                    </span>
                @else
                    <span class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-times-circle mr-2"></i>Inactive
                    </span>
                @endif
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-700 mb-1">Total Bins</p>
                            <p class="text-3xl font-bold text-blue-800">{{ number_format($stats['total_bins']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-blue-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-boxes text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-700 mb-1">Available Bins</p>
                            <p class="text-3xl font-bold text-green-800">{{ number_format($stats['available_bins']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-green-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-purple-700 mb-1">Occupied Bins</p>
                            <p class="text-3xl font-bold text-purple-800">{{ number_format($stats['occupied_bins']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-purple-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-box text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-orange-700 mb-1">Utilization</p>
                            <p class="text-3xl font-bold text-orange-800">{{ number_format($stats['utilization'], 1) }}%</p>
                        </div>
                        <div class="w-14 h-14 bg-orange-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-pie text-orange-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Warehouse Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 border-b border-blue-700">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-info-circle mr-2"></i>Warehouse Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse Code</label>
                            <p class="text-gray-900 font-semibold">{{ $warehouse->code }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse Name</label>
                            <p class="text-gray-900 font-semibold">{{ $warehouse->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                            <p class="text-gray-900">
                                @if($warehouse->manager)
                                    <i class="fas fa-user-tie text-blue-600 mr-1"></i>
                                    {{ $warehouse->manager->name }}
                                @else
                                    <span class="text-gray-400">Not assigned</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <p class="text-gray-900">
                                @if($warehouse->phone)
                                    <i class="fas fa-phone text-green-600 mr-1"></i>
                                    {{ $warehouse->phone }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <p class="text-gray-900">
                                @if($warehouse->email)
                                    <i class="fas fa-envelope text-blue-600 mr-1"></i>
                                    {{ $warehouse->email }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Area</label>
                            <p class="text-gray-900">
                                @if($warehouse->total_area_sqm)
                                    <i class="fas fa-ruler-combined text-purple-600 mr-1"></i>
                                    {{ number_format($warehouse->total_area_sqm, 2) }} m²
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <p class="text-gray-900">
                            @if($warehouse->full_address)
                                <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                                {{ $warehouse->full_address }}
                            @else
                                <span class="text-gray-400">Address not provided</span>
                            @endif
                        </p>
                    </div>

                    @if($warehouse->notes)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <p class="text-gray-900">{{ $warehouse->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Activities --}}
            @if($activities->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600 border-b border-purple-700">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-history mr-2"></i>Recent Activities
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($activities as $activity)
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-900">
                                        <span class="font-semibold">{{ $activity->causer->name ?? 'System' }}</span>
                                        {{ $activity->description }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

    </div>

</div>
@endsection