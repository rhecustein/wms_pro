{{-- resources/views/equipment/vehicles/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Vehicle Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-truck text-blue-600 mr-2"></i>
                Vehicle Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">Complete information about {{ $vehicle->vehicle_number }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('equipment.vehicles.edit', $vehicle) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('equipment.vehicles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Basic Information
                    </h3>
                    {!! $vehicle->status_badge !!}
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Vehicle Number</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $vehicle->vehicle_number }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">License Plate</label>
                        <p class="text-lg font-mono font-semibold text-gray-900">{{ $vehicle->license_plate }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Vehicle Type</label>
                        <div class="mt-1">
                            {!! $vehicle->type_badge !!}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Ownership</label>
                        <div class="mt-1">
                            {!! $vehicle->ownership_badge !!}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Brand</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $vehicle->brand ?: '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Model</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $vehicle->model ?: '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Year</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $vehicle->year ?: '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fuel Type</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $vehicle->fuel_type ?: '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Capacity Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-weight text-purple-600 mr-2"></i>
                    Capacity Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-purple-700">Weight Capacity</label>
                            <i class="fas fa-weight-hanging text-purple-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-purple-900">
                            {{ $vehicle->capacity_kg ? number_format($vehicle->capacity_kg, 0) . ' kg' : '-' }}
                        </p>
                    </div>

                    <div class="bg-indigo-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-indigo-700">Volume Capacity</label>
                            <i class="fas fa-cube text-indigo-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-indigo-900">
                            {{ $vehicle->capacity_cbm ? number_format($vehicle->capacity_cbm, 2) . ' m³' : '-' }}
                        </p>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-blue-700">Odometer</label>
                            <i class="fas fa-tachometer-alt text-blue-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-blue-900">
                            {{ number_format($vehicle->odometer_km, 0) }} km
                        </p>
                    </div>
                </div>
            </div>

            {{-- Maintenance Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-tools text-yellow-600 mr-2"></i>
                    Maintenance Information
                </h3>
                
                <div class="space-y-4">
                    {{-- Maintenance Status Alert --}}
                    @php
                        $maintenanceStatus = $vehicle->maintenance_status;
                    @endphp
                    
                    @if($maintenanceStatus === 'overdue')
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-red-800 font-semibold">Maintenance Overdue!</p>
                                    <p class="text-red-700 text-sm">This vehicle's maintenance is past due. Please schedule immediately.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($maintenanceStatus === 'due_soon')
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-yellow-600 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-yellow-800 font-semibold">Maintenance Due Soon</p>
                                    <p class="text-yellow-700 text-sm">Maintenance is scheduled within the next 7 days.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($maintenanceStatus === 'scheduled')
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-green-800 font-semibold">Maintenance Scheduled</p>
                                    <p class="text-green-700 text-sm">Next maintenance is properly scheduled.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 border-l-4 border-gray-400 p-4 rounded-r-lg">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-gray-600 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-gray-800 font-semibold">No Maintenance Scheduled</p>
                                    <p class="text-gray-700 text-sm">Please schedule the next maintenance date.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Maintenance Dates --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-500 mb-2">
                                <i class="fas fa-calendar-check text-gray-400 mr-1"></i>
                                Last Maintenance
                            </label>
                            @if($vehicle->last_maintenance_date)
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $vehicle->last_maintenance_date->format('d M Y') }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $vehicle->last_maintenance_date->diffForHumans() }}
                                </p>
                            @else
                                <p class="text-lg font-semibold text-gray-400">Not recorded</p>
                            @endif
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-500 mb-2">
                                <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                Next Maintenance
                            </label>
                            @if($vehicle->next_maintenance_date)
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $vehicle->next_maintenance_date->format('d M Y') }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $vehicle->next_maintenance_date->diffForHumans() }}
                                </p>
                            @else
                                <p class="text-lg font-semibold text-gray-400">Not scheduled</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Notes --}}
            @if($vehicle->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-sticky-note text-green-600 mr-2"></i>
                    Additional Notes
                </h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 whitespace-pre-line">{{ $vehicle->notes }}</p>
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('equipment.vehicles.edit', $vehicle) }}" class="block w-full px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-center font-semibold">
                        <i class="fas fa-edit mr-2"></i>Edit Vehicle
                    </a>
                    
                    @if($vehicle->status === 'available')
                        <button class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-truck-moving mr-2"></i>Assign to Trip
                        </button>
                    @endif

                    @if($vehicle->status === 'in_use')
                        <button class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                            <i class="fas fa-check-circle mr-2"></i>Mark as Available
                        </button>
                    @endif

                    <button class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                        <i class="fas fa-tools mr-2"></i>Schedule Maintenance
                    </button>

                    <button class="w-full px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-semibold">
                        <i class="fas fa-print mr-2"></i>Print Details
                    </button>

                    <form action="{{ route('equipment.vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                            <i class="fas fa-trash mr-2"></i>Delete Vehicle
                        </button>
                    </form>
                </div>
            </div>

            {{-- Vehicle Statistics --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                    Statistics
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-day text-blue-600 mr-3"></i>
                            <span class="text-sm text-gray-700">Total Days</span>
                        </div>
                        <span class="text-lg font-bold text-blue-900">
                            {{ $vehicle->created_at->diffInDays(now()) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-route text-green-600 mr-3"></i>
                            <span class="text-sm text-gray-700">Total Trips</span>
                        </div>
                        <span class="text-lg font-bold text-green-900">0</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-tools text-purple-600 mr-3"></i>
                            <span class="text-sm text-gray-700">Maintenance Count</span>
                        </div>
                        <span class="text-lg font-bold text-purple-900">0</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-gas-pump text-yellow-600 mr-3"></i>
                            <span class="text-sm text-gray-700">Fuel Expenses</span>
                        </div>
                        <span class="text-lg font-bold text-yellow-900">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- Audit Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-gray-600 mr-2"></i>
                    Audit Information
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $vehicle->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-xs text-gray-600">{{ $vehicle->created_at->diffForHumans() }}</p>
                    </div>

                    @if($vehicle->creator)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Created By</label>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                <span class="text-xs font-semibold text-blue-600">
                                    {{ strtoupper(substr($vehicle->creator->name, 0, 2)) }}
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">{{ $vehicle->creator->name }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="border-t border-gray-200 pt-3">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $vehicle->updated_at->format('d M Y, H:i') }}</p>
                        <p class="text-xs text-gray-600">{{ $vehicle->updated_at->diffForHumans() }}</p>
                    </div>

                    @if($vehicle->updater)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Updated By</label>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                <span class="text-xs font-semibold text-green-600">
                                    {{ strtoupper(substr($vehicle->updater->name, 0, 2)) }}
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">{{ $vehicle->updater->name }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Vehicle Status Card --}}
            <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Vehicle Status</h3>
                    <i class="fas fa-truck text-3xl opacity-50"></i>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Current Status:</span>
                        <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                            {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Ownership:</span>
                        <span class="font-semibold">{{ ucfirst($vehicle->ownership) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Type:</span>
                        <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)) }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection