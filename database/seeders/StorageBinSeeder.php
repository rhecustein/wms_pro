<?php

namespace Database\Seeders;

use App\Models\StorageBin;
use App\Models\Warehouse;
use App\Models\StorageArea;
use App\Models\Customer;
use App\Models\User;
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
        
        // Ambil admin user untuk last_count_by (opsional)
        $adminUser = User::first();

        foreach ($warehouses as $warehouse) {
            $this->createBinsForWarehouse($warehouse, $customers, $adminUser);
        }
    }

    /**
     * Create storage bins for a specific warehouse
     */
    private function createBinsForWarehouse($warehouse, $customers, $adminUser): void
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
                            
                            // Jika occupied, isi weight, volume, dan quantity
                            $isOccupied = $status === 'occupied';
                            $currentWeight = $isOccupied ? rand(100, 800) : 0;
                            $currentVolume = $isOccupied ? round(rand(10, 200) / 100, 3) : 0; // 0.1 - 2.0 CBM
                            $currentQuantity = $isOccupied ? rand(10, 100) : 0;
                            
                            // Assign customer untuk beberapa bin (dedicated storage)
                            $customerId = null;
                            if ($customers->isNotEmpty() && rand(1, 10) > 8) {
                                $customerId = $customers->random()->id;
                            }
                            
                            // Random temperature control
                            $isTempControlled = rand(1, 20) === 1; // 5% temperature controlled
                            
                            // Last count date untuk bins yang occupied
                            $lastCountDate = $isOccupied ? now()->subDays(rand(1, 30)) : null;
                            $lastCountBy = ($isOccupied && $adminUser) ? $adminUser->id : null;
                            $lastMovementDate = $isOccupied ? now()->subDays(rand(1, 7)) : null;
                            
                            $bins[] = [
                                // Relations
                                'warehouse_id' => $warehouse->id,
                                'storage_area_id' => $sprArea->id,
                                'customer_id' => $customerId,
                                
                                // Location identifiers
                                'code' => $code,
                                'aisle' => $aisleCode,
                                'row' => $rowStr,
                                'column' => $colStr,
                                'level' => $levelCode,
                                
                                // Status & Type
                                'status' => $status,
                                'bin_type' => $binType,
                                'packaging_restriction' => $this->getRandomPackaging(),
                                
                                // Capacity - Weight
                                'max_weight_kg' => 1000.00,
                                'current_weight_kg' => $currentWeight,
                                
                                // Capacity - Volume
                                'max_volume_cbm' => 2.500,
                                'current_volume_cbm' => $currentVolume,
                                
                                // Capacity - Quantity
                                'current_quantity' => $currentQuantity,
                                'min_quantity' => 0,
                                'max_quantity' => 150.00,
                                
                                // Physical dimensions of the bin itself
                                'bin_length_cm' => 120.00,
                                'bin_width_cm' => 100.00,
                                'bin_height_cm' => ($level === 0) ? 150.00 : 200.00, // Pick face lebih pendek
                                
                                // Flags
                                'is_occupied' => $isOccupied,
                                'is_hazmat' => rand(1, 20) === 1, // 5% hazmat
                                'is_active' => true,
                                'is_temperature_controlled' => $isTempControlled,
                                'is_locked' => false,
                                
                                // Temperature control
                                'min_temperature_c' => $isTempControlled ? -18.00 : null,
                                'max_temperature_c' => $isTempControlled ? -15.00 : null,
                                
                                // Additional info
                                'picking_priority' => ($level === 0) ? rand(80, 100) : rand(20, 50), // Pick face priority tinggi
                                'barcode' => 'BRC-' . $code,
                                'rfid_tag' => 'RFID-' . $code,
                                'notes' => null,
                                
                                // Audit fields
                                'last_count_date' => $lastCountDate,
                                'last_count_by' => $lastCountBy,
                                'last_movement_date' => $lastMovementDate,
                                
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
                    $isOccupied = $status === 'occupied';
                    $currentWeight = $isOccupied ? rand(500, 1800) : 0;
                    $currentVolume = $isOccupied ? round(rand(200, 400) / 100, 3) : 0; // 2.0 - 4.0 CBM
                    $currentQuantity = $isOccupied ? rand(5, 30) : 0;
                    
                    $bins[] = [
                        // Relations
                        'warehouse_id' => $warehouse->id,
                        'storage_area_id' => $bulkyArea->id,
                        'customer_id' => null,
                        
                        // Location identifiers
                        'code' => $code,
                        'aisle' => 'BLK',
                        'row' => $rowStr,
                        'column' => $colStr,
                        'level' => 'A',
                        
                        // Status & Type
                        'status' => $status,
                        'bin_type' => 'high_rack',
                        'packaging_restriction' => 'pallet',
                        
                        // Capacity - Weight
                        'max_weight_kg' => 2000.00,
                        'current_weight_kg' => $currentWeight,
                        
                        // Capacity - Volume
                        'max_volume_cbm' => 5.000,
                        'current_volume_cbm' => $currentVolume,
                        
                        // Capacity - Quantity
                        'current_quantity' => $currentQuantity,
                        'min_quantity' => 0,
                        'max_quantity' => 50.00,
                        
                        // Physical dimensions
                        'bin_length_cm' => 250.00,
                        'bin_width_cm' => 120.00,
                        'bin_height_cm' => 200.00,
                        
                        // Flags
                        'is_occupied' => $isOccupied,
                        'is_hazmat' => false,
                        'is_active' => true,
                        'is_temperature_controlled' => false,
                        'is_locked' => false,
                        
                        // Temperature control
                        'min_temperature_c' => null,
                        'max_temperature_c' => null,
                        
                        // Additional info
                        'picking_priority' => rand(10, 30),
                        'barcode' => 'BRC-' . $code,
                        'rfid_tag' => 'RFID-' . $code,
                        'notes' => 'Area untuk produk bulky',
                        
                        // Audit fields
                        'last_count_date' => $isOccupied ? now()->subDays(rand(1, 30)) : null,
                        'last_count_by' => ($isOccupied && $adminUser) ? $adminUser->id : null,
                        'last_movement_date' => $isOccupied ? now()->subDays(rand(1, 7)) : null,
                        
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
                    
                    $isOccupied = rand(1, 3) === 1; // 33% occupied
                    
                    $bins[] = [
                        // Relations
                        'warehouse_id' => $warehouse->id,
                        'storage_area_id' => $quarantineArea->id,
                        'customer_id' => null,
                        
                        // Location identifiers
                        'code' => $code,
                        'aisle' => 'QRN',
                        'row' => $rowStr,
                        'column' => $colStr,
                        'level' => 'A',
                        
                        // Status & Type
                        'status' => $isOccupied ? 'occupied' : 'available',
                        'bin_type' => 'quarantine',
                        'packaging_restriction' => null,
                        
                        // Capacity - Weight
                        'max_weight_kg' => 1000.00,
                        'current_weight_kg' => $isOccupied ? rand(100, 800) : 0,
                        
                        // Capacity - Volume
                        'max_volume_cbm' => 2.000,
                        'current_volume_cbm' => $isOccupied ? round(rand(50, 150) / 100, 3) : 0,
                        
                        // Capacity - Quantity
                        'current_quantity' => $isOccupied ? rand(5, 50) : 0,
                        'min_quantity' => 0,
                        'max_quantity' => 100.00,
                        
                        // Physical dimensions
                        'bin_length_cm' => 120.00,
                        'bin_width_cm' => 100.00,
                        'bin_height_cm' => 180.00,
                        
                        // Flags
                        'is_occupied' => $isOccupied,
                        'is_hazmat' => false,
                        'is_active' => true,
                        'is_temperature_controlled' => false,
                        'is_locked' => $isOccupied, // Quarantine area usually locked
                        
                        // Temperature control
                        'min_temperature_c' => null,
                        'max_temperature_c' => null,
                        
                        // Additional info
                        'picking_priority' => 0, // Quarantine has no priority
                        'barcode' => 'BRC-' . $code,
                        'rfid_tag' => 'RFID-' . $code,
                        'notes' => 'Area karantina untuk produk yang perlu inspeksi',
                        
                        // Audit fields
                        'last_count_date' => $isOccupied ? now()->subDays(rand(1, 15)) : null,
                        'last_count_by' => ($isOccupied && $adminUser) ? $adminUser->id : null,
                        'last_movement_date' => $isOccupied ? now()->subDays(rand(1, 5)) : null,
                        
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
                
                $status = $this->getRandomStatus();
                $isOccupied = $status === 'occupied';
                
                $bins[] = [
                    // Relations
                    'warehouse_id' => $warehouse->id,
                    'storage_area_id' => $staging1Area->id,
                    'customer_id' => null,
                    
                    // Location identifiers
                    'code' => $code,
                    'aisle' => 'STG1',
                    'row' => '01',
                    'column' => $colStr,
                    'level' => 'A',
                    
                    // Status & Type
                    'status' => $status,
                    'bin_type' => 'staging',
                    'packaging_restriction' => null,
                    
                    // Capacity - Weight
                    'max_weight_kg' => 1500.00,
                    'current_weight_kg' => $isOccupied ? rand(200, 1200) : 0,
                    
                    // Capacity - Volume
                    'max_volume_cbm' => 3.000,
                    'current_volume_cbm' => $isOccupied ? round(rand(100, 250) / 100, 3) : 0,
                    
                    // Capacity - Quantity
                    'current_quantity' => $isOccupied ? rand(20, 150) : 0,
                    'min_quantity' => 0,
                    'max_quantity' => 200.00,
                    
                    // Physical dimensions
                    'bin_length_cm' => 300.00,
                    'bin_width_cm' => 150.00,
                    'bin_height_cm' => 100.00, // Staging area biasanya lebih rendah
                    
                    // Flags
                    'is_occupied' => $isOccupied,
                    'is_hazmat' => false,
                    'is_active' => true,
                    'is_temperature_controlled' => false,
                    'is_locked' => false,
                    
                    // Temperature control
                    'min_temperature_c' => null,
                    'max_temperature_c' => null,
                    
                    // Additional info
                    'picking_priority' => 100, // Staging priority sangat tinggi
                    'barcode' => 'BRC-' . $code,
                    'rfid_tag' => 'RFID-' . $code,
                    'notes' => 'Staging inbound - area penerimaan barang',
                    
                    // Audit fields
                    'last_count_date' => $isOccupied ? now()->subDays(rand(1, 3)) : null,
                    'last_count_by' => ($isOccupied && $adminUser) ? $adminUser->id : null,
                    'last_movement_date' => $isOccupied ? now()->subHours(rand(1, 24)) : null,
                    
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
                
                $status = $this->getRandomStatus();
                $isOccupied = $status === 'occupied';
                
                $bins[] = [
                    // Relations
                    'warehouse_id' => $warehouse->id,
                    'storage_area_id' => $staging2Area->id,
                    'customer_id' => null,
                    
                    // Location identifiers
                    'code' => $code,
                    'aisle' => 'STG2',
                    'row' => '01',
                    'column' => $colStr,
                    'level' => 'A',
                    
                    // Status & Type
                    'status' => $status,
                    'bin_type' => 'staging',
                    'packaging_restriction' => null,
                    
                    // Capacity - Weight
                    'max_weight_kg' => 1500.00,
                    'current_weight_kg' => $isOccupied ? rand(200, 1200) : 0,
                    
                    // Capacity - Volume
                    'max_volume_cbm' => 3.000,
                    'current_volume_cbm' => $isOccupied ? round(rand(100, 250) / 100, 3) : 0,
                    
                    // Capacity - Quantity
                    'current_quantity' => $isOccupied ? rand(20, 150) : 0,
                    'min_quantity' => 0,
                    'max_quantity' => 200.00,
                    
                    // Physical dimensions
                    'bin_length_cm' => 300.00,
                    'bin_width_cm' => 150.00,
                    'bin_height_cm' => 100.00,
                    
                    // Flags
                    'is_occupied' => $isOccupied,
                    'is_hazmat' => false,
                    'is_active' => true,
                    'is_temperature_controlled' => false,
                    'is_locked' => false,
                    
                    // Temperature control
                    'min_temperature_c' => null,
                    'max_temperature_c' => null,
                    
                    // Additional info
                    'picking_priority' => 100, // Staging priority sangat tinggi
                    'barcode' => 'BRC-' . $code,
                    'rfid_tag' => 'RFID-' . $code,
                    'notes' => 'Staging outbound - area pengiriman barang',
                    
                    // Audit fields
                    'last_count_date' => $isOccupied ? now()->subDays(rand(1, 3)) : null,
                    'last_count_by' => ($isOccupied && $adminUser) ? $adminUser->id : null,
                    'last_movement_date' => $isOccupied ? now()->subHours(rand(1, 24)) : null,
                    
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

        $this->command->info("✓ Created " . count($bins) . " storage bins for warehouse: {$warehouse->name}");
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