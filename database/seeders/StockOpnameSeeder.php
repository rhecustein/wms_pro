<?php

namespace Database\Seeders;

use App\Models\StockOpname;
use App\Models\Warehouse;
use App\Models\StorageArea;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockOpnameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $storageAreas = StorageArea::all();
        $users = User::all();

        if ($warehouses->isEmpty()) {
            $this->command->warn('Pastikan seeder Warehouse sudah dijalankan terlebih dahulu!');
            return;
        }

        $opnames = [];
        $opnameTypes = ['full', 'cycle', 'spot'];
        $statuses = ['planned', 'in_progress', 'completed', 'cancelled'];

        // Generate opnames untuk 6 bulan terakhir
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        $opnameNumber = 1;

        foreach ($warehouses as $warehouse) {
            $warehouseAreas = $storageAreas->where('warehouse_id', $warehouse->id);

            // Generate 10-15 opnames per warehouse
            $opnameCount = rand(10, 15);

            for ($i = 0; $i < $opnameCount; $i++) {
                $opnameDate = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                );
                
                $status = $statuses[array_rand($statuses)];
                $opnameType = $opnameTypes[array_rand($opnameTypes)];
                
                // Set timestamps based on status
                $startedAt = null;
                $completedAt = null;
                $scheduledBy = $users->isNotEmpty() ? $users->random()->id : null;
                $completedBy = null;

                if (in_array($status, ['in_progress', 'completed'])) {
                    $startedAt = (clone $opnameDate)->addHours(rand(1, 4));
                }

                if ($status === 'completed') {
                    $completedAt = (clone $startedAt)->addHours(rand(2, 8));
                    $completedBy = $users->isNotEmpty() ? $users->random()->id : null;
                }

                // Calculate statistics for completed opnames
                $totalItemsPlanned = $this->getTotalItemsPlanned($opnameType);
                $totalItemsCounted = 0;
                $varianceCount = 0;
                $accuracyPercentage = 0;

                if ($status === 'completed') {
                    $totalItemsCounted = $totalItemsPlanned;
                    $varianceCount = rand(0, (int)($totalItemsPlanned * 0.1)); // Max 10% variance
                    $accuracyPercentage = $totalItemsPlanned > 0 
                        ? round((($totalItemsPlanned - $varianceCount) / $totalItemsPlanned) * 100, 2)
                        : 0;
                } elseif ($status === 'in_progress') {
                    $totalItemsCounted = rand((int)($totalItemsPlanned * 0.3), (int)($totalItemsPlanned * 0.8));
                    $varianceCount = rand(0, (int)($totalItemsCounted * 0.1));
                    $accuracyPercentage = $totalItemsCounted > 0 
                        ? round((($totalItemsCounted - $varianceCount) / $totalItemsCounted) * 100, 2)
                        : 0;
                }

                $opname = [
                    'opname_number' => 'OPN-' . str_pad($opnameNumber++, 5, '0', STR_PAD_LEFT),
                    'warehouse_id' => $warehouse->id,
                    'storage_area_id' => $warehouseAreas->isNotEmpty() && rand(0, 10) > 3 
                        ? $warehouseAreas->random()->id 
                        : null,
                    'opname_date' => $opnameDate,
                    'opname_type' => $opnameType,
                    'status' => $status,
                    'scheduled_by' => $scheduledBy,
                    'completed_by' => $completedBy,
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                    'total_items_planned' => $totalItemsPlanned,
                    'total_items_counted' => $totalItemsCounted,
                    'variance_count' => $varianceCount,
                    'accuracy_percentage' => $accuracyPercentage,
                    'notes' => $this->generateNotes($opnameType, $status),
                    'created_by' => $scheduledBy,
                    'updated_by' => $completedBy ?? $scheduledBy,
                    'created_at' => $opnameDate,
                    'updated_at' => $completedAt ?? $startedAt ?? $opnameDate,
                    'deleted_at' => $status === 'cancelled' && rand(0, 10) > 7 
                        ? $opnameDate->copy()->addDays(rand(1, 3)) 
                        : null,
                ];

                $opnames[] = $opname;
            }
        }

        // Tambahkan opnames khusus untuk testing
        if ($warehouses->count() > 0 && $users->isNotEmpty()) {
            $mainWarehouse = $warehouses->first();
            $mainAreas = $storageAreas->where('warehouse_id', $mainWarehouse->id);
            $user = $users->first();
            $now = Carbon::now();

            // 1. Planned Full Stock Opname - Upcoming
            $opnames[] = [
                'opname_number' => 'OPN-' . str_pad($opnameNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'storage_area_id' => null, // Full warehouse
                'opname_date' => $now->copy()->addDays(7),
                'opname_type' => 'full',
                'status' => 'planned',
                'scheduled_by' => $user->id,
                'completed_by' => null,
                'started_at' => null,
                'completed_at' => null,
                'total_items_planned' => 500,
                'total_items_counted' => 0,
                'variance_count' => 0,
                'accuracy_percentage' => 0,
                'notes' => 'Annual full inventory count - All operations suspended during count',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
                'deleted_at' => null,
            ];

            // 2. In Progress Cycle Count
            $startedDate = $now->copy()->subHours(3);
            $opnames[] = [
                'opname_number' => 'OPN-' . str_pad($opnameNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'storage_area_id' => $mainAreas->isNotEmpty() ? $mainAreas->first()->id : null,
                'opname_date' => $startedDate,
                'opname_type' => 'cycle',
                'status' => 'in_progress',
                'scheduled_by' => $user->id,
                'completed_by' => null,
                'started_at' => $startedDate,
                'completed_at' => null,
                'total_items_planned' => 120,
                'total_items_counted' => 75,
                'variance_count' => 3,
                'accuracy_percentage' => 96.00,
                'notes' => 'Weekly cycle count - Zone A - Currently in progress',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $startedDate->copy()->subDays(1),
                'updated_at' => $now,
                'deleted_at' => null,
            ];

            // 3. Completed Cycle Count - High Accuracy
            $completedDate = $now->copy()->subDays(7);
            $opnames[] = [
                'opname_number' => 'OPN-' . str_pad($opnameNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'storage_area_id' => $mainAreas->isNotEmpty() ? $mainAreas->last()->id : null,
                'opname_date' => $completedDate,
                'opname_type' => 'cycle',
                'status' => 'completed',
                'scheduled_by' => $user->id,
                'completed_by' => $user->id,
                'started_at' => $completedDate->copy()->addHours(2),
                'completed_at' => $completedDate->copy()->addHours(6),
                'total_items_planned' => 150,
                'total_items_counted' => 150,
                'variance_count' => 2,
                'accuracy_percentage' => 98.67,
                'notes' => 'Cycle count completed - High accuracy achieved',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $completedDate->copy()->subDays(1),
                'updated_at' => $completedDate->copy()->addHours(6),
                'deleted_at' => null,
            ];

            // 4. Completed Spot Check - Low Accuracy
            $spotDate = $now->copy()->subDays(14);
            $opnames[] = [
                'opname_number' => 'OPN-' . str_pad($opnameNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'storage_area_id' => $mainAreas->isNotEmpty() ? $mainAreas->random()->id : null,
                'opname_date' => $spotDate,
                'opname_type' => 'spot',
                'status' => 'completed',
                'scheduled_by' => $user->id,
                'completed_by' => $user->id,
                'started_at' => $spotDate->copy()->addHours(1),
                'completed_at' => $spotDate->copy()->addHours(3),
                'total_items_planned' => 30,
                'total_items_counted' => 30,
                'variance_count' => 8,
                'accuracy_percentage' => 73.33,
                'notes' => 'Spot check triggered by discrepancy alert - Multiple variances found requiring investigation',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $spotDate,
                'updated_at' => $spotDate->copy()->addHours(3),
                'deleted_at' => null,
            ];

            // 5. Completed Full Count - Quarter End
            $quarterDate = $now->copy()->subMonths(1);
            $opnames[] = [
                'opname_number' => 'OPN-' . str_pad($opnameNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'storage_area_id' => null,
                'opname_date' => $quarterDate,
                'opname_type' => 'full',
                'status' => 'completed',
                'scheduled_by' => $user->id,
                'completed_by' => $user->id,
                'started_at' => $quarterDate->copy()->addHours(1),
                'completed_at' => $quarterDate->copy()->addHours(12),
                'total_items_planned' => 450,
                'total_items_counted' => 450,
                'variance_count' => 18,
                'accuracy_percentage' => 96.00,
                'notes' => 'Q3 2024 Full Physical Inventory - Financial reporting requirement',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $quarterDate->copy()->subDays(7),
                'updated_at' => $quarterDate->copy()->addHours(12),
                'deleted_at' => null,
            ];

            // 6. Cancelled Opname
            $cancelledDate = $now->copy()->subDays(3);
            $opnames[] = [
                'opname_number' => 'OPN-' . str_pad($opnameNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'storage_area_id' => $mainAreas->isNotEmpty() ? $mainAreas->random()->id : null,
                'opname_date' => $cancelledDate,
                'opname_type' => 'cycle',
                'status' => 'cancelled',
                'scheduled_by' => $user->id,
                'completed_by' => null,
                'started_at' => null,
                'completed_at' => null,
                'total_items_planned' => 100,
                'total_items_counted' => 0,
                'variance_count' => 0,
                'accuracy_percentage' => 0,
                'notes' => 'Cancelled due to urgent shipment requirements - Rescheduled for next week',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $cancelledDate->copy()->subDays(2),
                'updated_at' => $cancelledDate,
                'deleted_at' => $cancelledDate,
            ];
        }

        // Insert semua data
        foreach ($opnames as $opname) {
            StockOpname::create($opname);
        }

        $this->command->info('Stock Opname seeder berhasil dijalankan! Total: ' . count($opnames) . ' records');
    }

    /**
     * Get total items planned based on opname type
     */
    private function getTotalItemsPlanned(string $opnameType): int
    {
        return match($opnameType) {
            'full' => rand(300, 600),
            'cycle' => rand(80, 200),
            'spot' => rand(20, 50),
            default => rand(50, 150)
        };
    }

    /**
     * Generate notes based on opname type and status
     */
    private function generateNotes(string $opnameType, string $status): ?string
    {
        if ($status === 'cancelled') {
            return [
                'Cancelled - Operational priorities changed',
                'Postponed due to high order volume',
                'Rescheduled - Staff shortage',
                'Cancelled - System maintenance required',
            ][rand(0, 3)];
        }

        $notes = match($opnameType) {
            'full' => [
                'Annual physical inventory count',
                'Year-end stock verification',
                'Quarterly full warehouse audit',
                'Complete inventory reconciliation',
            ],
            'cycle' => [
                'Weekly cycle count schedule',
                'Monthly rotating zone count',
                'ABC classification cycle count',
                'Regular inventory accuracy check',
            ],
            'spot' => [
                'Random spot check verification',
                'Discrepancy investigation count',
                'High-value items verification',
                'Fast-moving items spot check',
            ],
            default => 'Stock opname in progress'
        };

        return $notes[array_rand($notes)];
    }
}