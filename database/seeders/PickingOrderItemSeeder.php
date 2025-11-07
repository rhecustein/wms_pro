<?php

namespace Database\Seeders;

use App\Models\PickingOrder;
use App\Models\PickingOrderItem;
use App\Models\SalesOrderItem;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PickingOrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pickingOrders = PickingOrder::with('salesOrder.items.product')->get();
        $storageBins = StorageBin::where('is_active', true)->get();
        $users = User::limit(5)->get();

        if ($pickingOrders->isEmpty()) {
            $this->command->warn('Tidak ada picking orders. Jalankan PickingOrderSeeder terlebih dahulu.');
            return;
        }

        if ($storageBins->isEmpty()) {
            $this->command->warn('Tidak ada storage bins. Jalankan StorageBinSeeder terlebih dahulu.');
            return;
        }

        foreach ($pickingOrders as $pickingOrder) {
            $salesOrderItems = $pickingOrder->salesOrder->items;
            $pickSequence = 1;

            foreach ($salesOrderItems as $soItem) {
                $product = $soItem->product;
                
                // Tentukan storage bin berdasarkan product type atau random
                $storageBin = $this->getStorageBinForProduct($product, $storageBins);
                
                if (!$storageBin) {
                    $this->command->warn("No storage bin available for product {$product->sku}, skipping...");
                    continue;
                }

                // Generate batch number untuk produk yang batch tracked
                $batchNumber = $product->is_batch_tracked ? $this->generateBatchNumber($product) : null;
                
                // Generate serial number untuk produk yang serialized
                $serialNumber = $product->is_serialized ? $this->generateSerialNumber($product, $soItem->quantity_ordered) : null;
                
                // Generate expiry date untuk consumable products
                $expiryDate = $this->getExpiryDate($product);

                // Tentukan quantity picked berdasarkan status picking order
                $quantityPicked = $this->getQuantityPicked(
                    $pickingOrder->status, 
                    $soItem->quantity_ordered
                );

                // Tentukan status item
                $itemStatus = $this->getItemStatus($pickingOrder->status, $quantityPicked, $soItem->quantity_ordered);

                // Tentukan picked_by dan picked_at
                $pickedBy = null;
                $pickedAt = null;
                
                if (in_array($itemStatus, ['picked', 'short'])) {
                    $pickedBy = $pickingOrder->assigned_to;
                    $pickedAt = $pickingOrder->completed_at ?? $pickingOrder->started_at;
                } elseif ($pickingOrder->status === 'in_progress' && $quantityPicked > 0) {
                    $pickedBy = $pickingOrder->assigned_to;
                    $pickedAt = Carbon::now()->subHours(rand(1, 3));
                }

                PickingOrderItem::create([
                    'picking_order_id' => $pickingOrder->id,
                    'sales_order_item_id' => $soItem->id,
                    'product_id' => $product->id,
                    'storage_bin_id' => $storageBin->id,
                    'batch_number' => $batchNumber,
                    'serial_number' => $serialNumber,
                    'expiry_date' => $expiryDate,
                    'quantity_requested' => $soItem->quantity_ordered,
                    'quantity_picked' => $quantityPicked,
                    'unit_of_measure' => $soItem->unit_of_measure,
                    'pick_sequence' => $pickSequence++,
                    'status' => $itemStatus,
                    'picked_by' => $pickedBy,
                    'picked_at' => $pickedAt,
                    'notes' => $this->generateItemNotes($product, $itemStatus, $quantityPicked, $soItem->quantity_ordered),
                ]);
            }
        }

        $this->command->info('✓ Created picking order items successfully for all picking orders!');
    }

    /**
     * Get appropriate storage bin for product
     */
    private function getStorageBinForProduct($product, $storageBins)
    {
        // Filter bins berdasarkan product type
        $filteredBins = $storageBins->filter(function ($bin) use ($product) {
            // Electronics products -> specific zones
            if (str_contains($product->sku, 'ELC-')) {
                return str_contains($bin->bin_location, 'A') || str_contains($bin->bin_location, 'B');
            }
            
            // Food & Beverage -> specific zones (mungkin perlu cold storage)
            if (str_contains($product->sku, 'FNB-')) {
                return str_contains($bin->bin_location, 'C') || str_contains($bin->bin_location, 'D');
            }
            
            // Raw materials -> specific zones
            if (str_contains($product->sku, 'RAW-')) {
                return str_contains($bin->bin_location, 'E') || str_contains($bin->bin_location, 'F');
            }
            
            // Office & others -> remaining zones
            return true;
        });

        return $filteredBins->isNotEmpty() ? $filteredBins->random() : $storageBins->random();
    }

    /**
     * Generate batch number
     */
    private function generateBatchNumber($product): string
    {
        $prefix = substr($product->sku, 0, 3);
        $date = Carbon::now()->format('Ymd');
        $random = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Generate serial numbers (untuk serialized products)
     */
    private function generateSerialNumber($product, $quantity): ?string
    {
        if (!$product->is_serialized) {
            return null;
        }

        // Untuk serialized products, bisa generate multiple serial numbers
        // Disini kita simplify dengan satu serial number saja
        // Dalam real implementation, bisa create separate records per serial
        $prefix = substr($product->sku, 0, 3);
        $year = Carbon::now()->format('y');
        $random = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}{$year}{$random}";
    }

    /**
     * Get expiry date for products
     */
    private function getExpiryDate($product): ?Carbon
    {
        // Hanya untuk consumable/food products
        if ($product->type !== 'consumable' && !str_contains($product->sku, 'FNB-')) {
            return null;
        }

        // Generate expiry date 6-24 bulan dari sekarang
        $monthsToAdd = rand(6, 24);
        return Carbon::now()->addMonths($monthsToAdd);
    }

    /**
     * Get quantity picked based on picking order status
     */
    private function getQuantityPicked($status, $quantityRequested): int
    {
        switch ($status) {
            case 'completed':
                // Completed orders have full quantity picked
                return $quantityRequested;
                
            case 'in_progress':
                // In progress orders have partial quantity picked (40-80%)
                $percentage = rand(40, 80) / 100;
                return (int) floor($quantityRequested * $percentage);
                
            case 'assigned':
            case 'pending':
                // Not started yet
                return 0;
                
            case 'cancelled':
                return 0;
                
            default:
                return 0;
        }
    }

    /**
     * Get item status
     */
    private function getItemStatus($pickingOrderStatus, $quantityPicked, $quantityRequested): string
    {
        if ($pickingOrderStatus === 'cancelled') {
            return 'cancelled';
        }

        if ($pickingOrderStatus === 'pending' || $pickingOrderStatus === 'assigned') {
            return 'pending';
        }

        if ($quantityPicked === 0) {
            return 'pending';
        }

        if ($quantityPicked < $quantityRequested) {
            // Short pick - tidak semua quantity bisa dipick
            return 'short';
        }

        if ($quantityPicked >= $quantityRequested) {
            return 'picked';
        }

        return 'pending';
    }

    /**
     * Generate notes for picking items
     */
    private function generateItemNotes($product, $status, $quantityPicked, $quantityRequested): ?string
    {
        $notes = [];

        // Product specific notes
        if ($product->is_serialized) {
            $notes[] = 'Serial number tracked';
        }

        if ($product->is_batch_tracked) {
            $notes[] = 'Batch number recorded';
        }

        // Status specific notes
        if ($status === 'short') {
            $shortage = $quantityRequested - $quantityPicked;
            $notes[] = "Short pick: {$shortage} units tidak tersedia di bin location";
        }

        if ($status === 'picked' && $product->type === 'consumable') {
            $notes[] = 'FEFO check done - expiry date verified';
        }

        if ($product->weight > 20) {
            $notes[] = 'Heavy item - memerlukan forklift atau team lifting';
        }

        if (str_contains($product->sku, 'ELC-')) {
            $notes[] = 'Fragile - handle with care';
        }

        return !empty($notes) ? implode('. ', $notes) : null;
    }
}