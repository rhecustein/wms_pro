@extends('layouts.app')

@section('title', 'Packing Order Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-box-open text-blue-600 mr-2"></i>
                Packing Order Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $packingOrder->packing_number }}</p>
        </div>
        <div class="flex space-x-3">
            @if($packingOrder->status === 'pending')
                <form action="{{ route('outbound.packing-orders.start', $packingOrder) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-play mr-2"></i>Start Packing
                    </button>
                </form>
            @endif
            
            @if($packingOrder->status === 'in_progress')
                <a href="{{ route('outbound.packing-orders.execute', $packingOrder) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-box-open mr-2"></i>Execute Packing
                </a>
                <form action="{{ route('outbound.packing-orders.complete', $packingOrder) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to complete this packing order?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-check mr-2"></i>Complete
                    </button>
                </form>
            @endif
            
            @if($packingOrder->status === 'completed')
                <a href="{{ route('outbound.packing-orders.print-label', $packingOrder) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition" target="_blank">
                    <i class="fas fa-print mr-2"></i>Print Label
                </a>
            @endif
            
            <a href="{{ route('outbound.packing-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
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
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Order Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Packing Number</label>
                        <p class="text-base font-semibold text-gray-900 font-mono">{{ $packingOrder->packing_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1">{!! $packingOrder->status_badge !!}</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Sales Order</label>
                        <p class="text-base font-semibold text-gray-900">{{ $packingOrder->salesOrder->order_number }}</p>
                        <p class="text-sm text-gray-600">{{ $packingOrder->salesOrder->customer->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Picking Order</label>
                        <p class="text-base font-semibold text-gray-900">{{ $packingOrder->pickingOrder->picking_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Warehouse</label>
                        <p class="text-base font-semibold text-gray-900">{{ $packingOrder->warehouse->name }}</p>
                        <p class="text-sm text-gray-600">{{ $packingOrder->warehouse->code }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Packing Date</label>
                        <p class="text-base font-semibold text-gray-900">{{ $packingOrder->packing_date->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Total Boxes</label>
                        <p class="text-base font-semibold text-gray-900">
                            <i class="fas fa-boxes text-orange-500 mr-1"></i>
                            {{ $packingOrder->total_boxes }} boxes
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Total Weight</label>
                        <p class="text-base font-semibold text-gray-900">
                            <i class="fas fa-weight text-blue-500 mr-1"></i>
                            {{ number_format($packingOrder->total_weight_kg, 2) }} kg
                        </p>
                    </div>
                </div>

                @if($packingOrder->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="text-sm font-medium text-gray-500">Notes</label>
                        <p class="text-base text-gray-900 mt-1">{{ $packingOrder->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Packed Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-box text-blue-600 mr-2"></i>
                        Packed Items ({{ $packingOrder->items->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Serial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Box Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weight</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Packed By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($packingOrder->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-box text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->product->sku ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($item->batch_number)
                                            <div class="text-sm text-gray-900">Batch: {{ $item->batch_number }}</div>
                                        @endif
                                        @if($item->serial_number)
                                            <div class="text-sm text-gray-900">Serial: {{ $item->serial_number }}</div>
                                        @endif
                                        @if(!$item->batch_number && !$item->serial_number)
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($item->quantity_packed) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-box mr-1"></i>
                                            {{ $item->box_number ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ number_format($item->box_weight_kg ?? 0, 2) }} kg</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($item->packedBy)
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ $item->packedBy->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $item->packed_at?->format('d M, H:i') }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                                <i class="fas fa-box text-3xl text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-600">No items packed yet</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Timeline
                </h3>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-plus text-blue-600"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-semibold text-gray-900">Order Created</p>
                            <p class="text-sm text-gray-600">{{ $packingOrder->created_at->format('d M Y, H:i') }}</p>
                            @if($packingOrder->createdBy)
                                <p class="text-xs text-gray-500">by {{ $packingOrder->createdBy->name }}</p>
                            @endif
                        </div>
                    </div>

                    @if($packingOrder->started_at)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-play text-green-600"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-900">Packing Started</p>
                                <p class="text-sm text-gray-600">{{ $packingOrder->started_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($packingOrder->completed_at)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-purple-600"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-900">Packing Completed</p>
                                <p class="text-sm text-gray-600">{{ $packingOrder->completed_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Assignment Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user-check text-blue-600 mr-2"></i>
                    Assignment
                </h3>
                @if($packingOrder->assignedUser)
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $packingOrder->assignedUser->name }}</p>
                            <p class="text-xs text-gray-500">{{ $packingOrder->assignedUser->email }}</p>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-yellow-800">Not assigned to any user</p>
                    </div>
                @endif

                @if(in_array($packingOrder->status, ['pending', 'in_progress']))
                    <button type="button" onclick="document.getElementById('assignModal').classList.remove('hidden')" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-user-plus mr-2"></i>Assign User
                    </button>
                @endif
            </div>

            {{-- Quick Stats --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                    Quick Stats
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Items</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $packingOrder->items->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Boxes</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $packingOrder->total_boxes }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Weight</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($packingOrder->total_weight_kg, 2) }} kg</span>
                    </div>
                    @if($packingOrder->started_at && $packingOrder->completed_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Duration</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ $packingOrder->started_at->diffForHumans($packingOrder->completed_at, true) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-cogs text-blue-600 mr-2"></i>
                    Actions
                </h3>
                <div class="space-y-3">
                    @if(in_array($packingOrder->status, ['pending', 'in_progress']))
                        <a href="{{ route('outbound.packing-orders.edit', $packingOrder) }}" class="block w-full px-4 py-2 bg-yellow-600 text-white text-center rounded-lg hover:bg-yellow-700 transition">
                            <i class="fas fa-edit mr-2"></i>Edit Order
                        </a>
                    @endif
                    
                    @if($packingOrder->status === 'completed')
                        <a href="{{ route('outbound.packing-orders.print-label', $packingOrder) }}" class="block w-full px-4 py-2 bg-indigo-600 text-white text-center rounded-lg hover:bg-indigo-700 transition" target="_blank">
                            <i class="fas fa-print mr-2"></i>Print Label
                        </a>
                    @endif
                    
                    @if($packingOrder->status === 'pending')
                        <form action="{{ route('outbound.packing-orders.destroy', $packingOrder) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this packing order?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-trash mr-2"></i>Delete Order
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Assign Modal --}}
<div id="assignModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Assign User</h3>
            <button onclick="document.getElementById('assignModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('outbound.packing-orders.assign', $packingOrder) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                <select name="assigned_to" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">Choose a user...</option>
                    @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}" {{ $packingOrder->assigned_to == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-check mr-2"></i>Assign
                </button>
                <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection