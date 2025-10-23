{{-- resources/views/inventory/opnames/count.blade.php --}}
@extends('layouts.app')

@section('title', 'Count Items')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-clipboard-check text-blue-600 mr-2"></i>
                Count Items
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $opname->opname_number }} - {{ $opname->warehouse->name }}</p>
        </div>
        <a href="{{ route('inventory.opnames.show', $opname) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Details
        </a>
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

    {{-- Progress Card --}}
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <div class="text-sm opacity-90 mb-1">Total Items</div>
                <div class="text-3xl font-bold">{{ $opname->total_items_planned }}</div>
            </div>
            <div>
                <div class="text-sm opacity-90 mb-1">Counted</div>
                <div class="text-3xl font-bold text-green-300">{{ $opname->items->where('status', '!=', 'pending')->count() }}</div>
            </div>
            <div>
                <div class="text-sm opacity-90 mb-1">Pending</div>
                <div class="text-3xl font-bold text-yellow-300">{{ $opname->items->where('status', 'pending')->count() }}</div>
            </div>
            <div>
                <div class="text-sm opacity-90 mb-1">Progress</div>
                @php
                    $countedItems = $opname->items->where('status', '!=', 'pending')->count();
                    $progress = $opname->total_items_planned > 0 ? ($countedItems / $opname->total_items_planned) * 100 : 0;
                @endphp
                <div class="text-3xl font-bold">{{ number_format($progress, 0) }}%</div>
            </div>
        </div>
        <div class="mt-4">
            <div class="w-full bg-blue-400 rounded-full h-3">
                <div class="bg-white h-3 rounded-full transition-all" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>

    {{-- Items Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($opname->items as $item)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                {{-- Header --}}
                <div class="bg-gray-50 px-4 py-3 border-b flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-cube text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $item->product->name }}</h4>
                            <p class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                        </div>
                    </div>
                    {!! $item->status_badge !!}
                </div>

                {{-- Body --}}
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="text-xs text-gray-500">Storage Bin</label>
                            <p class="text-sm font-semibold text-gray-900">{{ $item->storageBin->bin_code }}</p>
                        </div>
                        @if($item->batch_number)
                            <div>
                                <label class="text-xs text-gray-500">Batch</label>
                                <p class="text-sm font-semibold text-gray-900">{{ $item->batch_number }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">System Quantity:</span>
                            <span class="text-lg font-bold text-gray-900">{{ $item->system_quantity }}</span>
                        </div>
                    </div>

                    @if($item->status !== 'pending')
                        <div class="bg-blue-50 rounded-lg p-3 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-600">Physical Quantity:</span>
                                <span class="text-lg font-bold text-blue-900">{{ $item->physical_quantity }}</span>
                            </div>
                        </div>

                        @if($item->variance != 0)
                            @php
                                $varianceClass = $item->variance > 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600';
                                $varianceBold = $item->variance > 0 ? 'text-green-900' : 'text-red-900';
                                $varianceIcon = $item->variance > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                            @endphp
                            <div class="{{ $varianceClass }} rounded-lg p-3 mb-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">Variance:</span>
                                    <span class="text-lg font-bold {{ $varianceBold }}">
                                        <i class="fas {{ $varianceIcon }} mr-1"></i>
                                        {{ abs($item->variance) }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        @if($item->countedBy)
                            <div class="text-xs text-gray-500 mb-3">
                                <i class="fas fa-user mr-1"></i>
                                Counted by {{ $item->countedBy->name }}
                                <span class="ml-2">{{ $item->counted_at->format('H:i') }}</span>
                            </div>
                        @endif
                    @endif

                    {{-- Count Form --}}
                    <form action="{{ route('inventory.opnames.update-count', [$opname, $item]) }}" method="POST" class="count-form">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Physical Count *</label>
                            <input type="number" name="physical_quantity" min="0" required 
                                   value="{{ old('physical_quantity', $item->physical_quantity) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-lg font-semibold text-center"
                                   placeholder="Enter count">
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="2" 
                                      class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                      placeholder="Any observations...">{{ old('notes', $item->notes) }}</textarea>
                        </div>

                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>
                            {{ $item->status === 'pending' ? 'Save Count' : 'Update Count' }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Complete Button --}}
    @if($opname->items->where('status', 'pending')->count() === 0)
        <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-6 text-center">
            <i class="fas fa-check-circle text-green-600 text-4xl mb-3"></i>
            <h3 class="text-lg font-semibold text-green-900 mb-2">All Items Counted!</h3>
            <p class="text-green-700 mb-4">You can now complete this stock opname.</p>
            <form action="{{ route('inventory.opnames.complete', $opname) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold" onclick="return confirm('Complete this opname? This action cannot be undone.')">
                    <i class="fas fa-check-double mr-2"></i>Complete Stock Opname
                </button>
            </form>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
// Auto-focus on first pending item
document.addEventListener('DOMContentLoaded', function() {
    const firstPendingInput = document.querySelector('.count-form input[name="physical_quantity"]');
    if (firstPendingInput) {
        firstPendingInput.focus();
    }
});

// Keyboard shortcuts for quick counting
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S to save current form
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const focusedInput = document.activeElement;
        if (focusedInput && focusedInput.name === 'physical_quantity') {
            focusedInput.closest('form').submit();
        }
    }
});
</script>
@endpush