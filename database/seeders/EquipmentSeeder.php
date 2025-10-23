<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\Warehouse;
use App\Models\User;

class EquipmentSeeder extends Seeder
{
    public function run()
    {
        $warehouses = Warehouse::all();
        $users = User::all();

        if ($warehouses->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Please seed warehouses and users first!');
            return;
        }

        $equipmentTypes = ['forklift', 'reach_truck', 'pallet_jack', 'scanner'];
        $statuses = ['available', 'in_use', 'maintenance', 'damaged', 'inactive'];
        $brands = ['Toyota', 'Crown', 'Yale', 'Hyster', 'Jungheinrich'];

        for ($i = 1; $i <= 50; $i++) {
            $type = $equipmentTypes[array_rand($equipmentTypes)];
            $status = $statuses[array_rand($statuses)];
            $brand = $brands[array_rand($brands)];
            
            $lastMaintenance = now()->subDays(rand(1, 180));
            $nextMaintenance = $lastMaintenance->copy()->addDays(rand(30, 90));

            Equipment::create([
                'equipment_number' => 'EQ-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'equipment_type' => $type,
                'brand' => $brand,
                'model' => strtoupper($brand[0]) . rand(1000, 9999),
                'serial_number' => 'SN' . rand(100000, 999999),
                'warehouse_id' => $warehouses->random()->id,
                'status' => $status,
                'last_maintenance_date' => $lastMaintenance,
                'next_maintenance_date' => $nextMaintenance,
                'operating_hours' => rand(100, 5000),
                'notes' => rand(0, 1) ? 'Regular equipment in good condition' : null,
                'created_by' => $users->random()->id,
                'updated_by' => $users->random()->id,
            ]);
        }

        $this->command->info('Equipment seeded successfully!');
    }
}