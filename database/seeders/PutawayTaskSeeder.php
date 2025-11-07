<?php

namespace Database\Seeders;

use App\Models\PutawayTask;
use App\Models\GoodReceiving;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PutawayTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $userId = $adminUser ? $adminUser->id : null;

        $goodReceivings = GoodReceiving::with(['items', 'warehouse'])->get();
        $products = Product::all();
        $storageBins = StorageBin::all();

        if ($goodReceivings->isEmpty()) {
            $this->command->warn('Tidak ada good receiving records. Jalankan GoodReceivingSeeder terlebih dahulu.');
            return;
        }

        if ($storageBins->isEmpty()) {
            $this->command->warn('Tidak ada storage bins. Jalankan StorageBinSeeder terlebih dahulu.');
            $this->command->info('Creating putaway tasks without storage bin assignments...');
        }

        $taskCounter = 1;

        // ========================================
        // GR-2024-001 - Smartphones (COMPLETED)
        // All tasks completed
        // ========================================
        $gr1 = $goodReceivings->where('gr_number', 'GR-2024-001')->first();
        if ($gr1 && $gr1->items) {
            foreach ($gr1->items as $grItem) {
                // Create multiple tasks for large quantities
                $totalQty = $grItem->quantity_accepted;
                $tasksNeeded = ceil($totalQty / 10); // Max 10 units per task for smartphones
                
                for ($i = 0; $i < $tasksNeeded; $i++) {
                    $qtyForTask = min(10, $totalQty - ($i * 10));
                    
                    if ($qtyForTask <= 0) break;

                    $storageBin = $storageBins->isNotEmpty() ? $storageBins->random() : null;

                    PutawayTask::create([
                        'task_number' => sprintf('PUT-%05d', $taskCounter++),
                        'good_receiving_id' => $gr1->id,
                        'warehouse_id' => $gr1->warehouse_id,
                        'product_id' => $grItem->product_id,
                        'batch_number' => $grItem->batch_number,
                        'serial_number' => null,
                        'quantity' => $qtyForTask,
                        'unit_of_measure' => $grItem->unit_of_measure,
                        'from_location' => 'STAGING-A1',
                        'to_storage_bin_id' => $storageBin?->id,
                        'pallet_id' => null,
                        'priority' => 'high',
                        'status' => 'completed',
                        'assigned_to' => $userId,
                        'assigned_at' => Carbon::now()->subMonths(3)->addDays(5)->setHour(11)->setMinute(0),
                        'started_at' => Carbon::now()->subMonths(3)->addDays(5)->setHour(11)->setMinute(15),
                        'completed_at' => Carbon::now()->subMonths(3)->addDays(5)->setHour(12)->setMinute(30),
                        'suggested_by_system' => true,
                        'packaging_type' => 'Original Box',
                        'notes' => "High-value electronics - handled with care. Task batch " . ($i + 1) . " of " . $tasksNeeded,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                }
            }
        }

        // ========================================
        // GR-2024-002 - MacBook Pro (COMPLETED)
        // ========================================
        $gr2 = $goodReceivings->where('gr_number', 'GR-2024-002')->first();
        if ($gr2 && $gr2->items) {
            foreach ($gr2->items as $grItem) {
                // MacBook Pro - 5 units per pallet
                $totalQty = $grItem->quantity_accepted;
                $tasksNeeded = ceil($totalQty / 5);
                
                for ($i = 0; $i < $tasksNeeded; $i++) {
                    $qtyForTask = min(5, $totalQty - ($i * 5));
                    
                    if ($qtyForTask <= 0) break;

                    $storageBin = $storageBins->isNotEmpty() ? $storageBins->random() : null;

                    PutawayTask::create([
                        'task_number' => sprintf('PUT-%05d', $taskCounter++),
                        'good_receiving_id' => $gr2->id,
                        'warehouse_id' => $gr2->warehouse_id,
                        'product_id' => $grItem->product_id,
                        'batch_number' => $grItem->batch_number,
                        'quantity' => $qtyForTask,
                        'unit_of_measure' => $grItem->unit_of_measure,
                        'from_location' => 'STAGING-A2',
                        'to_storage_bin_id' => $storageBin?->id,
                        'pallet_id' => null,
                        'priority' => 'high',
                        'status' => 'completed',
                        'assigned_to' => $userId,
                        'assigned_at' => Carbon::now()->subMonths(2)->addDays(12)->setHour(13)->setMinute(0),
                        'started_at' => Carbon::now()->subMonths(2)->addDays(12)->setHour(13)->setMinute(20),
                        'completed_at' => Carbon::now()->subMonths(2)->addDays(12)->setHour(14)->setMinute(45),
                        'suggested_by_system' => true,
                        'packaging_type' => 'Apple Original Packaging',
                        'notes' => "Premium laptops - climate controlled storage required. Pallet " . ($i + 1),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                }
            }
        }

        // ========================================
        // GR-2024-003 - Dell XPS (COMPLETED)
        // ========================================
        $gr3 = $goodReceivings->where('gr_number', 'GR-2024-003')->first();
        if ($gr3 && $gr3->items) {
            foreach ($gr3->items as $grItem) {
                $totalQty = $grItem->quantity_accepted;
                $tasksNeeded = ceil($totalQty / 5);
                
                for ($i = 0; $i < $tasksNeeded; $i++) {
                    $qtyForTask = min(5, $totalQty - ($i * 5));
                    
                    if ($qtyForTask <= 0) break;

                    $storageBin = $storageBins->isNotEmpty() ? $storageBins->random() : null;

                    PutawayTask::create([
                        'task_number' => sprintf('PUT-%05d', $taskCounter++),
                        'good_receiving_id' => $gr3->id,
                        'warehouse_id' => $gr3->warehouse_id,
                        'product_id' => $grItem->product_id,
                        'batch_number' => $grItem->batch_number,
                        'quantity' => $qtyForTask,
                        'unit_of_measure' => $grItem->unit_of_measure,
                        'from_location' => 'STAGING-A3',
                        'to_storage_bin_id' => $storageBin?->id,
                        'priority' => 'high',
                        'status' => 'completed',
                        'assigned_to' => $userId,
                        'assigned_at' => Carbon::now()->subMonths(2)->addDays(12)->setHour(16)->setMinute(0),
                        'started_at' => Carbon::now()->subMonths(2)->addDays(12)->setHour(16)->setMinute(15),
                        'completed_at' => Carbon::now()->subMonths(2)->addDays(12)->setHour(17)->setMinute(30),
                        'suggested_by_system' => true,
                        'packaging_type' => 'Dell Factory Box',
                        'notes' => "Dell XPS units stored in electronics section. Group " . ($i + 1),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                }
            }
        }

        // ========================================
        // GR-2024-004 - Raw Materials (IN PROGRESS)
        // Some completed, some in progress
        // ========================================
        $gr4 = $goodReceivings->where('gr_number', 'GR-2024-004')->first();
        if ($gr4 && $gr4->items) {
            foreach ($gr4->items as $index => $grItem) {
                if ($grItem->quantity_accepted <= 0) continue;

                // Steel plates - heavy items
                if (str_contains($grItem->product->sku, 'RAW-MT')) {
                    $tasksNeeded = ceil($grItem->quantity_accepted / 10); // 10 sheets per task
                    
                    for ($i = 0; $i < $tasksNeeded; $i++) {
                        $qtyForTask = min(10, $grItem->quantity_accepted - ($i * 10));
                        
                        if ($qtyForTask <= 0) break;

                        $storageBin = $storageBins->isNotEmpty() ? $storageBins->random() : null;

                        // First batch completed, second batch in progress
                        $status = $i < ($tasksNeeded / 2) ? 'completed' : 'in_progress';
                        $completedAt = $status === 'completed' 
                            ? Carbon::now()->subDays(3)->setHour(14)->setMinute(30)
                            : null;

                        PutawayTask::create([
                            'task_number' => sprintf('PUT-%05d', $taskCounter++),
                            'good_receiving_id' => $gr4->id,
                            'warehouse_id' => $gr4->warehouse_id,
                            'product_id' => $grItem->product_id,
                            'batch_number' => $grItem->batch_number,
                            'quantity' => $qtyForTask,
                            'unit_of_measure' => $grItem->unit_of_measure,
                            'from_location' => 'STAGING-RAW-1',
                            'to_storage_bin_id' => $storageBin?->id,
                            'priority' => 'medium',
                            'status' => $status,
                            'assigned_to' => $userId,
                            'assigned_at' => Carbon::now()->subDays(3)->setHour(11)->setMinute(0),
                            'started_at' => $status === 'in_progress' 
                                ? Carbon::now()->setHour(10)->setMinute(0) 
                                : Carbon::now()->subDays(3)->setHour(11)->setMinute(30),
                            'completed_at' => $completedAt,
                            'suggested_by_system' => true,
                            'packaging_type' => 'Pallet',
                            'notes' => "Heavy steel plates - forklift required. Batch " . ($i + 1) . " of " . $tasksNeeded,
                            'created_by' => $userId,
                            'updated_by' => $userId,
                        ]);
                    }
                }
                
                // Plastic pellets - bags
                if (str_contains($grItem->product->sku, 'RAW-PL')) {
                    $tasksNeeded = ceil($grItem->quantity_accepted / 20); // 20 bags per pallet
                    
                    for ($i = 0; $i < $tasksNeeded; $i++) {
                        $qtyForTask = min(20, $grItem->quantity_accepted - ($i * 20));
                        
                        if ($qtyForTask <= 0) break;

                        $storageBin = $storageBins->isNotEmpty() ? $storageBins->random() : null;

                        PutawayTask::create([
                            'task_number' => sprintf('PUT-%05d', $taskCounter++),
                            'good_receiving_id' => $gr4->id,
                            'warehouse_id' => $gr4->warehouse_id,
                            'product_id' => $grItem->product_id,
                            'batch_number' => $grItem->batch_number,
                            'quantity' => $qtyForTask,
                            'unit_of_measure' => $grItem->unit_of_measure,
                            'from_location' => 'STAGING-RAW-2',
                            'to_storage_bin_id' => $storageBin?->id,
                            'priority' => 'medium',
                            'status' => 'completed',
                            'assigned_to' => $userId,
                            'assigned_at' => Carbon::now()->subDays(3)->setHour(12)->setMinute(0),
                            'started_at' => Carbon::now()->subDays(3)->setHour(12)->setMinute(30),
                            'completed_at' => Carbon::now()->subDays(3)->setHour(13)->setMinute(45),
                            'suggested_by_system' => true,
                            'packaging_type' => 'Bags on Pallet',
                            'notes' => "Plastic pellets - keep dry. Pallet " . ($i + 1) . ". COA attached.",
                            'created_by' => $userId,
                            'updated_by' => $userId,
                        ]);
                    }
                }
            }
        }

        // ========================================
        // GR-2024-005 - Direct GR (ASSIGNED)
        // Tasks assigned but not started
        // ========================================
        $gr5 = $goodReceivings->where('gr_number', 'GR-2024-005')->first();
        if ($gr5 && $gr5->items) {
            foreach ($gr5->items as $grItem) {
                $totalQty = $grItem->quantity_accepted;
                
                // Office supplies - large quantities per task
                $tasksNeeded = ceil($totalQty / 50);
                
                for ($i = 0; $i < $tasksNeeded; $i++) {
                    $qtyForTask = min(50, $totalQty - ($i * 50));
                    
                    if ($qtyForTask <= 0) break;

                    $storageBin = $storageBins->isNotEmpty() ? $storageBins->random() : null;

                    PutawayTask::create([
                        'task_number' => sprintf('PUT-%05d', $taskCounter++),
                        'good_receiving_id' => $gr5->id,
                        'warehouse_id' => $gr5->warehouse_id,
                        'product_id' => $grItem->product_id,
                        'batch_number' => $grItem->batch_number,
                        'quantity' => $qtyForTask,
                        'unit_of_measure' => $grItem->unit_of_measure,
                        'from_location' => 'STAGING-OFFICE',
                        'to_storage_bin_id' => $storageBin?->id,
                        'priority' => 'low',
                        'status' => 'assigned',
                        'assigned_to' => $userId,
                        'assigned_at' => Carbon::now()->subDays(9)->setHour(15)->setMinute(0),
                        'started_at' => null,
                        'completed_at' => null,
                        'suggested_by_system' => true,
                        'packaging_type' => 'Carton',
                        'notes' => "Emergency stock - office supplies. Batch " . ($i + 1),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                }
            }
        }

        // ========================================
        // GR-2024-006 - Chemicals (PENDING)
        // Quality check in progress, putaway pending
        // ========================================
        $gr6 = $goodReceivings->where('gr_number', 'GR-2024-006')->first();
        if ($gr6 && $gr6->items) {
            foreach ($gr6->items as $grItem) {
                $totalQty = $grItem->quantity_received;
                $tasksNeeded = ceil($totalQty / 30); // 30 units per task
                
                for ($i = 0; $i < $tasksNeeded; $i++) {
                    $qtyForTask = min(30, $totalQty - ($i * 30));
                    
                    if ($qtyForTask <= 0) break;

                    $storageBin = $storageBins->isNotEmpty() ? $storageBins->random() : null;

                    PutawayTask::create([
                        'task_number' => sprintf('PUT-%05d', $taskCounter++),
                        'good_receiving_id' => $gr6->id,
                        'warehouse_id' => $gr6->warehouse_id,
                        'product_id' => $grItem->product_id,
                        'batch_number' => $grItem->batch_number,
                        'quantity' => $qtyForTask,
                        'unit_of_measure' => $grItem->unit_of_measure,
                        'from_location' => 'STAGING-CHEMICAL',
                        'to_storage_bin_id' => $storageBin?->id,
                        'priority' => 'high',
                        'status' => 'pending',
                        'assigned_to' => null,
                        'assigned_at' => null,
                        'started_at' => null,
                        'completed_at' => null,
                        'suggested_by_system' => true,
                        'packaging_type' => 'Chemical Container',
                        'notes' => "HAZMAT: Awaiting quality check completion. MSDS verification required before putaway. Batch " . ($i + 1),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                }
            }
        }

        $this->command->info('✓ Created putaway tasks successfully!');
        $this->command->info("  Total tasks created: " . ($taskCounter - 1));
        $this->command->info('  - Status breakdown:');
        $this->command->info('    * Completed: ' . PutawayTask::where('status', 'completed')->count());
        $this->command->info('    * In Progress: ' . PutawayTask::where('status', 'in_progress')->count());
        $this->command->info('    * Assigned: ' . PutawayTask::where('status', 'assigned')->count());
        $this->command->info('    * Pending: ' . PutawayTask::where('status', 'pending')->count());
    }
}