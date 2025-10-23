<?php

namespace Database\Seeders;

use App\Models\StockAdjustmentItem;
use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\InventoryStock;
use Illuminate\Database\Seeder;

class StockAdjustmentItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adjustments = StockAdjustment::all();
        $products = Product::all();
        $storageBins = StorageBin::all();
        $inventoryStocks = InventoryStock::all();

        if ($adjustments->isEmpty() || $products->isEmpty() || $storageBins->isEmpty()) {
            $this->command->warn('Pastikan seeder StockAdjustment, Product, dan StorageBin sudah dijalankan terlebih dahulu!');
            return;
        }

        $units = ['PCS', 'BOX', 'CARTON', 'PALLET', 'KG', 'LITER'];
        $totalItems = 0;

        foreach ($adjustments as $adjustment) {
            $warehouseBins = $storageBins->where('warehouse_id', $adjustment->warehouse_id);
            
            if ($warehouseBins->isEmpty()) {
                continue;
            }

            // Tentukan jumlah items berdasarkan status dan reason
            $itemCount = $this->getItemCount($adjustment->status, $adjustment->reason);
            $items = [];

            for ($i = 0; $i < $itemCount; $i++) {
                $product = $products->random();
                $storageBin = $warehouseBins->random();
                
                // Cari inventory stock yang sesuai atau buat data dummy
                $currentQuantity = $this->getCurrentQuantity($adjustment, $inventoryStocks);
                $adjustedQuantity = $this->getAdjustedQuantity(
                    $currentQuantity,
                    $adjustment->adjustment_type,
                    $adjustment->reason
                );

                $item = [
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $product->id,
                    'storage_bin_id' => $storageBin->id,
                    'batch_number' => rand(0, 10) > 4 ? 'BATCH-' . strtoupper(substr(md5(uniqid()), 0, 8)) : null,
                    'serial_number' => rand(0, 10) > 8 ? 'SN-' . strtoupper(substr(md5(uniqid()), 0, 10)) : null,
                    'current_quantity' => $currentQuantity,
                    'adjusted_quantity' => $adjustedQuantity,
                    'unit_of_measure' => $units[array_rand($units)],
                    'reason' => $this->getItemReason($adjustment->reason),
                    'notes' => $this->generateItemNotes($adjustment->reason, $currentQuantity, $adjustedQuantity),
                    'created_at' => $adjustment->created_at,
                    'updated_at' => $adjustment->updated_at,
                ];

                $items[] = $item;
            }

            // Insert items
            foreach ($items as $item) {
                StockAdjustmentItem::create($item);
            }

            // Update total_items di adjustment
            $adjustment->update(['total_items' => count($items)]);
            $totalItems += count($items);
        }

        $this->command->info('Stock Adjustment Item seeder berhasil dijalankan! Total: ' . $totalItems . ' records');
    }

    /**
     * Get item count based on status and reason
     */
    private function getItemCount(string $status, string $reason): int
    {
        if ($status === 'cancelled') {
            return rand(1, 3);
        }

        return match($reason) {
            'damaged' => rand(1, 5),
            'expired' => rand(2, 8),
            'lost' => rand(1, 6),
            'found' => rand(1, 4),
            'count_correction' => rand(3, 15),
            default => rand(2, 10)
        };
    }

    /**
     * Get current quantity for inventory
     */
    private function getCurrentQuantity(StockAdjustment $adjustment, $inventoryStocks): int
    {
        // Coba ambil dari inventory stock yang ada
        $stock = $inventoryStocks
            ->where('warehouse_id', $adjustment->warehouse_id)
            ->first();

        if ($stock) {
            return rand(50, $stock->quantity);
        }

        // Jika tidak ada, generate random
        return match($adjustment->adjustment_type) {
            'addition' => rand(0, 100),
            'reduction' => rand(100, 500),
            'correction' => rand(50, 500),
            default => rand(50, 300)
        };
    }

    /**
     * Get adjusted quantity based on adjustment type
     */
    private function getAdjustedQuantity(int $currentQuantity, string $adjustmentType, string $reason): int
    {
        return match($adjustmentType) {
            'addition' => $currentQuantity + $this->getVariance($reason, 'addition'),
            'reduction' => max(0, $currentQuantity - $this->getVariance($reason, 'reduction')),
            'correction' => $this->getCorrectionQuantity($currentQuantity, $reason),
            default => $currentQuantity
        };
    }

    /**
     * Get variance amount for adjustment
     */
    private function getVariance(string $reason, string $type): int
    {
        if ($type === 'addition') {
            return match($reason) {
                'found' => rand(10, 100),
                'count_correction' => rand(1, 50),
                default => rand(5, 50)
            };
        }

        // reduction
        return match($reason) {
            'damaged' => rand(5, 50),
            'expired' => rand(10, 100),
            'lost' => rand(5, 75),
            'count_correction' => rand(1, 50),
            default => rand(5, 50)
        };
    }

    /**
     * Get correction quantity (can be higher or lower)
     */
    private function getCorrectionQuantity(int $currentQuantity, string $reason): int
    {
        if ($reason !== 'count_correction') {
            return $currentQuantity;
        }

        // 50% chance of increase or decrease
        $isIncrease = rand(0, 1);
        $variance = rand(1, (int)($currentQuantity * 0.1)); // Max 10% variance

        return $isIncrease 
            ? $currentQuantity + $variance 
            : max(0, $currentQuantity - $variance);
    }

    /**
     * Get item-specific reason
     */
    private function getItemReason(string $adjustmentReason): ?string
    {
        return match($adjustmentReason) {
            'damaged' => ['Physical damage', 'Water damage', 'Impact damage', 'Packaging damaged'][rand(0, 3)],
            'expired' => ['Past expiry date', 'Near expiry - removed', 'Expired batch'][rand(0, 2)],
            'lost' => ['Not found during count', 'Missing from location', 'Unable to locate'][rand(0, 2)],
            'found' => ['Found in wrong location', 'Discovered during audit', 'Located after search'][rand(0, 2)],
            'count_correction' => ['Physical count variance', 'System discrepancy', 'Cycle count adjustment'][rand(0, 2)],
            default => null
        };
    }

    /**
     * Generate item notes
     */
    private function generateItemNotes(string $reason, int $current, int $adjusted): ?string
    {
        $difference = $adjusted - $current;
        $diffText = $difference > 0 ? "+$difference" : "$difference";

        return match($reason) {
            'damaged' => "Variance: $diffText units - Damaged and removed from saleable inventory",
            'expired' => "Variance: $diffText units - Expired products segregated for disposal",
            'lost' => "Variance: $diffText units - Items unaccounted for after thorough search",
            'found' => "Variance: $diffText units - Items located and added back to inventory",
            'count_correction' => "Variance: $diffText units - Physical count correction applied",
            default => "Adjustment: $diffText units"
        };
    }
}