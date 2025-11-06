{{-- resources/views/equipment/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Equipment Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-tools text-blue-600 mr-2"></i>
                Equipment Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View complete equipment information</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('equipment.equipments.edit', $equipment) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('equipment.equipments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    {{-- Success Message --}}
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
            
            {{-- Equipment Header Card --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm text-blue-100 mb-2">Equipment Number</p>
                        <h2 class="text-3xl font-bold font-mono mb-4">{{ $equipment->equipment_number }}</h2>
                        <div class="flex flex-wrap gap-2">
                            {!! str_replace(['bg-blue-100', 'text-blue-800', 'bg-purple-100', 'text-purple-800', 'bg-green-100', 'text-green-800', 'bg-yellow-100', 'text-yellow-800'], 
                                           ['bg-white bg-opacity-20', 'text-white', 'bg-white bg-opacity-20', 'text-white', 'bg-white bg-opacity-20', 'text-white', 'bg-white bg-opacity-20', 'text-white'], 
                                           $equipment->type_badge) !!}
                            {!! str_replace(['bg-green-100', 'text-green-800', 'bg-blue-100', 'text-blue-800', 'bg-yellow-100', 'text-yellow-800', 'bg-red-100', 'text-red-800', 'bg-gray-100', 'text-gray-800'], 
                                           ['bg-white bg-opacity-20', 'text-white', 'bg-white bg-opacity-20', 'text-white', 'bg-white bg-opacity-20', 'text-white', 'bg-white bg-opacity-20', 'text-white', 'bg-white bg-opacity-20', 'text-white'], 
                                           $equipment->status_badge) !!}
                        </div>
                    </div>
                    <div class="w-20 h-20 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tools text-4xl"></i>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-blue-400">
                    <div>
                        <p class="text-sm text-blue-100 mb-1">Operating Hours</p>
                        <p class="text-xl font-bold">{{ number_format($equipment->operating_hours) }}h</p>
                    </div>
                    <div>
                        <p class="text-sm text-blue-100 mb-1">Created</p>
                        <p class="text-xl font-bold">{{ $equipment->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-blue-100 mb-1">Last Update</p>
                        <p class="text-xl font-bold">{{ $equipment->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Equipment Type</label>
                        <p class="text-base font-semibold text-gray-900">
                            {{ ucfirst(str_replace('_', ' ', $equipment->equipment_type)) }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <div class="mt-1">
                            {!! $equipment->status_badge !!}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Brand</label>
                        <p class="text-base font-semibold text-gray-900">{{ $equipment->brand ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Model</label>
                        <p class="text-base font-semibold text-gray-900">{{ $equipment->model ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Serial Number</label>
                        <p class="text-base font-semibold text-gray-900 font-mono">{{ $equipment->serial_number ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Warehouse Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-warehouse text-purple-600 mr-2"></i>
                    Warehouse Information
                </h3>
                
                <div class="flex items-center p-4 bg-purple-50 rounded-lg">
                    <div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-warehouse text-purple-600 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-gray-900">{{ $equipment->warehouse->name }}</h4>
                        <p class="text-sm text-gray-600">Code: <span class="font-mono font-semibold">{{ $equipment->warehouse->code }}</span></p>
                        @if($equipment->warehouse->address)
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $equipment->warehouse->address }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Maintenance Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-wrench text-yellow-600 mr-2"></i>
                    Maintenance Information
                </h3>
                
                <div class="space-y-4">
                    {{-- Maintenance Status --}}
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Maintenance Status</span>
                            {!! $equipment->maintenance_status_badge !!}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Last Maintenance Date</label>
                            @if($equipment->last_maintenance_date)
                                <div class="flex items-center text-base font-semibold text-gray-900">
                                    <i class="fas fa-calendar-check text-green-600 mr-2"></i>
                                    {{ $equipment->last_maintenance_date->format('d M Y') }}
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $equipment->last_maintenance_date->diffForHumans() }}
                                </p>
                            @else
                                <p class="text-base text-gray-400">Not recorded</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Next Maintenance Date</label>
                            @if($equipment->next_maintenance_date)
                                <div class="flex items-center text-base font-semibold text-gray-900">
                                    <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                                    {{ $equipment->next_maintenance_date->format('d M Y') }}
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $equipment->next_maintenance_date->diffForHumans() }}
                                </p>
                            @else
                                <p class="text-base text-gray-400">Not scheduled</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Operating Hours</label>
                        <div class="flex items-center">
                            <div class="flex-1">
                                <div class="flex items-center text-2xl font-bold text-gray-900">
                                    <i class="fas fa-clock text-blue-600 mr-2"></i>
                                    {{ number_format($equipment->operating_hours) }} hours
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Notes --}}
            @if($equipment->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-sticky-note text-green-600 mr-2"></i>
                    Additional Notes
                </h3>
                
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-700 whitespace-pre-line">{{ $equipment->notes }}</p>
                </div>
            </div>
            @endif

            {{-- Activity Log --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-gray-600 mr-2"></i>
                    Activity Log
                </h3>
                
                <div class="space-y-4">
                    @if($equipment->createdBy)
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-plus text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Equipment Created</p>
                            <p class="text-sm text-gray-600">
                                By {{ $equipment->createdBy->name }} • {{ $equipment->created_at->format('d M Y, H:i') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $equipment->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif

                    @if($equipment->updatedBy && $equipment->updated_at != $equipment->created_at)
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-edit text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Equipment Updated</p>
                            <p class="text-sm text-gray-600">
                                By {{ $equipment->updatedBy->name }} • {{ $equipment->updated_at->format('d M Y, H:i') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $equipment->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('equipment.equipments.edit', $equipment) }}" class="w-full px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Equipment
                    </a>
                    
                    <button onclick="window.print()" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>
                        Print Details
                    </button>
                    
                    <form action="{{ route('equipment.equipments.destroy', $equipment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this equipment? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Equipment
                        </button>
                    </form>
                </div>
            </div>

            {{-- Status Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Summary</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Equipment Status</span>
                        </div>
                        {!! $equipment->status_badge !!}
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Maintenance Status</span>
                        </div>
                        {!! $equipment->maintenance_status_badge !!}
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Operating Hours</span>
                            <span class="text-lg font-bold text-gray-900">{{ number_format($equipment->operating_hours) }}h</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Equipment Statistics --}}
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl border border-blue-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Statistics
                </h3>
                
                <div class="space-y-3">
                    <div class="bg-white rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Days Since Creation</span>
                            <span class="text-lg font-bold text-blue-600">{{ $equipment->created_at->diffInDays(now()) }}</span>
                        </div>
                    </div>

                    @if($equipment->last_maintenance_date)
                    <div class="bg-white rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Days Since Maintenance</span>
                            <span class="text-lg font-bold text-green-600">{{ $equipment->last_maintenance_date->diffInDays(now()) }}</span>
                        </div>
                    </div>
                    @endif

                    @if($equipment->next_maintenance_date)
                    <div class="bg-white rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Days Until Maintenance</span>
                            <span class="text-lg font-bold {{ $equipment->next_maintenance_date->isPast() ? 'text-red-600' : 'text-yellow-600' }}">
                                {{ abs($equipment->next_maintenance_date->diffInDays(now())) }}
                                @if($equipment->next_maintenance_date->isPast())
                                    <span class="text-xs">(Overdue)</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Equipment Type Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Equipment Type</h3>
                
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    @php
                        $typeIcons = [
                            'forklift' => 'fa-forklift',
                            'reach_truck' => 'fa-truck-loading',
                            'pallet_jack' => 'fa-dolly',
                            'scanner' => 'fa-barcode',
                        ];
                        $typeColors = [
                            'forklift' => 'text-blue-600',
                            'reach_truck' => 'text-purple-600',
                            'pallet_jack' => 'text-green-600',
                            'scanner' => 'text-yellow-600',
                        ];
                    @endphp
                    <i class="fas {{ $typeIcons[$equipment->equipment_type] ?? 'fa-tools' }} text-5xl {{ $typeColors[$equipment->equipment_type] ?? 'text-gray-600' }} mb-3"></i>
                    <p class="text-xl font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $equipment->equipment_type)) }}</p>
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
            background: white;
        }
    }
</style>
@endsection