<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Order - {{ $return->return_number }}</title>
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
            padding: 30px;
            max-width: 210mm;
            margin: 0 auto;
        }

        .header {
            border-bottom: 3px solid {{ theme_color('primary') }};
            padding-bottom: 20px;
            margin-bottom: 30px;
            page-break-after: avoid;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: start;
        }

        .company-info h1 {
            font-size: 24px;
            color: {{ theme_color('primary') }};
            margin-bottom: 5px;
        }

        .company-info p {
            font-size: 11px;
            color: #666;
        }

        .company-logo {
            max-width: 200px;
            max-height: 80px;
            margin-bottom: 10px;
        }

        .document-info {
            text-align: right;
        }

        .document-info h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .document-info p {
            font-size: 11px;
            margin: 3px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-received { background: #cfe2ff; color: #084298; }
        .status-inspected { background: #e0cffc; color: #6f42c1; }
        .status-restocked { background: #d1e7dd; color: #0f5132; }
        .status-disposed { background: #f8d7da; color: #842029; }
        .status-cancelled { background: #e2e3e5; color: #41464b; }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
            page-break-inside: avoid;
        }

        .info-box {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
        }

        .info-box h3 {
            font-size: 13px;
            color: {{ theme_color('primary') }};
            margin-bottom: 12px;
            border-bottom: 2px solid {{ theme_color('primary') }};
            padding-bottom: 5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            color: #666;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table thead {
            background: {{ theme_color('primary') }};
            color: white;
        }

        .items-table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }

        .items-table tbody tr:hover {
            background: #f8f9fa;
        }

        .item-notes {
            background: #f8f9fa;
            padding: 8px;
            font-size: 11px;
            color: #666;
            border-left: 3px solid {{ theme_color('primary') }};
        }

        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .summary-box {
            width: 300px;
            border: 2px solid {{ theme_color('primary') }};
            border-radius: 8px;
            padding: 15px;
            background: #fff5f5;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-size: 14px;
            font-weight: bold;
            color: {{ theme_color('primary') }};
        }

        .notes-section {
            margin-bottom: 30px;
        }

        .notes-section h3 {
            font-size: 13px;
            color: {{ theme_color('primary') }};
            margin-bottom: 10px;
        }

        .notes-content {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
            font-size: 11px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 50px;
            margin-bottom: 30px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 8px;
            font-size: 11px;
            font-weight: bold;
        }

        .signature-label {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 3cm 2.5cm;  /* Margin lebih besar: Top/Bottom: 3cm, Left/Right: 2.5cm */
            }

            /* Add extra padding to content when printing */
            .header,
            .info-section,
            .items-table,
            .summary-section,
            .notes-section,
            .signatures,
            .footer {
                padding-left: 5mm;
                padding-right: 5mm;
            }

            /* Prevent page breaks inside elements */
            .info-section,
            .info-box,
            .items-table,
            .summary-section,
            .notes-section {
                page-break-inside: avoid;
            }

            /* Prevent page breaks after headers */
            h1, h2, h3 {
                page-break-after: avoid;
            }

            /* Allow page breaks before new sections */
            .info-section,
            .items-table,
            .summary-section,
            .signatures {
                page-break-before: auto;
            }

            /* Keep table rows together */
            .items-table tr {
                page-break-inside: avoid;
            }

            /* Ensure signatures stay at bottom */
            .signatures {
                margin-top: 50px;
                page-break-inside: avoid;
            }

            /* Better print quality */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Reduce font size slightly for better fit */
            body {
                font-size: 11px;
            }

            /* Adjust table cell padding for print */
            .items-table th,
            .items-table td {
                padding: 8px 6px;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: {{ theme_color('primary') }};
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .print-button:hover {
            background: {{ theme_color('secondary') }};
        }

        .condition-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .condition-good { background: #d1e7dd; color: #0f5132; }
        .condition-damaged { background: #f8d7da; color: #842029; }
        .condition-expired { background: #fff3cd; color: #856404; }
        .condition-defective { background: #ffc107; color: #664d03; }
    </style>
</head>
<body>
    {{-- Print Button --}}
    <button onclick="window.print()" class="print-button no-print">
        Print Document
    </button>

    {{-- Header --}}
    <div class="header">
        <div class="header-content">
            <div class="company-info">
                @if(site_logo())
                    <img src="{{ site_logo() }}" alt="{{ company_name() }}" class="company-logo">
                @endif
                <h1>{{ company_name() }}</h1>
                <p>{{ site_name() }}</p>
                @php $companyInfo = company_info(); @endphp
                @if($companyInfo['email'])
                    <p>Email: {{ $companyInfo['email'] }}</p>
                @endif
                @if($companyInfo['phone'])
                    <p>Phone: {{ $companyInfo['phone'] }}</p>
                @endif
                @if($companyInfo['address'])
                    <p>{{ $companyInfo['address'] }}
                    @if($companyInfo['city']), {{ $companyInfo['city'] }}@endif
                    @if($companyInfo['postal_code']) {{ $companyInfo['postal_code'] }}@endif
                    </p>
                @endif
            </div>
            <div class="document-info">
                <h2>RETURN ORDER</h2>
                <p><strong>Return Number:</strong> {{ $return->return_number ?? 'N/A' }}</p>
                <p><strong>Date:</strong> 
                    @if($return->return_date)
                        {{ format_datetime($return->return_date) }}
                    @else
                        -
                    @endif
                </p>
                <p><strong>Status:</strong> 
                    <span class="status-badge status-{{ $return->status ?? 'pending' }}">
                        {{ ucfirst($return->status ?? 'Pending') }}
                    </span>
                </p>
                <p><strong>Print Date:</strong> {{ format_datetime(now()) }}</p>
            </div>
        </div>
    </div>

    {{-- Customer and Warehouse Information --}}
    <div class="info-section">
        <div class="info-box">
            <h3>CUSTOMER INFORMATION</h3>
            <div class="info-row">
                <span class="info-label">Customer Name:</span>
                <span class="info-value">{{ $return->customer->name ?? '-' }}</span>
            </div>
            @if(isset($return->customer->code) && $return->customer->code)
                <div class="info-row">
                    <span class="info-label">Customer Code:</span>
                    <span class="info-value">{{ $return->customer->code }}</span>
                </div>
            @endif
            @if(isset($return->customer->email) && $return->customer->email)
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $return->customer->email }}</span>
                </div>
            @endif
            @if(isset($return->customer->phone) && $return->customer->phone)
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $return->customer->phone }}</span>
                </div>
            @endif
            @if(isset($return->customer->address) && $return->customer->address)
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $return->customer->address }}</span>
                </div>
            @endif
        </div>

        <div class="info-box">
            <h3>WAREHOUSE INFORMATION</h3>
            <div class="info-row">
                <span class="info-label">Warehouse:</span>
                <span class="info-value">{{ $return->warehouse->name ?? '-' }}</span>
            </div>
            @if(isset($return->warehouse->code) && $return->warehouse->code)
                <div class="info-row">
                    <span class="info-label">Code:</span>
                    <span class="info-value">{{ $return->warehouse->code }}</span>
                </div>
            @endif
            @if(isset($return->warehouse->address) && $return->warehouse->address)
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $return->warehouse->address }}</span>
                </div>
            @endif
        </div>

        <div class="info-box">
            <h3>RETURN DETAILS</h3>
            <div class="info-row">
                <span class="info-label">Return Type:</span>
                <span class="info-value">{{ ucfirst(str_replace('_', ' ', $return->return_type ?? 'N/A')) }}</span>
            </div>
            @if($return->deliveryOrder)
                <div class="info-row">
                    <span class="info-label">DO Number:</span>
                    <span class="info-value">{{ $return->deliveryOrder->do_number ?? '-' }}</span>
                </div>
            @endif
            @if($return->salesOrder)
                <div class="info-row">
                    <span class="info-label">SO Number:</span>
                    <span class="info-value">{{ $return->salesOrder->order_number ?? '-' }}</span>
                </div>
            @endif
            @if($return->disposition)
                <div class="info-row">
                    <span class="info-label">Disposition:</span>
                    <span class="info-value">{{ ucfirst($return->disposition) }}</span>
                </div>
            @endif
            @if($return->receivedBy)
                <div class="info-row">
                    <span class="info-label">Received By:</span>
                    <span class="info-value">{{ $return->receivedBy->name ?? '-' }}</span>
                </div>
            @endif
            @if($return->inspectedBy)
                <div class="info-row">
                    <span class="info-label">Inspected By:</span>
                    <span class="info-value">{{ $return->inspectedBy->name ?? '-' }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Product</th>
                <th style="width: 15%;">Batch/Serial</th>
                <th style="width: 10%;">Qty Returned</th>
                <th style="width: 10%;">Qty Restocked</th>
                <th style="width: 12%;">Condition</th>
                <th style="width: 12%;">Disposition</th>
                <th style="width: 11%;">Bin Location</th>
            </tr>
        </thead>
        <tbody>
            @forelse($return->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name ?? 'Unknown Product' }}</strong><br>
                        <small style="color: #666;">SKU: {{ $item->product->sku ?? '-' }}</small>
                    </td>
                    <td>
                        @if($item->batch_number)
                            <div><small><strong>Batch:</strong> {{ $item->batch_number }}</small></div>
                        @endif
                        @if($item->serial_number)
                            <div><small><strong>Serial:</strong> {{ $item->serial_number }}</small></div>
                        @endif
                        @if(!$item->batch_number && !$item->serial_number)
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td><strong>{{ number_format($item->quantity_returned ?? 0) }}</strong></td>
                    <td>{{ number_format($item->quantity_restocked ?? 0) }}</td>
                    <td>
                        <span class="condition-badge condition-{{ $item->condition ?? 'good' }}">
                            {{ ucfirst($item->condition ?? 'Good') }}
                        </span>
                    </td>
                    <td>{{ $item->disposition ? ucfirst($item->disposition) : '-' }}</td>
                    <td>
                        @if($item->restockedToBin)
                            {{ $item->restockedToBin->bin_code ?? '-' }}
                        @elseif($item->quarantineBin)
                            {{ $item->quarantineBin->bin_code ?? '-' }} (Q)
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @if($item->return_reason || $item->inspection_notes || $item->notes)
                    <tr>
                        <td colspan="8" style="padding: 0;">
                            <div class="item-notes">
                                @if($item->return_reason)
                                    <strong>Return Reason:</strong> {{ $item->return_reason }}<br>
                                @endif
                                @if($item->inspection_notes)
                                    <strong>Inspection Notes:</strong> {{ $item->inspection_notes }}<br>
                                @endif
                                @if($item->notes)
                                    <strong>Additional Notes:</strong> {{ $item->notes }}
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #999;">
                        No items found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Summary --}}
    <div class="summary-section">
        <div class="summary-box">
            <div class="summary-row">
                <span>Total Items:</span>
                <span><strong>{{ $return->total_items ?? 0 }}</strong></span>
            </div>
            <div class="summary-row">
                <span>Total Quantity Returned:</span>
                <span><strong>{{ number_format($return->total_quantity ?? 0) }}</strong></span>
            </div>
            @if(isset($return->refund_amount) && $return->refund_amount > 0)
                <div class="summary-row">
                    <span>Refund Amount:</span>
                    <span><strong>{{ format_currency($return->refund_amount) }}</strong></span>
                </div>
                @if($return->refund_status)
                    <div class="summary-row">
                        <span>Refund Status:</span>
                        <span><strong>{{ ucfirst($return->refund_status) }}</strong></span>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Notes --}}
    @if($return->notes)
        <div class="notes-section">
            <h3>ADDITIONAL NOTES</h3>
            <div class="notes-content">
                {{ $return->notes }}
            </div>
        </div>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">
                Prepared By
            </div>
            <div class="signature-label">
                @if($return->createdBy)
                    {{ $return->createdBy->name ?? 'Staff' }}
                @else
                    _______________
                @endif
                <br>
                @if($return->created_at)
                    {{ format_date($return->created_at) }}
                @else
                    _______________
                @endif
            </div>
        </div>

        <div class="signature-box">
            <div class="signature-line">
                Received By
            </div>
            <div class="signature-label">
                @if($return->receivedBy)
                    {{ $return->receivedBy->name ?? 'Warehouse Staff' }}
                @else
                    Warehouse Staff
                @endif
                <br>
                @if($return->received_at)
                    {{ format_date($return->received_at) }}
                @else
                    Date: _______________
                @endif
            </div>
        </div>

        @if($return->inspectedBy)
            <div class="signature-box">
                <div class="signature-line">
                    Inspected By
                </div>
                <div class="signature-label">
                    {{ $return->inspectedBy->name ?? 'Inspector' }}
                    <br>
                    @if($return->inspected_at)
                        {{ format_date($return->inspected_at) }}
                    @else
                        _______________
                    @endif
                </div>
            </div>
        @else
            <div class="signature-box">
                <div class="signature-line">
                    Inspected By
                </div>
                <div class="signature-label">
                    Quality Inspector
                    <br>Date: _______________
                </div>
            </div>
        @endif

        <div class="signature-box">
            <div class="signature-line">
                Approved By
            </div>
            <div class="signature-label">
                Manager/Supervisor
                <br>Date: _______________
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ setting('email_footer_text', 'This is a computer-generated document. No signature is required.') }}</p>
        @php $companyInfo = company_info(); @endphp
        @if($companyInfo['email'])
            <p>For any inquiries, please contact {{ $companyInfo['email'] }}</p>
        @endif
        <p>&copy; {{ now()->year }} {{ company_name() }}. All rights reserved.</p>
        @if($companyInfo['tax_number'])
            <p>Tax ID: {{ $companyInfo['tax_number'] }}</p>
        @endif
    </div>

    <script>
        // Auto print when page loads (optional - uncomment if needed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>