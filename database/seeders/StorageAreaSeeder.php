<?php

namespace Database\Seeders;

use App\Models\StorageArea;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;

class StorageAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $createdBy = $adminUser ? $adminUser->id : null;

        // Ambil semua warehouse
        $warehouses = Warehouse::all();

        if ($warehouses->isEmpty()) {
            $this->command->warn('Tidak ada warehouse. Jalankan WarehouseSeeder terlebih dahulu.');
            return;
        }

        foreach ($warehouses as $warehouse) {
            $this->createStorageAreasForWarehouse($warehouse, $createdBy);
        }
    }

    /**
     * Create storage areas for a specific warehouse
     */
    private function createStorageAreasForWarehouse($warehouse, $createdBy): void
    {
        $storageAreas = [
            // SPR Areas (Standard Pallet Racking)
            [
                'warehouse_id' => $warehouse->id,
                'code' => 'SPR-001',
                'name' => 'SPR Area 1 - Fast Moving',
                'type' => 'spr',
                'area_sqm' => 500.00,
                'height_meters' => 6.00,
                'capacity_pallets' => 200,
                'is_active' => true,
                'description' => 'Area untuk produk fast moving dengan akses mudah',
                'created_by' => $createdBy,
            ],
            [
                'warehouse_id' => $warehouse->id,
                'code' => 'SPR-002',
                'name' => 'SPR Area 2 - Medium Moving',
                'type' => 'spr',
                'area_sqm' => 600.00,
                'height_meters' => 6.00,
                'capacity_pallets' => 240,
                'is_active' => true,
                'description' => 'Area untuk produk medium moving',
                'created_by' => $createdBy,
            ],
            [
                'warehouse_id' => $warehouse->id,
                'code' => 'SPR-003',
                'name' => 'SPR Area 3 - Slow Moving',
                'type' => 'spr',
                'area_sqm' => 400.00,
                'height_meters' => 6.00,
                'capacity_pallets' => 160,
                'is_active' => true,
                'description' => 'Area untuk produk slow moving',
                'created_by' => $createdBy,
            ],

            // Bulky Area
            [
                'warehouse_id' => $warehouse->id,
                'code' => 'BLK-001',
                'name' => 'Bulky Area 1',
                'type' => 'bulky',
                'area_sqm' => 800.00,
                'height_meters' => 4.00,
                'capacity_pallets' => 100,
                'is_active' => true,
                'description' => 'Area untuk produk berukuran besar dan tidak standar',
                'created_by' => $createdBy,
            ],

            // Quarantine Area
            [
                'warehouse_id' => $warehouse->id,
                'code' => 'QRN-001',
                'name' => 'Quarantine Area',
                'type' => 'quarantine',
                'area_sqm' => 200.00,
                'height_meters' => 5.00,
                'capacity_pallets' => 50,
                'is_active' => true,
                'description' => 'Area karantina untuk produk yang perlu inspeksi atau bermasalah',
                'created_by' => $createdBy,
            ],

            // Staging Area 1 (Inbound)
            [
                'warehouse_id' => $warehouse->id,
                'code' => 'STG1-001',
                'name' => 'Staging Area 1 - Inbound',
                'type' => 'staging_1',
                'area_sqm' => 300.00,
                'height_meters' => 3.00,
                'capacity_pallets' => 80,
                'is_active' => true,
                'description' => 'Area staging untuk penerimaan barang masuk',
                'created_by' => $createdBy,
            ],

            // Staging Area 2 (Outbound)
            [
                'warehouse_id' => $warehouse->id,
                'code' => 'STG2-001',
                'name' => 'Staging Area 2 - Outbound',
                'type' => 'staging_2',
                'area_sqm' => 350.00,
                'height_meters' => 3.00,
                'capacity_pallets' => 90,
                'is_active' => true,
                'description' => 'Area staging untuk persiapan pengiriman barang keluar',
                'created_by' => $createdBy,
            ],

            // Virtual Area
            [
                'warehouse_id' => $warehouse->id,
                'code' => 'VRT-001',
                'name' => 'Virtual Area - Damaged',
                'type' => 'virtual',
                'area_sqm' => null,
                'height_meters' => null,
                'capacity_pallets' => null,
                'is_active' => true,
                'description' => 'Area virtual untuk tracking produk rusak',
                'created_by' => $createdBy,
            ],
            [
                'warehouse_id' => $warehouse->id,
                'code' => 'VRT-002',
                'name' => 'Virtual Area - In Transit',
                'type' => 'virtual',
                'area_sqm' => null,
                'height_meters' => null,
                'capacity_pallets' => null,
                'is_active' => true,
                'description' => 'Area virtual untuk tracking produk dalam perjalanan',
                'created_by' => $createdBy,
            ],
        ];

        foreach ($storageAreas as $area) {
            StorageArea::create($area);
        }

        $this->command->info("Created storage areas for warehouse: {$warehouse->name}");
    }
}