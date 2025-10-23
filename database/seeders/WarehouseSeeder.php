<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user untuk manager dan created_by
        $adminUser = User::first();
        $managerId = $adminUser ? $adminUser->id : null;

        $warehouses = [
            [
                'code' => 'WH001',
                'name' => 'Gudang Pusat Jakarta',
                'address' => 'Jl. Raya Bekasi KM 28 No. 123',
                'city' => 'Jakarta Timur',
                'province' => 'DKI Jakarta',
                'postal_code' => '13920',
                'country' => 'Indonesia',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'phone' => '021-12345678',
                'email' => 'wh001@example.com',
                'manager_id' => $managerId,
                'total_area_sqm' => 5000.00,
                'height_meters' => 8.00,
                'is_active' => true,
                'created_by' => $managerId,
            ],
            [
                'code' => 'WH002',
                'name' => 'Gudang Surabaya',
                'address' => 'Jl. Industri Raya No. 45',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60177',
                'country' => 'Indonesia',
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'phone' => '031-87654321',
                'email' => 'wh002@example.com',
                'manager_id' => $managerId,
                'total_area_sqm' => 3500.00,
                'height_meters' => 7.50,
                'is_active' => true,
                'created_by' => $managerId,
            ],
            [
                'code' => 'WH003',
                'name' => 'Gudang Bandung',
                'address' => 'Jl. Soekarno Hatta No. 789',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40286',
                'country' => 'Indonesia',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'phone' => '022-23456789',
                'email' => 'wh003@example.com',
                'manager_id' => $managerId,
                'total_area_sqm' => 2800.00,
                'height_meters' => 6.50,
                'is_active' => true,
                'created_by' => $managerId,
            ],
            [
                'code' => 'WH004',
                'name' => 'Gudang Medan',
                'address' => 'Jl. Gatot Subroto KM 7',
                'city' => 'Medan',
                'province' => 'Sumatera Utara',
                'postal_code' => '20122',
                'country' => 'Indonesia',
                'latitude' => 3.5952,
                'longitude' => 98.6722,
                'phone' => '061-34567890',
                'email' => 'wh004@example.com',
                'manager_id' => $managerId,
                'total_area_sqm' => 4200.00,
                'height_meters' => 7.00,
                'is_active' => true,
                'created_by' => $managerId,
            ],
            [
                'code' => 'WH005',
                'name' => 'Gudang Semarang',
                'address' => 'Jl. Perintis Kemerdekaan No. 56',
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
                'postal_code' => '50149',
                'country' => 'Indonesia',
                'latitude' => -6.9932,
                'longitude' => 110.4203,
                'phone' => '024-45678901',
                'email' => 'wh005@example.com',
                'manager_id' => $managerId,
                'total_area_sqm' => 3000.00,
                'height_meters' => 6.00,
                'is_active' => false, // Non-aktif sebagai contoh
                'created_by' => $managerId,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}