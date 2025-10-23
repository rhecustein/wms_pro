@extends('layouts.app')

@section('title', 'Warehouse Details - ' . $warehouse->name)

@section('content')
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-warehouse text-blue-600 mr-3"></i>{{ $warehouse->name }}
            </h1>
            <p class="text-gray-600 mt-1">{{ $warehouse->code }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('master.warehouses.layout', $warehouse) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-th mr-2"></i>View Layout
            </a>
            <a href="{{ route('master.warehouses.edit', $warehouse) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('master.warehouses.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    {{-- STATISTICS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        {{-- Total Bins --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-cubes text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Total Bins</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_bins']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Occupied Bins --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-check-circle text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Occupied</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['occupied_bins']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Utilization --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-chart-pie text-white text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Utilization</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['utilization'] }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- WAREHOUSE DETAILS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Basic Info --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-info-circle mr-2"></i>Basic Information
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Code:</span>
                    <span class="text-gray-900 font-semibold">{{ $warehouse->code }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Name:</span>
                    <span class="text-gray-900 font-semibold">{{ $warehouse->name }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Manager:</span>
                    <span class="text-gray-900 font-semibold">{{ $warehouse->manager->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Status:</span>
                    <span>
                        @if($warehouse->is_active)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Inactive
                            </span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600 font-medium">Created:</span>
                    <span class="text-gray-900">{{ $warehouse->created_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Location Info --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-map-marker-alt mr-2"></i>Location Details
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="py-3 border-b border-gray-200">
                    <span class="text-gray-600 font-medium block mb-2">Address:</span>
                    <span class="text-gray-900">{{ $warehouse->full_address }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Phone:</span>
                    <span class="text-gray-900">{{ $warehouse->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Email:</span>
                    <span class="text-gray-900">{{ $warehouse->email ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600 font-medium">Coordinates:</span>
                    <span class="text-gray-900">
                        @if($warehouse->latitude && $warehouse->longitude)
                            {{ $warehouse->latitude }}, {{ $warehouse->longitude }}
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- RECENT ACTIVITIES --}}
    <div class="bg-white overflow-hidden shadow-lg rounded-xl">
        <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-700 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-history mr-2"></i>Recent Activities
            </h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Time</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $activity->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                {{ $activity->causer->name ?? 'System' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $activity->description }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-sm text-center text-gray-500">
                                No recent activities
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection