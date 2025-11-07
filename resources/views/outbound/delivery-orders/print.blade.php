<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Order - {{ $deliveryOrder->do_number }}</title>
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
            @page {
                margin: 1cm;
                size: A4;
            }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .barcode {
            font-family: 'Libre Barcode 128', cursive;
            font-size: 48px;
            letter-spacing: 0;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Print Button -->
    <div class="no-print fixed top-4 right-4 z-50">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 transition-colors">
            <i class="fas fa-print"></i>
            <span>Print Delivery Order</span>
        </button>
        <button onclick="window.history.back()" class="mt-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </button>
    </div>

    <!-- Print Content -->
    <div class="max-w-4xl mx-auto bg-white shadow-lg my-8 p-8">
        <!-- Header -->
        <div class="border-b-4 border-blue-600 pb-6 mb-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">DELIVERY ORDER</h1>
                    <p class="text-gray-600">{{ $deliveryOrder->warehouse->name ?? 'Warehouse Name' }}</p>
                    <p class="text-sm text-gray-500">{{ $deliveryOrder->warehouse->address ?? 'Warehouse Address' }}</p>
                    <p class="text-sm text-gray-500">Tel: {{ $deliveryOrder->warehouse->phone ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <div class="bg-blue-600 text-white px-4 py-2 rounded-lg inline-block mb-3">
                        <p class="text-xs uppercase">DO Number</p>
                        <p class="text-xl font-bold">{{ $deliveryOrder->do_number }}</p>
                    </div>
                    <div class="barcode text-center">
                        *{{ $deliveryOrder->do_number }}*
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Information Grid -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <!-- Left Column -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3 border-b pb-2">Informasi Pengiriman</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex">
                        <span class="text-gray-600 w-32">Tanggal Kirim:</span>
                        <span class="font-semibold">{{ \Carbon\Carbon::parse($deliveryOrder->delivery_date)->format('d M Y') }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-600 w-32">Status:</span>
                        <span class="font-semibold">
                            @php
                                $statusLabels = [
                                    'prepared' => 'Disiapkan',
                                    'loaded' => 'Dimuat',
                                    'in_transit' => 'Dalam Perjalanan',
                                    'delivered' => 'Terkirim',
                                    'returned' => 'Dikembalikan',
                                    'cancelled' => 'Dibatalkan'
                                ];
                            @endphp
                            {{ $statusLabels[$deliveryOrder->status] ?? $deliveryOrder->status }}
                        </span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-600 w-32">SO Number:</span>
                        <span class="font-semibold">{{ $deliveryOrder->salesOrder->so_number ?? '-' }}</span>
                    </div>
                    @if($deliveryOrder->packingOrder)
                    <div class="flex">
                        <span class="text-gray-600 w-32">PO Number:</span>
                        <span class="font-semibold">{{ $deliveryOrder->packingOrder->po_number ?? '-' }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3 border-b pb-2">Detail Pelanggan</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex">
                        <span class="text-gray-600 w-32">Pelanggan:</span>
                        <span class="font-semibold">{{ $deliveryOrder->customer->name ?? '-' }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-600 w-32">Penerima:</span>
                        <span class="font-semibold">{{ $deliveryOrder->recipient_name ?? '-' }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-600 w-32">Telepon:</span>
                        <span class="font-semibold">{{ $deliveryOrder->recipient_phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3 border-b pb-2">Alamat Pengiriman</h3>
            <p class="text-sm bg-gray-50 p-4 rounded-lg">
                {{ $deliveryOrder->shipping_address ?? $deliveryOrder->customer->address ?? 'Alamat tidak tersedia' }}
            </p>
        </div>

        <!-- Vehicle & Driver Information -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3 border-b pb-2">Kendaraan</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex">
                        <span class="text-gray-600 w-32">Nomor Polisi:</span>
                        <span class="font-semibold">{{ $deliveryOrder->vehicle->license_plate ?? '-' }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-600 w-32">Tipe:</span>
                        <span class="font-semibold">{{ $deliveryOrder->vehicle->type ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3 border-b pb-2">Pengemudi</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex">
                        <span class="text-gray-600 w-32">Nama:</span>
                        <span class="font-semibold">{{ $deliveryOrder->driver->name ?? '-' }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-600 w-32">Telepon:</span>
                        <span class="font-semibold">{{ $deliveryOrder->driver->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Details -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3 border-b pb-2">Detail Paket</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <p class="text-gray-600 text-sm mb-1">Total Kotak</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $deliveryOrder->total_boxes }}</p>
                    <p class="text-xs text-gray-500">boxes</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <p class="text-gray-600 text-sm mb-1">Total Berat</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($deliveryOrder->total_weight_kg, 2) }}</p>
                    <p class="text-xs text-gray-500">kg</p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($deliveryOrder->notes)
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3 border-b pb-2">Catatan</h3>
            <p class="text-sm bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
                {{ $deliveryOrder->notes }}
            </p>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="grid grid-cols-3 gap-6 mt-12 pt-6 border-t-2">
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-16">Disiapkan Oleh</p>
                <div class="border-t-2 border-gray-800 pt-2">
                    <p class="font-semibold">{{ $deliveryOrder->createdBy->name ?? 'System' }}</p>
                    <p class="text-xs text-gray-500">{{ $deliveryOrder->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600 mb-16">Pengemudi</p>
                <div class="border-t-2 border-gray-800 pt-2">
                    <p class="font-semibold">{{ $deliveryOrder->driver->name ?? '.........................' }}</p>
                    <p class="text-xs text-gray-500">Tanda Tangan & Cap</p>
                </div>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600 mb-16">Penerima</p>
                <div class="border-t-2 border-gray-800 pt-2">
                    <p class="font-semibold">{{ $deliveryOrder->received_by_name ?? '.........................' }}</p>
                    <p class="text-xs text-gray-500">
                        @if($deliveryOrder->delivered_at)
                            {{ $deliveryOrder->delivered_at->format('d M Y H:i') }}
                        @else
                            Tanda Tangan & Tanggal
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t text-center text-xs text-gray-500">
            <p>Dokumen ini dicetak secara otomatis pada {{ now()->format('d M Y H:i') }}</p>
            <p class="mt-1">Harap periksa semua item sebelum menandatangani dokumen ini</p>
        </div>

        <!-- Delivery Proof (if exists) -->
        @if($deliveryOrder->delivery_proof_image)
        <div class="mt-8 page-break">
            <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3 border-b pb-2">Bukti Pengiriman</h3>
            <div class="text-center">
                <img src="{{ asset('storage/' . $deliveryOrder->delivery_proof_image) }}" 
                     alt="Delivery Proof" 
                     class="max-w-full h-auto mx-auto rounded-lg border-2 border-gray-300"
                     style="max-height: 400px;">
                <p class="text-sm text-gray-600 mt-2">
                    Diterima oleh: <span class="font-semibold">{{ $deliveryOrder->received_by_name }}</span>
                </p>
            </div>
        </div>
        @endif
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
        
        // Print function
        function printDocument() {
            window.print();
        }
    </script>
</body>
</html>