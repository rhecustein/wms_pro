{{-- resources/views/outbound/picking-orders/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Picking Order Detail - ' . $pickingOrder->picking_number)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('outbound.picking-orders.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-box-open text-green-600 mr-2"></i>
                    {{ $pickingOrder->picking_number }}
                </h1>
                <p class="text-sm text-gray-600 mt-1">Picking Order Details</p>
            </div>
        </div>
        <div class="flex space-x-2">
            @if(in_array($pickingOrder->status, ['pending', 'assigned']))
                <form action="{{ route('outbound.picking-orders.start', $pickingOrder) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-play mr-2"></i>Start Picking
                    </button>
                </form>
                <a href="{{ route('outbound.picking-orders.edit', $pickingOrder) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif

            @if($pickingOrder->status === 'in_progress')
                <a href="{{ route('outbound.picking-orders.execute', $pickingOrder) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-tasks mr-2"></i>Execute Picking
                </a>
                <form action="{{ route('outbound.picking-orders.complete', $pickingOrder) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to complete this picking order?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check-circle mr-2"></i>Complete
                    </button>
                </form>
            @endif

            @if(!in_array($pickingOrder->status, ['completed', 'cancelled']))
                <form action="{{ route('outbound.picking-orders.cancel', $pickingOrder) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this picking order?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-times-circle mr-2"></i>Cancel
                    </button>
                </form>
            @endif

            <a href="{{ route('outbound.picking-orders.print', $pickingOrder) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition" target="_blank">
                <i class="fas fa-print mr-2"></i>Print
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Main Information --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-green-600 mr-2"></i>
                    Picking Information
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Picking Number</label>
                        <p class="text-base font-mono font-semibold text-gray-900">{{ $pickingOrder->picking_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1">{!! $pickingOrder->status_badge !!}</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Picking Date</label>
                        <p class="text-base text-gray-900">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>
                            {{ $pickingOrder->picking_date->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Priority</label>
                        <div class="mt-1">{!! $pickingOrder->priority_badge !!}</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Picking Type</label>
                        <p class="text-base text-gray-900">{{ ucfirst(str_replace('_', ' ', $pickingOrder->picking_type)) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Total Items</label>
                        <p class="text-base font-semibold text-gray-900">{{ $pickingOrder->total_items }} items ({{ number_format($pickingOrder->total_quantity) }} qty)</p>
                    </div>
                </div>

                @if($pickingOrder->notes)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <label class="text-sm font-medium text-gray-500">Notes</label>
                        <p class="text-base text-gray-900 mt-1">{{ $pickingOrder->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Sales Order Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-shopping-cart text-blue-600 mr-2"></i>
                    Sales Order Information
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">SO Number</label>
                        <p class="text-base font-semibold text-gray-900">{{ $pickingOrder->salesOrder->so_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Customer</label>
                        <p class="text-base text-gray-900">{{ $pickingOrder->salesOrder->customer->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Order Date</label>
                        <p class="text-base text-gray-900">{{ $pickingOrder->salesOrder->order_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Delivery Date</label>
                        <p class="text-base text-gray-900">{{ $pickingOrder->salesOrder->delivery_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Warehouse Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-warehouse text-purple-600 mr-2"></i>
                    Warehouse Information
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Warehouse</label>
                        <p class="text-base font-semibold text-gray-900">{{ $pickingOrder->warehouse->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Warehouse Code</label>
                        <p class="text-base text-gray-900">{{ $pickingOrder->warehouse->code }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Location</label>
                        <p class="text-base text-gray-900">{{ $pickingOrder->warehouse->address ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            {{-- Assignment & Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-clock text-blue-600 mr-2"></i>
                    Assignment & Timeline
                </h2>
                
                {{-- Assigned To --}}
                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Assigned To</label>
                    @if($pickingOrder->assignedUser)
                        <div class="flex items-center mt-2">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-gray-900">{{ $pickingOrder->assignedUser->name }}</p>
                                <p class="text-xs text-gray-500">{{ $pickingOrder->assigned_at?->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-base text-gray-400 mt-2">Not assigned yet</p>
                        @if($pickingOrder->status === 'pending')
                            <button onclick="document.getElementById('assignModal').classList.remove('hidden')" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                                <i class="fas fa-user-plus mr-2"></i>Assign Picker
                            </button>
                        @endif
                    @endif
                </div>

                {{-- Timeline --}}
                <div class="space-y-3 mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-plus text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Created</p>
                            <p class="text-xs text-gray-500">{{ $pickingOrder->created_at->format('d M Y, H:i') }}</p>
                            @if($pickingOrder->createdBy)
                                <p class="text-xs text-gray-500">by {{ $pickingOrder->createdBy->name }}</p>
                            @endif
                        </div>
                    </div>

                    @if($pickingOrder->assigned_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-user-check text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Assigned</p>
                                <p class="text-xs text-gray-500">{{ $pickingOrder->assigned_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($pickingOrder->started_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-play text-yellow-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Started</p>
                                <p class="text-xs text-gray-500">{{ $pickingOrder->started_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($pickingOrder->completed_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Completed</p>
                                <p class="text-xs text-gray-500">{{ $pickingOrder->completed_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Progress Summary --}}
            @if($pickingOrder->status === 'in_progress')
                <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl shadow-sm border border-green-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-green-600 mr-2"></i>
                        Picking Progress
                    </h2>
                    <div class="text-center mb-4">
                        <div class="text-4xl font-bold text-green-600">{{ $pickingOrder->progress_percentage }}%</div>
                        <p class="text-sm text-gray-600 mt-1">Completion</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                        <div class="bg-gradient-to-r from-green-500 to-blue-500 h-3 rounded-full transition-all duration-500" style="width: {{ $pickingOrder->progress_percentage }}%"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-2xl font-bold text-green-600">{{ $pickingOrder->items->where('status', 'picked')->count() }}</p>
                            <p class="text-xs text-gray-600">Picked</p>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-2xl font-bold text-gray-600">{{ $pickingOrder->items->where('status', 'pending')->count() }}</p>
                            <p class="text-xs text-gray-600">Pending</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Picking Items --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list text-green-600 mr-2"></i>
                Picking Items ({{ $pickingOrder->items->count() }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seq</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Storage Bin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Serial</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Requested</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Picked</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Picked By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pickingOrder->items->sortBy('pick_sequence') as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-sm font-semibold text-gray-700 bg-gray-100 rounded-full">
                                    {{ $item->pick_sequence }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $item->storageBin->bin_code }}</div>
                                <div class="text-xs text-gray-500">{{ $item->storageBin->location ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->batch_number || $item->serial_number)
                                    <div class="text-sm text-gray-900">
                                        @if($item->batch_number)
                                            <div><span class="font-medium">Batch:</span> {{ $item->batch_number }}</div>
                                        @endif
                                        @if($item->serial_number)
                                            <div><span class="font-medium">Serial:</span> {{ $item->serial_number }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                                @if($item->expiry_date)
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Exp: {{ $item->expiry_date->format('d M Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($item->quantity_requested) }}</div>
                                <div class="text-xs text-gray-500">{{ $item->unit_of_measure }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold {{ $item->quantity_picked >= $item->quantity_requested ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ number_format($item->quantity_picked) }}
                                </div>
                                @if($item->quantity_picked > 0 && $item->quantity_picked < $item->quantity_requested)
                                    <div class="text-xs text-yellow-600">Short pick</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $item->status_badge !!}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->pickedBy)
                                    <div class="text-sm text-gray-900">{{ $item->pickedBy->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->picked_at?->format('d M, H:i') }}</div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No items found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Assignment Modal --}}
<div id="assignModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Assign Picker</h3>
            <button onclick="document.getElementById('assignModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('outbound.picking-orders.assign', $pickingOrder) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                <select name="assigned_to" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                    <option value="">Choose a picker...</option>
                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Assign
                </button>
            </div>
        </form>
    </div>
</div>

@endsection