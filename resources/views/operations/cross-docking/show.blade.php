{{-- resources/views/operations/cross-docking/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Cross Docking Order Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-exchange-alt text-blue-600 mr-2"></i>
                Cross Docking Order Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $crossDocking->cross_dock_number }}</p>
        </div>
        <div class="flex items-center space-x-2">
            @if($crossDocking->status === 'scheduled')
                <a href="{{ route('operations.cross-docking.edit', $crossDocking) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('operations.cross-docking.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            
            {{-- Status & Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-tasks text-blue-600 mr-2"></i>
                    Status & Timeline
                </h2>

                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-sm text-gray-600">Current Status:</span>
                        <div class="mt-1">{!! $crossDocking->status_badge !!}</div>
                    </div>
                    @if($crossDocking->duration)
                        <div class="text-right">
                            <span class="text-sm text-gray-600">Duration:</span>
                            <div class="text-lg font-semibold text-gray-900">{{ $crossDocking->duration }} minutes</div>
                        </div>
                    @endif
                </div>

                {{-- Process Timeline --}}
                <div class="relative">
                    <div class="absolute top-0 left-4 h-full w-0.5 bg-gray-200"></div>
                    
                    @php
                        $steps = [
                            ['status' => 'scheduled', 'label' => 'Scheduled', 'icon' => 'fa-calendar', 'color' => 'gray'],
                            ['status' => 'receiving', 'label' => 'Receiving', 'icon' => 'fa-truck-loading', 'color' => 'blue'],
                            ['status' => 'sorting', 'label' => 'Sorting', 'icon' => 'fa-sort', 'color' => 'yellow'],
                            ['status' => 'loading', 'label' => 'Loading', 'icon' => 'fa-boxes', 'color' => 'purple'],
                            ['status' => 'completed', 'label' => 'Completed', 'icon' => 'fa-check-circle', 'color' => 'green'],
                        ];
                        $currentIndex = array_search($crossDocking->status, array_column($steps, 'status'));
                    @endphp

                    @foreach($steps as $index => $step)
                        @php
                            $isCompleted = $index < $currentIndex || ($crossDocking->status === 'completed' && $step['status'] === 'completed');
                            $isCurrent = $step['status'] === $crossDocking->status;
                            $isCancelled = $crossDocking->status === 'cancelled';
                        @endphp
                        
                        <div class="relative flex items-center mb-4 pl-12">
                            <div class="absolute left-0 w-8 h-8 rounded-full flex items-center justify-center {{ $isCompleted ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-'.$step['color'].'-500 text-white' : 'bg-gray-200 text-gray-500') }}">
                                <i class="fas {{ $step['icon'] }} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $step['label'] }}</div>
                                @if($step['status'] === 'scheduled' && $crossDocking->scheduled_date)
                                    <div class="text-xs text-gray-500">{{ $crossDocking->scheduled_date->format('d M Y H:i') }}</div>
                                @elseif($step['status'] === 'receiving' && $crossDocking->started_at)
                                    <div class="text-xs text-gray-500">{{ $crossDocking->started_at->format('d M Y H:i') }}</div>
                                @elseif($step['status'] === 'completed' && $crossDocking->completed_at)
                                    <div class="text-xs text-gray-500">{{ $crossDocking->completed_at->format('d M Y H:i') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($crossDocking->status === 'cancelled')
                        <div class="relative flex items-center pl-12">
                            <div class="absolute left-0 w-8 h-8 rounded-full flex items-center justify-center bg-red-500 text-white">
                                <i class="fas fa-times-circle text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-red-600">Cancelled</div>
                                <div class="text-xs text-gray-500">{{ $crossDocking->updated_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-box text-blue-600 mr-2"></i>
                    Product Information
                </h2>

                <div class="flex items-start">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-box text-2xl text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $crossDocking->product->name }}</h3>
                        <p class="text-sm text-gray-600">SKU: {{ $crossDocking->product->sku }}</p>
                        <div class="mt-3 grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Quantity:</span>
                                <div class="text-xl font-bold text-gray-900">{{ number_format($crossDocking->quantity) }}</div>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Unit:</span>
                                <div class="text-xl font-bold text-gray-900">{{ $crossDocking->unit_of_measure }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dock Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-door-open text-green-600 mr-2"></i>
                    Dock Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-arrow-right text-green-600 mr-2"></i>
                            <span class="text-sm font-semibold text-gray-700">Receiving Dock (In)</span>
                        </div>
                        <div class="text-lg font-bold text-gray-900">
                            {{ $crossDocking->dock_in ?: '-' }}
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-arrow-left text-blue-600 mr-2"></i>
                            <span class="text-sm font-semibold text-gray-700">Shipping Dock (Out)</span>
                        </div>
                        <div class="text-lg font-bold text-gray-900">
                            {{ $crossDocking->dock_out ?: '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($crossDocking->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                        Notes
                    </h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $crossDocking->notes }}</p>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            
            {{-- Warehouse Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-warehouse text-purple-600 mr-2"></i>
                    Warehouse
                </h2>

                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-warehouse text-purple-600"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">{{ $crossDocking->warehouse->name }}</div>
                        <div class="text-sm text-gray-600">{{ $crossDocking->warehouse->code }}</div>
                    </div>
                </div>
            </div>

            {{-- Related Orders --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-link text-purple-600 mr-2"></i>
                    Related Orders
                </h2>

                {{-- Inbound Shipment --}}
                <div class="mb-4">
                    <span class="text-sm font-medium text-gray-600">Inbound Shipment:</span>
                    @if($crossDocking->inboundShipment)
                        <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                            <div class="font-semibold text-gray-900">{{ $crossDocking->inboundShipment->shipment_number }}</div>
                            <a href="#" class="text-xs text-blue-600 hover:underline">View Details</a>
                        </div>
                    @else
                        <div class="text-sm text-gray-500 mt-1">Not linked</div>
                    @endif
                </div>

                {{-- Outbound Order --}}
                <div>
                    <span class="text-sm font-medium text-gray-600">Outbound Order:</span>
                    @if($crossDocking->outboundOrder)
                        <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                            <div class="font-semibold text-gray-900">{{ $crossDocking->outboundOrder->order_number }}</div>
                            <a href="#" class="text-xs text-blue-600 hover:underline">View Details</a>
                        </div>
                    @else
                        <div class="text-sm text-gray-500 mt-1">Not linked</div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-cogs text-blue-600 mr-2"></i>
                    Actions
                </h2>

                <div class="space-y-2">
                    @if($crossDocking->canStartReceiving())
                        <form action="{{ route('operations.cross-docking.start-receiving', $crossDocking) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-truck-loading mr-2"></i>Start Receiving
                            </button>
                        </form>
                    @endif

                    @if($crossDocking->canStartSorting())
                        <form action="{{ route('operations.cross-docking.start-sorting', $crossDocking) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                <i class="fas fa-sort mr-2"></i>Start Sorting
                            </button>
                        </form>
                    @endif

                    @if($crossDocking->canStartLoading())
                        <form action="{{ route('operations.cross-docking.start-loading', $crossDocking) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                <i class="fas fa-boxes mr-2"></i>Start Loading
                            </button>
                        </form>
                    @endif

                    @if($crossDocking->canComplete())
                        <form action="{{ route('operations.cross-docking.complete', $crossDocking) }}" method="POST" onsubmit="return confirm('Are you sure you want to complete this cross docking order?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-check-circle mr-2"></i>Complete
                            </button>
                        </form>
                    @endif

                    @if($crossDocking->canCancel())
                        <form action="{{ route('operations.cross-docking.cancel', $crossDocking) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this cross docking order?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-times-circle mr-2"></i>Cancel
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Audit Information --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    Audit Information
                </h2>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium text-gray-900">{{ $crossDocking->created_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($crossDocking->creator)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created By:</span>
                            <span class="font-medium text-gray-900">{{ $crossDocking->creator->name }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Updated:</span>
                        <span class="font-medium text-gray-900">{{ $crossDocking->updated_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($crossDocking->updater)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Updated By:</span>
                            <span class="font-medium text-gray-900">{{ $crossDocking->updater->name }}</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection