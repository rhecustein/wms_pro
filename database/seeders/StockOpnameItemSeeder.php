<?php

namespace Database\Seeders;

use App\Models\StockOpnameItem;
use App\Models\StockOpname;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\InventoryStock;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockOpnameItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opnames = StockOpname::all();
        $products = Product::all();
        $storageBins = StorageBin::all();
        $inventoryStocks = InventoryStock::all();
        $users = User::all();

        if ($opnames->isEmpty() || $products->isEmpty() || $storageBins->isEmpty()) {
            $this->command->warn('Pastikan seeder StockOpname, Product, dan StorageBin sudah dijalankan terlebih dahulu!');
            return;
        }

        $totalItems = 0;

        foreach ($opnames as $opname) {
            $warehouseBins = $storageBins->where('warehouse_id', $opname->warehouse_id);
            
            if ($warehouseBins->isEmpty()) {
                continue;
            }

            // Filter bins by storage area if specified
            if ($opname->storage_area_id) {
                $warehouseBins = $warehouseBins->where('storage_area_id', $opname->storage_area_id);
            }

            if ($warehouseBins->isEmpty()) {
                continue;
            }

            $itemCount = $opname->total_items_planned;
            $items = [];

            // Calculate how many items should be counted based on status
            $countedItemsCount = match($opname->status) {
                'planned', 'cancelled' => 0,
                'in_progress' => (int)($itemCount * rand(30, 80) / 100),
                'completed' => $itemCount,
                default => 0
            };

            for ($i = 0; $i < $itemCount; $i++) {
                $product = $products->random();
                $storageBin = $warehouseBins->random();
                
                // Get system quantity from inventory or generate
                $systemQuantity = $this->getSystemQuantity($opname, $inventoryStocks);
                
                // Determine if this item should be counted
                $isCounted = $i < $countedItemsCount;
                
                // Calculate physical quantity and variance
                $physicalQuantity = null;
                $variance = null;
                $varianceValue = null;
                $status = 'pending';
                $countedBy = null;
                $countedAt = null;

                if ($isCounted) {
                    $physicalQuantity = $this->getPhysicalQuantity($systemQuantity, $opname->opname_type);
                    $variance = $physicalQuantity - $systemQuantity;
                    
                    // Estimate value (using random cost per unit)
                    $costPerUnit = rand(10000, 100000) / 100;
                    $varianceValue = abs($variance) * $costPerUnit;
                    
                    $status = $this->getItemStatus($variance, $opname->status);
                    $countedBy = $users->isNotEmpty() ? $users->random()->id : null;
                    
                    // Set counted_at based on opname progress
                    if ($opname->started_at) {
                        $countedAt = Carbon::parse($opname->started_at)
                            ->addMinutes(rand(10, 300));
                    }
                }

                $item = [
                    'stock_opname_id' => $opname->id,
                    'product_id' => $product->id,
                    'storage_bin_id' => $storageBin->id,
                    'batch_number' => rand(0, 10) > 4 
                        ? 'BATCH-' . strtoupper(substr(md5(uniqid()), 0, 8)) 
                        : null,
                    'serial_number' => rand(0, 10) > 8 
                        ? 'SN-' . strtoupper(substr(md5(uniqid()), 0, 10)) 
                        : null,
                    'system_quantity' => $systemQuantity,
                    'physical_quantity' => $physicalQuantity,
                    'variance' => $variance,
                    'variance_value' => $varianceValue,
                    'status' => $status,
                    'counted_by' => $countedBy,
                    'counted_at' => $countedAt,
                    'notes' => $this->generateItemNotes($variance, $status, $systemQuantity, $physicalQuantity),
                    'created_at' => $opname->created_at,
                    'updated_at' => $countedAt ?? $opname->updated_at,
                ];

                $items[] = $item;
            }

            // Insert items
            foreach ($items as $item) {
                StockOpnameItem::create($item);
            }

            // Update opname statistics
            $this->updateOpnameStatistics($opname, $items);
            $totalItems += count($items);
        }

        $this->command->info('Stock Opname Item seeder berhasil dijalankan! Total: ' . $totalItems . ' records');
    }

    /**
     * Get system quantity from inventory or generate
     */
    private function getSystemQuantity(StockOpname $opname, $inventoryStocks): int
    {
        // Try to get from actual inventory
        $stock = $inventoryStocks
            ->where('warehouse_id', $opname->warehouse_id)
            ->where('status', 'available')
            ->first();

        if ($stock) {
            return rand(10, $stock->quantity);
        }

        // Generate random system quantity
        return match($opname->opname_type) {
            'full' => rand(50, 500),
            'cycle' => rand(20, 300),
            'spot' => rand(10, 100),
            default => rand(20, 200)
        };
    }

    /**
     * Get physical quantity (may have variance)
     */
    private function getPhysicalQuantity(int $systemQuantity, string $opnameType): int
    {
        // Determine variance probability based on opname type
        $varianceProbability = match($opnameType) {
            'full' => 15, // 15% chance of variance
            'cycle' => 10, // 10% chance of variance
            'spot' => 25, // 25% chance of variance (triggered by discrepancy)
            default => 10
        };

        // Should this item have variance?
        if (rand(1, 100) <= $varianceProbability) {
            // Generate variance (±1% to ±10% of system quantity)
            $variancePercent = rand(1, 10) / 100;
            $maxVariance = max(1, (int)($systemQuantity * $variancePercent));
            $variance = rand(-$maxVariance, $maxVariance);
            
            return max(0, $systemQuantity + $variance);
        }

        // No variance - match system quantity
        return $systemQuantity;
    }

    /**
     * Get item status based on variance
     */
    private function getItemStatus(?int $variance, string $opnameStatus): string
    {
        if ($opnameStatus === 'planned') {
            return 'pending';
        }

        if ($variance === null) {
            return 'pending';
        }

        if ($variance === 0) {
            return 'counted';
        }

        // Has variance
        $absVariance = abs($variance);
        
        // Large variance may require recount
        if ($absVariance > 10 && rand(0, 10) > 6) {
            return 'recounted';
        }

        // Completed opnames with variance should be adjusted
        if ($opnameStatus === 'completed') {
            return 'adjusted';
        }

        return 'counted';
    }

    /**
     * Generate item notes
     */
    private function generateItemNotes(?int $variance, string $status, int $system, ?int $physical): ?string
    {
        if ($status === 'pending') {
            return null;
        }

        if ($variance === null || $physical === null) {
            return 'Awaiting physical count';
        }

        if ($variance === 0) {
            return 'Count verified - No discrepancy';
        }

        $diffText = $variance > 0 ? "+$variance" : "$variance";
        
        return match($status) {
            'counted' => "Variance detected: $diffText units (System: $system, Physical: $physical)",
            'recounted' => "Recounted due to significant variance: $diffText units - Recount confirmed",
            'adjusted' => "Stock adjusted: $diffText units - Adjustment posted to inventory (System: $system → Physical: $physical)",
            default => "Variance: $diffText units"
        };
    }

    /**
     * Update opname statistics based on items
     */
    private function updateOpnameStatistics(StockOpname $opname, array $items): void
    {
        $counted = collect($items)->where('physical_quantity', '!=', null);
        $withVariance = $counted->where('variance', '!=', 0);

        $totalCounted = $counted->count();
        $varianceCount = $withVariance->count();
        
        $accuracy = $totalCounted > 0 
            ? round((($totalCounted - $varianceCount) / $totalCounted) * 100, 2)
            : 0;

        $opname->update([
            'total_items_counted' => $totalCounted,
            'variance_count' => $varianceCount,
            'accuracy_percentage' => $accuracy,
        ]);
    }
}