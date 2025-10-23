@extends('layouts.app')

@section('title', 'Delivery Tracking')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-route text-purple-600 mr-2"></i>
                Delivery Tracking
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $deliveryOrder->do_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('outbound.delivery-orders.show', $deliveryOrder) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <a href="{{ route('outbound.delivery-orders.print', $deliveryOrder) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" target="_blank">
                <i class="fas fa-print mr-2"></i>Print
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Tracking Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">
                    <i class="fas fa-timeline mr-2 text-purple-600"></i>Tracking Timeline
                </h2>
                
                <div class="relative">
                    @foreach($timeline as $index => $item)
                        <div class="flex items-start mb-8 last:mb-0 relative">
                            {{-- Vertical Line --}}
                            @if(!$loop->last)
                                <div class="absolute left-5 top-12 bottom-0 w-0.5 {{ $item['active'] ? 'bg-blue-300' : 'bg-gray-300' }}"></div>
                            @endif
                            
                            {{-- Icon --}}
                            <div class="relative z-10 flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $item['active'] ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-500' }}">
                                <i class="fas {{ $item['icon'] }}"></i>
                            </div>
                            
                            {{-- Content --}}
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-semibold {{ $item['active'] ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $item['label'] }}
                                    </h3>
                                    @if($item['time'])
                                        <span class="text-sm {{ $item['active'] ? 'text-gray-600' : 'text-gray-400' }}">
                                            {{ $item['time']->format('d M Y, H:i') }}
                                        </span>
                                    @endif
                                </div>
                                
                                @if($item['status'] === 'prepared')
                                    <p class="text-sm text-gray-500 mt-1">Order prepared and ready for loading</p>
                                @elseif($item['status'] === 'loaded')
                                    <p class="text-sm text-gray-500 mt-1">Items loaded onto vehicle</p>
                                    @if($deliveryOrder->vehicle)
                                        <p class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-truck mr-1"></i>{{ $deliveryOrder->vehicle->name }} - {{ $deliveryOrder->vehicle->plate_number ?? '' }}
                                        </p>
                                    @endif
                                @elseif($item['status'] === 'in_transit')
                                    <p class="text-sm text-gray-500 mt-1">Vehicle departed and on the way to destination</p>
                                    @if($deliveryOrder->driver)
                                        <p class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-user mr-1"></i>Driver: {{ $deliveryOrder->driver->name }}
                                        </p>
                                    @endif
                                @elseif($item['status'] === 'delivered')
                                    <p class="text-sm text-gray-500 mt-1">Successfully delivered to recipient</p>
                                    @if($deliveryOrder->received_by_name)
                                        <p class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-user-check mr-1"></i>Received by: {{ $deliveryOrder->received_by_name }}
                                        </p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Delivery Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>Delivery Details
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-warehouse text-purple-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->warehouse->name }}</p>
                                <p class="text-xs text-gray-500">{{ $deliveryOrder->warehouse->address ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-map-marker-alt text-green-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->customer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $deliveryOrder->shipping_address ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Scheduled Delivery</label>
                        <p class="text-sm font-semibold text-gray-900">
                            <i class="fas fa-calendar-alt text-blue-500 mr-1"></i>
                            {{ $deliveryOrder->delivery_date->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Recipient</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->recipient_name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $deliveryOrder->recipient_phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Shipment Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-boxes mr-2 text-indigo-600"></i>Shipment Information
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($deliveryOrder->total_boxes) }}</div>
                        <div class="text-xs text-gray-600 mt-1">Total Boxes</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($deliveryOrder->total_weight_kg, 2) }}</div>
                        <div class="text-xs text-gray-600 mt-1">Weight (kg)</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">
                            @if($deliveryOrder->departed_at && $deliveryOrder->delivered_at)
                                {{ $deliveryOrder->departed_at->diffInHours($deliveryOrder->delivered_at) }}h
                            @else
                                -
                            @endif
                        </div>
                        <div class="text-xs text-gray-600 mt-1">Transit Time</div>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div class="text-sm font-bold text-orange-600">{{ ucfirst(str_replace('_', ' ', $deliveryOrder->status)) }}</div>
                        <div class="text-xs text-gray-600 mt-1">Current Status</div>
                    </div>
                </div>
            </div>

            {{-- Map Placeholder --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-map mr-2 text-red-600"></i>Delivery Route Map
                </h2>
                
                <div class="bg-gray-100 rounded-lg h-64 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500 text-sm">Map integration coming soon</p>
                        <p class="text-gray-400 text-xs mt-1">Track real-time location of your delivery</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Current Status Card --}}
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
                <h3 class="text-sm font-semibold mb-3 opacity-90">Current Status</h3>
                <div class="text-3xl font-bold mb-2">{{ ucfirst(str_replace('_', ' ', $deliveryOrder->status)) }}</div>
                {!! $deliveryOrder->status_badge !!}
                
                @if($deliveryOrder->status === 'in_transit')
                    <div class="mt-4 pt-4 border-t border-purple-400">
                        <p class="text-sm opacity-90">Estimated Time</p>
                        <p class="text-lg font-semibold">{{ $deliveryOrder->delivery_date->format('H:i') }}</p>
                    </div>
                @endif
            </div>

            {{-- Vehicle & Driver Info --}}
            @if($deliveryOrder->vehicle || $deliveryOrder->driver)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-600 mb-4">
                        <i class="fas fa-truck mr-2"></i>Vehicle & Driver
                    </h3>
                    
                    @if($deliveryOrder->vehicle)
                        <div class="mb-4">
                            <label class="block text-xs text-gray-500 mb-1">Vehicle</label>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-truck text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->vehicle->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $deliveryOrder->vehicle->plate_number ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($deliveryOrder->driver)
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Driver</label>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $deliveryOrder->driver->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $deliveryOrder->driver->phone ?? $deliveryOrder->driver->email }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-600 mb-4">Quick Actions</h3>
                
                <div class="space-y-2">
                    <a href="{{ route('outbound.delivery-orders.show', $deliveryOrder) }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm text-center">
                        <i class="fas fa-eye mr-2"></i>View Details
                    </a>
                    
                    <a href="{{ route('outbound.delivery-orders.print', $deliveryOrder) }}" class="block w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm text-center" target="_blank">
                        <i class="fas fa-print mr-2"></i>Print DO
                    </a>

                    @if($deliveryOrder->status === 'in_transit' || $deliveryOrder->status === 'delivered')
                        <a href="{{ route('outbound.delivery-orders.proof', $deliveryOrder) }}" class="block w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm text-center">
                            <i class="fas fa-camera mr-2"></i>Upload Proof
                        </a>
                    @endif
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-3">
                    <i class="fas fa-phone mr-2"></i>Need Help?
                </h3>
                <p class="text-xs text-blue-800 mb-3">Contact our support team for assistance</p>
                <div class="space-y-2 text-xs text-blue-700">
                    <div class="flex items-center">
                        <i class="fas fa-phone-alt w-4 mr-2"></i>
                        <span>+62 xxx xxxx xxxx</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope w-4 mr-2"></i>
                        <span>support@warehouse.com</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection