{{-- resources/views/outbound/sales-orders/print.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order - {{ $salesOrder->so_number }}</title>
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

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            font-size: 28px;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #666;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .info-box h3 {
            font-size: 14px;
            color: #2563eb;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #2563eb;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #666;
        }

        .info-value {
            text-align: right;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table thead {
            background: #2563eb;
            color: white;
        }

        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        .items-table th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .items-table tbody tr:hover {
            background: #f3f4f6;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            float: right;
            width: 350px;
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #2563eb;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        .summary-row.total {
            border-top: 2px solid #2563eb;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }

        .footer {
            clear: both;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            margin-top: 50px;
            margin-bottom: 30px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft { background: #e5e7eb; color: #374151; }
        .status-confirmed { background: #dbeafe; color: #1e40af; }
        .status-picking { background: #fef3c7; color: #92400e; }
        .status-packing { background: #fed7aa; color: #9a3412; }
        .status-shipped { background: #e9d5ff; color: #6b21a8; }
        .status-delivered { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .print-button:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Print Document
    </button>

    <div class="header">
        <h1>SALES ORDER</h1>
        <p>{{ $salesOrder->so_number }}</p>
        <span class="status-badge status-{{ $salesOrder->status }}">{{ strtoupper($salesOrder->status) }}</span>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Order Information</h3>
            <div class="info-row">
                <span class="info-label">SO Number:</span>
                <span class="info-value">{{ $salesOrder->so_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Order Date:</span>
                <span class="info-value">{{ $salesOrder->order_date->format('d M Y') }}</span>
            </div>
            @if($salesOrder->requested_delivery_date)
            <div class="info-row">
                <span class="info-label">Delivery Date:</span>
                <span class="info-value">{{ $salesOrder->requested_delivery_date->format('d M Y') }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ ucfirst($salesOrder->status) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Status:</span>
                <span class="info-value">{{ ucfirst($salesOrder->payment_status) }}</span>
            </div>
        </div>

        <div class="info-box">
            <h3>Customer Information</h3>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $salesOrder->customer->name }}</span>
            </div>
            @if($salesOrder->customer->email)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $salesOrder->customer->email }}</span>
            </div>
            @endif
            @if($salesOrder->customer->phone)
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $salesOrder->customer->phone }}</span>
            </div>
            @endif
        </div>

        <div class="info-box">
            <h3>Warehouse Information</h3>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $salesOrder->warehouse->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Code:</span>
                <span class="info-value">{{ $salesOrder->warehouse->code }}</span>
            </div>
            @if($salesOrder->warehouse->address)
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">{{ $salesOrder->warehouse->address }}</span>
            </div>
            @endif
        </div>

        @if($salesOrder->shipping_address)
        <div class="info-box">
            <h3>Shipping Information</h3>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">{{ $salesOrder->shipping_address }}</span>
            </div>
            @if($salesOrder->shipping_city)
            <div class="info-row">
                <span class="info-label">City:</span>
                <span class="info-value">{{ $salesOrder->shipping_city }}</span>
            </div>
            @endif
            @if($salesOrder->shipping_province)
            <div class="info-row">
                <span class="info-label">Province:</span>
                <span class="info-value">{{ $salesOrder->shipping_province }}</span>
            </div>
            @endif
            @if($salesOrder->shipping_postal_code)
            <div class="info-row">
                <span class="info-label">Postal Code:</span>
                <span class="info-value">{{ $salesOrder->shipping_postal_code }}</span>
            </div>
            @endif
        </div>
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">#</th>
                <th style="width: 35%;">Product</th>
                <th style="width: 15%;">SKU</th>
                <th class="text-right" style="width: 10%;">Qty</th>
                <th class="text-right" style="width: 15%;">Unit Price</th>
                <th class="text-right" style="width: 10%;">Discount</th>
                <th class="text-right" style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesOrder->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->product->sku }}</td>
                <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->discount, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($item->total, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>{{ $salesOrder->currency }} {{ number_format($salesOrder->subtotal, 2) }}</span>
        </div>
        @if($salesOrder->discount_amount > 0)
        <div class="summary-row">
            <span>Discount:</span>
            <span style="color: #dc2626;">-{{ $salesOrder->currency }} {{ number_format($salesOrder->discount_amount, 2) }}</span>
        </div>
        @endif
        @if($salesOrder->tax_amount > 0)
        <div class="summary-row">
            <span>Tax:</span>
            <span>{{ $salesOrder->currency }} {{ number_format($salesOrder->tax_amount, 2) }}</span>
        </div>
        @endif
        @if($salesOrder->shipping_cost > 0)
        <div class="summary-row">
            <span>Shipping:</span>
            <span>{{ $salesOrder->currency }} {{ number_format($salesOrder->shipping_cost, 2) }}</span>
        </div>
        @endif
        <div class="summary-row total">
            <span>TOTAL:</span>
            <span>{{ $salesOrder->currency }} {{ number_format($salesOrder->total_amount, 2) }}</span>
        </div>
    </div>

    @if($salesOrder->notes)
    <div style="clear: both; margin-top: 30px; padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 5px;">
        <strong>Notes:</strong><br>
        {{ $salesOrder->notes }}
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <strong>Prepared By</strong>
            <div class="signature-line">
                @if($salesOrder->createdBy)
                    {{ $salesOrder->createdBy->name }}
                @else
                    ___________________
                @endif
            </div>
        </div>
        <div class="signature-box">
            <strong>Approved By</strong>
            <div class="signature-line">
                ___________________
            </div>
        </div>
        <div class="signature-box">
            <strong>Received By</strong>
            <div class="signature-line">
                ___________________
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>Generated on:</strong> {{ now()->format('d M Y, H:i:s') }}</p>
        <p>This is a computer-generated document. No signature is required.</p>
    </div>

</body>
</html>