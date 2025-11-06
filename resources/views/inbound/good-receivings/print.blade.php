<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Good Receiving - {{ $goodReceiving->gr_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }

        /* Header */
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 8px;
        }

        .company-info h1 {
            font-size: 22px;
            color: #1e40af;
            margin-bottom: 4px;
        }

        .company-info p {
            font-size: 10px;
            color: #666;
            line-height: 1.5;
        }

        .document-title {
            text-align: right;
        }

        .document-title h2 {
            font-size: 18px;
            color: #1e3a8a;
            margin-bottom: 4px;
        }

        .document-title .gr-number {
            font-size: 15px;
            font-weight: bold;
            color: #2563eb;
        }

        /* Info Boxes */
        .info-section {
            display: flex;
            gap: 12px;
            margin-bottom: 15px;
        }

        .info-box {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            background: #f9fafb;
        }

        .info-box h3 {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .info-row {
            display: flex;
            margin-bottom: 4px;
        }

        .info-label {
            width: 110px;
            color: #6b7280;
            font-size: 10px;
        }

        .info-value {
            flex: 1;
            color: #111827;
            font-weight: 500;
            font-size: 10px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }

        .status-draft { background: #f3f4f6; color: #374151; }
        .status-in_progress { background: #dbeafe; color: #1e40af; }
        .status-quality_check { background: #fef3c7; color: #92400e; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-partial { background: #fed7aa; color: #9a3412; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .quality-pending { background: #f3f4f6; color: #374151; }
        .quality-passed { background: #d1fae5; color: #065f46; }
        .quality-failed { background: #fee2e2; color: #991b1b; }

        /* Items Table */
        .items-section {
            margin-bottom: 15px;
        }

        .items-section h3 {
            font-size: 12px;
            color: #1f2937;
            margin-bottom: 8px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table thead {
            background: #f3f4f6;
        }

        table th {
            padding: 6px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            border-bottom: 2px solid #d1d5db;
        }

        table td {
            padding: 6px;
            font-size: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tbody tr:hover {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-gray {
            color: #6b7280;
        }

        /* Summary */
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }

        .summary-box {
            width: 280px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            background: #f9fafb;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 11px;
            padding-top: 8px;
        }

        /* Notes */
        .notes-section {
            margin-bottom: 15px;
        }

        .notes-section h3 {
            font-size: 11px;
            color: #1f2937;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .notes-content {
            padding: 8px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            font-size: 10px;
            line-height: 1.5;
        }

        /* Signatures */
        .signatures {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-box {
            flex: 1;
            text-align: center;
        }

        .signature-box h4 {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 40px;
        }

        .signature-line {
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 10px;
            font-weight: 600;
        }

        .signature-date {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }

        /* Print Styles */
        @media print {
            body {
                padding: 10px;
            }

            .print-container {
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 10mm 12mm;
                size: A4;
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

            .signatures {
                page-break-inside: avoid;
            }

            .header {
                page-break-after: avoid;
            }

            .info-section {
                page-break-inside: avoid;
            }
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background: #1d4ed8;
        }

        .back-button {
            position: fixed;
            top: 20px;
            right: 150px;
            padding: 10px 20px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            display: inline-block;
        }

        .back-button:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    {{-- Print & Back Buttons --}}
    <a href="{{ route('inbound.good-receivings.show', $goodReceiving) }}" class="back-button no-print">
        ← Back
    </a>
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Print
    </button>

    <div class="print-container">
        {{-- Header --}}
        <div class="header">
            <div class="header-top">
                <div class="company-info">
                    <h1>Your Company Name</h1>
                    <p>
                        123 Warehouse Street, Industrial Area<br>
                        City, State 12345<br>
                        Phone: (123) 456-7890 | Email: info@company.com
                    </p>
                </div>
                <div class="document-title">
                    <h2>GOOD RECEIVING</h2>
                    <div class="gr-number">{{ $goodReceiving->gr_number }}</div>
                    <div style="margin-top: 5px;">
                        <span class="status-badge status-{{ $goodReceiving->status }}">
                            {{ strtoupper(str_replace('_', ' ', $goodReceiving->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Information Section --}}
        <div class="info-section">
            {{-- Receiving Info --}}
            <div class="info-box">
                <h3>Receiving Information</h3>
                <div class="info-row">
                    <span class="info-label">Receiving Date:</span>
                    <span class="info-value">{{ $goodReceiving->receiving_date->format('d M Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Warehouse:</span>
                    <span class="info-value">{{ $goodReceiving->warehouse->name }} ({{ $goodReceiving->warehouse->code }})</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Received By:</span>
                    <span class="info-value">{{ $goodReceiving->receivedBy->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Quality Status:</span>
                    <span class="info-value">
                        <span class="status-badge quality-{{ $goodReceiving->quality_status }}">
                            {{ strtoupper($goodReceiving->quality_status) }}
                        </span>
                    </span>
                </div>
            </div>

            {{-- Supplier Info --}}
            <div class="info-box">
                <h3>Supplier Information</h3>
                <div class="info-row">
                    <span class="info-label">Supplier Name:</span>
                    <span class="info-value">{{ $goodReceiving->supplier->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Supplier Code:</span>
                    <span class="info-value">{{ $goodReceiving->supplier->code ?? '-' }}</span>
                </div>
                @if($goodReceiving->supplier->contact_person)
                <div class="info-row">
                    <span class="info-label">Contact Person:</span>
                    <span class="info-value">{{ $goodReceiving->supplier->contact_person }}</span>
                </div>
                @endif
                @if($goodReceiving->supplier->phone)
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $goodReceiving->supplier->phone }}</span>
                </div>
                @endif
            </div>

            {{-- Reference Info --}}
            <div class="info-box">
                <h3>Reference Documents</h3>
                @if($goodReceiving->purchase_order_id)
                <div class="info-row">
                    <span class="info-label">Purchase Order:</span>
                    <span class="info-value">{{ $goodReceiving->purchaseOrder->po_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">PO Date:</span>
                    <span class="info-value">{{ $goodReceiving->purchaseOrder->po_date->format('d M Y') }}</span>
                </div>
                @endif
                @if($goodReceiving->inbound_shipment_id)
                <div class="info-row">
                    <span class="info-label">Shipment:</span>
                    <span class="info-value">{{ $goodReceiving->inboundShipment->shipment_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Shipment Date:</span>
                    <span class="info-value">{{ $goodReceiving->inboundShipment->shipment_date->format('d M Y') }}</span>
                </div>
                @endif
                @if(!$goodReceiving->purchase_order_id && !$goodReceiving->inbound_shipment_id)
                <div class="info-row">
                    <span class="info-value" style="color: #6b7280;">No reference documents</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Items Table --}}
        <div class="items-section">
            <h3>Received Items</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Product</th>
                        <th style="width: 10%;" class="text-center">Unit</th>
                        <th style="width: 10%;" class="text-right">Expected</th>
                        <th style="width: 10%;" class="text-right">Received</th>
                        <th style="width: 10%;" class="text-right">Accepted</th>
                        <th style="width: 10%;" class="text-right">Rejected</th>
                        <th style="width: 15%;">Batch/Serial</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($goodReceiving->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="font-semibold">{{ $item->product->name }}</div>
                            <div class="text-gray" style="font-size: 10px;">{{ $item->product->sku }}</div>
                            @if($item->product->category)
                            <div class="text-gray" style="font-size: 9px;">{{ $item->product->category->name }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->unit_of_measure }}</td>
                        <td class="text-right">{{ number_format($item->quantity_expected) }}</td>
                        <td class="text-right font-semibold">{{ number_format($item->quantity_received) }}</td>
                        <td class="text-right" style="color: #065f46;">{{ number_format($item->quantity_accepted) }}</td>
                        <td class="text-right" style="color: #991b1b;">{{ number_format($item->quantity_rejected) }}</td>
                        <td style="font-size: 9px;">
                            @if($item->batch_number)
                            <div><strong>Batch:</strong> {{ $item->batch_number }}</div>
                            @endif
                            @if($item->serial_number)
                            <div><strong>Serial:</strong> {{ $item->serial_number }}</div>
                            @endif
                            @if($item->expiry_date)
                            <div><strong>Exp:</strong> {{ $item->expiry_date->format('d/m/Y') }}</div>
                            @endif
                            @if(!$item->batch_number && !$item->serial_number && !$item->expiry_date)
                            <span class="text-gray">-</span>
                            @endif
                        </td>
                    </tr>
                    @if($item->notes)
                    <tr>
                        <td colspan="8" style="padding: 5px 8px; background: #fef3c7; font-size: 10px;">
                            <strong>Note:</strong> {{ $item->notes }}
                        </td>
                    </tr>
                    @endif
                    @if($item->rejection_reason)
                    <tr>
                        <td colspan="8" style="padding: 5px 8px; background: #fee2e2; font-size: 10px; color: #991b1b;">
                            <strong>Rejection Reason:</strong> {{ $item->rejection_reason }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary --}}
        <div class="summary-section">
            <div class="summary-box">
                <div class="summary-row">
                    <span>Total Items:</span>
                    <span class="font-semibold">{{ $goodReceiving->total_items }}</span>
                </div>
                <div class="summary-row">
                    <span>Total Quantity Expected:</span>
                    <span class="font-semibold">{{ number_format($goodReceiving->items->sum('quantity_expected')) }}</span>
                </div>
                <div class="summary-row">
                    <span>Total Quantity Received:</span>
                    <span class="font-semibold">{{ number_format($goodReceiving->total_quantity) }}</span>
                </div>
                <div class="summary-row">
                    <span>Total Quantity Accepted:</span>
                    <span class="font-semibold" style="color: #065f46;">{{ number_format($goodReceiving->items->sum('quantity_accepted')) }}</span>
                </div>
                <div class="summary-row">
                    <span>Total Quantity Rejected:</span>
                    <span class="font-semibold" style="color: #991b1b;">{{ number_format($goodReceiving->items->sum('quantity_rejected')) }}</span>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($goodReceiving->notes)
        <div class="notes-section">
            <h3>Notes</h3>
            <div class="notes-content">
                {{ $goodReceiving->notes }}
            </div>
        </div>
        @endif

        {{-- Quality Check Info --}}
        @if($goodReceiving->quality_checked_at)
        <div class="notes-section">
            <h3>Quality Check Information</h3>
            <div class="notes-content">
                <div style="margin-bottom: 5px;">
                    <strong>Checked By:</strong> {{ $goodReceiving->qualityCheckedBy->name ?? '-' }}
                </div>
                <div style="margin-bottom: 5px;">
                    <strong>Checked At:</strong> {{ $goodReceiving->quality_checked_at->format('d M Y H:i') }}
                </div>
                <div>
                    <strong>Status:</strong> 
                    <span class="status-badge quality-{{ $goodReceiving->quality_status }}">
                        {{ strtoupper($goodReceiving->quality_status) }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        {{-- Signatures --}}
        <div class="signatures">
            <div class="signature-box">
                <h4>Prepared By</h4>
                <div class="signature-line">
                    {{ $goodReceiving->createdBy->name ?? '-' }}
                </div>
                <div class="signature-date">
                    {{ $goodReceiving->created_at->format('d M Y') }}
                </div>
            </div>

            <div class="signature-box">
                <h4>Received By</h4>
                <div class="signature-line">
                    {{ $goodReceiving->receivedBy->name ?? '_________________' }}
                </div>
                <div class="signature-date">
                    {{ $goodReceiving->receivedBy ? $goodReceiving->receiving_date->format('d M Y') : '' }}
                </div>
            </div>

            <div class="signature-box">
                <h4>Quality Checked By</h4>
                <div class="signature-line">
                    {{ $goodReceiving->qualityCheckedBy->name ?? '_________________' }}
                </div>
                <div class="signature-date">
                    {{ $goodReceiving->quality_checked_at ? $goodReceiving->quality_checked_at->format('d M Y') : '' }}
                </div>
            </div>

            <div class="signature-box">
                <h4>Approved By</h4>
                <div class="signature-line">
                    _________________
                </div>
                <div class="signature-date">
                    
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>This is a computer-generated document. No signature is required.</p>
            <p>Printed on: {{ now()->format('d M Y H:i:s') }}</p>
            <p style="margin-top: 5px;">{{ $goodReceiving->gr_number }} | Page 1 of 1</p>
        </div>
    </div>

    <script>
        // Auto print when loaded (optional, comment out if not needed)
        // window.onload = function() { 
        //     window.print(); 
        // }
    </script>
</body>
</html>