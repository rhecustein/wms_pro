<?php

namespace Database\Seeders;

use App\Models\PickingOrder;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PickingOrderSeeder extends Seeder
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
        
        // Ambil sales orders yang membutuhkan picking
        $salesOrders = SalesOrder::whereIn('status', [
            'confirmed', 
            'picking', 
            'packing', 
            'shipped', 
            'delivered'
        ])->get();

        if ($salesOrders->isEmpty()) {
            $this->command->warn('Tidak ada sales orders yang valid. Jalankan SalesOrderSeeder terlebih dahulu.');
            return;
        }

        $pickingOrders = [];

        // ========================================
        // COMPLETED PICKING ORDERS (untuk delivered orders)
        // ========================================
        
        // SO-00001 (DELIVERED)
        $so1 = $salesOrders->firstWhere('so_number', 'SO-00001');
        if ($so1) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00001',
                'sales_order_id' => $so1->id,
                'warehouse_id' => $so1->warehouse_id,
                'picking_date' => Carbon::now()->subDays(44),
                'picking_type' => 'single_order',
                'priority' => 'urgent',
                'status' => 'completed',
                'assigned_to' => $users->skip(0)->first()?->id,
                'assigned_at' => Carbon::now()->subDays(44)->addHours(1),
                'started_at' => Carbon::now()->subDays(44)->addHours(2),
                'completed_at' => Carbon::now()->subDays(44)->addHours(5),
                'total_items' => 4,
                'total_quantity' => 37,
                'notes' => 'VIP customer order - prioritas tinggi. Picking selesai lebih cepat.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(44),
                'updated_at' => Carbon::now()->subDays(44)->addHours(5),
            ];
        }

        // SO-00002 (DELIVERED)
        $so2 = $salesOrders->firstWhere('so_number', 'SO-00002');
        if ($so2) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00002',
                'sales_order_id' => $so2->id,
                'warehouse_id' => $so2->warehouse_id,
                'picking_date' => Carbon::now()->subDays(39),
                'picking_type' => 'wave',
                'priority' => 'high',
                'status' => 'completed',
                'assigned_to' => $users->skip(1)->first()?->id,
                'assigned_at' => Carbon::now()->subDays(39)->addHours(1),
                'started_at' => Carbon::now()->subDays(39)->addHours(2),
                'completed_at' => Carbon::now()->subDays(39)->addHours(4),
                'total_items' => 4,
                'total_quantity' => 530,
                'notes' => 'Wholesale FMCG order - wave picking untuk efisiensi.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(39),
                'updated_at' => Carbon::now()->subDays(39)->addHours(4),
            ];
        }

        // SO-00003 (DELIVERED)
        $so3 = $salesOrders->firstWhere('so_number', 'SO-00003');
        if ($so3) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00003',
                'sales_order_id' => $so3->id,
                'warehouse_id' => $so3->warehouse_id,
                'picking_date' => Carbon::now()->subDays(34),
                'picking_type' => 'single_order',
                'priority' => 'medium',
                'status' => 'completed',
                'assigned_to' => $users->skip(2)->first()?->id,
                'assigned_at' => Carbon::now()->subDays(34)->addHours(2),
                'started_at' => Carbon::now()->subDays(34)->addHours(3),
                'completed_at' => Carbon::now()->subDays(34)->addHours(6),
                'total_items' => 4,
                'total_quantity' => 230,
                'notes' => 'Order retail standar untuk toko Semarang.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(34),
                'updated_at' => Carbon::now()->subDays(34)->addHours(6),
            ];
        }

        // SO-00004 (SHIPPED)
        $so4 = $salesOrders->firstWhere('so_number', 'SO-00004');
        if ($so4) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00004',
                'sales_order_id' => $so4->id,
                'warehouse_id' => $so4->warehouse_id,
                'picking_date' => Carbon::now()->subDays(7),
                'picking_type' => 'single_order',
                'priority' => 'urgent',
                'status' => 'completed',
                'assigned_to' => $users->skip(0)->first()?->id,
                'assigned_at' => Carbon::now()->subDays(7)->addHours(1),
                'started_at' => Carbon::now()->subDays(7)->addHours(2),
                'completed_at' => Carbon::now()->subDays(7)->addHours(5),
                'total_items' => 3,
                'total_quantity' => 18,
                'notes' => 'VIP electronics order - handled with care untuk barang high-value.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7)->addHours(5),
            ];
        }

        // SO-00005 (SHIPPED)
        $so5 = $salesOrders->firstWhere('so_number', 'SO-00005');
        if ($so5) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00005',
                'sales_order_id' => $so5->id,
                'warehouse_id' => $so5->warehouse_id,
                'picking_date' => Carbon::now()->subDays(4),
                'picking_type' => 'batch',
                'priority' => 'high',
                'status' => 'completed',
                'assigned_to' => $users->skip(1)->first()?->id,
                'assigned_at' => Carbon::now()->subDays(4)->addHours(1),
                'started_at' => Carbon::now()->subDays(4)->addHours(2),
                'completed_at' => Carbon::now()->subDays(4)->addHours(5),
                'total_items' => 3,
                'total_quantity' => 420,
                'notes' => 'Batch picking untuk order Bandung - multiple items dari zona yang sama.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4)->addHours(5),
            ];
        }

        // ========================================
        // PACKING STATUS ORDERS (completed picking)
        // ========================================
        
        // SO-00006 (PACKING)
        $so6 = $salesOrders->firstWhere('so_number', 'SO-00006');
        if ($so6) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00006',
                'sales_order_id' => $so6->id,
                'warehouse_id' => $so6->warehouse_id,
                'picking_date' => Carbon::now()->subDays(3)->addHours(8),
                'picking_type' => 'single_order',
                'priority' => 'medium',
                'status' => 'completed',
                'assigned_to' => $users->skip(2)->first()?->id,
                'assigned_at' => Carbon::now()->subDays(3)->addHours(9),
                'started_at' => Carbon::now()->subDays(3)->addHours(10),
                'completed_at' => Carbon::now()->subDays(3)->addHours(13),
                'total_items' => 3,
                'total_quantity' => 140,
                'notes' => 'Picking selesai, barang sudah di staging area untuk packing.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(3)->addHours(8),
                'updated_at' => Carbon::now()->subDays(3)->addHours(13),
            ];
        }

        // SO-00007 (PACKING)
        $so7 = $salesOrders->firstWhere('so_number', 'SO-00007');
        if ($so7) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00007',
                'sales_order_id' => $so7->id,
                'warehouse_id' => $so7->warehouse_id,
                'picking_date' => Carbon::now()->subDays(2)->addHours(6),
                'picking_type' => 'zone',
                'priority' => 'high',
                'status' => 'completed',
                'assigned_to' => $users->skip(3)->first()?->id,
                'assigned_at' => Carbon::now()->subDays(2)->addHours(7),
                'started_at' => Carbon::now()->subDays(2)->addHours(8),
                'completed_at' => Carbon::now()->subDays(2)->addHours(12),
                'total_items' => 4,
                'total_quantity' => 285,
                'notes' => 'Zone picking untuk regional Makassar - efisiensi routing di warehouse.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(2)->addHours(6),
                'updated_at' => Carbon::now()->subDays(2)->addHours(12),
            ];
        }

        // ========================================
        // IN_PROGRESS PICKING ORDERS
        // ========================================
        
        // SO-00008 (PICKING)
        $so8 = $salesOrders->firstWhere('so_number', 'SO-00008');
        if ($so8) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00008',
                'sales_order_id' => $so8->id,
                'warehouse_id' => $so8->warehouse_id,
                'picking_date' => Carbon::now()->subHours(12),
                'picking_type' => 'single_order',
                'priority' => 'medium',
                'status' => 'in_progress',
                'assigned_to' => $users->skip(1)->first()?->id,
                'assigned_at' => Carbon::now()->subHours(11),
                'started_at' => Carbon::now()->subHours(10),
                'completed_at' => null,
                'total_items' => 3,
                'total_quantity' => 105,
                'notes' => 'Sedang proses picking untuk minimarket Bali - 70% sudah selesai.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(6),
            ];
        }

        // SO-00009 (PICKING)
        $so9 = $salesOrders->firstWhere('so_number', 'SO-00009');
        if ($so9) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00009',
                'sales_order_id' => $so9->id,
                'warehouse_id' => $so9->warehouse_id,
                'picking_date' => Carbon::now()->subHours(8),
                'picking_type' => 'batch',
                'priority' => 'high',
                'status' => 'in_progress',
                'assigned_to' => $users->skip(2)->first()?->id,
                'assigned_at' => Carbon::now()->subHours(7),
                'started_at' => Carbon::now()->subHours(6),
                'completed_at' => null,
                'total_items' => 4,
                'total_quantity' => 230,
                'notes' => 'Batch picking untuk Kalimantan - koordinasi dengan logistik laut. Progress 60%.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subHours(8),
                'updated_at' => Carbon::now()->subHours(2),
            ];
        }

        // ========================================
        // ASSIGNED (belum mulai picking)
        // ========================================
        
        // SO-00010 (CONFIRMED)
        $so10 = $salesOrders->firstWhere('so_number', 'SO-00010');
        if ($so10) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00010',
                'sales_order_id' => $so10->id,
                'warehouse_id' => $so10->warehouse_id,
                'picking_date' => Carbon::now()->subHours(4),
                'picking_type' => 'single_order',
                'priority' => 'urgent',
                'status' => 'assigned',
                'assigned_to' => $users->skip(0)->first()?->id,
                'assigned_at' => Carbon::now()->subHours(3),
                'started_at' => null,
                'completed_at' => null,
                'total_items' => 3,
                'total_quantity' => 32,
                'notes' => 'VIP repeat order - assigned ke picker terbaik, akan segera dimulai.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subHours(4),
                'updated_at' => Carbon::now()->subHours(3),
            ];
        }

        // SO-00011 (CONFIRMED)
        $so11 = $salesOrders->firstWhere('so_number', 'SO-00011');
        if ($so11) {
            $pickingOrders[] = [
                'picking_number' => 'PICK-00011',
                'sales_order_id' => $so11->id,
                'warehouse_id' => $so11->warehouse_id,
                'picking_date' => Carbon::now()->subHours(2),
                'picking_type' => 'single_order',
                'priority' => 'high',
                'status' => 'assigned',
                'assigned_to' => $users->skip(3)->first()?->id,
                'assigned_at' => Carbon::now()->subHours(1),
                'started_at' => null,
                'completed_at' => null,
                'total_items' => 3,
                'total_quantity' => 55,
                'notes' => 'Order electronics Padang - assigned, menunggu picker selesai task sebelumnya.',
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(1),
            ];
        }

        // ========================================
        // PENDING (baru dibuat, belum assigned)
        // ========================================
        
        // Draft orders tidak perlu picking order karena belum confirmed
        // Hanya create untuk confirmed orders

        foreach ($pickingOrders as $order) {
            PickingOrder::create($order);
        }

        $this->command->info('✓ Created ' . count($pickingOrders) . ' picking orders successfully with various statuses!');
    }
}