<?php

namespace Database\Seeders;

use App\Models\DeliveryOrder;
use App\Models\PackingOrder;
use App\Models\SalesOrder;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DeliveryOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $createdBy = $adminUser ? $adminUser->id : null;

        // Ambil data yang diperlukan
        $packingOrders = PackingOrder::with(['salesOrder.customer', 'warehouse'])
            ->where('status', 'completed')
            ->get();

        $vehicles = Vehicle::where('status', '!=', 'maintenance')->get();
        $drivers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Driver');
        })->get();

        if ($packingOrders->isEmpty()) {
            $this->command->warn('Tidak ada packing orders yang completed. Jalankan PackingOrderSeeder terlebih dahulu.');
            return;
        }

        if ($vehicles->isEmpty()) {
            $this->command->warn('Tidak ada vehicles available. Jalankan VehicleSeeder terlebih dahulu.');
            return;
        }

        $deliveryOrders = [];
        $doCounter = 1;

        foreach ($packingOrders as $packingOrder) {
            $salesOrder = $packingOrder->salesOrder;
            
            // Skip jika sales order belum shipped atau delivered
            if (!in_array($salesOrder->status, ['shipped', 'delivered'])) {
                continue;
            }

            $doNumber = 'DO-' . str_pad($doCounter, 5, '0', STR_PAD_LEFT);
            
            // Tentukan status berdasarkan sales order status
            $status = 'prepared';
            $loadedAt = null;
            $departedAt = null;
            $deliveredAt = null;
            $receivedByName = null;
            
            if ($salesOrder->status === 'delivered') {
                $status = 'delivered';
                $loadedAt = $packingOrder->completed_at?->addHours(2);
                $departedAt = $loadedAt?->addHours(1);
                $deliveredAt = $departedAt?->addHours(rand(4, 24));
                $receivedByName = $salesOrder->customer->contact_person ?? 'Customer Representative';
            } elseif ($salesOrder->status === 'shipped') {
                $status = 'in_transit';
                $loadedAt = $packingOrder->completed_at?->addHours(2);
                $departedAt = $loadedAt?->addHours(1);
            }

            // Pilih vehicle dan driver
            $vehicle = $vehicles->random();
            $driver = $drivers->isNotEmpty() ? $drivers->random() : null;

            $deliveryOrders[] = [
                'do_number' => $doNumber,
                'sales_order_id' => $salesOrder->id,
                'packing_order_id' => $packingOrder->id,
                'warehouse_id' => $salesOrder->warehouse_id,
                'customer_id' => $salesOrder->customer_id,
                'delivery_date' => $salesOrder->requested_delivery_date,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver?->id,
                'status' => $status,
                'total_boxes' => $packingOrder->total_boxes,
                'total_weight_kg' => $packingOrder->total_weight_kg,
                'shipping_address' => $salesOrder->shipping_address . ', ' . $salesOrder->shipping_city . ', ' . $salesOrder->shipping_province . ' ' . $salesOrder->shipping_postal_code,
                'recipient_name' => $salesOrder->customer->contact_person,
                'recipient_phone' => $salesOrder->customer->contact_phone,
                'loaded_at' => $loadedAt,
                'departed_at' => $departedAt,
                'delivered_at' => $deliveredAt,
                'received_by_name' => $receivedByName,
                'received_by_signature' => $status === 'delivered' ? 'signature_' . $doNumber . '.png' : null,
                'delivery_proof_image' => $status === 'delivered' ? 'proof_' . $doNumber . '.jpg' : null,
                'notes' => $this->generateNotes($salesOrder, $status, $vehicle),
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => $packingOrder->completed_at ?? Carbon::now()->subDays(rand(1, 10)),
                'updated_at' => $deliveredAt ?? Carbon::now()->subHours(rand(1, 6)),
            ];

            $doCounter++;
        }

        // Tambahkan beberapa DO dengan status lain untuk variasi
        $this->addAdditionalDeliveryOrders($deliveryOrders, $packingOrders, $vehicles, $drivers, $createdBy, $doCounter);

        foreach ($deliveryOrders as $order) {
            DeliveryOrder::create($order);
        }

        $this->command->info('✓ Created ' . count($deliveryOrders) . ' delivery orders successfully!');
    }

    /**
     * Add additional delivery orders with various statuses
     */
    private function addAdditionalDeliveryOrders(&$deliveryOrders, $packingOrders, $vehicles, $drivers, $createdBy, $startCounter)
    {
        // Ambil beberapa packing orders untuk DO status loaded dan prepared
        $additionalPackingOrders = PackingOrder::with(['salesOrder.customer', 'warehouse'])
            ->where('status', 'completed')
            ->whereNotIn('id', collect($deliveryOrders)->pluck('packing_order_id'))
            ->limit(3)
            ->get();

        foreach ($additionalPackingOrders as $index => $packingOrder) {
            $salesOrder = $packingOrder->salesOrder;
            $doNumber = 'DO-' . str_pad($startCounter + $index, 5, '0', STR_PAD_LEFT);
            
            // Buat DO dengan status prepared atau loaded
            $status = $index === 0 ? 'loaded' : 'prepared';
            $loadedAt = $status === 'loaded' ? Carbon::now()->subHours(rand(1, 4)) : null;

            $vehicle = $vehicles->random();
            $driver = $drivers->isNotEmpty() ? $drivers->random() : null;

            $deliveryOrders[] = [
                'do_number' => $doNumber,
                'sales_order_id' => $salesOrder->id,
                'packing_order_id' => $packingOrder->id,
                'warehouse_id' => $salesOrder->warehouse_id,
                'customer_id' => $salesOrder->customer_id,
                'delivery_date' => Carbon::now()->addDays(rand(1, 3)),
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver?->id,
                'status' => $status,
                'total_boxes' => $packingOrder->total_boxes,
                'total_weight_kg' => $packingOrder->total_weight_kg,
                'shipping_address' => $salesOrder->shipping_address . ', ' . $salesOrder->shipping_city . ', ' . $salesOrder->shipping_province . ' ' . $salesOrder->shipping_postal_code,
                'recipient_name' => $salesOrder->customer->contact_person,
                'recipient_phone' => $salesOrder->customer->contact_phone,
                'loaded_at' => $loadedAt,
                'departed_at' => null,
                'delivered_at' => null,
                'received_by_name' => null,
                'received_by_signature' => null,
                'delivery_proof_image' => null,
                'notes' => $this->generateNotes($salesOrder, $status, $vehicle),
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subHours(rand(4, 12)),
                'updated_at' => $loadedAt ?? Carbon::now()->subHours(rand(1, 3)),
            ];
        }
    }

    /**
     * Generate notes based on sales order, status, and vehicle
     */
    private function generateNotes($salesOrder, $status, $vehicle): ?string
    {
        $notes = [];

        // Status specific notes
        switch ($status) {
            case 'delivered':
                $notes[] = 'Delivery completed successfully';
                $notes[] = 'POD (Proof of Delivery) collected';
                break;
            case 'in_transit':
                $notes[] = 'Package in transit';
                $notes[] = 'Estimated delivery time: ' . Carbon::now()->addHours(rand(2, 8))->format('H:i');
                break;
            case 'loaded':
                $notes[] = 'Loading completed, ready for departure';
                break;
            case 'prepared':
                $notes[] = 'DO prepared, waiting for loading';
                break;
        }

        // Vehicle info
        $notes[] = "Vehicle: {$vehicle->brand} {$vehicle->model} ({$vehicle->license_plate})";

        // Customer type notes
        if ($salesOrder->customer->customer_type === 'vip') {
            $notes[] = 'VIP customer - priority delivery';
        }

        // Add SO number reference
        $notes[] = "SO: {$salesOrder->so_number}";

        return !empty($notes) ? implode('. ', $notes) : null;
    }
}