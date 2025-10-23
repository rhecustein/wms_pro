@extends('layouts.app')

@section('title', 'Transfer Order Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-exchange-alt text-indigo-600 mr-2"></i>
                Transfer Order Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $transferOrder->transfer_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('operations.transfer-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            
            @if(in_array($transferOrder->status, ['draft', 'approved']))
                <a href="{{ route('operations.transfer-orders.edit', $transferOrder) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
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
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Transfer Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Transfer Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Transfer Number</label>
                        <p class="text-lg font-mono font-semibold text-gray-900">{{ $transferOrder->transfer_number }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Transfer Type</label>
                        <div>{!! $transferOrder->transfer_type_badge !!}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">From Warehouse</label>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-warehouse text-orange-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $transferOrder->fromWarehouse->name }}</p>
                                <p class="text-xs text-gray-500">{{ $transferOrder->fromWarehouse->code }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">To Warehouse</label>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-warehouse text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $transferOrder->toWarehouse->name }}</p>
                                <p class="text-xs text-gray-500">{{ $transferOrder->toWarehouse->code }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Transfer Date</label>
                        <p class="text-gray-900 font-semibold">
                            <i class="fas fa-calendar text-blue-600 mr-2"></i>
                            {{ $transferOrder->transfer_date->format('d M Y, H:i') }}
                        </p>
                    </div>
                    
                    @if($transferOrder->expected_arrival_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Expected Arrival</label>
                            <p class="text-gray-900 font-semibold">
                                <i class="fas fa-calendar-check text-green-600 mr-2"></i>
                                {{ $transferOrder->expected_arrival_date->format('d M Y, H:i') }}
                            </p>
                        </div>
                    @endif
                    
                    @if($transferOrder->vehicle)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Vehicle</label>
                            <p class="text-gray-900 font-semibold">
                                <i class="fas fa-truck text-indigo-600 mr-2"></i>
                                {{ $transferOrder->vehicle->vehicle_number }}
                            </p>
                        </div>
                    @endif
                    
                    @if($transferOrder->driver)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Driver</label>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <p class="text-gray-900 font-semibold">{{ $transferOrder->driver->name }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                @if($transferOrder->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Notes</label>
                        <p class="text-gray-700">{{ $transferOrder->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Transfer Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-boxes text-purple-600 mr-2"></i>
                        Transfer Items
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From → To Bin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Serial</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Shipped</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($transferOrder->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-box text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->product->sku ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            @if($item->fromStorageBin)
                                                <div class="text-gray-600">
                                                    <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                                                    {{ $item->fromStorageBin->bin_code }}
                                                </div>
                                            @endif
                                            @if($item->toStorageBin)
                                                <div class="text-gray-400 text-xs my-1">
                                                    <i class="fas fa-arrow-down"></i>
                                                </div>
                                                <div class="text-gray-900 font-semibold">
                                                    <i class="fas fa-layer-group text-green-500 mr-1"></i>
                                                    {{ $item->toStorageBin->bin_code }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            @if($item->batch_number)
                                                <div class="text-gray-600">
                                                    <i class="fas fa-barcode mr-1"></i>
                                                    {{ $item->batch_number }}
                                                </div>
                                            @endif
                                            @if($item->serial_number)
                                                <div class="text-gray-600">
                                                    <i class="fas fa-hashtag mr-1"></i>
                                                    {{ $item->serial_number }}
                                                </div>
                                            @endif
                                            @if(!$item->batch_number && !$item->serial_number)
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-semibold text-gray-900">{{ number_format($item->quantity_requested) }}</span>
                                        <span class="text-xs text-gray-500 ml-1">{{ $item->unit_of_measure }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-semibold text-blue-600">{{ number_format($item->quantity_shipped) }}</span>
                                        <span class="text-xs text-gray-500 ml-1">{{ $item->unit_of_measure }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-semibold text-green-600">{{ number_format($item->quantity_received) }}</span>
                                        <span class="text-xs text-gray-500 ml-1">{{ $item->unit_of_measure }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {!! $item->status_badge !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-700">Total:</td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">{{ number_format($transferOrder->total_quantity) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-blue-600">{{ number_format($transferOrder->items->sum('quantity_shipped')) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-green-600">{{ number_format($transferOrder->items->sum('quantity_received')) }}</td>
                                <td class="px-6 py-4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Status Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Status</h3>
                <div class="mb-4">
                    {!! $transferOrder->status_badge !!}
                </div>
                
                {{-- Action Buttons --}}
                <div class="space-y-3">
                    @if($transferOrder->status === 'draft')
                        <form action="{{ route('operations.transfer-orders.approve', $transferOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" onclick="return confirm('Are you sure you want to approve this transfer order?')">
                                <i class="fas fa-check mr-2"></i>Approve Transfer
                            </button>
                        </form>
                    @endif
                    
                    @if($transferOrder->status === 'approved')
                        <form action="{{ route('operations.transfer-orders.ship', $transferOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition" onclick="return confirm('Are you sure you want to ship this transfer order?')">
                                <i class="fas fa-shipping-fast mr-2"></i>Ship Transfer
                            </button>
                        </form>
                    @endif
                    
                    @if($transferOrder->status === 'in_transit')
                        <form action="{{ route('operations.transfer-orders.receive', $transferOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition" onclick="return confirm('Are you sure you want to receive this transfer order?')">
                                <i class="fas fa-inbox mr-2"></i>Receive Transfer
                            </button>
                        </form>
                    @endif
                    
                    @if($transferOrder->status === 'received')
                        <form action="{{ route('operations.transfer-orders.complete', $transferOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" onclick="return confirm('Are you sure you want to complete this transfer order?')">
                                <i class="fas fa-check-circle mr-2"></i>Complete Transfer
                            </button>
                        </form>
                    @endif
                    
                    @if(!in_array($transferOrder->status, ['completed', 'cancelled']))
                        <form action="{{ route('operations.transfer-orders.cancel', $transferOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="return confirm('Are you sure you want to cancel this transfer order?')">
                                <i class="fas fa-times-circle mr-2"></i>Cancel Transfer
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Summary Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Summary</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total Items:</span>
                        <span class="font-bold text-gray-900">{{ $transferOrder->total_items }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total Quantity:</span>
                        <span class="font-bold text-gray-900">{{ number_format($transferOrder->total_quantity) }}</span>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Shipped:</span>
                            <span class="font-bold text-blue-600">{{ number_format($transferOrder->items->sum('quantity_shipped')) }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Received:</span>
                        <span class="font-bold text-green-600">{{ number_format($transferOrder->items->sum('quantity_received')) }}</span>
                    </div>
                </div>
            </div>

            {{-- Timeline Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Timeline</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-file-alt text-gray-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Created</p>
                            <p class="text-xs text-gray-500">{{ $transferOrder->created_at->format('d M Y, H:i') }}</p>
                            @if($transferOrder->createdBy)
                                <p class="text-xs text-gray-500">by {{ $transferOrder->createdBy->name }}</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($transferOrder->approved_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-check text-blue-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Approved</p>
                                <p class="text-xs text-gray-500">{{ $transferOrder->approved_at->format('d M Y, H:i') }}</p>
                                @if($transferOrder->approvedBy)
                                    <p class="text-xs text-gray-500">by {{ $transferOrder->approvedBy->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if($transferOrder->shipped_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-truck text-yellow-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Shipped</p>
                                <p class="text-xs text-gray-500">{{ $transferOrder->shipped_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($transferOrder->received_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-inbox text-purple-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Received</p>
                                <p class="text-xs text-gray-500">{{ $transferOrder->received_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($transferOrder->status === 'completed')
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Completed</p>
                                <p class="text-xs text-gray-500">{{ $transferOrder->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection