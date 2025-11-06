{{-- resources/views/equipment/vehicles/print.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle List - Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
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
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 13px;
            color: #666;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8fafc;
            border-radius: 5px;
        }

        .info-item {
            flex: 1;
        }

        .info-item strong {
            display: block;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .statistics {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .stat-card {
            flex: 1;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            text-align: center;
        }

        .stat-card .label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-card .value {
            font-size: 20px;
            font-weight: bold;
            color: #1e293b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #f1f5f9;
        }

        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            color: #475569;
            text-transform: uppercase;
            border-bottom: 2px solid #cbd5e1;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        tbody tr:hover {
            background-color: #f8fafc;
        }

        .vehicle-number {
            font-weight: 600;
            color: #1e40af;
            font-family: 'Courier New', monospace;
        }

        .license-plate {
            font-size: 10px;
            color: #64748b;
            font-family: 'Courier New', monospace;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-truck { background-color: #ddd6fe; color: #5b21b6; }
        .badge-van { background-color: #dbeafe; color: #1e40af; }
        .badge-forklift { background-color: #fed7aa; color: #c2410c; }
        .badge-reach-truck { background-color: #fce7f3; color: #9f1239; }

        .badge-available { background-color: #dcfce7; color: #166534; }
        .badge-in-use { background-color: #dbeafe; color: #1e40af; }
        .badge-maintenance { background-color: #fef3c7; color: #a16207; }
        .badge-inactive { background-color: #f1f5f9; color: #475569; }

        .badge-owned { background-color: #dcfce7; color: #166534; }
        .badge-rented { background-color: #dbeafe; color: #1e40af; }
        .badge-leased { background-color: #e9d5ff; color: #6b21a8; }

        .badge-overdue { background-color: #fee2e2; color: #991b1b; }
        .badge-due-soon { background-color: #fef3c7; color: #a16207; }
        .badge-scheduled { background-color: #dcfce7; color: #166534; }
        .badge-not-set { background-color: #f1f5f9; color: #64748b; }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: 600;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
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

            @page {
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>
    {{-- Print Button --}}
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            Print Document
        </button>
    </div>

    {{-- Header --}}
    <div class="header">
        <h1>Vehicle Fleet Report</h1>
        <p>Complete list of all registered vehicles</p>
    </div>

    {{-- Info Section --}}
    <div class="info-section">
        <div class="info-item">
            <strong>Report Date</strong>
            <div>{{ now()->format('d F Y, H:i') }}</div>
        </div>
        <div class="info-item">
            <strong>Total Records</strong>
            <div>{{ $vehicles->count() }} Vehicles</div>
        </div>
        <div class="info-item">
            <strong>Filters Applied</strong>
            <div>
                @if(request()->hasAny(['search', 'status', 'vehicle_type', 'ownership', 'maintenance_status']))
                    @if(request('search'))
                        Search: {{ request('search') }}<br>
                    @endif
                    @if(request('status'))
                        Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}<br>
                    @endif
                    @if(request('vehicle_type'))
                        Type: {{ ucfirst(str_replace('_', ' ', request('vehicle_type'))) }}<br>
                    @endif
                    @if(request('ownership'))
                        Ownership: {{ ucfirst(request('ownership')) }}<br>
                    @endif
                    @if(request('maintenance_status'))
                        Maintenance: {{ ucfirst(str_replace('_', ' ', request('maintenance_status'))) }}
                    @endif
                @else
                    No filters applied
                @endif
            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="statistics">
        <div class="stat-card">
            <div class="label">Total</div>
            <div class="value">{{ number_format($statistics['total']) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Available</div>
            <div class="value" style="color: #16a34a;">{{ number_format($statistics['available']) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">In Use</div>
            <div class="value" style="color: #2563eb;">{{ number_format($statistics['in_use']) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Maintenance</div>
            <div class="value" style="color: #ca8a04;">{{ number_format($statistics['maintenance']) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Inactive</div>
            <div class="value" style="color: #64748b;">{{ number_format($statistics['inactive']) }}</div>
        </div>
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Vehicle Info</th>
                <th style="width: 15%;">Details</th>
                <th style="width: 15%;">Type</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 12%;">Ownership</th>
                <th style="width: 15%;">Maintenance</th>
                <th style="width: 10%;" class="text-right">Odometer</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicles as $vehicle)
                <tr>
                    {{-- Vehicle Info --}}
                    <td>
                        <div class="vehicle-number">{{ $vehicle->vehicle_number }}</div>
                        <div class="license-plate">{{ $vehicle->license_plate }}</div>
                    </td>

                    {{-- Details --}}
                    <td>
                        <div class="font-bold">{{ $vehicle->brand ?? 'N/A' }}</div>
                        <div style="font-size: 10px; color: #64748b;">{{ $vehicle->model ?? 'N/A' }}</div>
                        @if($vehicle->year)
                            <div style="font-size: 10px; color: #94a3b8;">Year: {{ $vehicle->year }}</div>
                        @endif
                    </td>

                    {{-- Type --}}
                    <td>
                        <span class="badge badge-{{ str_replace('_', '-', $vehicle->vehicle_type) }}">
                            {{ ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)) }}
                        </span>
                        @if($vehicle->capacity_kg || $vehicle->capacity_cbm)
                            <div style="font-size: 9px; color: #64748b; margin-top: 5px;">
                                @if($vehicle->capacity_kg)
                                    {{ number_format($vehicle->capacity_kg, 0) }} kg
                                @endif
                                @if($vehicle->capacity_kg && $vehicle->capacity_cbm) | @endif
                                @if($vehicle->capacity_cbm)
                                    {{ number_format($vehicle->capacity_cbm, 2) }} m³
                                @endif
                            </div>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="badge badge-{{ str_replace('_', '-', $vehicle->status) }}">
                            {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
                        </span>
                    </td>

                    {{-- Ownership --}}
                    <td>
                        <span class="badge badge-{{ $vehicle->ownership }}">
                            {{ ucfirst($vehicle->ownership) }}
                        </span>
                    </td>

                    {{-- Maintenance --}}
                    <td>
                        @php
                            $maintenanceStatus = $vehicle->maintenance_status;
                            $labels = [
                                'overdue' => 'Overdue',
                                'due_soon' => 'Due Soon',
                                'scheduled' => 'Scheduled',
                                'not_scheduled' => 'Not Set',
                            ];
                        @endphp
                        <span class="badge badge-{{ str_replace('_', '-', $maintenanceStatus) }}">
                            {{ $labels[$maintenanceStatus] ?? 'Not Set' }}
                        </span>
                        @if($vehicle->next_maintenance_date)
                            <div style="font-size: 9px; color: #64748b; margin-top: 5px;">
                                {{ $vehicle->next_maintenance_date->format('d M Y') }}
                            </div>
                        @endif
                    </td>

                    {{-- Odometer --}}
                    <td class="text-right">
                        <div class="font-bold">{{ number_format($vehicle->odometer_km ?? 0) }}</div>
                        <div style="font-size: 9px; color: #64748b;">km</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 40px;">
                        No vehicles found with the current filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>This document was generated automatically on {{ now()->format('d F Y \a\t H:i') }}</p>
        <p>Vehicle Management System &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>