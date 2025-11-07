<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Picking List - {{ $pickingOrder->picking_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            @page { 
                margin: 1cm; 
                size: A4;
            }
            .page-break { page-break-after: always; }
        }
        body { 
            font-family: 'Arial', sans-serif; 
            background: white;
        }
        .barcode-font {
            font-family: 'Courier New', monospace;
            letter-spacing: 0.1em;
        }
    </style>
</head>
<body class="bg-white p-8">

    {{-- Print Button --}}
    <div class="no-print mb-6 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border-2 border-blue-200">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Print Preview</h2>
            <p class="text-sm text-gray-600">Review before printing</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all font-bold shadow-lg">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <button onclick="window.close()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all font-bold">
                <i class="fas fa-times mr-2"></i>Close
            </button>
        </div>
    </div>

    {{-- Header --}}
    <div class="border-4 border-gray-900 rounded-lg p-6 mb-6 bg-white shadow-lg">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2 tracking-tight">PICKING LIST</h1>
                <p class="text-sm text-gray-600 uppercase font-semibold">Warehouse Picking Order</p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold text-gray-900 barcode-font mb-3">
                    {{ $pickingOrder->picking_number }}
                </div>
                <div class="mt-2">
                    @php
                        $priorityColors = [
                            'urgent' => 'bg-red-600',
                            'high' => 'bg-orange-500',
                            'medium' => 'bg-blue-600',
                            'low' => 'bg-gray-600'
                        ];
                        $color = $priorityColors[$pickingOrder->priority] ?? 'bg-gray-600';
                    @endphp
                    <span class="px-5 py-2 {{ $color }} text-white font-bold text-base rounded-lg shadow-md inline-block">
                        <i class="fas fa-flag mr-2"></i>{{ strtoupper($pickingOrder->priority) }} PRIORITY
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mt-6 border-t-2 border-gray-300 pt-6">
            <div class="space-y-3">
                <div class="flex items-center">
                    <i class="fas fa-calendar-alt text-gray-500 w-6"></i>
                    <span class="font-semibold text-gray-700 w-32">Picking Date:</span>
                    <span class="text-gray-900">{{ $pickingOrder->picking_date->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-file-invoice text-gray-500 w-6"></i>
                    <span class="font-semibold text-gray-700 w-32">SO Number:</span>
                    <span class="text-gray-900 font-bold">{{ $pickingOrder->salesOrder->so_number }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-user text-gray-500 w-6"></i>
                    <span class="font-semibold text-gray-700 w-32">Customer:</span>
                    <span class="text-gray-900">{{ $pickingOrder->salesOrder->customer->name ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-warehouse text-gray-500 w-6"></i>
                    <span class="font-semibold text-gray-700 w-32">Warehouse:</span>
                    <span class="text-gray-900 font-bold">{{ $pickingOrder->warehouse->name }}</span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex items-center">
                    <i class="fas fa-layer-group text-gray-500 w-6"></i>
                    <span class="font-semibold text-gray-700 w-32">Picking Type:</span>
                    <span class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $pickingOrder->picking_type)) }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-user-check text-gray-500 w-6"></i>
                    <span class="font-semibold text-gray-700 w-32">Assigned To:</span>
                    <span class="text-gray-900 font-bold">{{ $pickingOrder->assignedUser->name ?? 'Unassigned' }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-boxes text-gray-500 w-6"></i>
                    <span class="font-semibold text-gray-700 w-32">Total Items:</span>
                    <span class="text-gray-900 font-bold text-lg">{{ $pickingOrder->total_items }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-cubes text-gray-500 w-6"></i>
                    <span class="font-semibold text-gray-700 w-32">Total Quantity:</span>
                    <span class="text-gray-900 font-bold text-lg">{{ number_format($pickingOrder->total_quantity) }}</span>
                </div>
            </div>
        </div>

        @if($pickingOrder->notes)
            <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                <p class="text-sm font-bold text-yellow-900 mb-1 flex items-center">
                    <i class="fas fa-sticky-note mr-2"></i>SPECIAL NOTES:
                </p>
                <p class="text-sm text-yellow-800">{{ $pickingOrder->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Picking Items --}}
    <div class="mb-6">
        <div class="bg-gray-900 text-white px-6 py-4 rounded-t-lg">
            <h2 class="text-2xl font-bold flex items-center">
                <i class="fas fa-clipboard-list mr-3"></i>
                ITEMS TO PICK ({{ $pickingOrder->items->count() }})
            </h2>
        </div>
        
        <table class="w-full border-4 border-gray-900">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border-2 border-gray-900 px-3 py-3 text-center font-bold text-xs uppercase w-16">SEQ</th>
                    <th class="border-2 border-gray-900 px-3 py-3 text-left font-bold text-xs uppercase">Product Info</th>
                    <th class="border-2 border-gray-900 px-3 py-3 text-left font-bold text-xs uppercase w-40">Location</th>
                    <th class="border-2 border-gray-900 px-3 py-3 text-left font-bold text-xs uppercase w-32">Batch/Lot/Serial</th>
                    <th class="border-2 border-gray-900 px-3 py-3 text-center font-bold text-xs uppercase w-24">Qty Req</th>
                    <th class="border-2 border-gray-900 px-3 py-3 text-center font-bold text-xs uppercase w-24">Qty Picked</th>
                    <th class="border-2 border-gray-900 px-3 py-3 text-center font-bold text-xs uppercase w-12">✓</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pickingOrder->items->sortBy('pick_sequence') as $item)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        {{-- Sequence --}}
                        <td class="border-2 border-gray-900 px-3 py-4 text-center align-top">
                            <div class="w-12 h-12 bg-gray-900 text-white rounded-full flex items-center justify-center font-bold text-xl mx-auto shadow-md">
                                {{ $item->pick_sequence }}
                            </div>
                        </td>
                        
                        {{-- Product Info --}}
                        <td class="border-2 border-gray-900 px-3 py-4 align-top">
                            <div class="font-bold text-gray-900 text-base mb-1">{{ $item->product->name }}</div>
                            <div class="text-xs text-gray-600 space-y-0.5">
                                <div><span class="font-semibold">SKU:</span> {{ $item->product->sku ?? $item->product->barcode ?? 'N/A' }}</div>
                                @if($item->expiry_date)
                                    <div class="text-red-600 font-bold bg-red-50 px-2 py-1 rounded inline-block mt-1">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        EXP: {{ \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        
                        {{-- Location - FIXED --}}
                        <td class="border-2 border-gray-900 px-3 py-4 align-top">
                            @if($item->storageBin)
                                <div class="font-bold text-2xl text-blue-700 barcode-font mb-1">
                                    {{ $item->storageBin->code ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ $item->storageBin->name ?? '' }}
                                </div>
                                @if($item->storageBin->zone || $item->storageBin->aisle)
                                    <div class="text-xs text-gray-500 mt-1">
                                        @if($item->storageBin->zone)
                                            Zone: {{ $item->storageBin->zone }}
                                        @endif
                                        @if($item->storageBin->aisle)
                                            | Aisle: {{ $item->storageBin->aisle }}
                                        @endif
                                    </div>
                                @endif
                            @else
                                <div class="text-gray-400 text-sm">No location</div>
                            @endif
                        </td>
                        
                        {{-- Batch/Lot/Serial --}}
                        <td class="border-2 border-gray-900 px-3 py-4 align-top">
                            <div class="text-xs space-y-1">
                                @if($item->batch_number)
                                    <div class="bg-blue-50 px-2 py-1 rounded">
                                        <strong class="text-blue-900">Batch:</strong> 
                                        <span class="text-blue-700 font-mono">{{ $item->batch_number }}</span>
                                    </div>
                                @endif
                                @if($item->lot_number)
                                    <div class="bg-green-50 px-2 py-1 rounded">
                                        <strong class="text-green-900">Lot:</strong> 
                                        <span class="text-green-700 font-mono">{{ $item->lot_number }}</span>
                                    </div>
                                @endif
                                @if($item->serial_number)
                                    <div class="bg-purple-50 px-2 py-1 rounded">
                                        <strong class="text-purple-900">Serial:</strong> 
                                        <span class="text-purple-700 font-mono">{{ $item->serial_number }}</span>
                                    </div>
                                @endif
                                @if(!$item->batch_number && !$item->lot_number && !$item->serial_number)
                                    <div class="text-gray-400 text-center">N/A</div>
                                @endif
                            </div>
                        </td>
                        
                        {{-- Qty Requested --}}
                        <td class="border-2 border-gray-900 px-3 py-4 text-center align-top">
                            <div class="text-3xl font-bold text-gray-900">{{ number_format($item->quantity_requested, 0) }}</div>
                            <div class="text-xs text-gray-600 mt-1 font-semibold">{{ $item->unit_of_measure }}</div>
                        </td>
                        
                        {{-- Qty Picked (Blank for manual entry) --}}
                        <td class="border-2 border-gray-900 px-2 py-4">
                            <div class="h-20 border-2 border-dashed border-gray-400 bg-yellow-50 rounded"></div>
                        </td>
                        
                        {{-- Checkbox --}}
                        <td class="border-2 border-gray-900 px-2 py-4">
                            <div class="w-14 h-14 border-4 border-gray-900 mx-auto rounded"></div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Summary Section --}}
    <div class="border-4 border-gray-900 rounded-lg p-6 mb-6 bg-gradient-to-r from-gray-50 to-gray-100">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-calculator mr-2"></i>PICKING SUMMARY
        </h3>
        <div class="grid grid-cols-3 gap-6 text-center">
            <div class="bg-white p-4 rounded-lg border-2 border-gray-300">
                <div class="text-xs font-bold text-gray-600 mb-2 uppercase">Total Items</div>
                <div class="text-5xl font-bold text-blue-600">{{ $pickingOrder->total_items }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg border-2 border-gray-300">
                <div class="text-xs font-bold text-gray-600 mb-2 uppercase">Total Quantity</div>
                <div class="text-5xl font-bold text-green-600">{{ number_format($pickingOrder->total_quantity, 0) }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg border-2 border-gray-300">
                <div class="text-xs font-bold text-gray-600 mb-2 uppercase">Completion %</div>
                <div class="text-5xl font-bold text-gray-900">_____%</div>
            </div>
        </div>
    </div>

    {{-- Signature Section --}}
    <div class="grid grid-cols-3 gap-6 mt-8">
        <div class="border-4 border-gray-900 rounded-lg p-5 bg-white">
            <div class="text-sm font-bold text-gray-900 mb-4 uppercase flex items-center">
                <i class="fas fa-user-check text-blue-600 mr-2"></i>Picked By
            </div>
            <div class="h-24 border-b-4 border-gray-900 mb-3"></div>
            <div class="space-y-2 text-xs text-gray-700">
                <div class="flex items-center">
                    <i class="fas fa-user w-4 text-gray-500"></i>
                    <span class="ml-2">Name: ____________________</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-calendar w-4 text-gray-500"></i>
                    <span class="ml-2">Date: ____________________</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-clock w-4 text-gray-500"></i>
                    <span class="ml-2">Time: ____________________</span>
                </div>
            </div>
        </div>
        
        <div class="border-4 border-gray-900 rounded-lg p-5 bg-white">
            <div class="text-sm font-bold text-gray-900 mb-4 uppercase flex items-center">
                <i class="fas fa-clipboard-check text-green-600 mr-2"></i>Checked By
            </div>
            <div class="h-24 border-b-4 border-gray-900 mb-3"></div>
            <div class="space-y-2 text-xs text-gray-700">
                <div class="flex items-center">
                    <i class="fas fa-user w-4 text-gray-500"></i>
                    <span class="ml-2">Name: ____________________</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-calendar w-4 text-gray-500"></i>
                    <span class="ml-2">Date: ____________________</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-clock w-4 text-gray-500"></i>
                    <span class="ml-2">Time: ____________________</span>
                </div>
            </div>
        </div>
        
        <div class="border-4 border-gray-900 rounded-lg p-5 bg-white">
            <div class="text-sm font-bold text-gray-900 mb-4 uppercase flex items-center">
                <i class="fas fa-user-shield text-purple-600 mr-2"></i>Approved By
            </div>
            <div class="h-24 border-b-4 border-gray-900 mb-3"></div>
            <div class="space-y-2 text-xs text-gray-700">
                <div class="flex items-center">
                    <i class="fas fa-user w-4 text-gray-500"></i>
                    <span class="ml-2">Name: ____________________</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-calendar w-4 text-gray-500"></i>
                    <span class="ml-2">Date: ____________________</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-clock w-4 text-gray-500"></i>
                    <span class="ml-2">Time: ____________________</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Instructions --}}
    <div class="mt-8 p-6 bg-blue-50 border-4 border-blue-300 rounded-lg">
        <h3 class="text-base font-bold text-blue-900 mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2"></i>PICKING INSTRUCTIONS
        </h3>
        <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm text-blue-900">
            <div class="flex items-start">
                <span class="font-bold mr-2">1.</span>
                <span>Follow the sequence number order for efficient picking</span>
            </div>
            <div class="flex items-start">
                <span class="font-bold mr-2">2.</span>
                <span>Verify product SKU and location before picking</span>
            </div>
            <div class="flex items-start">
                <span class="font-bold mr-2">3.</span>
                <span>Check batch numbers and expiry dates carefully</span>
            </div>
            <div class="flex items-start">
                <span class="font-bold mr-2">4.</span>
                <span>Write actual picked quantity in the QTY PICKED column</span>
            </div>
            <div class="flex items-start">
                <span class="font-bold mr-2">5.</span>
                <span>Check the box (✓) after picking each item</span>
            </div>
            <div class="flex items-start">
                <span class="font-bold mr-2">6.</span>
                <span>Report any discrepancies or issues immediately</span>
            </div>
            <div class="flex items-start">
                <span class="font-bold mr-2">7.</span>
                <span>Sign and date after completing all picks</span>
            </div>
            <div class="flex items-start">
                <span class="font-bold mr-2">8.</span>
                <span>Ensure proper handling of fragile items</span>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="mt-8 pt-6 border-t-2 border-gray-300">
        <div class="flex justify-between items-center text-xs text-gray-600">
            <div>
                <p><i class="fas fa-calendar-alt mr-1"></i>Generated: {{ now()->format('d M Y, H:i:s') }}</p>
                <p><i class="fas fa-hashtag mr-1"></i>Document: {{ $pickingOrder->picking_number }}</p>
            </div>
            <div class="text-right">
                <p><i class="fas fa-warehouse mr-1"></i>{{ $pickingOrder->warehouse->name }}</p>
                <p><i class="fas fa-print mr-1"></i>Page 1 of 1</p>
            </div>
        </div>
    </div>

    <script>
        // Auto print on page load (optional - uncomment to enable)
        // window.onload = function() { 
        //     setTimeout(() => window.print(), 500); 
        // }

        // Handle print button click
        function printDocument() {
            window.print();
        }

        // Handle close button
        function closeWindow() {
            window.close();
        }
    </script>

</body>
</html>