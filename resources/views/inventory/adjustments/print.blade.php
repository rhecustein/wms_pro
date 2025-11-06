{{-- resources/views/inventory/adjustments/print.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Adjustment {{ $adjustment->adjustment_number }} - Print</title>
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
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
            .page-break {
                page-break-before: always;
            }
        }
        
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }
    </style>
</head>
<body class="bg-white">
    
    {{-- Print Actions (Hidden when printing) --}}
    <div class="no-print fixed top-4 right-4 z-50 flex gap-2">
        <button onclick="window.print()" 
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-lg flex items-center gap-2">
            <i class="fas fa-print"></i>
            <span>Print Document</span>
        </button>
        <button onclick="window.close()" 
                class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition shadow-lg flex items-center gap-2">
            <i class="fas fa-times"></i>
            <span>Close</span>
        </button>
    </div>

    <div class="p-8 max-w-4xl mx-auto">
        
        {{-- Document Header --}}
        <div class="mb-8 border-b-2 border-gray-300 pb-6">
            <div class="text-center mb-4">
                <h1 class="text-3xl font-bold text-gray-900 mb-1">
                    STOCK ADJUSTMENT
                </h1>
                <p class="text-sm text-gray-600">Inventory Management System</p>
            </div>

            <div class="grid grid-cols-2 gap-6 mt-6">
                <div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 uppercase">Adjustment Number</span>
                        <p class="text-lg font-bold text-gray-900 font-mono">{{ $adjustment->adjustment_number }}</p>
                    </div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 uppercase">Date</span>
                        <p class="text-sm font-semibold text-gray-900">{{ $adjustment->adjustment_date->format('d F Y, H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 uppercase">Warehouse</span>
                        <p class="text-sm font-semibold text-gray-900">{{ $adjustment->warehouse->name }}</p>
                        <p class="text-xs text-gray-600">Code: {{ $adjustment->warehouse->code }}</p>
                    </div>
                </div>

                <div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 uppercase">Type</span>
                        <p class="text-sm font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $adjustment->adjustment_type) }}</p>
                    </div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 uppercase">Reason</span>
                        <p class="text-sm font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $adjustment->reason) }}</p>
                    </div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 uppercase">Status</span>
                        @php
                            $statusColors = [
                                'draft' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                'approved' => 'bg-blue-100 text-blue-800 border-blue-300',
                                'posted' => 'bg-green-100 text-green-800 border-green-300',
                                'cancelled' => 'bg-red-100 text-red-800 border-red-300'
                            ];
                        @endphp
                        <span class="inline-block px-3 py-1 text-xs font-bold uppercase rounded border {{ $statusColors[$adjustment->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                            {{ ucfirst($adjustment->status) }}
                        </span>
                    </div>
                </div>
            </div>

            @if($adjustment->notes)
                <div class="mt-4 bg-gray-50 border border-gray-200 rounded p-3">
                    <span class="text-xs text-gray-500 uppercase font-semibold">Notes:</span>
                    <p class="text-sm text-gray-900 mt-1">{{ $adjustment->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Summary Statistics --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $adjustment->total_items }}</div>
                <div class="text-xs text-gray-600 uppercase mt-1">Total Items</div>
            </div>
            @php
                $totalAdditions = 0;
                $totalReductions = 0;
                foreach($adjustment->items as $item) {
                    $diff = $item->adjusted_quantity - $item->current_quantity;
                    if($diff > 0) $totalAdditions += $diff;
                    if($diff < 0) $totalReductions += abs($diff);
                }
            @endphp
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600">+{{ $totalAdditions }}</div>
                <div class="text-xs text-gray-600 uppercase mt-1">Total Additions</div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-red-600">-{{ $totalReductions }}</div>
                <div class="text-xs text-gray-600 uppercase mt-1">Total Reductions</div>
            </div>
        </div>

        {{-- Adjustment Items Table --}}
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-boxes text-blue-600 mr-2"></i>
                Adjustment Items
            </h3>

            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">No</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">Product</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">Storage Bin</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-xs font-semibold text-gray-700">Current Qty</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-xs font-semibold text-gray-700">Adjusted Qty</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-xs font-semibold text-gray-700">Difference</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">Batch/Serial</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($adjustment->items as $index => $item)
                        @php
                            $difference = $item->adjusted_quantity - $item->current_quantity;
                            $diffClass = $difference > 0 ? 'text-green-700 font-bold' : ($difference < 0 ? 'text-red-700 font-bold' : 'text-gray-700');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-3 py-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 px-3 py-2">
                                <div class="font-semibold text-gray-900">{{ $item->product->name }}</div>
                                <div class="text-xs text-gray-600">SKU: {{ $item->product->sku }}</div>
                            </td>
                            <td class="border border-gray-300 px-3 py-2">
                                <div class="font-mono text-gray-900">{{ $item->storageBin->bin_code }}</div>
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                <span class="font-semibold">{{ number_format($item->current_quantity) }}</span>
                                <div class="text-xs text-gray-600">{{ $item->unit_of_measure }}</div>
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                <span class="font-semibold">{{ number_format($item->adjusted_quantity) }}</span>
                                <div class="text-xs text-gray-600">{{ $item->unit_of_measure }}</div>
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center {{ $diffClass }}">
                                <span class="text-lg">{{ $difference > 0 ? '+' : '' }}{{ $difference }}</span>
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-xs">
                                @if($item->batch_number)
                                    <div>Batch: <span class="font-semibold">{{ $item->batch_number }}</span></div>
                                @endif
                                @if($item->serial_number)
                                    <div>Serial: <span class="font-semibold">{{ $item->serial_number }}</span></div>
                                @endif
                                @if(!$item->batch_number && !$item->serial_number)
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @if($item->reason || $item->notes)
                            <tr>
                                <td colspan="7" class="border border-gray-300 px-3 py-2 bg-gray-50">
                                    <div class="text-xs">
                                        @if($item->reason)
                                            <span class="font-semibold">Reason:</span> {{ $item->reason }}
                                        @endif
                                        @if($item->notes)
                                            <span class="ml-4 font-semibold">Notes:</span> {{ $item->notes }}
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Audit Trail --}}
        <div class="mb-8 border-t-2 border-gray-300 pt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-history text-blue-600 mr-2"></i>
                Audit Information
            </h3>

            <div class="grid grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                    <div class="text-xs text-gray-500 uppercase mb-1">Created By</div>
                    <div class="font-semibold text-gray-900">{{ $adjustment->createdBy->name ?? 'System' }}</div>
                    <div class="text-xs text-gray-600 mt-1">{{ $adjustment->created_at->format('d M Y, H:i') }}</div>
                </div>

                @if($adjustment->approvedBy)
                    <div class="bg-green-50 border border-green-200 rounded p-3">
                        <div class="text-xs text-gray-500 uppercase mb-1">Approved By</div>
                        <div class="font-semibold text-gray-900">{{ $adjustment->approvedBy->name }}</div>
                        <div class="text-xs text-gray-600 mt-1">{{ $adjustment->approved_at->format('d M Y, H:i') }}</div>
                    </div>
                @endif

                @if($adjustment->updatedBy)
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <div class="text-xs text-gray-500 uppercase mb-1">Last Updated By</div>
                        <div class="font-semibold text-gray-900">{{ $adjustment->updatedBy->name }}</div>
                        <div class="text-xs text-gray-600 mt-1">{{ $adjustment->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Signature Section --}}
        <div class="mt-12 pt-8 border-t border-gray-300">
            <div class="grid grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="mb-16">
                        <p class="text-sm font-semibold text-gray-700">Prepared By</p>
                    </div>
                    <div class="border-t-2 border-gray-900 pt-2">
                        <p class="text-sm font-semibold">{{ $adjustment->createdBy->name ?? 'System' }}</p>
                        <p class="text-xs text-gray-600">{{ $adjustment->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="text-center">
                    <div class="mb-16">
                        <p class="text-sm font-semibold text-gray-700">Verified By</p>
                    </div>
                    <div class="border-t-2 border-gray-900 pt-2">
                        <p class="text-sm font-semibold">{{ $adjustment->approvedBy->name ?? '_______________' }}</p>
                        <p class="text-xs text-gray-600">{{ $adjustment->approved_at ? $adjustment->approved_at->format('d M Y') : '_______________' }}</p>
                    </div>
                </div>

                <div class="text-center">
                    <div class="mb-16">
                        <p class="text-sm font-semibold text-gray-700">Approved By</p>
                    </div>
                    <div class="border-t-2 border-gray-900 pt-2">
                        <p class="text-sm font-semibold">_______________</p>
                        <p class="text-xs text-gray-600">_______________</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 pt-4 border-t border-gray-300 text-xs text-gray-600">
            <div class="flex justify-between">
                <div>
                    <p><strong>Document:</strong> {{ $adjustment->adjustment_number }}</p>
                    <p><strong>Printed:</strong> {{ now()->format('d F Y, H:i:s') }}</p>
                </div>
                <div class="text-right">
                    <p><strong>Printed By:</strong> {{ auth()->user()->name ?? 'System' }}</p>
                    <p><strong>Page:</strong> 1 of 1</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>