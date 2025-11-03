<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Pest\ArchPresets\Custom;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            WarehouseSeeder::class,
            StorageAreaSeeder::class,
            StorageBinSeeder::class,
            ProductCategorySeeder::class,
            UnitSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            VendorSeeder::class,
            VehicleSeeder::class,
            EquipmentSeeder::class,
            PalletSeeder::class,
            InventoryStockSeeder::class,
            StockMovementSeeder::class,
            StockAdjustmentSeeder::class,      // Jalankan ini dulu
            StockAdjustmentItemSeeder::class,
            StockOpnameSeeder::class,        // Jalankan ini dulu
            StockOpnameItemSeeder::class,  
        ]);
    }
}