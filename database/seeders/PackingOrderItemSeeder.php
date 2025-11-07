<?php

namespace Database\Seeders;

use App\Models\PackingOrder;
use App\Models\PackingOrderItem;
use App\Models\PickingOrderItem;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PackingOrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packingOrders = PackingOrder::with([
            'pickingOrder.items.product'
        ])->get();

        if ($packingOrders->isEmpty()) {
            $this->command->warn('Tidak ada packing orders. Jalankan PackingOrderSeeder terlebih dahulu.');
            return;
        }

        foreach ($packingOrders as $packingOrder) {
            $pickingOrderItems = $packingOrder->pickingOrder->items;
            
            if ($pickingOrderItems->isEmpty()) {
                $this->command->warn("No picking items for packing order {$packingOrder->packing_number}");
                continue;
            }

            // Distribute items ke boxes
            $totalBoxes = $packingOrder->total_boxes;
            $currentBox = 1;

            foreach ($pickingOrderItems as $pickingItem) {
                $product = $pickingItem->product;
                
                // Determine box number
                $boxNumber = 'BOX-' . str_pad($currentBox, 3, '0', STR_PAD_LEFT);
                
                // Tentukan quantity packed berdasarkan status packing order
                $quantityPacked = $this->getQuantityPacked(
                    $packingOrder->status,
                    $pickingItem->quantity_picked
                );

                // Calculate box weight untuk item ini
                $boxWeight = ($product->weight ?? 1) * $quantityPacked;

                // Determine packed_by and packed_at
                $packedBy = null;
                $packedAt = null;
                
                if ($packingOrder->status === 'completed') {
                    $packedBy = $packingOrder->assigned_to;
                    $packedAt = $packingOrder->completed_at;
                } elseif ($packingOrder->status === 'in_progress' && $quantityPacked > 0) {
                    $packedBy = $packingOrder->assigned_to;
                    $packedAt = Carbon::now()->subHours(rand(1, 3));
                }

                PackingOrderItem::create([
                    'packing_order_id' => $packingOrder->id,
                    'picking_order_item_id' => $pickingItem->id,
                    'product_id' => $product->id,
                    'batch_number' => $pickingItem->batch_number,
                    'serial_number' => $pickingItem->serial_number,
                    'quantity_packed' => $quantityPacked,
                    'box_number' => $boxNumber,
                    'box_weight_kg' => round($boxWeight, 2),
                    'packed_by' => $packedBy,
                    'packed_at' => $packedAt,
                ]);

                // Move to next box after every 2 items (simplified logic)
                if (($pickingItem->id % 2) == 0 && $currentBox < $totalBoxes) {
                    $currentBox++;
                }
            }
        }

        $this->command->info('✓ Created packing order items successfully!');
    }

    /**
     * Get quantity packed based on packing order status
     */
    private function getQuantityPacked($status, $quantityPicked): int
    {
        switch ($status) {
            case 'completed':
                // All picked quantity is packed
                return $quantityPicked;
                
            case 'in_progress':
                // Partially packed (60-80%)
                $percentage = rand(60, 80) / 100;
                return (int) floor($quantityPicked * $percentage);
                
            case 'pending':
                return 0;
                
            case 'cancelled':
                return 0;
                
            default:
                return 0;
        }
    }
}