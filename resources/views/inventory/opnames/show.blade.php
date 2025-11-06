{{-- resources/views/inventory/opnames/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock Opname Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                Opname Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $opname->opname_number }}</p>
        </div>
        <div class="flex items-center space-x-2">
            {{-- Print Button --}}
            <a href="{{ route('inventory.opnames.print', $opname) }}" 
               target="_blank"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-print mr-2"></i>Print
            </a>

            {{-- Export Excel Button --}}
            <a href="{{ route('inventory.opnames.export', $opname) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-excel mr-2"></i>Export
            </a>

            @if($opname->status === 'in_progress')
                <a href="{{ route('inventory.opnames.count', $opname) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-clipboard-check mr-2"></i>Count Items
                </a>
            @endif
            @if($opname->status === 'planned')
                <a href="{{ route('inventory.opnames.edit', $opname) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('inventory.opnames.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Opname Number</label>
                        <p class="text-gray-900 font-mono font-semibold">{{ $opname->opname_number }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Opname Date</label>
                        <p class="text-gray-900">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>
                            {{ $opname->opname_date->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Warehouse</label>
                        <p class="text-gray-900 font-semibold">{{ $opname->warehouse->name }}</p>
                        <p class="text-xs text-gray-500">{{ $opname->warehouse->code }}</p>
                    </div>

                    @if($opname->storageArea)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Storage Area</label>
                            <p class="text-gray-900">{{ $opname->storageArea->area_name }}</p>
                            <p class="text-xs text-gray-500">{{ $opname->storageArea->area_code }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                        <p>{!! $opname->type_badge !!}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <p>{!! $opname->status_badge !!}</p>
                    </div>

                    @if($opname->started_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Started At</label>
                            <p class="text-gray-900">{{ $opname->started_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif

                    @if($opname->completed_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Completed At</label>
                            <p class="text-gray-900">{{ $opname->completed_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif

                    @if($opname->notes)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Notes</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $opname->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Statistics (Only show when completed) --}}
            @if($opname->status === 'completed')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        Statistics
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-sm text-blue-600 mb-1">Accuracy</div>
                            <div class="text-2xl font-bold text-blue-900">{{ number_format($opname->accuracy_percentage, 1) }}%</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-sm text-green-600 mb-1">Items Counted</div>
                            <div class="text-2xl font-bold text-green-900">{{ $opname->total_items_counted }}</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="text-sm text-yellow-600 mb-1">Variances</div>
                            <div class="text-2xl font-bold text-yellow-900">{{ $opname->variance_count }}</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-sm text-purple-600 mb-1">Total Items</div>
                            <div class="text-2xl font-bold text-purple-900">{{ $opname->total_items_planned }}</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Opname Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-box text-blue-600 mr-2"></i>
                    Opname Items ({{ $opname->items->count() }})
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">#</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Product</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Storage Bin</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">System Qty</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Physical Qty</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Variance</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($opname->items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</div>
                                        @if($item->batch_number)
                                            <div class="text-xs text-gray-500">Batch: {{ $item->batch_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900">{{ $item->storageBin->bin_code }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->storageBin->bin_name }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-sm font-semibold text-gray-900">{{ $item->system_quantity }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($item->physical_quantity !== null)
                                            <span class="text-sm font-semibold text-gray-900">{{ $item->physical_quantity }}</span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($item->variance !== null)
                                            @php
                                                $varianceClass = $item->variance > 0 ? 'text-green-600' : ($item->variance < 0 ? 'text-red-600' : 'text-gray-600');
                                                $varianceIcon = $item->variance > 0 ? 'fa-arrow-up' : ($item->variance < 0 ? 'fa-arrow-down' : 'fa-equals');
                                            @endphp
                                            <span class="text-sm font-bold {{ $varianceClass }}">
                                                <i class="fas {{ $varianceIcon }} mr-1"></i>
                                                {{ abs($item->variance) }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {!! $item->status_badge !!}
                                    </td>
                                </tr>
                                @if($item->notes)
                                    <tr>
                                        <td colspan="7" class="px-4 py-2 bg-gray-50">
                                            <div class="text-xs text-gray-600">
                                                <strong>Notes:</strong> {{ $item->notes }}
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Audit Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Audit Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block text-gray-500 mb-1">Scheduled By</label>
                        <p class="text-gray-900 font-semibold">
                            <i class="fas fa-user text-gray-400 mr-1"></i>
                            {{ $opname->scheduledBy->name ?? 'System' }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $opname->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    @if($opname->completedBy)
                        <div>
                            <label class="block text-gray-500 mb-1">Completed By</label>
                            <p class="text-gray-900 font-semibold">
                                <i class="fas fa-user-check text-green-500 mr-1"></i>
                                {{ $opname->completedBy->name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $opname->completed_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif

                    @if($opname->updatedBy)
                        <div>
                            <label class="block text-gray-500 mb-1">Last Updated By</label>
                            <p class="text-gray-900 font-semibold">
                                <i class="fas fa-user text-gray-400 mr-1"></i>
                                {{ $opname->updatedBy->name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $opname->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-cog text-blue-600 mr-2"></i>
                    Actions
                </h3>
                
                <div class="space-y-2">
                    @if($opname->status === 'planned')
                        <form action="{{ route('inventory.opnames.start', $opname) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold" onclick="return confirm('Start counting items now?')">
                                <i class="fas fa-play mr-2"></i>Start Counting
                            </button>
                        </form>

                        <a href="{{ route('inventory.opnames.edit', $opname) }}" class="block w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-center text-sm font-semibold">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>

                        <form action="{{ route('inventory.opnames.cancel', $opname) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold" onclick="return confirm('Are you sure you want to cancel this opname?')">
                                <i class="fas fa-ban mr-2"></i>Cancel
                            </button>
                        </form>
                    @endif

                    @if($opname->status === 'in_progress')
                        <a href="{{ route('inventory.opnames.count', $opname) }}" class="block w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-center text-sm font-semibold">
                            <i class="fas fa-clipboard-check mr-2"></i>Count Items
                        </a>

                        <form action="{{ route('inventory.opnames.complete', $opname) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold" onclick="return confirm('Complete this opname? Make sure all items are counted.')">
                                <i class="fas fa-check-double mr-2"></i>Complete
                            </button>
                        </form>

                        <form action="{{ route('inventory.opnames.cancel', $opname) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold" onclick="return confirm('Are you sure you want to cancel this opname?')">
                                <i class="fas fa-ban mr-2"></i>Cancel
                            </button>
                        </form>
                    @endif

                    @if($opname->status === 'completed')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                            <p class="text-sm text-green-800 font-semibold">Completed</p>
                        </div>
                    @endif

                    @if($opname->status === 'cancelled')
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                            <i class="fas fa-ban text-red-600 text-2xl mb-2"></i>
                            <p class="text-sm text-red-800 font-semibold">Cancelled</p>
                        </div>
                    @endif

                    <hr class="my-3">

                    <a href="{{ route('inventory.opnames.print', $opname) }}" target="_blank" class="block w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm font-semibold text-center">
                        <i class="fas fa-print mr-2"></i>Print
                    </a>

                    <a href="{{ route('inventory.opnames.export', $opname) }}" class="block w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-semibold text-center">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>

                    @if($opname->status === 'planned')
                        <form action="{{ route('inventory.opnames.destroy', $opname) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this opname?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-semibold">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Progress Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-tasks text-blue-600 mr-2"></i>
                    Progress
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-gray-600">Planned Items:</span>
                        <span class="font-semibold text-gray-900">{{ $opname->total_items_planned }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-gray-600">Counted Items:</span>
                        <span class="font-semibold text-gray-900">{{ $opname->total_items_counted }}</span>
                    </div>

                    @php
                        $progress = $opname->total_items_planned > 0 ? ($opname->total_items_counted / $opname->total_items_planned) * 100 : 0;
                    @endphp

                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Completion:</span>
                            <span class="font-semibold text-gray-900">{{ number_format($progress, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    @if($opname->status === 'completed')
                        <div class="pt-2 border-t">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Variances:</span>
                                <span class="font-semibold text-yellow-600">{{ $opname->variance_count }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection