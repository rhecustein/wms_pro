{{-- resources/views/inventory/movements/print.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Movements Report - Print</title>
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
        }
        
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
    </style>
</head>
<body class="bg-white">
    
    {{-- Print Actions (Hidden when printing) --}}
    <div class="no-print fixed top-4 right-4 z-50 flex gap-2">
        <button onclick="window.print()" 
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-lg flex items-center gap-2">
            <i class="fas fa-print"></i>
            <span>Print Report</span>
        </button>
        <button onclick="window.close()" 
                class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition shadow-lg flex items-center gap-2">
            <i class="fas fa-times"></i>
            <span>Close</span>
        </button>
    </div>

    <div class="p-8 max-w-full">
        
        {{-- Report Header --}}
        <div class="mb-8">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-exchange-alt text-blue-600 mr-2"></i>
                    Stock Movements Report
                </h1>
                <p class="text-gray-600">Generated on {{ now()->format('d F Y, H:i:s') }}</p>
            </div>

            {{-- Filter Summary --}}
            @if(request()->hasAny(['search', 'movement_type', 'warehouse_id', 'reference_type', 'date_from', 'date_to']))
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Applied Filters:</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                    @if(request('search'))
                        <div><span class="font-medium">Search:</span> {{ request('search') }}</div>
                    @endif
                    @if(request('movement_type'))
                        <div><span class="font-medium">Type:</span> {{ ucfirst(request('movement_type')) }}</div>
                    @endif
                    @if(request('warehouse_id'))
                        <div><span class="font-medium">Warehouse ID:</span> {{ request('warehouse_id') }}</div>
                    @endif
                    @if(request('reference_type'))
                        <div><span class="font-medium">Reference:</span> {{ ucwords(str_replace('_', ' ', request('reference_type'))) }}</div>
                    @endif
                    @if(request('date_from'))
                        <div><span class="font-medium">From:</span> {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}</div>
                    @endif
                    @if(request('date_to'))
                        <div><span class="font-medium">To:</span> {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}</div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Summary Stats --}}
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $movements->count() }}</div>
                    <div class="text-xs text-gray-600 uppercase">Total Movements</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $movements->where('movement_type', 'inbound')->count() }}</div>
                    <div class="text-xs text-gray-600 uppercase">Inbound</div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $movements->where('movement_type', 'outbound')->count() }}</div>
                    <div class="text-xs text-gray-600 uppercase">Outbound</div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $movements->where('movement_type', 'transfer')->count() }}</div>
                    <div class="text-xs text-gray-600 uppercase">Transfers</div>
                </div>
            </div>
        </div>

        {{-- Movements Table --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">Date</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">Type</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">Product</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">Warehouse</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">Location</th>
                        <th class="border border-gray-300 px-3 py-2 text-right text-xs font-semibold text-gray-700">Quantity</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">Reference</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700">Performed By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-3 py-2 text-xs">
                            <div class="font-medium text-gray-900">{{ $movement->movement_date->format('d/m/Y') }}</div>
                            <div class="text-gray-500">{{ $movement->movement_date->format('H:i') }}</div>
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs">
                            @php
                                $typeColors = [
                                    'inbound' => 'bg-green-100 text-green-800',
                                    'outbound' => 'bg-red-100 text-red-800',
                                    'transfer' => 'bg-blue-100 text-blue-800',
                                    'adjustment' => 'bg-yellow-100 text-yellow-800',
                                    'putaway' => 'bg-purple-100 text-purple-800',
                                    'picking' => 'bg-orange-100 text-orange-800',
                                    'replenishment' => 'bg-indigo-100 text-indigo-800'
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($movement->movement_type) }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs">
                            <div class="font-medium text-gray-900">{{ $movement->product->name }}</div>
                            <div class="text-gray-500">SKU: {{ $movement->product->sku }}</div>
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs">
                            <div class="text-gray-900">{{ $movement->warehouse->name }}</div>
                            <div class="text-gray-500">{{ $movement->warehouse->code }}</div>
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs">
                            @if($movement->fromBin || $movement->toBin)
                                @if($movement->fromBin)
                                    <div class="text-red-600">From: {{ $movement->fromBin->code }}</div>
                                @endif
                                @if($movement->toBin)
                                    <div class="text-green-600">To: {{ $movement->toBin->code }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs text-right">
                            <div class="font-bold text-gray-900">{{ number_format($movement->quantity, 2) }}</div>
                            <div class="text-gray-500">{{ $movement->uom }}</div>
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs">
                            @if($movement->reference_number)
                                <div class="font-mono text-gray-900">{{ $movement->reference_number }}</div>
                                @if($movement->reference_type)
                                    <div class="text-gray-500">{{ ucwords(str_replace('_', ' ', $movement->reference_type)) }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                            @if($movement->batch_number)
                                <div class="text-gray-500 mt-1">Batch: {{ $movement->batch_number }}</div>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs">
                            @if($movement->performedBy)
                                <div class="text-gray-900">{{ $movement->performedBy->name }}</div>
                            @else
                                <span class="text-gray-500">System</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="border border-gray-300 px-3 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>No stock movements found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="mt-8 pt-4 border-t border-gray-300">
            <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                    <p><strong>Total Records:</strong> {{ $movements->count() }}</p>
                    <p><strong>Report Generated:</strong> {{ now()->format('d F Y, H:i:s') }}</p>
                </div>
                <div class="text-right">
                    <p><strong>Generated By:</strong> {{ auth()->user()->name ?? 'System' }}</p>
                    <p><strong>Page:</strong> <span class="page-number"></span></p>
                </div>
            </div>
        </div>

    </div>

    {{-- Auto print script (optional) --}}
    <script>
        // Uncomment below to auto-print when page loads
        // window.onload = function() {
        //     window.print();
        // }
        
        // Page numbering for print
        let pageNumber = 1;
        const pageNumberElements = document.querySelectorAll('.page-number');
        pageNumberElements.forEach(el => {
            el.textContent = pageNumber;
        });
    </script>

</body>
</html>