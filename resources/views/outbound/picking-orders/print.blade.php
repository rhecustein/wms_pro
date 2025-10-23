<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Picking List - {{ $pickingOrder->picking_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            @page { margin: 1cm; }
        }
        body { font-family: 'Arial', sans-serif; }
    </style>
</head>
<body class="bg-white p-8">

    {{-- Print Button --}}
    <div class="no-print mb-4 flex justify-end space-x-2">
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-print mr-2"></i>Print
        </button>
        <button onclick="window.close()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i class="fas fa-times mr-2"></i>Close
        </button>
    </div>

    {{-- Header --}}
    <div class="border-4 border-gray-800 p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">PICKING LIST</h1>
                <p class="text-sm text-gray-600">Warehouse Picking Order</p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold text-gray-900">{{ $pickingOrder->picking_number }}</div>
                <div class="mt-2">
                    @php
                        $priorityColors = [
                            'urgent' => 'bg-red-600',
                            'high' => 'bg-orange-500',
                            'medium' => 'bg-blue-500',
                            'low' => 'bg-gray-500'
                        ];
                        $color = $priorityColors[$pickingOrder->priority] ?? 'bg-gray-500';
                    @endphp
                    <span class="px-4 py-2 {{ $color }} text-white font-bold text-lg rounded">
                        {{ strtoupper($pickingOrder->priority) }} PRIORITY
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mt-6 border-t-2 border-gray-300 pt-4">
            <div>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1 font-semibold text-gray-700 w-1/3">Picking Date:</td>
                        <td class="py-1 text-gray-900">{{ $pickingOrder->picking_date->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-semibold text-gray-700">SO Number:</td>
                        <td class="py-1 text-gray-900 font-bold">{{ $pickingOrder->salesOrder->so_number }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-semibold text-gray-700">Customer:</td>
                        <td class="py-1 text-gray-900">{{ $pickingOrder->salesOrder->customer->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-semibold text-gray-700">Warehouse:</td>
                        <td class="py-1 text-gray-900">{{ $pickingOrder->warehouse->name }}</td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1 font-semibold text-gray-700 w-1/3">Picking Type:</td>
                        <td class="py-1 text-gray-900">{{ ucfirst(str_replace('_', ' ', $pickingOrder->picking_type)) }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-semibold text-gray-700">Assigned To:</td>
                        <td class="py-1 text-gray-900">{{ $pickingOrder->assignedUser->name ?? 'Unassigned' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-semibold text-gray-700">Total Items:</td>
                        <td class="py-1 text-gray-900 font-bold">{{ $pickingOrder->total_items }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-semibold text-gray-700">Total Quantity:</td>
                        <td class="py-1 text-gray-900 font-bold">{{ number_format($pickingOrder->total_quantity) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($pickingOrder->notes)
            <div class="mt-4 p-3 bg-yellow-100 border-2 border-yellow-400 rounded">
                <p class="text-sm font-semibold text-yellow-900">NOTES:</p>
                <p class="text-sm text-yellow-800">{{ $pickingOrder->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Picking Items --}}
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4 bg-gray-800 text-white px-4 py-2">ITEMS TO PICK</h2>
        
        <table class="w-full border-2 border-gray-800">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border-2 border-gray-800 px-3 py-2 text-left font-bold text-xs">SEQ</th>
                    <th class="border-2 border-gray-800 px-3 py-2 text-left font-bold text-xs">PRODUCT INFO</th>
                    <th class="border-2 border-gray-800 px-3 py-2 text-left font-bold text-xs">LOCATION</th>
                    <th class="border-2 border-gray-800 px-3 py-2 text-left font-bold text-xs">BATCH/SERIAL</th>
                    <th class="border-2 border-gray-800 px-3 py-2 text-center font-bold text-xs">QTY REQ</th>
                    <th class="border-2 border-gray-800 px-3 py-2 text-center font-bold text-xs">QTY PICKED</th>
                    <th class="border-2 border-gray-800 px-3 py-2 text-center font-bold text-xs">✓</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pickingOrder->items->sortBy('pick_sequence') as $item)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="border-2 border-gray-800 px-3 py-3 text-center">
                            <div class="w-10 h-10 bg-gray-800 text-white rounded-full flex items-center justify-center font-bold text-lg mx-auto">
                                {{ $item->pick_sequence }}
                            </div>
                        </td>
                        <td class="border-2 border-gray-800 px-3 py-3">
                            <div class="font-bold text-gray-900">{{ $item->product->name }}</div>
                            <div class="text-xs text-gray-600">SKU: {{ $item->product->sku }}</div>
                            @if($item->expiry_date)
                                <div class="text-xs text-red-600 font-semibold mt-1">
                                    EXP: {{ $item->expiry_date->format('d M Y') }}
                                </div>
                            @endif
                        </td>
                        <td class="border-2 border-gray-800 px-3 py-3">
                            <div class="font-bold text-lg text-blue-600">{{ $item->storageBin->bin_code }}</div>
                            <div class="text-xs text-gray-600">{{ $item->storageBin->location ?? 'N/A' }}</div>
                        </td>
                        <td class="border-2 border-gray-800 px-3 py-3">
                            @if($item->batch_number)
                                <div class="text-sm"><strong>Batch:</strong> {{ $item->batch_number }}</div>
                            @endif
                            @if($item->serial_number)
                                <div class="text-sm"><strong>Serial:</strong> {{ $item->serial_number }}</div>
                            @endif
                            @if(!$item->batch_number && !$item->serial_number)
                                <div class="text-sm text-gray-400">N/A</div>
                            @endif
                        </td>
                        <td class="border-2 border-gray-800 px-3 py-3 text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($item->quantity_requested) }}</div>
                            <div class="text-xs text-gray-600">{{ $item->unit_of_measure }}</div>
                        </td>
                        <td class="border-2 border-gray-800 px-3 py-3">
                            <div class="h-16 border-2 border-dashed border-gray-400 bg-gray-50"></div>
                        </td>
                        <td class="border-2 border-gray-800 px-3 py-3">
                            <div class="w-12 h-12 border-2 border-gray-400 mx-auto"></div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Summary Section --}}
    <div class="border-4 border-gray-800 p-4 mb-6">
        <div class="grid grid-cols-3 gap-6 text-center">
            <div>
                <div class="text-sm font-semibold text-gray-700 mb-2">TOTAL ITEMS</div>
                <div class="text-4xl font-bold text-gray-900">{{ $pickingOrder->total_items }}</div>
            </div>
            <div>
                <div class="text-sm font-semibold text-gray-700 mb-2">TOTAL QUANTITY</div>
                <div class="text-4xl font-bold text-gray-900">{{ number_format($pickingOrder->total_quantity) }}</div>
            </div>
            <div>
                <div class="text-sm font-semibold text-gray-700 mb-2">COMPLETION %</div>
                <div class="text-4xl font-bold text-gray-900">_____%</div>
            </div>
        </div>
    </div>

    {{-- Signature Section --}}
    <div class="grid grid-cols-3 gap-6 mt-8">
        <div class="border-2 border-gray-800 p-4">
            <div class="text-sm font-bold text-gray-700 mb-4">PICKED BY</div>
            <div class="h-20 border-b-2 border-gray-800 mb-2"></div>
            <div class="text-xs text-gray-600">Name: _________________</div>
            <div class="text-xs text-gray-600">Date: _________________</div>
            <div class="text-xs text-gray-600">Time: _________________</div>
        </div>
        <div class="border-2 border-gray-800 p-4">
            <div class="text-sm font-bold text-gray-700 mb-4">CHECKED BY</div>
            <div class="h-20 border-b-2 border-gray-800 mb-2"></div>
            <div class="text-xs text-gray-600">Name: _________________</div>
            <div class="text-xs text-gray-600">Date: _________________</div>
            <div class="text-xs text-gray-600">Time: _________________</div>
        </div>
        <div class="border-2 border-gray-800 p-4">
            <div class="text-sm font-bold text-gray-700 mb-4">APPROVED BY</div>
            <div class="h-20 border-b-2 border-gray-800 mb-2"></div>
            <div class="text-xs text-gray-600">Name: _________________</div>
            <div class="text-xs text-gray-600">Date: _________________</div>
            <div class="text-xs text-gray-600">Time: _________________</div>
        </div>
    </div>

    {{-- Instructions --}}
    <div class="mt-6 p-4 bg-gray-100 border-2 border-gray-400">
        <h3 class="text-sm font-bold text-gray-900 mb-2">PICKING INSTRUCTIONS:</h3>
        <ul class="text-xs text-gray-700 space-y-1">
            <li>1. Follow the sequence number order for efficient picking</li>
            <li>2. Verify product SKU and location before picking</li>
            <li>3. Check batch numbers and expiry dates carefully</li>
            <li>4. Write actual picked quantity in the QTY PICKED column</li>
            <li>5. Check the box (✓) after picking each item</li>
            <li>6. Report any discrepancies or issues immediately</li>
            <li>7. Sign and date after completing all picks</li>
        </ul>
    </div>

    {{-- Footer --}}
    <div class="text-center text-xs text-gray-500 mt-6">
        <p>Generated on {{ now()->format('d M Y, H:i:s') }}</p>
        <p>Document: {{ $pickingOrder->picking_number }}</p>
    </div>

    <script>
        // Auto print on page load (optional)
        // window.onload = function() { window.print(); }
    </script>

</body>
</html>