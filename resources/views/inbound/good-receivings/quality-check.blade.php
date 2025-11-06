@extends('layouts.app')

@section('title', 'Quality Check - Good Receiving')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-clipboard-check text-purple-600 mr-2"></i>
                Quality Check
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $goodReceiving->gr_number }}</p>
        </div>
        <a href="{{ route('inbound.good-receivings.show', $goodReceiving) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Details
        </a>
    </div>

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

            {{-- GR Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                    Good Receiving Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">GR Number</label>
                        <p class="text-base font-semibold text-gray-900 mt-1 font-mono">{{ $goodReceiving->gr_number }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Supplier</label>
                        <p class="text-base text-gray-900 mt-1">{{ $goodReceiving->supplier->name }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Warehouse</label>
                        <p class="text-base text-gray-900 mt-1">{{ $goodReceiving->warehouse->name }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Receiving Date</label>
                        <p class="text-base text-gray-900 mt-1">{{ $goodReceiving->receiving_date->format('d M Y, H:i') }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Current Status</label>
                        <div class="mt-1">{!! $goodReceiving->status_badge !!}</div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Received By</label>
                        <p class="text-base text-gray-900 mt-1">{{ $goodReceiving->receivedBy->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Items Inspection --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-boxes text-purple-600 mr-2"></i>
                    Items Inspection
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Expected</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Received</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
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
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->quantity_received >= $item->quantity_expected)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Complete
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation mr-1"></i>Short
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm text-gray-600">{{ $item->notes ?? '-' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quality Check Form --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-clipboard-check text-purple-600 mr-2"></i>
                    Quality Check Assessment
                </h3>

                <form action="{{ route('inbound.good-receivings.quality-check', $goodReceiving) }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        {{-- Quality Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Quality Status <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-green-50 transition {{ old('quality_status') === 'passed' ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                                    <input type="radio" name="quality_status" value="passed" class="sr-only" {{ old('quality_status') === 'passed' ? 'checked' : '' }} required>
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">Passed</div>
                                            <div class="text-xs text-gray-500">All items OK</div>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-red-50 transition {{ old('quality_status') === 'failed' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                                    <input type="radio" name="quality_status" value="failed" class="sr-only" {{ old('quality_status') === 'failed' ? 'checked' : '' }}>
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-times-circle text-2xl text-red-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">Failed</div>
                                            <div class="text-xs text-gray-500">Items rejected</div>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-yellow-50 transition {{ old('quality_status') === 'partial' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200' }}">
                                    <input type="radio" name="quality_status" value="partial" class="sr-only" {{ old('quality_status') === 'partial' ? 'checked' : '' }}>
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-exclamation-circle text-2xl text-yellow-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">Partial</div>
                                            <div class="text-xs text-gray-500">Some issues</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('quality_status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Quality Notes --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quality Notes</label>
                            <textarea name="quality_notes" rows="6" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" placeholder="Provide detailed quality check notes including:
- Physical condition of items
- Packaging quality
- Any damages or defects found
- Temperature/storage condition (if applicable)
- Other observations">{{ old('quality_notes') }}</textarea>
                            @error('quality_notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="flex space-x-3 pt-4">
                            <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                                <i class="fas fa-check-circle mr-2"></i>Submit Quality Check
                            </button>
                            <a href="{{ route('inbound.good-receivings.show', $goodReceiving) }}" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6 space-y-6">
                
                {{-- Summary --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
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

                {{-- Inspection Checklist --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-tasks text-purple-600 mr-2"></i>
                        Inspection Checklist
                    </h3>

                    <div class="space-y-2 text-sm">
                        <div class="flex items-start">
                            <i class="fas fa-check-square text-purple-600 mr-2 mt-1"></i>
                            <span class="text-gray-700">Verify quantity matches PO/Shipment</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-square text-purple-600 mr-2 mt-1"></i>
                            <span class="text-gray-700">Inspect packaging condition</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-square text-purple-600 mr-2 mt-1"></i>
                            <span class="text-gray-700">Check for damages or defects</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-square text-purple-600 mr-2 mt-1"></i>
                            <span class="text-gray-700">Verify product specifications</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-square text-purple-600 mr-2 mt-1"></i>
                            <span class="text-gray-700">Check expiry dates (if applicable)</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-square text-purple-600 mr-2 mt-1"></i>
                            <span class="text-gray-700">Verify batch/lot numbers</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-square text-purple-600 mr-2 mt-1"></i>
                            <span class="text-gray-700">Document any discrepancies</span>
                        </div>
                    </div>
                </div>

                {{-- Guidelines --}}
                <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                    <h4 class="font-semibold text-purple-800 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>Guidelines
                    </h4>
                    <ul class="text-xs text-purple-700 space-y-1">
                        <li>• Be thorough in your inspection</li>
                        <li>• Document all findings clearly</li>
                        <li>• Take photos of any damages</li>
                        <li>• Report discrepancies immediately</li>
                        <li>• Follow safety protocols</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
// Auto-select radio button on label click
document.querySelectorAll('label[for^="quality_status"]').forEach(label => {
    label.addEventListener('click', function() {
        const radio = this.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            // Update border styles
            document.querySelectorAll('label').forEach(l => {
                l.classList.remove('border-green-500', 'border-red-500', 'border-yellow-500', 'bg-green-50', 'bg-red-50', 'bg-yellow-50');
                l.classList.add('border-gray-200');
            });
            
            if (radio.value === 'passed') {
                this.classList.add('border-green-500', 'bg-green-50');
            } else if (radio.value === 'failed') {
                this.classList.add('border-red-500', 'bg-red-50');
            } else if (radio.value === 'partial') {
                this.classList.add('border-yellow-500', 'bg-yellow-50');
            }
        }
    });
});
</script>
@endpush

@endsection