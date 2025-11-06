@extends('layouts.app')

@section('title', 'Good Receiving Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-box-open text-green-600 mr-2"></i>
                Good Receiving Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $goodReceiving->gr_number }}</p>
        </div>
        <div class="flex space-x-2">
            @if(in_array($goodReceiving->status, ['draft', 'in_progress']))
                <a href="{{ route('inbound.good-receivings.edit', $goodReceiving) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('inbound.good-receivings.print', $goodReceiving) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" target="_blank">
                <i class="fas fa-print mr-2"></i>Print
            </a>
            <a href="{{ route('inbound.good-receivings.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-green-600 mr-2"></i>
                    Basic Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">GR Number</label>
                        <p class="text-base font-semibold text-gray-900 mt-1 font-mono">{{ $goodReceiving->gr_number }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1">{!! $goodReceiving->status_badge !!}</div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Receiving Date</label>
                        <p class="text-base text-gray-900 mt-1">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>
                            {{ $goodReceiving->receiving_date->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Quality Status</label>
                        <div class="mt-1">{!! $goodReceiving->quality_status_badge !!}</div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Warehouse</label>
                        <p class="text-base text-gray-900 mt-1">
                            <i class="fas fa-warehouse text-purple-600 mr-1"></i>
                            {{ $goodReceiving->warehouse->name }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $goodReceiving->warehouse->code }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Supplier</label>
                        <p class="text-base text-gray-900 mt-1">
                            <i class="fas fa-building text-blue-600 mr-1"></i>
                            {{ $goodReceiving->supplier->name }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $goodReceiving->supplier->code ?? '-' }}</p>
                    </div>

                    @if($goodReceiving->purchase_order_id)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Reference PO</label>
                            <p class="text-base text-gray-900 mt-1 font-mono">
                                <i class="fas fa-link text-gray-400 mr-1"></i>
                                {{ $goodReceiving->purchaseOrder->po_number }}
                            </p>
                        </div>
                    @endif

                    @if($goodReceiving->inbound_shipment_id)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Reference Shipment</label>
                            <p class="text-base text-gray-900 mt-1 font-mono">
                                <i class="fas fa-shipping-fast text-gray-400 mr-1"></i>
                                {{ $goodReceiving->inboundShipment->shipment_number }}
                            </p>
                        </div>
                    @endif

                    @if($goodReceiving->receivedBy)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Received By</label>
                            <p class="text-base text-gray-900 mt-1">
                                <i class="fas fa-user text-gray-400 mr-1"></i>
                                {{ $goodReceiving->receivedBy->name }}
                            </p>
                        </div>
                    @endif

                    @if($goodReceiving->quality_checked_at)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Quality Checked By</label>
                            <p class="text-base text-gray-900 mt-1">
                                <i class="fas fa-user-check text-gray-400 mr-1"></i>
                                {{ $goodReceiving->qualityCheckedBy->name ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $goodReceiving->quality_checked_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                </div>

                @if($goodReceiving->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="text-sm font-medium text-gray-500">Notes</label>
                        <p class="text-base text-gray-900 mt-2 whitespace-pre-line">{{ $goodReceiving->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-boxes text-green-600 mr-2"></i>
                    Items ({{ $goodReceiving->items->count() }})
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Expected</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Received</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pallets</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($goodReceiving->items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->product->sku }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-sm text-gray-900">{{ number_format($item->quantity_expected) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-sm font-semibold {{ $item->quantity_received < $item->quantity_expected ? 'text-yellow-600' : 'text-green-600' }}">
                                            {{ number_format($item->quantity_received) }}
                                        </span>
                                        @if($item->quantity_received < $item->quantity_expected)
                                            <span class="ml-1 text-xs text-yellow-600">
                                                ({{ number_format($item->quantity_expected - $item->quantity_received) }} short)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-sm text-gray-900">{{ $item->pallets }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm text-gray-600">{{ $item->notes ?? '-' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-900">Total</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">{{ number_format($goodReceiving->items->sum('quantity_expected')) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">{{ number_format($goodReceiving->total_quantity) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">{{ $goodReceiving->total_pallets }}</td>
                                <td class="px-4 py-3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>

        {{-- Sidebar Actions --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6 space-y-6">
                
                {{-- Summary --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-clipboard-list text-green-600 mr-2"></i>
                        Summary
                    </h3>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Items</span>
                            <span class="text-lg font-bold text-gray-900">{{ $goodReceiving->total_items }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Quantity</span>
                            <span class="text-lg font-bold text-gray-900">{{ number_format($goodReceiving->total_quantity) }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Pallets</span>
                            <span class="text-lg font-bold text-gray-900">{{ $goodReceiving->total_pallets }}</span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-tasks text-green-600 mr-2"></i>
                        Actions
                    </h3>

                    <div class="space-y-2">
                        @if($goodReceiving->status === 'draft')
                            <form action="{{ route('inbound.good-receivings.start', $goodReceiving) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                                    <i class="fas fa-play mr-2"></i>Start Receiving
                                </button>
                            </form>
                        @endif

                        @if(in_array($goodReceiving->status, ['in_progress', 'quality_check']))
                            <button type="button" onclick="openQualityCheckModal()" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                                <i class="fas fa-clipboard-check mr-2"></i>Quality Check
                            </button>

                            <form action="{{ route('inbound.good-receivings.complete', $goodReceiving) }}" method="POST" onsubmit="return confirm('Are you sure you want to complete this receiving?')">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                                    <i class="fas fa-check-circle mr-2"></i>Complete Receiving
                                </button>
                            </form>
                        @endif

                        @if(!in_array($goodReceiving->status, ['completed', 'cancelled']))
                            <button type="button" onclick="openCancelModal()" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                                <i class="fas fa-ban mr-2"></i>Cancel Receiving
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Audit Trail --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-history text-gray-600 mr-2"></i>
                        Audit Trail
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-gray-500">Created By:</span>
                            <p class="text-gray-900 font-medium">{{ $goodReceiving->createdBy->name ?? '-' }}</p>
                            <p class="text-gray-500">{{ $goodReceiving->created_at->format('d M Y, H:i') }}</p>
                        </div>

                        @if($goodReceiving->updatedBy)
                            <div>
                                <span class="text-gray-500">Last Updated By:</span>
                                <p class="text-gray-900 font-medium">{{ $goodReceiving->updatedBy->name }}</p>
                                <p class="text-gray-500">{{ $goodReceiving->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Quality Check Modal --}}
<div id="qualityCheckModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-clipboard-check text-purple-600 mr-2"></i>
                Quality Check
            </h3>

            <form action="{{ route('inbound.good-receivings.quality-check', $goodReceiving) }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quality Status <span class="text-red-500">*</span></label>
                        <select name="quality_status" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required>
                            <option value="">Select Status...</option>
                            <option value="passed">Passed</option>
                            <option value="failed">Failed</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quality Notes</label>
                        <textarea name="quality_notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" placeholder="Add quality check notes..."></textarea>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6">
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-check mr-2"></i>Submit
                    </button>
                    <button type="button" onclick="closeQualityCheckModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-ban text-red-600 mr-2"></i>
                Cancel Good Receiving
            </h3>

            <form action="{{ route('inbound.good-receivings.cancel', $goodReceiving) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason <span class="text-red-500">*</span></label>
                    <textarea name="cancellation_reason" rows="4" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="Please provide a reason for cancellation..." required></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-ban mr-2"></i>Cancel GR
                    </button>
                    <button type="button" onclick="closeCancelModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openQualityCheckModal() {
    document.getElementById('qualityCheckModal').classList.remove('hidden');
}

function closeQualityCheckModal() {
    document.getElementById('qualityCheckModal').classList.add('hidden');
}

function openCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQualityCheckModal();
        closeCancelModal();
    }
});
</script>
@endpush

@endsection