<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\Inbound;
use App\Models\PackingOrder;
use App\Models\PickingOrder;
use App\Models\PutawayTask;
use Illuminate\Database\Seeder;
use Pest\ArchPresets\Custom;
use Spatie\LaravelPackageTools\Package;

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
            SupplierSeeder::class,
            VehicleSeeder::class,
            EquipmentSeeder::class,
            PalletSeeder::class,
            InventoryStockSeeder::class,
            StockMovementSeeder::class,
            StockAdjustmentSeeder::class,      // Jalankan ini dulu
            StockAdjustmentItemSeeder::class,
            StockOpnameSeeder::class,        // Jalankan ini dulu
            StockOpnameItemSeeder::class,  
            PurchaseOrderSeeder::class,
            PurchaseOrderItemSeeder::class,
            InboundShipmentSeeder::class,
            GoodReceivingSeeder::class,
            PutawayTaskSeeder::class,
            SalesOrderSeeder::class,
            SalesOrderItemSeeder::class,
            PickingOrderSeeder::class,
            PickingOrderItemSeeder::class,
            PackingOrderSeeder::class,
            PackingOrderItemSeeder::class,
            DeliveryOrderSeeder::class,
            DeliveryOrderItemSeeder::class,
        ]);
    }
}