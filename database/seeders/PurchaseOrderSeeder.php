<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $createdBy = $adminUser ? $adminUser->id : null;
        $approvedBy = $adminUser ? $adminUser->id : null;

        // Ambil warehouses dan suppliers
        $warehouses = Warehouse::all();
        $suppliers = Supplier::all();

        if ($warehouses->isEmpty()) {
            $this->command->warn('Tidak ada warehouse. Jalankan WarehouseSeeder terlebih dahulu.');
            return;
        }

        if ($suppliers->isEmpty()) {
            $this->command->warn('Tidak ada supplier. Jalankan SupplierSeeder terlebih dahulu.');
            return;
        }

        $purchaseOrders = [
            // ========================================
            // PO COMPLETED - Already Received & Paid
            // ========================================
            [
                'po_number' => 'PO-2024-001',
                'warehouse_id' => $this->getWarehouseId($warehouses, 'WH001'),
                'supplier_id' => $this->getSupplierByCode($suppliers, 'SUP-001'),
                'po_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                'expected_delivery_date' => Carbon::now()->subMonths(3)->addDays(7)->format('Y-m-d'),
                'actual_delivery_date' => Carbon::now()->subMonths(3)->addDays(5)->format('Y-m-d'),
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_terms' => 'Net 30',
                'payment_due_days' => 30,
                'subtotal' => 50000000.00,
                'tax_amount' => 5500000.00,
                'tax_rate' => 11.00,
                'discount_amount' => 500000.00,
                'discount_rate' => 1.00,
                'shipping_cost' => 250000.00,
                'other_cost' => 0.00,
                'total_amount' => 55250000.00,
                'paid_amount' => 55250000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Raya Bekasi KM 28 No. 123, Jakarta Timur, DKI Jakarta 13920',
                'shipping_method' => 'Truck Delivery',
                'tracking_number' => 'TRK-2024-001',
                'reference_number' => 'REF-001/2024',
                'supplier_invoice_number' => 'INV-SUP001-2024-001',
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subMonths(3)->addDay(),
                'notes' => 'Purchase order untuk restocking smartphone flagship. Pengiriman tepat waktu.',
                'terms_conditions' => 'Payment: Net 30 days. Warranty: 1 year manufacturer warranty. Return policy: 7 days DOA.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],

            // ========================================
            // PO RECEIVED - Fully Received, Waiting Payment
            // ========================================
            [
                'po_number' => 'PO-2024-002',
                'warehouse_id' => $this->getWarehouseId($warehouses, 'WH002'),
                'supplier_id' => $this->getSupplierByCode($suppliers, 'SUP-002'),
                'po_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'expected_delivery_date' => Carbon::now()->subMonths(2)->addDays(10)->format('Y-m-d'),
                'actual_delivery_date' => Carbon::now()->subMonths(2)->addDays(12)->format('Y-m-d'),
                'status' => 'received',
                'payment_status' => 'partial',
                'payment_terms' => 'Net 45',
                'payment_due_days' => 45,
                'subtotal' => 120000000.00,
                'tax_amount' => 13200000.00,
                'tax_rate' => 11.00,
                'discount_amount' => 2400000.00,
                'discount_rate' => 2.00,
                'shipping_cost' => 500000.00,
                'other_cost' => 100000.00,
                'total_amount' => 131400000.00,
                'paid_amount' => 65700000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Industri Raya No. 45, Surabaya, Jawa Timur 60177',
                'shipping_method' => 'Container Shipping',
                'tracking_number' => 'CNT-2024-045',
                'reference_number' => 'REF-002/2024',
                'supplier_invoice_number' => 'INV-SUP002-2024-078',
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subMonths(2)->addHours(6),
                'notes' => 'Bulk order laptops untuk corporate. Pembayaran 50% DP sudah dilakukan.',
                'terms_conditions' => 'Payment: 50% DP, 50% upon delivery. Warranty: 2 years international warranty.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],

            // ========================================
            // PO PARTIAL RECEIVED - Sedang Proses Receiving
            // ========================================
            [
                'po_number' => 'PO-2024-003',
                'warehouse_id' => $this->getWarehouseId($warehouses, 'WH001'),
                'supplier_id' => $this->getSupplierByCode($suppliers, 'SUP-003'),
                'po_date' => Carbon::now()->subMonth()->format('Y-m-d'),
                'expected_delivery_date' => Carbon::now()->subMonth()->addDays(14)->format('Y-m-d'),
                'actual_delivery_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'status' => 'partial_received',
                'payment_status' => 'unpaid',
                'payment_terms' => 'Net 60',
                'payment_due_days' => 60,
                'subtotal' => 85000000.00,
                'tax_amount' => 9350000.00,
                'tax_rate' => 11.00,
                'discount_amount' => 0.00,
                'discount_rate' => 0.00,
                'shipping_cost' => 750000.00,
                'other_cost' => 250000.00,
                'total_amount' => 95350000.00,
                'paid_amount' => 0.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Raya Bekasi KM 28 No. 123, Jakarta Timur, DKI Jakarta 13920',
                'shipping_method' => 'Multiple Truck Delivery',
                'tracking_number' => 'TRK-2024-123, TRK-2024-124',
                'reference_number' => 'REF-003/2024',
                'supplier_invoice_number' => null,
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subMonth()->addDays(2),
                'notes' => 'Raw materials untuk produksi. Pengiriman bertahap, batch pertama sudah diterima.',
                'terms_conditions' => 'Payment: Net 60 days from full delivery. Material certificate required per batch.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],

            // ========================================
            // PO CONFIRMED - Menunggu Pengiriman
            // ========================================
            [
                'po_number' => 'PO-2024-004',
                'warehouse_id' => $this->getWarehouseId($warehouses, 'WH003'),
                'supplier_id' => $this->getSupplierByCode($suppliers, 'SUP-001'),
                'po_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
                'expected_delivery_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'actual_delivery_date' => null,
                'status' => 'confirmed',
                'payment_status' => 'partial',
                'payment_terms' => 'COD',
                'payment_due_days' => 0,
                'subtotal' => 35000000.00,
                'tax_amount' => 3850000.00,
                'tax_rate' => 11.00,
                'discount_amount' => 350000.00,
                'discount_rate' => 1.00,
                'shipping_cost' => 200000.00,
                'other_cost' => 0.00,
                'total_amount' => 38700000.00,
                'paid_amount' => 19350000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Soekarno Hatta No. 789, Bandung, Jawa Barat 40286',
                'shipping_method' => 'Express Courier',
                'tracking_number' => null,
                'reference_number' => 'REF-004/2024',
                'supplier_invoice_number' => null,
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subDays(14),
                'notes' => 'Office furniture untuk cabang Bandung. DP 50% sudah transfer.',
                'terms_conditions' => 'Payment: 50% DP, 50% COD. Assembly service included.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],

            // ========================================
            // PO APPROVED - Baru Disetujui
            // ========================================
            [
                'po_number' => 'PO-2024-005',
                'warehouse_id' => $this->getWarehouseId($warehouses, 'WH004'),
                'supplier_id' => $this->getSupplierByCode($suppliers, 'SUP-002'),
                'po_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'expected_delivery_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
                'actual_delivery_date' => null,
                'status' => 'approved',
                'payment_status' => 'unpaid',
                'payment_terms' => 'Net 30',
                'payment_due_days' => 30,
                'subtotal' => 95000000.00,
                'tax_amount' => 10450000.00,
                'tax_rate' => 11.00,
                'discount_amount' => 1900000.00,
                'discount_rate' => 2.00,
                'shipping_cost' => 800000.00,
                'other_cost' => 150000.00,
                'total_amount' => 104500000.00,
                'paid_amount' => 0.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Gatot Subroto KM 7, Medan, Sumatera Utara 20122',
                'shipping_method' => 'Sea Freight',
                'tracking_number' => null,
                'reference_number' => 'REF-005/2024',
                'supplier_invoice_number' => null,
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subDays(3),
                'notes' => 'Consumables dan safety equipment untuk warehouse Medan.',
                'terms_conditions' => 'Payment: Net 30 days. All items must comply with SNI standard.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],

            // ========================================
            // PO SUBMITTED - Menunggu Approval
            // ========================================
            [
                'po_number' => 'PO-2024-006',
                'warehouse_id' => $this->getWarehouseId($warehouses, 'WH002'),
                'supplier_id' => $this->getSupplierByCode($suppliers, 'SUP-003'),
                'po_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'expected_delivery_date' => Carbon::now()->addDays(21)->format('Y-m-d'),
                'actual_delivery_date' => null,
                'status' => 'submitted',
                'payment_status' => 'unpaid',
                'payment_terms' => 'Net 45',
                'payment_due_days' => 45,
                'subtotal' => 28000000.00,
                'tax_amount' => 3080000.00,
                'tax_rate' => 11.00,
                'discount_amount' => 0.00,
                'discount_rate' => 0.00,
                'shipping_cost' => 350000.00,
                'other_cost' => 0.00,
                'total_amount' => 31430000.00,
                'paid_amount' => 0.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Industri Raya No. 45, Surabaya, Jawa Timur 60177',
                'shipping_method' => 'Standard Delivery',
                'tracking_number' => null,
                'reference_number' => 'REF-006/2024',
                'supplier_invoice_number' => null,
                'approved_by' => null,
                'approved_at' => null,
                'notes' => 'Restock chemicals dan lubricants. Menunggu approval management.',
                'terms_conditions' => 'Payment: Net 45 days. MSDS certificate required for all chemical products.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],

            // ========================================
            // PO DRAFT - Masih Draft
            // ========================================
            [
                'po_number' => 'PO-2024-007',
                'warehouse_id' => $this->getWarehouseId($warehouses, 'WH001'),
                'supplier_id' => $this->getSupplierByCode($suppliers, 'SUP-001'),
                'po_date' => Carbon::now()->format('Y-m-d'),
                'expected_delivery_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'actual_delivery_date' => null,
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'payment_terms' => 'Net 30',
                'payment_due_days' => 30,
                'subtotal' => 15000000.00,
                'tax_amount' => 1650000.00,
                'tax_rate' => 11.00,
                'discount_amount' => 0.00,
                'discount_rate' => 0.00,
                'shipping_cost' => 150000.00,
                'other_cost' => 0.00,
                'total_amount' => 16800000.00,
                'paid_amount' => 0.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Raya Bekasi KM 28 No. 123, Jakarta Timur, DKI Jakarta 13920',
                'shipping_method' => 'TBD',
                'tracking_number' => null,
                'reference_number' => null,
                'supplier_invoice_number' => null,
                'approved_by' => null,
                'approved_at' => null,
                'notes' => 'Draft PO untuk office supplies. Masih dalam review.',
                'terms_conditions' => null,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],

            // ========================================
            // PO CANCELLED - Dibatalkan
            // ========================================
            [
                'po_number' => 'PO-2024-008',
                'warehouse_id' => $this->getWarehouseId($warehouses, 'WH003'),
                'supplier_id' => $this->getSupplierByCode($suppliers, 'SUP-002'),
                'po_date' => Carbon::now()->subDays(20)->format('Y-m-d'),
                'expected_delivery_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'actual_delivery_date' => null,
                'status' => 'cancelled',
                'payment_status' => 'unpaid',
                'payment_terms' => 'Net 30',
                'payment_due_days' => 30,
                'subtotal' => 42000000.00,
                'tax_amount' => 4620000.00,
                'tax_rate' => 11.00,
                'discount_amount' => 840000.00,
                'discount_rate' => 2.00,
                'shipping_cost' => 300000.00,
                'other_cost' => 0.00,
                'total_amount' => 46080000.00,
                'paid_amount' => 0.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Soekarno Hatta No. 789, Bandung, Jawa Barat 40286',
                'shipping_method' => 'N/A',
                'tracking_number' => null,
                'reference_number' => 'REF-008/2024',
                'supplier_invoice_number' => null,
                'approved_by' => null,
                'approved_at' => null,
                'notes' => 'CANCELLED: Supplier tidak bisa memenuhi lead time. Order dialihkan ke supplier lain.',
                'terms_conditions' => null,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],
        ];

        foreach ($purchaseOrders as $po) {
            PurchaseOrder::create($po);
        }

        $this->command->info('✓ Created ' . count($purchaseOrders) . ' purchase orders successfully with various statuses!');
    }

    /**
     * Get warehouse ID by code
     */
    private function getWarehouseId($warehouses, $code): ?int
    {
        $warehouse = $warehouses->firstWhere('code', $code);
        return $warehouse ? $warehouse->id : $warehouses->first()?->id;
    }

    /**
     * Get supplier ID by code
     */
    private function getSupplierByCode($suppliers, $code): ?int
    {
        $supplier = $suppliers->firstWhere('code', $code);
        return $supplier ? $supplier->id : $suppliers->first()?->id;
    }
}