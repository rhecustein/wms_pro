<?php

namespace Database\Seeders;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\PackingOrderItem;
use App\Models\SalesOrderItem;
use Illuminate\Database\Seeder;

class DeliveryOrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deliveryOrders = DeliveryOrder::with([
            'packingOrder.items.product',
            'salesOrder.items'
        ])->get();

        if ($deliveryOrders->isEmpty()) {
            $this->command->warn('Tidak ada delivery orders. Jalankan DeliveryOrderSeeder terlebih dahulu.');
            return;
        }

        $totalItems = 0;

        foreach ($deliveryOrders as $deliveryOrder) {
            $packingOrderItems = $deliveryOrder->packingOrder->items;
            
            if ($packingOrderItems->isEmpty()) {
                $this->command->warn("No packing items for delivery order {$deliveryOrder->do_number}");
                continue;
            }

            foreach ($packingOrderItems as $packingItem) {
                // Find corresponding sales order item
                $salesOrderItem = $deliveryOrder->salesOrder->items
                    ->where('product_id', $packingItem->product_id)
                    ->first();

                if (!$salesOrderItem) {
                    $this->command->warn("Sales order item not found for product {$packingItem->product_id}");
                    continue;
                }

                // Determine quantity delivered based on DO status
                $quantityDelivered = $this->getQuantityDelivered(
                    $deliveryOrder->status,
                    $packingItem->quantity_packed
                );

                // Determine quantity returned (if any)
                $quantityReturned = $this->getQuantityReturned(
                    $deliveryOrder->status,
                    $quantityDelivered
                );

                // Determine condition
                $condition = $this->getCondition($deliveryOrder->status, $quantityReturned);

                DeliveryOrderItem::create([
                    'delivery_order_id' => $deliveryOrder->id,
                    'sales_order_item_id' => $salesOrderItem->id,
                    'product_id' => $packingItem->product_id,
                    'batch_number' => $packingItem->batch_number,
                    'serial_number' => $packingItem->serial_number,
                    'quantity_delivered' => $quantityDelivered,
                    'quantity_returned' => $quantityReturned,
                    'unit_of_measure' => $salesOrderItem->unit_of_measure,
                    'condition' => $condition,
                    'notes' => $this->generateItemNotes($deliveryOrder->status, $condition, $quantityReturned),
                ]);

                $totalItems++;
            }
        }

        $this->command->info("✓ Created {$totalItems} delivery order items successfully!");
    }

    /**
     * Get quantity delivered based on DO status
     */
    private function getQuantityDelivered($status, $quantityPacked): int
    {
        switch ($status) {
            case 'delivered':
            case 'in_transit':
            case 'loaded':
                // All packed quantity is delivered
                return $quantityPacked;
                
            case 'prepared':
                // Items prepared but not yet loaded
                return $quantityPacked;
                
            case 'returned':
                // Items were delivered but returned
                return $quantityPacked;
                
            case 'cancelled':
                return 0;
                
            default:
                return $quantityPacked;
        }
    }

    /**
     * Get quantity returned based on DO status
     */
    private function getQuantityReturned($status, $quantityDelivered): int
    {
        if ($status === 'returned') {
            // Simulate partial or full return (10-100% of delivered)
            $returnPercentage = rand(10, 100) / 100;
            return (int) ceil($quantityDelivered * $returnPercentage);
        }

        // Randomly add some returns to delivered orders (5% chance, 1-10% of quantity)
        if ($status === 'delivered' && rand(1, 20) === 1) {
            $returnPercentage = rand(1, 10) / 100;
            return (int) ceil($quantityDelivered * $returnPercentage);
        }

        return 0;
    }

    /**
     * Get condition based on status and returns
     */
    private function getCondition($status, $quantityReturned): string
    {
        if ($status === 'returned' || $quantityReturned > 0) {
            // Randomly determine if returned items are damaged or good
            return rand(1, 3) === 1 ? 'damaged' : 'returned';
        }

        // Small chance of damaged goods even in successful delivery (2% chance)
        if ($status === 'delivered' && rand(1, 50) === 1) {
            return 'damaged';
        }

        return 'good';
    }

    /**
     * Generate item notes based on status and condition
     */
    private function generateItemNotes($status, $condition, $quantityReturned): ?string
    {
        $notes = [];

        // Condition specific notes
        if ($condition === 'damaged') {
            $notes[] = 'Item diterima dalam kondisi rusak';
            $notes[] = 'Perlu claim asuransi';
        } elseif ($condition === 'returned') {
            $notes[] = 'Item dikembalikan oleh customer';
            if ($quantityReturned > 0) {
                $notes[] = "Qty returned: {$quantityReturned}";
            }
        }

        // Status specific notes
        switch ($status) {
            case 'delivered':
                if ($condition === 'good') {
                    $notes[] = 'Item delivered in good condition';
                }
                break;
            case 'in_transit':
                $notes[] = 'Item in transit to customer';
                break;
            case 'loaded':
                $notes[] = 'Item loaded on vehicle';
                break;
            case 'prepared':
                $notes[] = 'Item prepared for delivery';
                break;
        }

        return !empty($notes) ? implode('. ', $notes) : null;
    }
}