<?php
// database/seeders/VehicleSeeder.php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'vehicle_number' => 'VEH-0001',
                'license_plate' => 'B 1234 ABC',
                'vehicle_type' => 'truck',
                'brand' => 'Mitsubishi',
                'model' => 'Canter',
                'year' => 2022,
                'capacity_kg' => 5000,
                'capacity_cbm' => 25,
                'status' => 'available',
                'ownership' => 'owned',
                'odometer_km' => 15000,
                'fuel_type' => 'Diesel',
                'last_maintenance_date' => now()->subDays(30),
                'next_maintenance_date' => now()->addDays(60),
                'notes' => 'Regular maintenance scheduled',
            ],
            [
                'vehicle_number' => 'VEH-0002',
                'license_plate' => 'B 5678 XYZ',
                'vehicle_type' => 'van',
                'brand' => 'Toyota',
                'model' => 'Hiace',
                'year' => 2023,
                'capacity_kg' => 1500,
                'capacity_cbm' => 10,
                'status' => 'in_use',
                'ownership' => 'rented',
                'odometer_km' => 8000,
                'fuel_type' => 'Petrol',
                'last_maintenance_date' => now()->subDays(15),
                'next_maintenance_date' => now()->addDays(75),
                'notes' => 'Rented until December 2025',
            ],
            [
                'vehicle_number' => 'VEH-0003',
                'license_plate' => 'B 9012 DEF',
                'vehicle_type' => 'forklift',
                'brand' => 'Toyota',
                'model' => '8FG25',
                'year' => 2021,
                'capacity_kg' => 2500,
                'status' => 'maintenance',
                'ownership' => 'owned',
                'odometer_km' => 3500,
                'fuel_type' => 'LPG',
                'last_maintenance_date' => now()->subDays(5),
                'next_maintenance_date' => now()->addDays(3),
                'notes' => 'Under maintenance for hydraulic system',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}