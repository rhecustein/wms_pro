<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchaseOrder->po_number }}</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border: 1px solid #ddd;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
            margin-bottom: 30px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.5;
        }
        
        .po-title {
            text-align: right;
        }
        
        .po-title h1 {
            font-size: 28px;
            color: #3b82f6;
            margin-bottom: 5px;
        }
        
        .po-number {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            background: #f5f5f5;
            padding: 8px 15px;
            border-radius: 4px;
            display: inline-block;
        }
        
        /* Info Section */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        
        .info-box {
            flex: 1;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        
        .info-box h3 {
            font-size: 13px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #3b82f6;
        }
        
        .info-box p {
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .info-box strong {
            color: #333;
            font-weight: 600;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft { background: #e5e7eb; color: #374151; }
        .status-submitted { background: #dbeafe; color: #1e40af; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-confirmed { background: #e9d5ff; color: #6b21a8; }
        .status-completed { background: #d1fae5; color: #047857; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table thead {
            background: #3b82f6;
            color: white;
        }
        
        .items-table th {
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
        }
        
        .items-table tbody tr:hover {
            background: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Summary */
        .summary {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        
        .summary-table {
            width: 350px;
        }
        
        .summary-table tr {
            border-bottom: 1px solid #e0e0e0;
        }
        
        .summary-table td {
            padding: 8px 15px;
            font-size: 12px;
        }
        
        .summary-table .label {
            text-align: right;
            color: #666;
            font-weight: 500;
        }
        
        .summary-table .value {
            text-align: right;
            font-weight: 600;
            color: #333;
        }
        
        .summary-table .total-row {
            background: #3b82f6;
            color: white;
            font-size: 14px;
        }
        
        .summary-table .total-row td {
            padding: 12px 15px;
            font-weight: bold;
        }
        
        /* Notes */
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
        }
        
        .notes-section h4 {
            font-size: 12px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 8px;
        }
        
        .notes-section p {
            font-size: 11px;
            color: #555;
            white-space: pre-wrap;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .signature-box {
            flex: 1;
            text-align: center;
            padding: 0 10px;
        }
        
        .signature-box .title {
            font-size: 11px;
            font-weight: bold;
            color: #666;
            margin-bottom: 50px;
        }
        
        .signature-box .line {
            border-top: 1px solid #333;
            margin: 0 auto 5px;
            width: 150px;
        }
        
        .signature-box .name {
            font-size: 11px;
            font-weight: bold;
            color: #333;
        }
        
        .signature-box .date {
            font-size: 10px;
            color: #666;
        }
        
        .footer-info {
            text-align: center;
            font-size: 10px;
            color: #999;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }
            
            .container {
                border: none;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            @page {
                margin: 1cm;
                size: A4;
            }
        }
        
        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .print-button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    {{-- Print Button --}}
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Print Document
    </button>

    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="company-info">
                @if(config('app.logo'))
                    <img src="{{ asset(config('app.logo')) }}" alt="{{ config('app.name') }}" class="company-logo">
                @else
                    <div class="company-name">{{ config('app.name', 'WMS Company') }}</div>
                @endif
                
                <div class="company-details">
                    @if(config('company.address'))
                        <strong>Address:</strong> {{ config('company.address') }}
                        @if(config('company.city')), {{ config('company.city') }}@endif
                        @if(config('company.state')), {{ config('company.state') }}@endif
                        @if(config('company.postal_code')) {{ config('company.postal_code') }}@endif
                        <br>
                    @endif
                    
                    @if(config('company.phone'))
                        <strong>Phone:</strong> {{ config('company.phone') }}
                    @endif
                    
                    @if(config('company.email'))
                        | <strong>Email:</strong> {{ config('company.email') }}
                    @endif
                    
                    @if(config('company.website'))
                        <br><strong>Website:</strong> {{ config('company.website') }}
                    @endif
                    
                    @if(config('company.tax_number'))
                        <br><strong>Tax ID:</strong> {{ config('company.tax_number') }}
                    @endif
                </div>
            </div>
            
            <div class="po-title">
                <h1>PURCHASE ORDER</h1>
                <div class="po-number">{{ $purchaseOrder->po_number }}</div>
                <div style="margin-top: 10px;">
                    <span class="status-badge status-{{ $purchaseOrder->status }}">
                        {{ strtoupper($purchaseOrder->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Info Section --}}
        <div class="info-section">
            {{-- Supplier Information --}}
            <div class="info-box">
                <h3>SUPPLIER INFORMATION</h3>
                <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier->name }}</p>
                @if($purchaseOrder->supplier->code)
                    <p><strong>Supplier Code:</strong> {{ $purchaseOrder->supplier->code }}</p>
                @endif
                @if($purchaseOrder->supplier->company_name)
                    <p><strong>Company:</strong> {{ $purchaseOrder->supplier->company_name }}</p>
                @endif
                @if($purchaseOrder->supplier->address)
                    <p><strong>Address:</strong> {{ $purchaseOrder->supplier->address }}</p>
                @endif
                @if($purchaseOrder->supplier->phone)
                    <p><strong>Phone:</strong> {{ $purchaseOrder->supplier->phone }}</p>
                @endif
                @if($purchaseOrder->supplier->email)
                    <p><strong>Email:</strong> {{ $purchaseOrder->supplier->email }}</p>
                @endif
            </div>

            {{-- Order Information --}}
            <div class="info-box">
                <h3>ORDER INFORMATION</h3>
                <p><strong>PO Date:</strong> {{ $purchaseOrder->po_date->format('d/m/Y') }}</p>
                @if($purchaseOrder->expected_delivery_date)
                    <p><strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date->format('d/m/Y') }}</p>
                @endif
                <p><strong>Payment Terms:</strong> {{ $purchaseOrder->payment_terms ?? '-' }}</p>
                @if($purchaseOrder->payment_due_days)
                    <p><strong>Payment Due:</strong> {{ $purchaseOrder->payment_due_days }} days</p>
                @endif
                @if($purchaseOrder->reference_number)
                    <p><strong>Reference No:</strong> {{ $purchaseOrder->reference_number }}</p>
                @endif
                <p><strong>Warehouse:</strong> {{ $purchaseOrder->warehouse->name }}</p>
            </div>
        </div>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Product Description</th>
                    <th style="width: 12%;" class="text-center">Qty</th>
                    <th style="width: 10%;" class="text-center">Unit</th>
                    <th style="width: 13%;" class="text-right">Unit Price</th>
                    <th style="width: 10%;" class="text-right">Tax</th>
                    <th style="width: 15%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->product_sku)
                                <br><small style="color: #666;">SKU: {{ $item->product_sku }}</small>
                            @endif
                            @if($item->notes)
                                <br><small style="color: #666; font-style: italic;">{{ $item->notes }}</small>
                            @endif
                        </td>
                        <td class="text-center"><strong>{{ number_format($item->quantity_ordered, 2) }}</strong></td>
                        <td class="text-center">{{ $item->unit->name ?? '-' }}</td>
                        <td class="text-right">{{ $purchaseOrder->currency }} {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">
                            @if($item->tax_rate > 0)
                                {{ $item->tax_rate }}%<br>
                                <small>{{ $purchaseOrder->currency }} {{ number_format($item->tax_amount, 2) }}</small>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right"><strong>{{ $purchaseOrder->currency }} {{ number_format($item->line_total, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Summary --}}
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->subtotal, 2) }}</td>
                </tr>
                
                @if($purchaseOrder->tax_amount > 0)
                    <tr>
                        <td class="label">Tax @if($purchaseOrder->tax_rate > 0)({{ $purchaseOrder->tax_rate }}%)@endif:</td>
                        <td class="value">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                    </tr>
                @endif
                
                @if($purchaseOrder->discount_amount > 0)
                    <tr>
                        <td class="label">Discount @if($purchaseOrder->discount_rate > 0)({{ $purchaseOrder->discount_rate }}%)@endif:</td>
                        <td class="value" style="color: #dc2626;">- {{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->discount_amount, 2) }}</td>
                    </tr>
                @endif
                
                @if($purchaseOrder->shipping_cost > 0)
                    <tr>
                        <td class="label">Shipping Cost:</td>
                        <td class="value">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->shipping_cost, 2) }}</td>
                    </tr>
                @endif
                
                @if($purchaseOrder->other_cost > 0)
                    <tr>
                        <td class="label">Other Cost:</td>
                        <td class="value">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->other_cost, 2) }}</td>
                    </tr>
                @endif
                
                <tr class="total-row">
                    <td>GRAND TOTAL:</td>
                    <td>{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        {{-- Notes --}}
        @if($purchaseOrder->notes)
            <div class="notes-section">
                <h4>Notes:</h4>
                <p>{{ $purchaseOrder->notes }}</p>
            </div>
        @endif

        @if($purchaseOrder->terms_conditions)
            <div class="notes-section">
                <h4>Terms & Conditions:</h4>
                <p>{{ $purchaseOrder->terms_conditions }}</p>
            </div>
        @endif

        {{-- Footer / Signatures --}}
        <div class="footer">
            <div class="signatures">
                <div class="signature-box">
                    <div class="title">Prepared By</div>
                    <div class="line"></div>
                    <div class="name">{{ $purchaseOrder->creator->name ?? '-' }}</div>
                    <div class="date">{{ $purchaseOrder->created_at->format('d/m/Y') }}</div>
                </div>

                @if($purchaseOrder->approved_by)
                    <div class="signature-box">
                        <div class="title">Approved By</div>
                        <div class="line"></div>
                        <div class="name">{{ $purchaseOrder->approver->name ?? '-' }}</div>
                        <div class="date">{{ $purchaseOrder->approved_at ? $purchaseOrder->approved_at->format('d/m/Y') : '-' }}</div>
                    </div>
                @endif

                <div class="signature-box">
                    <div class="title">Supplier Acknowledgment</div>
                    <div class="line"></div>
                    <div class="name">_____________________</div>
                    <div class="date">Date: ______________</div>
                </div>
            </div>

            <div class="footer-info">
                <p><strong>{{ config('app.name', 'WMS Company') }}</strong></p>
                <p>This is a computer-generated document. No signature is required.</p>
                <p>Printed on: {{ now()->format('d/m/Y H:i') }} | Page 1 of 1</p>
                @if(config('company.website'))
                    <p>{{ config('company.website') }}</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
        
        // Close window after print
        window.onafterprint = function() {
            // window.close(); // Uncomment to auto-close after print
        }
    </script>
</body>
</html>