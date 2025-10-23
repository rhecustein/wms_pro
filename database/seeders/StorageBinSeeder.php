<?php

namespace Database\Seeders;

use App\Models\StorageBin;
use App\Models\Warehouse;
use App\Models\StorageArea;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class StorageBinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = Warehouse::with('storageAreas')->get();

        if ($warehouses->isEmpty()) {
            $this->command->warn('Tidak ada warehouse. Jalankan WarehouseSeeder terlebih dahulu.');
            return;
        }

        // Ambil beberapa customer untuk dedicated bins (jika ada)
        $customers = Customer::limit(3)->get();

        foreach ($warehouses as $warehouse) {
            $this->createBinsForWarehouse($warehouse, $customers);
        }
    }

    /**
     * Create storage bins for a specific warehouse
     */
    private function createBinsForWarehouse($warehouse, $customers): void
    {
        // Ambil storage areas berdasarkan type
        $sprAreas = $warehouse->storageAreas()->where('type', 'spr')->get();
        $bulkyArea = $warehouse->storageAreas()->where('type', 'bulky')->first();
        $quarantineArea = $warehouse->storageAreas()->where('type', 'quarantine')->first();
        $staging1Area = $warehouse->storageAreas()->where('type', 'staging_1')->first();
        $staging2Area = $warehouse->storageAreas()->where('type', 'staging_2')->first();

        $bins = [];
        
        // Prefix untuk warehouse agar code unique
        $warehousePrefix = $warehouse->code; // WH001, WH002, dst

        // 1. SPR Area Bins (Standard Pallet Racking)
        foreach ($sprAreas as $index => $sprArea) {
            $aisleLetter = chr(65 + $index); // A, B, C, dst.
            
            // Buat bins untuk SPR: 5 aisles x 10 rows x 3 columns x 4 levels
            for ($aisle = 0; $aisle < 5; $aisle++) {
                // Gunakan kombinasi aisle letter dan aisle number untuk unique code
                $aisleNumber = $aisle + 1;
                $aisleCode = $aisleLetter . str_pad($aisleNumber, 2, '0', STR_PAD_LEFT); // A01, A02, A03, dst
                
                for ($row = 1; $row <= 10; $row++) {
                    for ($column = 1; $column <= 3; $column++) {
                        for ($level = 0; $level < 4; $level++) {
                            $levelCode = chr(65 + $level); // A, B, C, D
                            $rowStr = str_pad($row, 2, '0', STR_PAD_LEFT);
                            $colStr = str_pad($column, 2, '0', STR_PAD_LEFT);
                            
                            // Format: WH001-A01-0101-A (warehouse-aisle-rowcol-level)
                            $code = "{$warehousePrefix}-{$aisleCode}-{$rowStr}{$colStr}-{$levelCode}";
                            
                            // Tentukan bin type berdasarkan level
                            $binType = ($level === 0) ? 'pick_face' : 'high_rack';
                            
                            // Random status untuk variasi
                            $status = $this->getRandomStatus();
                            
                            // Jika occupied, isi weight dan volume
                            $currentWeight = 0;
                            $currentVolume = 0;
                            $currentQuantity = 0;
                            
                            if ($status === 'occupied') {
                                $currentWeight = rand(100, 800);
                                $currentVolume = rand(1, 2);
                                $currentQuantity = rand(10, 100);
                            }
                            
                            // Assign customer untuk beberapa bin (dedicated storage)
                            $customerId = null;
                            if ($customers->isNotEmpty() && rand(1, 10) > 8) {
                                $customerId = $customers->random()->id;
                            }
                            
                            $bins[] = [
                                'warehouse_id' => $warehouse->id,
                                'storage_area_id' => $sprArea->id,
                                'code' => $code,
                                'aisle' => $aisleCode,
                                'row' => $rowStr,
                                'column' => $colStr,
                                'level' => $levelCode,
                                'status' => $status,
                                'max_weight_kg' => 1000.00,
                                'current_weight_kg' => $currentWeight,
                                'max_volume_cbm' => 2.50,
                                'current_volume_cbm' => $currentVolume,
                                'current_quantity' => $currentQuantity,
                                'bin_type' => $binType,
                                'packaging_restriction' => $this->getRandomPackaging(),
                                'customer_id' => $customerId,
                                'is_hazmat' => rand(1, 20) === 1, // 5% hazmat
                                'is_active' => true,
                                'notes' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
            }
        }

        // 2. Bulky Area Bins
        if ($bulkyArea) {
            for ($row = 1; $row <= 5; $row++) {
                for ($column = 1; $column <= 4; $column++) {
                    $rowStr = str_pad($row, 2, '0', STR_PAD_LEFT);
                    $colStr = str_pad($column, 2, '0', STR_PAD_LEFT);
                    $code = "{$warehousePrefix}-BLK-{$rowStr}{$colStr}-A";
                    
                    $status = $this->getRandomStatus();
                    
                    $bins[] = [
                        'warehouse_id' => $warehouse->id,
                        'storage_area_id' => $bulkyArea->id,
                        'code' => $code,
                        'aisle' => 'BLK',
                        'row' => $rowStr,
                        'column' => $colStr,
                        'level' => 'A',
                        'status' => $status,
                        'max_weight_kg' => 2000.00,
                        'current_weight_kg' => $status === 'occupied' ? rand(500, 1800) : 0,
                        'max_volume_cbm' => 5.00,
                        'current_volume_cbm' => $status === 'occupied' ? rand(2, 4) : 0,
                        'current_quantity' => $status === 'occupied' ? rand(5, 30) : 0,
                        'bin_type' => 'high_rack',
                        'packaging_restriction' => 'pallet',
                        'customer_id' => null,
                        'is_hazmat' => false,
                        'is_active' => true,
                        'notes' => 'Area untuk produk bulky',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // 3. Quarantine Area Bins
        if ($quarantineArea) {
            for ($row = 1; $row <= 3; $row++) {
                for ($column = 1; $column <= 3; $column++) {
                    $rowStr = str_pad($row, 2, '0', STR_PAD_LEFT);
                    $colStr = str_pad($column, 2, '0', STR_PAD_LEFT);
                    $code = "{$warehousePrefix}-QRN-{$rowStr}{$colStr}-A";
                    
                    $bins[] = [
                        'warehouse_id' => $warehouse->id,
                        'storage_area_id' => $quarantineArea->id,
                        'code' => $code,
                        'aisle' => 'QRN',
                        'row' => $rowStr,
                        'column' => $colStr,
                        'level' => 'A',
                        'status' => rand(1, 3) === 1 ? 'occupied' : 'available',
                        'max_weight_kg' => 1000.00,
                        'current_weight_kg' => 0,
                        'max_volume_cbm' => 2.00,
                        'current_volume_cbm' => 0,
                        'current_quantity' => 0,
                        'bin_type' => 'quarantine',
                        'packaging_restriction' => null,
                        'customer_id' => null,
                        'is_hazmat' => false,
                        'is_active' => true,
                        'notes' => 'Area karantina',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // 4. Staging Area 1 (Inbound) Bins
        if ($staging1Area) {
            for ($column = 1; $column <= 10; $column++) {
                $colStr = str_pad($column, 2, '0', STR_PAD_LEFT);
                $code = "{$warehousePrefix}-STG1-{$colStr}";
                
                $bins[] = [
                    'warehouse_id' => $warehouse->id,
                    'storage_area_id' => $staging1Area->id,
                    'code' => $code,
                    'aisle' => 'STG1',
                    'row' => '01',
                    'column' => $colStr,
                    'level' => 'A',
                    'status' => $this->getRandomStatus(),
                    'max_weight_kg' => 1500.00,
                    'current_weight_kg' => 0,
                    'max_volume_cbm' => 3.00,
                    'current_volume_cbm' => 0,
                    'current_quantity' => 0,
                    'bin_type' => 'staging',
                    'packaging_restriction' => null,
                    'customer_id' => null,
                    'is_hazmat' => false,
                    'is_active' => true,
                    'notes' => 'Staging inbound',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // 5. Staging Area 2 (Outbound) Bins
        if ($staging2Area) {
            for ($column = 1; $column <= 10; $column++) {
                $colStr = str_pad($column, 2, '0', STR_PAD_LEFT);
                $code = "{$warehousePrefix}-STG2-{$colStr}";
                
                $bins[] = [
                    'warehouse_id' => $warehouse->id,
                    'storage_area_id' => $staging2Area->id,
                    'code' => $code,
                    'aisle' => 'STG2',
                    'row' => '01',
                    'column' => $colStr,
                    'level' => 'A',
                    'status' => $this->getRandomStatus(),
                    'max_weight_kg' => 1500.00,
                    'current_weight_kg' => 0,
                    'max_volume_cbm' => 3.00,
                    'current_volume_cbm' => 0,
                    'current_quantity' => 0,
                    'bin_type' => 'staging',
                    'packaging_restriction' => null,
                    'customer_id' => null,
                    'is_hazmat' => false,
                    'is_active' => true,
                    'notes' => 'Staging outbound',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert bins in chunks untuk performa
        $chunks = array_chunk($bins, 500);
        foreach ($chunks as $chunk) {
            StorageBin::insert($chunk);
        }

        $this->command->info("Created " . count($bins) . " storage bins for warehouse: {$warehouse->name}");
    }

    /**
     * Get random status for bin
     */
    private function getRandomStatus(): string
    {
        $statuses = [
            'available' => 60,  // 60% available
            'occupied' => 25,   // 25% occupied
            'reserved' => 8,    // 8% reserved
            'blocked' => 5,     // 5% blocked
            'maintenance' => 2, // 2% maintenance
        ];

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($statuses as $status => $percentage) {
            $cumulative += $percentage;
            if ($random <= $cumulative) {
                return $status;
            }
        }

        return 'available';
    }

    /**
     * Get random packaging restriction
     */
    private function getRandomPackaging(): ?string
    {
        $packagings = ['none', 'drum', 'carton', 'pallet', null];
        return $packagings[array_rand($packagings)];
    }
}