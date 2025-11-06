{{-- resources/views/inbound/shipments/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Shipment Details - ' . $shipment->shipment_number)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-truck-loading text-white"></i>
                    </div>
                    Shipment Details
                </h1>
                <p class="text-gray-600 mt-2">
                    Shipment: <span class="font-mono font-semibold text-blue-600">{{ $shipment->shipment_number }}</span>
                    <span class="mx-2">•</span>
                    {!! $shipment->status_badge !!}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                @if($shipment->can_edit)
                    <a href="{{ route('inbound.shipments.edit', $shipment) }}" class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl hover:from-yellow-600 hover:to-yellow-700 transition-all shadow-lg shadow-yellow-500/50 flex items-center">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endif
                <a href="{{ route('inbound.shipments.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl mb-6 shadow-sm animate-fade-in">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 shadow-sm animate-fade-in">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-circle text-white"></i>
                    </div>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Shipment Overview --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Shipment Overview
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Shipment Number --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-600 block mb-1">Shipment Number</label>
                            <p class="text-lg font-mono font-bold text-gray-900">{{ $shipment->shipment_number }}</p>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-600 block mb-1">Status</label>
                            <div>{!! $shipment->status_badge !!}</div>
                        </div>

                        {{-- Purchase Order --}}
                        @if($shipment->purchaseOrder)
                        <div>
                            <label class="text-sm font-semibold text-gray-600 block mb-1">
                                <i class="fas fa-file-invoice text-gray-400 mr-1"></i>Purchase Order
                            </label>
                            <p class="text-base font-semibold text-gray-900">{{ $shipment->purchaseOrder->po_number }}</p>
                        </div>
                        @endif

                        {{-- Dock Number --}}
                        @if($shipment->dock_number)
                        <div>
                            <label class="text-sm font-semibold text-gray-600 block mb-1">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>Dock Number
                            </label>
                            <p class="text-base font-semibold text-gray-900">{{ $shipment->dock_number }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Supplier & Warehouse Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Supplier --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                        <h3 class="text-base font-bold text-gray-900 flex items-center">
                            <i class="fas fa-building text-blue-600 mr-2"></i>
                            Supplier Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                <i class="fas fa-building text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">{{ $shipment->supplier->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $shipment->supplier->code }}</p>
                            </div>
                        </div>
                        
                        @if($shipment->supplier->contact_person)
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-user w-5 text-gray-400"></i>
                                <span>{{ $shipment->supplier->contact_person }}</span>
                            </div>
                            @if($shipment->supplier->contact_phone)
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-phone w-5 text-gray-400"></i>
                                <span>{{ $shipment->supplier->contact_phone }}</span>
                            </div>
                            @endif
                            @if($shipment->supplier->email)
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-envelope w-5 text-gray-400"></i>
                                <span>{{ $shipment->supplier->email }}</span>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Warehouse --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                        <h3 class="text-base font-bold text-gray-900 flex items-center">
                            <i class="fas fa-warehouse text-purple-600 mr-2"></i>
                            Warehouse Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                <i class="fas fa-warehouse text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">{{ $shipment->warehouse->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $shipment->warehouse->code }}</p>
                            </div>
                        </div>
                        
                        @if($shipment->warehouse->address)
                        <div class="space-y-2 text-sm">
                            <div class="flex items-start text-gray-700">
                                <i class="fas fa-map-marker-alt w-5 text-gray-400 mt-0.5"></i>
                                <span>{{ $shipment->warehouse->address }}</span>
                            </div>
                            @if($shipment->warehouse->phone)
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-phone w-5 text-gray-400"></i>
                                <span>{{ $shipment->warehouse->phone }}</span>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Dates & Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                        Timeline & Dates
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {{-- Scheduled Date --}}
                        @if($shipment->scheduled_date)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-check text-blue-600"></i>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 block">Scheduled Date</label>
                                <p class="text-sm font-bold text-gray-900">{{ $shipment->scheduled_date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $shipment->scheduled_date->format('H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        {{-- Shipment Date --}}
                        @if($shipment->shipment_date)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-shipping-fast text-purple-600"></i>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 block">Shipment Date</label>
                                <p class="text-sm font-bold text-gray-900">{{ $shipment->shipment_date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $shipment->shipment_date->format('H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        {{-- Arrival Date --}}
                        @if($shipment->arrival_date)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box-open text-yellow-600"></i>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 block">Arrival Date</label>
                                <p class="text-sm font-bold text-gray-900">{{ $shipment->arrival_date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $shipment->arrival_date->format('H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        {{-- Unloading Start --}}
                        @if($shipment->unloading_start)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-hourglass-start text-orange-600"></i>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 block">Unloading Start</label>
                                <p class="text-sm font-bold text-gray-900">{{ $shipment->unloading_start->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $shipment->unloading_start->format('H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        {{-- Unloading End --}}
                        @if($shipment->unloading_end)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-hourglass-end text-teal-600"></i>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 block">Unloading End</label>
                                <p class="text-sm font-bold text-gray-900">{{ $shipment->unloading_end->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $shipment->unloading_end->format('H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        {{-- Completed At --}}
                        @if($shipment->completed_at)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 block">Completed At</label>
                                <p class="text-sm font-bold text-gray-900">{{ $shipment->completed_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $shipment->completed_at->format('H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Shipment Quantities --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-boxes text-purple-600 mr-2"></i>
                        Shipment Quantities
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Pallets --}}
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-pallet text-white text-xl"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-semibold text-blue-600 uppercase">Pallets</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-blue-900">Expected:</span>
                                    <span class="text-sm font-bold text-blue-900">{{ $shipment->expected_pallets ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-blue-900">Received:</span>
                                    <span class="text-lg font-bold text-blue-700">{{ $shipment->received_pallets ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Boxes --}}
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-box text-white text-xl"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-semibold text-purple-600 uppercase">Boxes</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-purple-900">Expected:</span>
                                    <span class="text-sm font-bold text-purple-900">{{ $shipment->expected_boxes ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-purple-900">Received:</span>
                                    <span class="text-lg font-bold text-purple-700">{{ $shipment->received_boxes ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Weight --}}
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-weight text-white text-xl"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-semibold text-green-600 uppercase">Weight (kg)</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-green-900">Expected:</span>
                                    <span class="text-sm font-bold text-green-900">{{ number_format($shipment->expected_weight ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-green-900">Actual:</span>
                                    <span class="text-lg font-bold text-green-700">{{ number_format($shipment->actual_weight ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    @if($shipment->expected_pallets)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700">Receiving Progress</span>
                            <span class="text-sm font-bold text-blue-600">{{ $shipment->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-4 rounded-full transition-all duration-500 shadow-sm flex items-center justify-end pr-2" style="width: {{ min($shipment->progress_percentage, 100) }}%">
                                @if($shipment->progress_percentage >= 20)
                                <span class="text-xs font-bold text-white">{{ $shipment->progress_percentage }}%</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Vehicle & Driver Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-truck text-green-600 mr-2"></i>
                        Vehicle & Driver Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Vehicle Info --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-truck-pickup text-green-600 mr-2"></i>Vehicle Details
                            </h4>
                            <div class="space-y-3">
                                @if($shipment->vehicle_number)
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-hashtag w-5 text-gray-400"></i>
                                        <span class="font-semibold">{{ $shipment->vehicle_number }}</span>
                                    </div>
                                    @if($shipment->vehicle_type)
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-tag w-5 text-gray-400"></i>
                                        <span class="capitalize">{{ $shipment->vehicle_type }}</span>
                                    </div>
                                    @endif
                                @else
                                    <p class="text-gray-500 italic">No vehicle information</p>
                                @endif

                                @if($shipment->container_number)
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-container-storage w-5 text-gray-400"></i>
                                    <span>{{ $shipment->container_number }}</span>
                                </div>
                                @endif

                                @if($shipment->seal_number)
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-lock w-5 text-gray-400"></i>
                                    <span>{{ $shipment->seal_number }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Driver Info --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-user text-green-600 mr-2"></i>Driver Details
                            </h4>
                            <div class="space-y-3">
                                @if($shipment->driver_name)
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-user w-5 text-gray-400"></i>
                                        <span class="font-semibold">{{ $shipment->driver_name }}</span>
                                    </div>
                                    @if($shipment->driver_phone)
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-phone w-5 text-gray-400"></i>
                                        <span>{{ $shipment->driver_phone }}</span>
                                    </div>
                                    @endif
                                    @if($shipment->driver_id_number)
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-id-card w-5 text-gray-400"></i>
                                        <span>{{ $shipment->driver_id_number }}</span>
                                    </div>
                                    @endif
                                @else
                                    <p class="text-gray-500 italic">No driver information</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shipping Documents --}}
            @if($shipment->bill_of_lading || $shipment->packing_list)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-indigo-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-file-alt text-indigo-600 mr-2"></i>
                        Shipping Documents
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($shipment->bill_of_lading)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-invoice text-indigo-600"></i>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 block">Bill of Lading</label>
                                <p class="text-sm font-bold text-gray-900">{{ $shipment->bill_of_lading }}</p>
                            </div>
                        </div>
                        @endif

                        @if($shipment->packing_list)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-clipboard-list text-indigo-600"></i>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 block">Packing List</label>
                                <p class="text-sm font-bold text-gray-900">{{ $shipment->packing_list }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Inspection Information --}}
            @if($shipment->inspection_result)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4 border-b border-yellow-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-clipboard-check text-yellow-600 mr-2"></i>
                        Inspection Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-semibold text-gray-600 block mb-1">Inspection Result</label>
                            <p class="text-base font-bold text-gray-900 capitalize">
                                @if($shipment->inspection_result === 'passed')
                                    <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Passed</span>
                                @elseif($shipment->inspection_result === 'failed')
                                    <span class="text-red-600"><i class="fas fa-times-circle mr-1"></i>Failed</span>
                                @else
                                    <span class="text-yellow-600"><i class="fas fa-exclamation-circle mr-1"></i>Partial</span>
                                @endif
                            </p>
                        </div>

                        @if($shipment->inspectedBy)
                        <div>
                            <label class="text-sm font-semibold text-gray-600 block mb-1">Inspected By</label>
                            <p class="text-base font-bold text-gray-900">{{ $shipment->inspectedBy->name }}</p>
                        </div>
                        @endif

                        @if($shipment->has_damages)
                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-600 block mb-1">Damage Description</label>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <p class="text-sm text-red-900">{{ $shipment->damage_description }}</p>
                            </div>
                        </div>
                        @endif

                        @if($shipment->inspection_notes)
                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-600 block mb-1">Inspection Notes</label>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="text-sm text-gray-900">{{ $shipment->inspection_notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Notes --}}
            @if($shipment->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-sticky-note text-gray-600 mr-2"></i>
                        Additional Notes
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $shipment->notes }}</p>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Status Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 border-b">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-tasks mr-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($shipment->status === 'scheduled')
                        <form action="{{ route('inbound.shipments.mark-in-transit', $shipment) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all shadow-lg flex items-center justify-center font-semibold">
                                <i class="fas fa-shipping-fast mr-2"></i>
                                Mark In Transit
                            </button>
                        </form>
                    @endif

                    @if(in_array($shipment->status, ['scheduled', 'in_transit']))
                        <button onclick="document.getElementById('markArrivedModal').classList.remove('hidden')" class="w-full px-4 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl hover:from-yellow-600 hover:to-yellow-700 transition-all shadow-lg flex items-center justify-center font-semibold">
                            <i class="fas fa-box-open mr-2"></i>
                            Mark Arrived
                        </button>
                    @endif

                    @if($shipment->status === 'arrived')
                        <form action="{{ route('inbound.shipments.start-unloading', $shipment) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg flex items-center justify-center font-semibold">
                                <i class="fas fa-dolly mr-2"></i>
                                Start Unloading
                            </button>
                        </form>
                    @endif

                    @if($shipment->status === 'unloading')
                        <form action="{{ route('inbound.shipments.start-inspection', $shipment) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-xl hover:from-teal-600 hover:to-teal-700 transition-all shadow-lg flex items-center justify-center font-semibold">
                                <i class="fas fa-clipboard-check mr-2"></i>
                                Start Inspection
                            </button>
                        </form>
                    @endif

                    @if($shipment->status === 'inspection')
                        <button onclick="document.getElementById('completeInspectionModal').classList.remove('hidden')" class="w-full px-4 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-lg flex items-center justify-center font-semibold">
                            <i class="fas fa-check-double mr-2"></i>
                            Complete Inspection
                        </button>
                    @endif

                    @if(in_array($shipment->status, ['inspection', 'received']))
                        <button onclick="document.getElementById('completeShipmentModal').classList.remove('hidden')" class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-all shadow-lg flex items-center justify-center font-semibold">
                            <i class="fas fa-check-circle mr-2"></i>
                            Complete Shipment
                        </button>
                    @endif

                    @if(!in_array($shipment->status, ['completed', 'cancelled']))
                        <button onclick="document.getElementById('cancelShipmentModal').classList.remove('hidden')" class="w-full px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all shadow-lg flex items-center justify-center font-semibold">
                            <i class="fas fa-times-circle mr-2"></i>
                            Cancel Shipment
                        </button>
                    @endif
                </div>
            </div>

            {{-- Shipment Summary --}}
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl border border-indigo-200 overflow-hidden">
                <div class="px-6 py-4 bg-indigo-600 border-b border-indigo-700">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Shipment Info
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-indigo-200">
                        <span class="text-sm font-semibold text-indigo-900">Created:</span>
                        <span class="text-sm text-indigo-700">{{ $shipment->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-indigo-200">
                        <span class="text-sm font-semibold text-indigo-900">Created By:</span>
                        <span class="text-sm text-indigo-700">{{ $shipment->createdBy->name ?? '-' }}</span>
                    </div>
                    @if($shipment->receivedBy)
                    <div class="flex justify-between items-center pb-3 border-b border-indigo-200">
                        <span class="text-sm font-semibold text-indigo-900">Received By:</span>
                        <span class="text-sm text-indigo-700">{{ $shipment->receivedBy->name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-indigo-900">Last Update:</span>
                        <span class="text-sm text-indigo-700">{{ $shipment->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Purchase Order Items --}}
            @if($shipment->purchaseOrder && $shipment->purchaseOrder->items->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-list mr-2 text-blue-600"></i>
                        PO Items ({{ $shipment->purchaseOrder->items->count() }} items)
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($shipment->purchaseOrder->items as $item)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl hover:from-blue-50 hover:to-blue-100 transition-all border border-gray-200 hover:border-blue-300">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $item->product->name }}</p>
                                        <p class="text-xs text-gray-500 flex items-center mt-0.5">
                                            <i class="fas fa-barcode mr-1"></i>
                                            {{ $item->product->sku }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <div class="flex items-center justify-end mb-1">
                                    <span class="text-2xl font-bold text-blue-600">{{ number_format($item->quantity) }}</span>
                                    <span class="text-xs text-gray-500 ml-2">
                                        @if(is_object($item->unit))
                                            {{ $item->unit->short_code ?? $item->unit->name ?? 'Unit' }}
                                        @elseif(is_string($item->unit))
                                            {{ $item->unit }}
                                        @else
                                            Unit
                                        @endif
                                    </span>
                                </div>
                                @if($item->unit_price)
                                <p class="text-xs text-gray-500">
                                    @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </p>
                                @endif
                                @if($item->total_price)
                                <p class="text-sm font-semibold text-gray-700 mt-1">
                                    Total: Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Summary --}}
                    @if($shipment->purchaseOrder->items->count() > 0)
                    <div class="mt-6 pt-6 border-t border-gray-300">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Total Items:</span>
                            <span class="text-lg font-bold text-blue-600">{{ $shipment->purchaseOrder->items->count() }} items</span>
                        </div>
                        @if($shipment->purchaseOrder->items->sum('total_price'))
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-sm font-semibold text-gray-700">Total Value:</span>
                            <span class="text-lg font-bold text-green-600">Rp {{ number_format($shipment->purchaseOrder->items->sum('total_price'), 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

    </div>

</div>

{{-- Mark Arrived Modal --}}
<div id="markArrivedModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4 rounded-t-2xl">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-box-open mr-2"></i>
                Mark Shipment as Arrived
            </h3>
        </div>
        <form action="{{ route('inbound.shipments.mark-arrived', $shipment) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Received Pallets</label>
                    <input type="number" name="received_pallets" min="0" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 shadow-sm" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Received Boxes</label>
                    <input type="number" name="received_boxes" min="0" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 shadow-sm" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Actual Weight (kg)</label>
                    <input type="number" name="actual_weight" min="0" step="0.01" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 shadow-sm" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 shadow-sm" placeholder="Additional notes..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('markArrivedModal').classList.add('hidden')" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition font-semibold">
                    Confirm Arrival
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Complete Inspection Modal --}}
<div id="completeInspectionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4 rounded-t-2xl">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-clipboard-check mr-2"></i>
                Complete Inspection
            </h3>
        </div>
        <form action="{{ route('inbound.shipments.complete-inspection', $shipment) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Inspection Result <span class="text-red-500">*</span></label>
                    <select name="inspection_result" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                        <option value="">Select Result</option>
                        <option value="passed">Passed</option>
                        <option value="failed">Failed</option>
                        <option value="partial">Partial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Inspection Notes</label>
                    <textarea name="inspection_notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="Inspection findings..."></textarea>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="has_damages" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm font-semibold text-gray-700">Has Damages</span>
                    </label>
                </div>
                <div id="damageDescriptionField" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Damage Description <span class="text-red-500">*</span></label>
                    <textarea name="damage_description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm" placeholder="Describe damages..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('completeInspectionModal').classList.add('hidden')" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-lg hover:from-indigo-600 hover:to-indigo-700 transition font-semibold">
                    Complete Inspection
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Complete Shipment Modal --}}
<div id="completeShipmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 rounded-t-2xl">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                Complete Shipment
            </h3>
        </div>
        <form action="{{ route('inbound.shipments.complete', $shipment) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Final Received Pallets <span class="text-red-500">*</span></label>
                    <input type="number" name="received_pallets" min="0" value="{{ $shipment->received_pallets }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Final Received Boxes</label>
                    <input type="number" name="received_boxes" min="0" value="{{ $shipment->received_boxes }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Final Actual Weight (kg)</label>
                    <input type="number" name="actual_weight" min="0" step="0.01" value="{{ $shipment->actual_weight }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Final Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm" placeholder="Final notes...">{{ $shipment->notes }}</textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('completeShipmentModal').classList.add('hidden')" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition font-semibold">
                    Complete Shipment
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Cancel Shipment Modal --}}
<div id="cancelShipmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-2xl">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-times-circle mr-2"></i>
                Cancel Shipment
            </h3>
        </div>
        <form action="{{ route('inbound.shipments.cancel', $shipment) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-red-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Are you sure you want to cancel this shipment? This action cannot be undone.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cancellation Reason <span class="text-red-500">*</span></label>
                    <textarea name="notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm" placeholder="Please provide a reason for cancellation (min. 10 characters)..." required></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('cancelShipmentModal').classList.add('hidden')" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Close
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition font-semibold">
                    Yes, Cancel Shipment
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide damage description field
    document.querySelector('input[name="has_damages"]')?.addEventListener('change', function() {
        const damageField = document.getElementById('damageDescriptionField');
        if (this.checked) {
            damageField.classList.remove('hidden');
            damageField.querySelector('textarea').setAttribute('required', 'required');
        } else {
            damageField.classList.add('hidden');
            damageField.querySelector('textarea').removeAttribute('required');
        }
    });
</script>
@endpush
@endsection