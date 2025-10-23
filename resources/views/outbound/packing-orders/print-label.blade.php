<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Label - {{ $packingOrder->packing_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .page-break {
                page-break-after: always;
            }
        }
        
        @page {
            size: A4;
            margin: 10mm;
        }

        .barcode {
            font-family: 'Libre Barcode 128 Text', cursive;
            font-size: 48px;
            letter-spacing: 0;
        }
    </style>
</head>
<body class="bg-white">
    
    {{-- Print Button --}}
    <div class="no-print fixed top-4 right-4 z-50">
        <button onclick="window.print()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-lg">
            <i class="fas fa-print mr-2"></i>Print Label
        </button>
        <button onclick="window.close()" class="ml-2 px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition shadow-lg">
            <i class="fas fa-times mr-2"></i>Close
        </button>
    </div>

    {{-- Label Container --}}
    <div class="max-w-4xl mx-auto p-8">
        
        {{-- Main Label --}}
        <div class="border-4 border-gray-800 rounded-lg p-8 mb-8">
            
            {{-- Header --}}
            <div class="flex items-center justify-between mb-8 pb-6 border-b-2 border-gray-300">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">PACKING LABEL</h1>
                    <p class="text-lg text-gray-600 mt-1">{{ $packingOrder->warehouse->name }}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Date</div>
                    <div class="text-xl font-bold text-gray-900">{{ $packingOrder->packing_date->format('d M Y') }}</div>
                </div>
            </div>

            {{-- Packing Number with Barcode --}}
            <div class="text-center mb-8 pb-6 border-b-2 border-gray-300">
                <div class="text-sm text-gray-600 mb-2">PACKING NUMBER</div>
                <div class="text-4xl font-bold text-gray-900 mb-4">{{ $packingOrder->packing_number }}</div>
                <div class="barcode text-center" style="font-size: 64px;">
                    *{{ $packingOrder->packing_number }}*
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="grid grid-cols-2 gap-8 mb-8 pb-6 border-b-2 border-gray-300">
                <div>
                    <div class="text-sm font-semibold text-gray-600 mb-2">SHIP TO:</div>
                    <div class="text-xl font-bold text-gray-900">{{ $packingOrder->salesOrder->customer->name ?? 'N/A' }}</div>
                    @if($packingOrder->salesOrder->customer)
                        <div class="text-base text-gray-700 mt-2">
                            {{ $packingOrder->salesOrder->customer->address ?? '' }}<br>
                            {{ $packingOrder->salesOrder->customer->city ?? '' }} {{ $packingOrder->salesOrder->customer->postal_code ?? '' }}<br>
                            {{ $packingOrder->salesOrder->customer->phone ?? '' }}
                        </div>
                    @endif
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-600 mb-2">ORDER DETAILS:</div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Sales Order:</span>
                            <span class="font-bold text-gray-900">{{ $packingOrder->salesOrder->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Picking Order:</span>
                            <span class="font-bold text-gray-900">{{ $packingOrder->pickingOrder->picking_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Total Boxes:</span>
                            <span class="font-bold text-gray-900">{{ $packingOrder->total_boxes }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Total Weight:</span>
                            <span class="font-bold text-gray-900">{{ number_format($packingOrder->total_weight_kg, 2) }} kg</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items List --}}
            <div class="mb-6">
                <div class="text-lg font-bold text-gray-900 mb-4">CONTENTS:</div>
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Product</th>
                            <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Box</th>
                            <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Quantity</th>
                            <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packingOrder->items as $item)
                            <tr class="border-b border-gray-200">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $item->product->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $item->product->sku ?? '-' }}</div>
                                    @if($item->batch_number)
                                        <div class="text-xs text-gray-500">Batch: {{ $item->batch_number }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 bg-gray-200 text-gray-900 font-semibold rounded">
                                        {{ $item->box_number ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    {{ number_format($item->quantity_packed) }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    {{ number_format($item->box_weight_kg ?? 0, 2) }} kg
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="2" class="px-4 py-3 text-right font-bold text-gray-900">TOTAL:</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">
                                {{ number_format($packingOrder->items->sum('quantity_packed')) }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">
                                {{ number_format($packingOrder->total_weight_kg, 2) }} kg
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Footer --}}
            <div class="mt-8 pt-6 border-t-2 border-gray-300">
                <div class="grid grid-cols-3 gap-8">
                    <div>
                        <div class="text-sm font-semibold text-gray-600 mb-2">PACKED BY:</div>
                        <div class="text-base font-bold text-gray-900">{{ $packingOrder->assignedUser->name ?? '-' }}</div>
                        <div class="text-sm text-gray-600">{{ $packingOrder->completed_at?->format('d M Y, H:i') ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-600 mb-2">VERIFIED BY:</div>
                        <div class="h-16 border-b-2 border-gray-400"></div>
                        <div class="text-xs text-gray-500 mt-1">Signature & Date</div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-600 mb-2">RECEIVED BY:</div>
                        <div class="h-16 border-b-2 border-gray-400"></div>
                        <div class="text-xs text-gray-500 mt-1">Signature & Date</div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($packingOrder->notes)
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-300 rounded">
                    <div class="text-sm font-semibold text-gray-700 mb-1">NOTES:</div>
                    <div class="text-sm text-gray-800">{{ $packingOrder->notes }}</div>
                </div>
            @endif

        </div>

        {{-- Box Labels (if multiple boxes) --}}
        @if($packingOrder->total_boxes > 1)
            @foreach($packingOrder->items->groupBy('box_number') as $boxNumber => $boxItems)
                <div class="page-break border-4 border-gray-800 rounded-lg p-6 mb-8">
                    <div class="text-center mb-6">
                        <div class="text-2xl font-bold text-gray-900">BOX LABEL</div>
                        <div class="text-xl text-gray-600 mt-2">{{ $packingOrder->packing_number }}</div>
                    </div>

                    <div class="text-center mb-6 pb-6 border-b-2 border-gray-300">
                        <div class="text-6xl font-bold text-gray-900 mb-2">BOX {{ $boxNumber }}</div>
                        <div class="text-2xl text-gray-600">of {{ $packingOrder->total_boxes }}</div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between text-lg">
                            <span class="text-gray-700">Customer:</span>
                            <span class="font-bold text-gray-900">{{ $packingOrder->salesOrder->customer->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between text-lg">
                            <span class="text-gray-700">Order:</span>
                            <span class="font-bold text-gray-900">{{ $packingOrder->salesOrder->order_number }}</span>
                        </div>
                        <div class="flex justify-between text-lg">
                            <span class="text-gray-700">Items in Box:</span>
                            <span class="font-bold text-gray-900">{{ $boxItems->count() }}</span>
                        </div>
                        <div class="flex justify-between text-lg">
                            <span class="text-gray-700">Box Weight:</span>
                            <span class="font-bold text-gray-900">{{ number_format($boxItems->sum('box_weight_kg'), 2) }} kg</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t-2 border-gray-300">
                        <div class="text-sm font-semibold text-gray-600 mb-3">CONTENTS:</div>
                        <ul class="space-y-2">
                            @foreach($boxItems as $item)
                                <li class="flex justify-between">
                                    <span class="text-gray-800">{{ $item->product->name }}</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($item->quantity_packed) }} pcs</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-8 text-center">
                        <div class="barcode" style="font-size: 48px;">
                            *{{ $packingOrder->packing_number }}-{{ $boxNumber }}*
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>

</body>
</html>