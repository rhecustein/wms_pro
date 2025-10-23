<?php

namespace Database\Seeders;

use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockAdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $users = User::all();

        if ($warehouses->isEmpty()) {
            $this->command->warn('Pastikan seeder Warehouse sudah dijalankan terlebih dahulu!');
            return;
        }

        $adjustments = [];
        $adjustmentTypes = ['addition', 'reduction', 'correction'];
        $reasons = ['damaged', 'expired', 'lost', 'found', 'count_correction'];
        $statuses = ['draft', 'approved', 'posted', 'cancelled'];

        // Generate adjustments untuk 6 bulan terakhir
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        $adjustmentNumber = 1;

        foreach ($warehouses as $warehouse) {
            // Generate 8-12 adjustments per warehouse
            $adjustmentCount = rand(8, 12);

            for ($i = 0; $i < $adjustmentCount; $i++) {
                $adjustmentDate = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                );
                
                $status = $statuses[array_rand($statuses)];
                $adjustmentType = $adjustmentTypes[array_rand($adjustmentTypes)];
                $reason = $reasons[array_rand($reasons)];
                
                // Jika status approved atau posted, set approved_by dan approved_at
                $approvedBy = null;
                $approvedAt = null;
                if (in_array($status, ['approved', 'posted']) && $users->isNotEmpty()) {
                    $approvedBy = $users->random()->id;
                    $approvedAt = (clone $adjustmentDate)->addHours(rand(1, 48));
                }

                $createdBy = $users->isNotEmpty() ? $users->random()->id : null;
                $updatedBy = $createdBy;

                $adjustment = [
                    'adjustment_number' => 'ADJ-' . str_pad($adjustmentNumber++, 5, '0', STR_PAD_LEFT),
                    'warehouse_id' => $warehouse->id,
                    'adjustment_date' => $adjustmentDate,
                    'adjustment_type' => $adjustmentType,
                    'reason' => $reason,
                    'status' => $status,
                    'total_items' => 0, // Will be updated by items seeder
                    'approved_by' => $approvedBy,
                    'approved_at' => $approvedAt,
                    'notes' => $this->generateNotes($adjustmentType, $reason, $status),
                    'created_by' => $createdBy,
                    'updated_by' => $updatedBy,
                    'created_at' => $adjustmentDate,
                    'updated_at' => $approvedAt ?? $adjustmentDate,
                    'deleted_at' => $status === 'cancelled' && rand(0, 10) > 7 ? $adjustmentDate->addDays(rand(1, 5)) : null,
                ];

                $adjustments[] = $adjustment;
            }
        }

        // Tambahkan adjustments khusus untuk testing
        if ($warehouses->count() > 0 && $users->isNotEmpty()) {
            $mainWarehouse = $warehouses->first();
            $user = $users->first();
            $now = Carbon::now();

            // 1. Draft Adjustment - Cycle Count
            $adjustments[] = [
                'adjustment_number' => 'ADJ-' . str_pad($adjustmentNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'adjustment_date' => $now->copy()->subDays(1),
                'adjustment_type' => 'correction',
                'reason' => 'count_correction',
                'status' => 'draft',
                'total_items' => 0,
                'approved_by' => null,
                'approved_at' => null,
                'notes' => 'Monthly cycle count - Zone A - Pending review',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
                'deleted_at' => null,
            ];

            // 2. Approved Adjustment - Damaged Goods
            $approvedDate = $now->copy()->subDays(7);
            $adjustments[] = [
                'adjustment_number' => 'ADJ-' . str_pad($adjustmentNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'adjustment_date' => $approvedDate,
                'adjustment_type' => 'reduction',
                'reason' => 'damaged',
                'status' => 'approved',
                'total_items' => 0,
                'approved_by' => $user->id,
                'approved_at' => $approvedDate->copy()->addHours(2),
                'notes' => 'Damaged during handling - Forklift accident in Zone B',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $approvedDate,
                'updated_at' => $approvedDate->copy()->addHours(2),
                'deleted_at' => null,
            ];

            // 3. Posted Adjustment - Expired Products
            $postedDate = $now->copy()->subDays(15);
            $adjustments[] = [
                'adjustment_number' => 'ADJ-' . str_pad($adjustmentNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'adjustment_date' => $postedDate,
                'adjustment_type' => 'reduction',
                'reason' => 'expired',
                'status' => 'posted',
                'total_items' => 0,
                'approved_by' => $user->id,
                'approved_at' => $postedDate->copy()->addHours(4),
                'notes' => 'Quarterly expiry check - Products removed from inventory',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $postedDate,
                'updated_at' => $postedDate->copy()->addHours(5),
                'deleted_at' => null,
            ];

            // 4. Posted Adjustment - Found Items
            $foundDate = $now->copy()->subDays(20);
            $adjustments[] = [
                'adjustment_number' => 'ADJ-' . str_pad($adjustmentNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'adjustment_date' => $foundDate,
                'adjustment_type' => 'addition',
                'reason' => 'found',
                'status' => 'posted',
                'total_items' => 0,
                'approved_by' => $user->id,
                'approved_at' => $foundDate->copy()->addHours(1),
                'notes' => 'Items found during warehouse reorganization - Zone C',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $foundDate,
                'updated_at' => $foundDate->copy()->addHours(2),
                'deleted_at' => null,
            ];

            // 5. Posted Adjustment - Lost Items
            $lostDate = $now->copy()->subDays(30);
            $adjustments[] = [
                'adjustment_number' => 'ADJ-' . str_pad($adjustmentNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'adjustment_date' => $lostDate,
                'adjustment_type' => 'reduction',
                'reason' => 'lost',
                'status' => 'posted',
                'total_items' => 0,
                'approved_by' => $user->id,
                'approved_at' => $lostDate->copy()->addDays(1),
                'notes' => 'Annual inventory audit - Discrepancies identified and investigated',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $lostDate,
                'updated_at' => $lostDate->copy()->addDays(2),
                'deleted_at' => null,
            ];

            // 6. Cancelled Adjustment
            $cancelledDate = $now->copy()->subDays(5);
            $adjustments[] = [
                'adjustment_number' => 'ADJ-' . str_pad($adjustmentNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'adjustment_date' => $cancelledDate,
                'adjustment_type' => 'correction',
                'reason' => 'count_correction',
                'status' => 'cancelled',
                'total_items' => 0,
                'approved_by' => null,
                'approved_at' => null,
                'notes' => 'Cancelled - Incorrect count, recount scheduled',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $cancelledDate,
                'updated_at' => $cancelledDate->copy()->addHours(3),
                'deleted_at' => $cancelledDate->copy()->addHours(3),
            ];

            // 7. Large Correction - Annual Physical Count
            $physicalCountDate = $now->copy()->subMonths(2);
            $adjustments[] = [
                'adjustment_number' => 'ADJ-' . str_pad($adjustmentNumber++, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $mainWarehouse->id,
                'adjustment_date' => $physicalCountDate,
                'adjustment_type' => 'correction',
                'reason' => 'count_correction',
                'status' => 'posted',
                'total_items' => 0,
                'approved_by' => $user->id,
                'approved_at' => $physicalCountDate->copy()->addDays(2),
                'notes' => 'Annual physical inventory count - Full warehouse audit completed',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $physicalCountDate,
                'updated_at' => $physicalCountDate->copy()->addDays(3),
                'deleted_at' => null,
            ];
        }

        // Insert semua data
        foreach ($adjustments as $adjustment) {
            StockAdjustment::create($adjustment);
        }

        $this->command->info('Stock Adjustment seeder berhasil dijalankan! Total: ' . count($adjustments) . ' records');
    }

    /**
     * Generate notes based on adjustment type and reason
     */
    private function generateNotes(string $adjustmentType, string $reason, string $status): ?string
    {
        if ($status === 'cancelled') {
            return 'Adjustment cancelled - ' . ['Incorrect data', 'Duplicate entry', 'System error'][rand(0, 2)];
        }

        $notes = match($reason) {
            'damaged' => [
                'Damaged during handling - Items marked for disposal',
                'Physical damage identified during inspection',
                'Forklift accident - Products damaged beyond repair',
                'Water damage from roof leak in storage area',
            ],
            'expired' => [
                'Products past expiry date - Removed from inventory',
                'Quarterly expiration check completed',
                'Fast expiring items identified and segregated',
                'Expiry date validation - Items expired',
            ],
            'lost' => [
                'Items missing during cycle count',
                'Discrepancy identified - Unable to locate items',
                'Shrinkage identified during audit',
                'Investigation ongoing for missing inventory',
            ],
            'found' => [
                'Items found during warehouse reorganization',
                'Previously unrecorded items discovered',
                'Misplaced items located and added to inventory',
                'Items found in incorrect location during audit',
            ],
            'count_correction' => [
                'Cycle count adjustment - System quantity corrected',
                'Physical count variance reconciliation',
                'Inventory accuracy improvement initiative',
                'Regular stock verification adjustment',
            ],
            default => 'Stock adjustment processed'
        };

        return $notes[array_rand($notes)];
    }
}