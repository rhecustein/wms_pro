<?php

namespace Database\Seeders;

use App\Models\InventoryStock;
use App\Models\Warehouse;
use App\Models\StorageBin;
use App\Models\Product;
use App\Models\Pallet;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InventoryStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data yang dibutuhkan
        $warehouses = Warehouse::all();
        $storageBins = StorageBin::all();
        $products = Product::all();
        $pallets = Pallet::all();
        $customers = Customer::all();
        $vendors = Vendor::all();

        // Pastikan ada data yang dibutuhkan
        if ($warehouses->isEmpty() || $storageBins->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Pastikan seeder Warehouse, StorageBin, dan Product sudah dijalankan terlebih dahulu!');
            return;
        }

        $statuses = ['available', 'reserved', 'quarantine', 'damaged', 'expired'];
        $locationTypes = ['pick_face', 'high_rack', 'staging', 'quarantine'];
        $units = ['PCS', 'BOX', 'CARTON', 'PALLET', 'KG', 'LITER'];

        $stocks = [];

        // Generate stock untuk setiap warehouse
        foreach ($warehouses as $warehouse) {
            // Ambil storage bins untuk warehouse ini
            $warehouseBins = $storageBins->where('warehouse_id', $warehouse->id);
            
            if ($warehouseBins->isEmpty()) {
                continue;
            }

            // Generate 10-15 stock items per warehouse
            $stockCount = rand(10, 15);
            
            for ($i = 0; $i < $stockCount; $i++) {
                $product = $products->random();
                $storageBin = $warehouseBins->random();
                $status = $statuses[array_rand($statuses)];
                $locationType = $locationTypes[array_rand($locationTypes)];
                
                // Generate quantity based on status
                $quantity = match($status) {
                    'available' => rand(100, 1000),
                    'reserved' => rand(50, 500),
                    'quarantine' => rand(10, 100),
                    'damaged' => rand(5, 50),
                    'expired' => rand(5, 30),
                    default => rand(100, 500)
                };

                $reservedQty = $status === 'reserved' ? rand(10, $quantity) : 0;
                
                // Generate dates
                $receivedDate = Carbon::now()->subDays(rand(1, 180));
                $manufacturingDate = (clone $receivedDate)->subDays(rand(30, 90));
                $expiryDate = $status === 'expired' 
                    ? Carbon::now()->subDays(rand(1, 30))
                    : (clone $manufacturingDate)->addMonths(rand(6, 24));

                $stock = [
                    'warehouse_id' => $warehouse->id,
                    'storage_bin_id' => $storageBin->id,
                    'product_id' => $product->id,
                    'batch_number' => 'BATCH-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                    'serial_number' => rand(0, 10) > 7 ? 'SN-' . strtoupper(substr(md5(uniqid()), 0, 10)) : null,
                    'quantity' => $quantity,
                    'reserved_quantity' => $reservedQty,
                    'unit_of_measure' => $units[array_rand($units)],
                    'manufacturing_date' => $manufacturingDate,
                    'expiry_date' => $expiryDate,
                    'received_date' => $receivedDate,
                    'pallet_id' => $pallets->isNotEmpty() && rand(0, 10) > 5 ? $pallets->random()->id : null,
                    'status' => $status,
                    'location_type' => $locationType,
                    'customer_id' => $customers->isNotEmpty() && rand(0, 10) > 7 ? $customers->random()->id : null,
                    'vendor_id' => $vendors->isNotEmpty() && rand(0, 10) > 6 ? $vendors->random()->id : null,
                    'cost_per_unit' => rand(1000, 100000) / 100,
                    'notes' => $this->generateNotes($status),
                    'created_at' => $receivedDate,
                    'updated_at' => Carbon::now(),
                ];

                $stocks[] = $stock;
            }
        }

        // Tambahkan beberapa stock khusus untuk testing
        if ($products->count() > 0 && $warehouses->count() > 0) {
            $mainWarehouse = $warehouses->first();
            $mainBins = $storageBins->where('warehouse_id', $mainWarehouse->id);
            
            if ($mainBins->isNotEmpty()) {
                // Stock dengan quantity tinggi
                $stocks[] = [
                    'warehouse_id' => $mainWarehouse->id,
                    'storage_bin_id' => $mainBins->first()->id,
                    'product_id' => $products->first()->id,
                    'batch_number' => 'BATCH-HIGH-QTY-001',
                    'serial_number' => null,
                    'quantity' => 5000,
                    'reserved_quantity' => 500,
                    'unit_of_measure' => 'PCS',
                    'manufacturing_date' => Carbon::now()->subMonths(2),
                    'expiry_date' => Carbon::now()->addMonths(10),
                    'received_date' => Carbon::now()->subMonths(1),
                    'pallet_id' => $pallets->isNotEmpty() ? $pallets->first()->id : null,
                    'status' => 'available',
                    'location_type' => 'high_rack',
                    'customer_id' => null,
                    'vendor_id' => $vendors->isNotEmpty() ? $vendors->first()->id : null,
                    'cost_per_unit' => 15000.00,
                    'notes' => 'High volume stock - Fast moving item',
                    'created_at' => Carbon::now()->subMonths(1),
                    'updated_at' => Carbon::now(),
                ];

                // Stock dengan status expired
                $stocks[] = [
                    'warehouse_id' => $mainWarehouse->id,
                    'storage_bin_id' => $mainBins->last()->id,
                    'product_id' => $products->count() > 1 ? $products->skip(1)->first()->id : $products->first()->id,
                    'batch_number' => 'BATCH-EXPIRED-001',
                    'serial_number' => null,
                    'quantity' => 50,
                    'reserved_quantity' => 0,
                    'unit_of_measure' => 'BOX',
                    'manufacturing_date' => Carbon::now()->subMonths(24),
                    'expiry_date' => Carbon::now()->subDays(15),
                    'received_date' => Carbon::now()->subMonths(23),
                    'pallet_id' => null,
                    'status' => 'expired',
                    'location_type' => 'quarantine',
                    'customer_id' => null,
                    'vendor_id' => null,
                    'cost_per_unit' => 25000.00,
                    'notes' => 'Expired - Awaiting disposal approval',
                    'created_at' => Carbon::now()->subMonths(23),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        // Insert semua data
        foreach ($stocks as $stock) {
            InventoryStock::create($stock);
        }

        $this->command->info('Inventory Stock seeder berhasil dijalankan! Total: ' . count($stocks) . ' records');
    }

    /**
     * Generate notes based on status
     */
    private function generateNotes(string $status): ?string
    {
        return match($status) {
            'available' => rand(0, 10) > 7 ? 'Ready for dispatch' : null,
            'reserved' => 'Reserved for order #ORD-' . rand(1000, 9999),
            'quarantine' => 'Quality check pending - Hold until inspection complete',
            'damaged' => 'Damaged during handling - Requires assessment',
            'expired' => 'Expired product - Awaiting disposal authorization',
            default => null
        };
    }
}