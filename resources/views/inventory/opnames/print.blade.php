<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Stock Opname - {{ $opname->opname_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .print-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #333;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header .subtitle {
            font-size: 14px;
            color: #666;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            padding: 5px 10px;
            background: #f5f5f5;
        }

        .info-value {
            display: table-cell;
            padding: 5px 10px;
            border-bottom: 1px solid #ddd;
        }

        .summary-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
        }

        .summary-card .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .summary-card .value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background: #333;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-pending {
            background: #ffc107;
            color: #000;
        }

        .badge-counted {
            background: #17a2b8;
            color: white;
        }

        .badge-adjusted {
            background: #28a745;
            color: white;
        }

        .badge-variance {
            background: #dc3545;
            color: white;
        }

        .badge-match {
            background: #28a745;
            color: white;
        }

        .footer-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 30%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            font-weight: bold;
        }

        .signature-label {
            margin-top: 5px;
            font-size: 10px;
            color: #666;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 15mm;
            }
        }

        .print-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Print</button>
        <a href="{{ route('inventory.opnames.show', $opname) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>Stock Opname Report</h1>
            <div class="subtitle">{{ config('app.name', 'Your Company') }}</div>
        </div>

        <!-- Opname Information -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Opname Number</div>
                <div class="info-value">{{ $opname->opname_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Warehouse</div>
                <div class="info-value">{{ $opname->warehouse->name ?? '-' }}</div>
            </div>
            @if($opname->storageArea)
            <div class="info-row">
                <div class="info-label">Storage Area</div>
                <div class="info-value">{{ $opname->storageArea->area_name }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Opname Date</div>
                <div class="info-value">{{ $opname->opname_date->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Type</div>
                <div class="info-value">{{ strtoupper($opname->opname_type) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">{{ strtoupper($opname->status) }}</div>
            </div>
            @if($opname->scheduledBy)
            <div class="info-row">
                <div class="info-label">Scheduled By</div>
                <div class="info-value">{{ $opname->scheduledBy->name }}</div>
            </div>
            @endif
            @if($opname->completedBy)
            <div class="info-row">
                <div class="info-label">Completed By</div>
                <div class="info-value">{{ $opname->completedBy->name }}</div>
            </div>
            @endif
        </div>

        <!-- Summary Cards -->
        @if($opname->status === 'completed')
        <div class="summary-cards">
            <div class="summary-card">
                <div class="label">Total Items</div>
                <div class="value">{{ $opname->total_items_counted }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Variance Items</div>
                <div class="value">{{ $opname->variance_count }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Accuracy</div>
                <div class="value">{{ number_format($opname->accuracy_percentage, 2) }}%</div>
            </div>
            <div class="summary-card">
                <div class="label">Match Items</div>
                <div class="value">{{ $opname->total_items_counted - $opname->variance_count }}</div>
            </div>
        </div>
        @endif

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th style="width: 120px;">Product Code</th>
                    <th>Product Name</th>
                    <th style="width: 100px;">Storage Bin</th>
                    <th style="width: 100px;">Batch/Serial</th>
                    <th class="text-center" style="width: 80px;">System Qty</th>
                    <th class="text-center" style="width: 80px;">Physical Qty</th>
                    <th class="text-center" style="width: 80px;">Variance</th>
                    <th class="text-center" style="width: 80px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($opname->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product->product_code ?? '-' }}</td>
                    <td>{{ $item->product->product_name ?? '-' }}</td>
                    <td>{{ $item->storageBin->bin_code ?? '-' }}</td>
                    <td>
                        @if($item->batch_number)
                            B: {{ $item->batch_number }}
                        @elseif($item->serial_number)
                            S: {{ $item->serial_number }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->system_quantity) }}</td>
                    <td class="text-center">
                        {{ $item->physical_quantity !== null ? number_format($item->physical_quantity) : '-' }}
                    </td>
                    <td class="text-center">
                        @if($item->variance !== null)
                            @if($item->variance != 0)
                                <span class="badge badge-variance">{{ $item->variance > 0 ? '+' : '' }}{{ number_format($item->variance) }}</span>
                            @else
                                <span class="badge badge-match">0</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $item->status }}">{{ $item->status }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No items found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($opname->notes)
        <div style="margin-bottom: 30px;">
            <strong>Notes:</strong>
            <div style="padding: 10px; background: #f9f9f9; border-left: 3px solid #333; margin-top: 5px;">
                {{ $opname->notes }}
            </div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="footer-section">
            <div class="signature-box">
                <div>Prepared By</div>
                <div class="signature-line">{{ $opname->scheduledBy->name ?? '_______________' }}</div>
                <div class="signature-label">{{ $opname->created_at->format('d M Y') }}</div>
            </div>

            @if($opname->status === 'completed')
            <div class="signature-box">
                <div>Verified By</div>
                <div class="signature-line">{{ $opname->completedBy->name ?? '_______________' }}</div>
                <div class="signature-label">{{ $opname->completed_at ? $opname->completed_at->format('d M Y') : '' }}</div>
            </div>
            @endif

            <div class="signature-box">
                <div>Approved By</div>
                <div class="signature-line">_______________</div>
                <div class="signature-label">Date: _______________</div>
            </div>
        </div>

        <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #999;">
            Printed on: {{ now()->format('d F Y H:i') }}
        </div>
    </div>
</body>
</html>