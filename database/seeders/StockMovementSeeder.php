<?php

namespace Database\Seeders;

use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\User;
use App\Models\InventoryStock;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data yang dibutuhkan
        $warehouses = Warehouse::all();
        $products = Product::all();
        $storageBins = StorageBin::all();
        $users = User::all();
        $inventoryStocks = InventoryStock::all();

        // Pastikan ada data yang dibutuhkan
        if ($warehouses->isEmpty() || $products->isEmpty() || $storageBins->isEmpty()) {
            $this->command->warn('Pastikan seeder Warehouse, Product, dan StorageBin sudah dijalankan terlebih dahulu!');
            return;
        }

        $movements = [];
        $movementTypes = ['inbound', 'outbound', 'transfer', 'adjustment', 'putaway', 'picking', 'replenishment'];
        $referenceTypes = ['purchase_order', 'sales_order', 'transfer', 'adjustment'];
        $units = ['PCS', 'BOX', 'CARTON', 'PALLET', 'KG', 'LITER'];

        // Generate movements untuk 6 bulan terakhir
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        foreach ($warehouses as $warehouse) {
            $warehouseBins = $storageBins->where('warehouse_id', $warehouse->id);
            
            if ($warehouseBins->isEmpty()) {
                continue;
            }

            // Generate 30-50 movements per warehouse
            $movementCount = rand(30, 50);
            
            for ($i = 0; $i < $movementCount; $i++) {
                $product = $products->random();
                $movementType = $movementTypes[array_rand($movementTypes)];
                $movementDate = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                );
                
                // Tentukan bins berdasarkan movement type
                $fromBin = null;
                $toBin = null;
                
                switch ($movementType) {
                    case 'inbound':
                    case 'putaway':
                        $toBin = $warehouseBins->random()->id;
                        break;
                        
                    case 'outbound':
                    case 'picking':
                        $fromBin = $warehouseBins->random()->id;
                        break;
                        
                    case 'transfer':
                    case 'replenishment':
                        $fromBin = $warehouseBins->random()->id;
                        $toBin = $warehouseBins->where('id', '!=', $fromBin)->random()->id ?? null;
                        break;
                        
                    case 'adjustment':
                        $toBin = $warehouseBins->random()->id;
                        break;
                }

                // Tentukan reference type dan reference number
                $referenceType = $this->getReferenceType($movementType);
                $referenceNumber = $referenceType ? $this->generateReferenceNumber($referenceType) : null;
                
                $movement = [
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $product->id,
                    'from_bin_id' => $fromBin,
                    'to_bin_id' => $toBin,
                    'batch_number' => rand(0, 10) > 3 ? 'BATCH-' . strtoupper(substr(md5(uniqid()), 0, 8)) : null,
                    'serial_number' => rand(0, 10) > 8 ? 'SN-' . strtoupper(substr(md5(uniqid()), 0, 10)) : null,
                    'quantity' => $this->getQuantityByType($movementType),
                    'unit_of_measure' => $units[array_rand($units)],
                    'movement_type' => $movementType,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceType ? rand(1, 1000) : null,
                    'reference_number' => $referenceNumber,
                    'movement_date' => $movementDate,
                    'performed_by' => $users->isNotEmpty() ? $users->random()->id : null,
                    'notes' => $this->generateNotes($movementType),
                    'created_at' => $movementDate,
                ];

                $movements[] = $movement;
            }
        }

        // Tambahkan movements khusus untuk testing berbagai skenario
        if ($warehouses->count() > 0 && $products->count() > 0) {
            $mainWarehouse = $warehouses->first();
            $mainBins = $storageBins->where('warehouse_id', $mainWarehouse->id);
            
            if ($mainBins->count() >= 2) {
                $product = $products->first();
                $user = $users->isNotEmpty() ? $users->first()->id : null;
                $now = Carbon::now();

                // 1. Inbound Receipt dari Purchase Order
                $movements[] = [
                    'warehouse_id' => $mainWarehouse->id,
                    'product_id' => $product->id,
                    'from_bin_id' => null,
                    'to_bin_id' => $mainBins->first()->id,
                    'batch_number' => 'BATCH-PO-12345',
                    'serial_number' => null,
                    'quantity' => 1000,
                    'unit_of_measure' => 'PCS',
                    'movement_type' => 'inbound',
                    'reference_type' => 'purchase_order',
                    'reference_id' => 12345,
                    'reference_number' => 'PO-2024-12345',
                    'movement_date' => $now->copy()->subDays(30),
                    'performed_by' => $user,
                    'notes' => 'Received from vendor - Quality checked and approved',
                    'created_at' => $now->copy()->subDays(30),
                ];

                // 2. Putaway dari receiving area ke storage
                $movements[] = [
                    'warehouse_id' => $mainWarehouse->id,
                    'product_id' => $product->id,
                    'from_bin_id' => $mainBins->first()->id,
                    'to_bin_id' => $mainBins->skip(1)->first()->id,
                    'batch_number' => 'BATCH-PO-12345',
                    'serial_number' => null,
                    'quantity' => 1000,
                    'unit_of_measure' => 'PCS',
                    'movement_type' => 'putaway',
                    'reference_type' => 'purchase_order',
                    'reference_id' => 12345,
                    'reference_number' => 'PO-2024-12345',
                    'movement_date' => $now->copy()->subDays(29),
                    'performed_by' => $user,
                    'notes' => 'Moved to high rack storage location',
                    'created_at' => $now->copy()->subDays(29),
                ];

                // 3. Picking untuk Sales Order
                $movements[] = [
                    'warehouse_id' => $mainWarehouse->id,
                    'product_id' => $product->id,
                    'from_bin_id' => $mainBins->skip(1)->first()->id,
                    'to_bin_id' => null,
                    'batch_number' => 'BATCH-PO-12345',
                    'serial_number' => null,
                    'quantity' => 250,
                    'unit_of_measure' => 'PCS',
                    'movement_type' => 'picking',
                    'reference_type' => 'sales_order',
                    'reference_id' => 54321,
                    'reference_number' => 'SO-2024-54321',
                    'movement_date' => $now->copy()->subDays(15),
                    'performed_by' => $user,
                    'notes' => 'Picked for customer order - Priority shipment',
                    'created_at' => $now->copy()->subDays(15),
                ];

                // 4. Outbound Shipment
                $movements[] = [
                    'warehouse_id' => $mainWarehouse->id,
                    'product_id' => $product->id,
                    'from_bin_id' => $mainBins->first()->id,
                    'to_bin_id' => null,
                    'batch_number' => 'BATCH-PO-12345',
                    'serial_number' => null,
                    'quantity' => 250,
                    'unit_of_measure' => 'PCS',
                    'movement_type' => 'outbound',
                    'reference_type' => 'sales_order',
                    'reference_id' => 54321,
                    'reference_number' => 'SO-2024-54321',
                    'movement_date' => $now->copy()->subDays(14),
                    'performed_by' => $user,
                    'notes' => 'Shipped to customer - Tracking: TRK123456789',
                    'created_at' => $now->copy()->subDays(14),
                ];

                // 5. Replenishment dari high rack ke pick face
                $movements[] = [
                    'warehouse_id' => $mainWarehouse->id,
                    'product_id' => $product->id,
                    'from_bin_id' => $mainBins->skip(1)->first()->id,
                    'to_bin_id' => $mainBins->first()->id,
                    'batch_number' => 'BATCH-PO-12345',
                    'serial_number' => null,
                    'quantity' => 100,
                    'unit_of_measure' => 'PCS',
                    'movement_type' => 'replenishment',
                    'reference_type' => null,
                    'reference_id' => null,
                    'reference_number' => 'REP-' . date('Ymd') . '-001',
                    'movement_date' => $now->copy()->subDays(10),
                    'performed_by' => $user,
                    'notes' => 'Automatic replenishment - Pick face below minimum',
                    'created_at' => $now->copy()->subDays(10),
                ];

                // 6. Stock Adjustment (positive)
                $movements[] = [
                    'warehouse_id' => $mainWarehouse->id,
                    'product_id' => $product->id,
                    'from_bin_id' => null,
                    'to_bin_id' => $mainBins->skip(1)->first()->id,
                    'batch_number' => 'BATCH-PO-12345',
                    'serial_number' => null,
                    'quantity' => 50,
                    'unit_of_measure' => 'PCS',
                    'movement_type' => 'adjustment',
                    'reference_type' => 'adjustment',
                    'reference_id' => 101,
                    'reference_number' => 'ADJ-2024-101',
                    'movement_date' => $now->copy()->subDays(5),
                    'performed_by' => $user,
                    'notes' => 'Stock count adjustment - Found additional units during cycle count',
                    'created_at' => $now->copy()->subDays(5),
                ];

                // 7. Stock Adjustment (negative)
                $movements[] = [
                    'warehouse_id' => $mainWarehouse->id,
                    'product_id' => $product->id,
                    'from_bin_id' => $mainBins->skip(1)->first()->id,
                    'to_bin_id' => null,
                    'batch_number' => 'BATCH-PO-12345',
                    'serial_number' => null,
                    'quantity' => 25,
                    'unit_of_measure' => 'PCS',
                    'movement_type' => 'adjustment',
                    'reference_type' => 'adjustment',
                    'reference_id' => 102,
                    'reference_number' => 'ADJ-2024-102',
                    'movement_date' => $now->copy()->subDays(3),
                    'performed_by' => $user,
                    'notes' => 'Damaged items removed from inventory',
                    'created_at' => $now->copy()->subDays(3),
                ];

                // 8. Transfer antar warehouse (jika ada lebih dari 1 warehouse)
                if ($warehouses->count() > 1) {
                    $targetWarehouse = $warehouses->skip(1)->first();
                    $targetBins = $storageBins->where('warehouse_id', $targetWarehouse->id);
                    
                    if ($targetBins->isNotEmpty()) {
                        $movements[] = [
                            'warehouse_id' => $mainWarehouse->id,
                            'product_id' => $product->id,
                            'from_bin_id' => $mainBins->skip(1)->first()->id,
                            'to_bin_id' => null,
                            'batch_number' => 'BATCH-PO-12345',
                            'serial_number' => null,
                            'quantity' => 200,
                            'unit_of_measure' => 'PCS',
                            'movement_type' => 'transfer',
                            'reference_type' => 'transfer',
                            'reference_id' => 999,
                            'reference_number' => 'TRF-2024-999',
                            'movement_date' => $now->copy()->subDays(7),
                            'performed_by' => $user,
                            'notes' => "Transfer to {$targetWarehouse->name} - Stock balancing",
                            'created_at' => $now->copy()->subDays(7),
                        ];
                    }
                }
            }
        }

        // Insert semua data
        foreach ($movements as $movement) {
            StockMovement::create($movement);
        }

        $this->command->info('Stock Movement seeder berhasil dijalankan! Total: ' . count($movements) . ' records');
    }

    /**
     * Get reference type based on movement type
     */
    private function getReferenceType(string $movementType): ?string
    {
        return match($movementType) {
            'inbound', 'putaway' => rand(0, 10) > 3 ? 'purchase_order' : null,
            'outbound', 'picking' => rand(0, 10) > 3 ? 'sales_order' : null,
            'transfer' => 'transfer',
            'adjustment' => 'adjustment',
            'replenishment' => null,
            default => null
        };
    }

    /**
     * Generate reference number based on type
     */
    private function generateReferenceNumber(string $referenceType): string
    {
        $prefix = match($referenceType) {
            'purchase_order' => 'PO',
            'sales_order' => 'SO',
            'transfer' => 'TRF',
            'adjustment' => 'ADJ',
            default => 'REF'
        };

        return $prefix . '-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get quantity based on movement type
     */
    private function getQuantityByType(string $movementType): int
    {
        return match($movementType) {
            'inbound' => rand(100, 1000),
            'outbound' => rand(50, 500),
            'transfer' => rand(100, 500),
            'adjustment' => rand(-100, 100), // bisa negative untuk adjustment
            'putaway' => rand(100, 1000),
            'picking' => rand(10, 200),
            'replenishment' => rand(50, 200),
            default => rand(10, 100)
        };
    }

    /**
     * Generate notes based on movement type
     */
    private function generateNotes(string $movementType): ?string
    {
        $notes = match($movementType) {
            'inbound' => [
                'Received from vendor - Quality inspection passed',
                'Goods receipt completed successfully',
                'Received and verified against PO',
                'Delivery received in good condition',
            ],
            'outbound' => [
                'Shipped to customer via express courier',
                'Order fulfilled and dispatched',
                'Loaded for delivery',
                'Shipped - tracking number provided to customer',
            ],
            'transfer' => [
                'Inter-warehouse transfer for stock balancing',
                'Transferred to optimize inventory distribution',
                'Stock relocation for demand fulfillment',
            ],
            'adjustment' => [
                'Cycle count adjustment',
                'Physical inventory correction',
                'System reconciliation adjustment',
                'Damaged goods write-off',
            ],
            'putaway' => [
                'Stored in designated location',
                'Put away to storage bin',
                'Moved to optimal storage position',
            ],
            'picking' => [
                'Picked for order fulfillment',
                'Items selected for shipment preparation',
                'Order picking completed',
            ],
            'replenishment' => [
                'Replenished from reserve location',
                'Pick face restocked',
                'Automatic replenishment triggered',
            ],
            default => null
        };

        return $notes ? $notes[array_rand($notes)] : null;
    }
}