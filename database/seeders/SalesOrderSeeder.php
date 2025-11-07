<?php

namespace Database\Seeders;

use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SalesOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $createdBy = $adminUser ? $adminUser->id : null;

        // Ambil data yang diperlukan
        $warehouses = Warehouse::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->get();

        if ($warehouses->isEmpty()) {
            $this->command->warn('Tidak ada warehouse aktif. Jalankan WarehouseSeeder terlebih dahulu.');
            return;
        }

        if ($customers->isEmpty()) {
            $this->command->warn('Tidak ada customer aktif. Jalankan CustomerSeeder terlebih dahulu.');
            return;
        }

        $salesOrders = [
            // ========================================
            // DELIVERED ORDERS (Completed)
            // ========================================
            [
                'so_number' => 'SO-00001',
                'warehouse_id' => $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST001')?->id ?? $customers->first()->id,
                'order_date' => Carbon::now()->subDays(45),
                'requested_delivery_date' => Carbon::now()->subDays(38),
                'status' => 'delivered',
                'payment_status' => 'paid',
                'subtotal' => 125000000.00,
                'tax_amount' => 13750000.00,
                'discount_amount' => 2500000.00,
                'shipping_cost' => 500000.00,
                'total_amount' => 136750000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Sudirman Kav. 52-53, SCBD',
                'shipping_city' => 'Jakarta Selatan',
                'shipping_province' => 'DKI Jakarta',
                'shipping_postal_code' => '12190',
                'notes' => 'Order untuk project kantor baru - prioritas tinggi. Delivery sukses, pembayaran lunas.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now()->subDays(35),
            ],
            [
                'so_number' => 'SO-00002',
                'warehouse_id' => $warehouses->skip(1)->first()?->id ?? $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST003')?->id ?? $customers->skip(1)->first()->id,
                'order_date' => Carbon::now()->subDays(40),
                'requested_delivery_date' => Carbon::now()->subDays(35),
                'status' => 'delivered',
                'payment_status' => 'paid',
                'subtotal' => 45000000.00,
                'tax_amount' => 4950000.00,
                'discount_amount' => 1000000.00,
                'shipping_cost' => 750000.00,
                'total_amount' => 49700000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Raya Darmo No. 123',
                'shipping_city' => 'Surabaya',
                'shipping_province' => 'Jawa Timur',
                'shipping_postal_code' => '60264',
                'notes' => 'Order reguler untuk restocking. Delivered on time.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(40),
                'updated_at' => Carbon::now()->subDays(33),
            ],
            [
                'so_number' => 'SO-00003',
                'warehouse_id' => $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST006')?->id ?? $customers->skip(2)->first()->id,
                'order_date' => Carbon::now()->subDays(35),
                'requested_delivery_date' => Carbon::now()->subDays(30),
                'status' => 'delivered',
                'payment_status' => 'paid',
                'subtotal' => 28500000.00,
                'tax_amount' => 3135000.00,
                'discount_amount' => 500000.00,
                'shipping_cost' => 350000.00,
                'total_amount' => 31485000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Pemuda No. 156',
                'shipping_city' => 'Semarang',
                'shipping_province' => 'Jawa Tengah',
                'shipping_postal_code' => '50132',
                'notes' => 'Order retail untuk toko. Pembayaran lunas via transfer.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(35),
                'updated_at' => Carbon::now()->subDays(28),
            ],

            // ========================================
            // SHIPPED ORDERS (In Transit)
            // ========================================
            [
                'so_number' => 'SO-00004',
                'warehouse_id' => $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST002')?->id ?? $customers->first()->id,
                'order_date' => Carbon::now()->subDays(8),
                'requested_delivery_date' => Carbon::now()->addDays(2),
                'status' => 'shipped',
                'payment_status' => 'paid',
                'subtotal' => 185000000.00,
                'tax_amount' => 20350000.00,
                'discount_amount' => 5000000.00,
                'shipping_cost' => 1200000.00,
                'total_amount' => 201550000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Gatot Subroto Kav. 21',
                'shipping_city' => 'Jakarta Pusat',
                'shipping_province' => 'DKI Jakarta',
                'shipping_postal_code' => '10270',
                'notes' => 'VIP customer - large order electronics. Currently in transit dengan ekspedisi JNE.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'so_number' => 'SO-00005',
                'warehouse_id' => $warehouses->skip(1)->first()?->id ?? $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST004')?->id ?? $customers->skip(1)->first()->id,
                'order_date' => Carbon::now()->subDays(5),
                'requested_delivery_date' => Carbon::now()->addDays(3),
                'status' => 'shipped',
                'payment_status' => 'partial',
                'subtotal' => 62000000.00,
                'tax_amount' => 6820000.00,
                'discount_amount' => 1500000.00,
                'shipping_cost' => 800000.00,
                'total_amount' => 68120000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Asia Afrika No. 88',
                'shipping_city' => 'Bandung',
                'shipping_province' => 'Jawa Barat',
                'shipping_postal_code' => '40111',
                'notes' => 'Order FMCG products. DP 50% dibayar, pelunasan saat terima barang.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(1),
            ],

            // ========================================
            // PACKING ORDERS
            // ========================================
            [
                'so_number' => 'SO-00006',
                'warehouse_id' => $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST007')?->id ?? $customers->skip(3)->first()->id,
                'order_date' => Carbon::now()->subDays(3),
                'requested_delivery_date' => Carbon::now()->addDays(4),
                'status' => 'packing',
                'payment_status' => 'pending',
                'subtotal' => 15600000.00,
                'tax_amount' => 1716000.00,
                'discount_amount' => 300000.00,
                'shipping_cost' => 250000.00,
                'total_amount' => 17266000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Malioboro No. 99',
                'shipping_city' => 'Yogyakarta',
                'shipping_province' => 'DI Yogyakarta',
                'shipping_postal_code' => '55271',
                'notes' => 'Order sedang dalam proses packing. Payment terms 30 days.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subHours(12),
            ],
            [
                'so_number' => 'SO-00007',
                'warehouse_id' => $warehouses->skip(2)->first()?->id ?? $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST010')?->id ?? $customers->skip(4)->first()->id,
                'order_date' => Carbon::now()->subDays(2),
                'requested_delivery_date' => Carbon::now()->addDays(5),
                'status' => 'packing',
                'payment_status' => 'pending',
                'subtotal' => 38500000.00,
                'tax_amount' => 4235000.00,
                'discount_amount' => 800000.00,
                'shipping_cost' => 950000.00,
                'total_amount' => 42885000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Pettarani No. 45',
                'shipping_city' => 'Makassar',
                'shipping_province' => 'Sulawesi Selatan',
                'shipping_postal_code' => '90222',
                'notes' => 'Regional distributor order. QC check sedang dilakukan saat packing.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subHours(8),
            ],

            // ========================================
            // PICKING ORDERS
            // ========================================
            [
                'so_number' => 'SO-00008',
                'warehouse_id' => $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST008')?->id ?? $customers->skip(5)->first()->id,
                'order_date' => Carbon::now()->subDays(1),
                'requested_delivery_date' => Carbon::now()->addDays(6),
                'status' => 'picking',
                'payment_status' => 'pending',
                'subtotal' => 12800000.00,
                'tax_amount' => 1408000.00,
                'discount_amount' => 200000.00,
                'shipping_cost' => 450000.00,
                'total_amount' => 14458000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Sunset Road No. 77',
                'shipping_city' => 'Denpasar',
                'shipping_province' => 'Bali',
                'shipping_postal_code' => '80361',
                'notes' => 'Order untuk minimarket Bali. Picking process sedang berjalan.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subHours(6),
            ],
            [
                'so_number' => 'SO-00009',
                'warehouse_id' => $warehouses->skip(1)->first()?->id ?? $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST011')?->id ?? $customers->skip(6)->first()->id,
                'order_date' => Carbon::now()->subHours(18),
                'requested_delivery_date' => Carbon::now()->addDays(7),
                'status' => 'picking',
                'payment_status' => 'pending',
                'subtotal' => 25400000.00,
                'tax_amount' => 2794000.00,
                'discount_amount' => 500000.00,
                'shipping_cost' => 1200000.00,
                'total_amount' => 28894000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Sudirman No. 123',
                'shipping_city' => 'Balikpapan',
                'shipping_province' => 'Kalimantan Timur',
                'shipping_postal_code' => '76114',
                'notes' => 'Order Kalimantan - koordinasi dengan logistik untuk pengiriman laut.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subHours(18),
                'updated_at' => Carbon::now()->subHours(4),
            ],

            // ========================================
            // CONFIRMED ORDERS
            // ========================================
            [
                'so_number' => 'SO-00010',
                'warehouse_id' => $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST001')?->id ?? $customers->first()->id,
                'order_date' => Carbon::now()->subHours(12),
                'requested_delivery_date' => Carbon::now()->addDays(10),
                'status' => 'confirmed',
                'payment_status' => 'pending',
                'subtotal' => 95000000.00,
                'tax_amount' => 10450000.00,
                'discount_amount' => 2000000.00,
                'shipping_cost' => 800000.00,
                'total_amount' => 104250000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Sudirman Kav. 52-53, SCBD',
                'shipping_city' => 'Jakarta Selatan',
                'shipping_province' => 'DKI Jakarta',
                'shipping_postal_code' => '12190',
                'notes' => 'VIP customer repeat order. Menunggu alokasi warehouse staff untuk picking.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(10),
            ],
            [
                'so_number' => 'SO-00011',
                'warehouse_id' => $warehouses->skip(2)->first()?->id ?? $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST012')?->id ?? $customers->skip(7)->first()->id,
                'order_date' => Carbon::now()->subHours(8),
                'requested_delivery_date' => Carbon::now()->addDays(8),
                'status' => 'confirmed',
                'payment_status' => 'pending',
                'subtotal' => 42000000.00,
                'tax_amount' => 4620000.00,
                'discount_amount' => 1200000.00,
                'shipping_cost' => 1500000.00,
                'total_amount' => 46920000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Sudirman No. 88',
                'shipping_city' => 'Padang',
                'shipping_province' => 'Sumatera Barat',
                'shipping_postal_code' => '25117',
                'notes' => 'Order elektronik untuk toko Padang. Payment terms 30 days.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subHours(8),
                'updated_at' => Carbon::now()->subHours(6),
            ],

            // ========================================
            // DRAFT ORDERS (Not yet confirmed)
            // ========================================
            [
                'so_number' => 'SO-00012',
                'warehouse_id' => $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST005')?->id ?? $customers->skip(8)->first()->id,
                'order_date' => Carbon::now()->subHours(4),
                'requested_delivery_date' => Carbon::now()->addDays(12),
                'status' => 'draft',
                'payment_status' => 'pending',
                'subtotal' => 18500000.00,
                'tax_amount' => 2035000.00,
                'discount_amount' => 350000.00,
                'shipping_cost' => 650000.00,
                'total_amount' => 20835000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Sisingamangaraja No. 45',
                'shipping_city' => 'Medan',
                'shipping_province' => 'Sumatera Utara',
                'shipping_postal_code' => '20217',
                'notes' => 'Draft order - menunggu konfirmasi dari customer. Stock checking in progress.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subHours(4),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            [
                'so_number' => 'SO-00013',
                'warehouse_id' => $warehouses->skip(1)->first()?->id ?? $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST009')?->id ?? $customers->skip(9)->first()->id,
                'order_date' => Carbon::now()->subHours(2),
                'requested_delivery_date' => Carbon::now()->addDays(15),
                'status' => 'draft',
                'payment_status' => 'pending',
                'subtotal' => 8900000.00,
                'tax_amount' => 979000.00,
                'discount_amount' => 150000.00,
                'shipping_cost' => 850000.00,
                'total_amount' => 10579000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Ahmad Yani KM 5',
                'shipping_city' => 'Banjarmasin',
                'shipping_province' => 'Kalimantan Selatan',
                'shipping_postal_code' => '70123',
                'notes' => 'Draft order Banjarmasin - menunggu approval harga dari sales manager.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(1),
            ],
            [
                'so_number' => 'SO-00014',
                'warehouse_id' => $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST013')?->id ?? $customers->skip(10)->first()->id,
                'order_date' => Carbon::now()->subMinutes(45),
                'requested_delivery_date' => Carbon::now()->addDays(14),
                'status' => 'draft',
                'payment_status' => 'pending',
                'subtotal' => 32500000.00,
                'tax_amount' => 3575000.00,
                'discount_amount' => 1000000.00,
                'shipping_cost' => 1200000.00,
                'total_amount' => 36275000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Raden Intan No. 56',
                'shipping_city' => 'Bandar Lampung',
                'shipping_province' => 'Lampung',
                'shipping_postal_code' => '35214',
                'notes' => 'Draft order baru - quotation sent, waiting for customer confirmation.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subMinutes(45),
                'updated_at' => Carbon::now()->subMinutes(30),
            ],

            // ========================================
            // CANCELLED ORDER (Example)
            // ========================================
            [
                'so_number' => 'SO-00015',
                'warehouse_id' => $warehouses->first()->id,
                'customer_id' => $this->getCustomerByCode($customers, 'CUST014')?->id ?? $customers->skip(11)->first()->id,
                'order_date' => Carbon::now()->subDays(10),
                'requested_delivery_date' => Carbon::now()->subDays(5),
                'status' => 'cancelled',
                'payment_status' => 'pending',
                'subtotal' => 15800000.00,
                'tax_amount' => 1738000.00,
                'discount_amount' => 300000.00,
                'shipping_cost' => 400000.00,
                'total_amount' => 17638000.00,
                'currency' => 'IDR',
                'shipping_address' => 'Jl. Ijen No. 77',
                'shipping_city' => 'Malang',
                'shipping_province' => 'Jawa Timur',
                'shipping_postal_code' => '65119',
                'notes' => 'Order dibatalkan oleh customer karena perubahan kebutuhan. Stock sudah dikembalikan.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(8),
                'deleted_at' => null, // Bisa set deleted_at jika ingin soft delete
            ],
        ];

        foreach ($salesOrders as $order) {
            SalesOrder::create($order);
        }

        $this->command->info('✓ Created ' . count($salesOrders) . ' sales orders successfully with various statuses!');
    }

    /**
     * Get customer by code
     */
    private function getCustomerByCode($customers, $code)
    {
        return $customers->firstWhere('code', $code);
    }
}