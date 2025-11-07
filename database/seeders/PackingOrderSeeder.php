<?php

namespace Database\Seeders;

use App\Models\PackingOrder;
use App\Models\PickingOrder;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PackingOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $createdBy = $adminUser ? $adminUser->id : null;

        // Ambil users untuk assignment
        $users = User::limit(5)->get();
        
        // Ambil picking orders yang sudah completed (siap untuk packing)
        $pickingOrders = PickingOrder::with('salesOrder')
            ->whereIn('status', ['completed'])
            ->get();

        if ($pickingOrders->isEmpty()) {
            $this->command->warn('Tidak ada picking orders yang completed. Jalankan PickingOrderSeeder terlebih dahulu.');
            return;
        }

        $packingOrders = [];

        // ========================================
        // COMPLETED PACKING ORDERS (untuk delivered/shipped sales orders)
        // ========================================
        
        foreach ($pickingOrders as $index => $pickingOrder) {
            $salesOrder = $pickingOrder->salesOrder;
            
            // Skip jika sales order belum delivered atau shipped
            if (!in_array($salesOrder->status, ['delivered', 'shipped', 'packing'])) {
                continue;
            }

            $packingNumber = 'PACK-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT);
            
            // Tentukan status berdasarkan sales order status
            $status = 'completed';
            $startedAt = $pickingOrder->completed_at?->addHours(1);
            $completedAt = $startedAt?->addHours(rand(2, 4));
            
            if ($salesOrder->status === 'packing') {
                $status = 'in_progress';
                $startedAt = Carbon::now()->subHours(rand(2, 6));
                $completedAt = null;
            }

            // Hitung total boxes berdasarkan jumlah items (simplified)
            $totalItems = $salesOrder->items->count();
            $totalBoxes = (int) ceil($totalItems / 2); // Assume 2 items per box avg

            // Hitung total weight (simplified dari products)
            $totalWeight = $salesOrder->items->sum(function ($item) {
                return ($item->product->weight ?? 1) * $item->quantity_ordered;
            });

            $packingOrders[] = [
                'packing_number' => $packingNumber,
                'picking_order_id' => $pickingOrder->id,
                'sales_order_id' => $salesOrder->id,
                'warehouse_id' => $salesOrder->warehouse_id,
                'packing_date' => $pickingOrder->completed_at ?? Carbon::now()->subDays(rand(1, 10)),
                'status' => $status,
                'assigned_to' => $users->random()->id ?? null,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'total_boxes' => $totalBoxes,
                'total_weight_kg' => round($totalWeight, 2),
                'notes' => $this->generateNotes($salesOrder, $status),
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => $pickingOrder->completed_at ?? Carbon::now()->subDays(rand(1, 10)),
                'updated_at' => $completedAt ?? Carbon::now()->subHours(rand(1, 3)),
            ];
        }

        foreach ($packingOrders as $order) {
            PackingOrder::create($order);
        }

        $this->command->info('✓ Created ' . count($packingOrders) . ' packing orders successfully!');
    }

    /**
     * Generate notes based on sales order and status
     */
    private function generateNotes($salesOrder, $status): ?string
    {
        $notes = [];

        // Status specific notes
        if ($status === 'completed') {
            $notes[] = 'Packing selesai dan QC passed';
        } elseif ($status === 'in_progress') {
            $notes[] = 'Sedang proses packing';
        }

        // Customer type notes
        if ($salesOrder->customer->customer_type === 'vip') {
            $notes[] = 'VIP customer - extra care packaging';
        }

        // Add SO number reference
        $notes[] = "SO: {$salesOrder->so_number}";

        return !empty($notes) ? implode('. ', $notes) : null;
    }
}