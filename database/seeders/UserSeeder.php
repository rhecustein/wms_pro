<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin User
        $superAdminId = DB::table('users')->insertGetId([
            'name' => 'Super Administrator',
            'email' => 'superadmin@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7890',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Warehouse Manager
        $warehouseManagerId = DB::table('users')->insertGetId([
            'name' => 'John Manager',
            'email' => 'manager@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7891',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $superAdminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Warehouse Supervisor
        $supervisorId = DB::table('users')->insertGetId([
            'name' => 'Sarah Supervisor',
            'email' => 'supervisor@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7892',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $superAdminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Receiving Staff
        $receivingStaffId = DB::table('users')->insertGetId([
            'name' => 'Mike Receiver',
            'email' => 'receiver@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7893',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $warehouseManagerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Warehouse Operator
        $operatorId = DB::table('users')->insertGetId([
            'name' => 'David Operator',
            'email' => 'operator@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7894',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $warehouseManagerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Picker
        $pickerId = DB::table('users')->insertGetId([
            'name' => 'Tom Picker',
            'email' => 'picker@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7895',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $supervisorId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Packer
        $packerId = DB::table('users')->insertGetId([
            'name' => 'Lisa Packer',
            'email' => 'packer@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7896',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $supervisorId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dispatcher
        $dispatcherId = DB::table('users')->insertGetId([
            'name' => 'James Dispatcher',
            'email' => 'dispatcher@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7897',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $supervisorId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Inventory Controller
        $inventoryControllerId = DB::table('users')->insertGetId([
            'name' => 'Anna Controller',
            'email' => 'inventory@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7898',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $warehouseManagerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Quality Control
        $qcId = DB::table('users')->insertGetId([
            'name' => 'Robert QC',
            'email' => 'qc@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7899',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $warehouseManagerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Customer Service
        $csId = DB::table('users')->insertGetId([
            'name' => 'Emily CS',
            'email' => 'cs@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7800',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $superAdminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Purchasing Staff
        $purchasingId = DB::table('users')->insertGetId([
            'name' => 'Daniel Purchasing',
            'email' => 'purchasing@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7801',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $superAdminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Finance Staff
        $financeId = DB::table('users')->insertGetId([
            'name' => 'Rachel Finance',
            'email' => 'finance@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7802',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $superAdminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Reporting Analyst
        $analystId = DB::table('users')->insertGetId([
            'name' => 'Chris Analyst',
            'email' => 'analyst@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7803',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $superAdminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Driver
        $driverId = DB::table('users')->insertGetId([
            'name' => 'Mark Driver',
            'email' => 'driver@wms.com',
            'password' => Hash::make('password'),
            'phone' => '+62812-3456-7804',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_by' => $supervisorId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign roles to users
        $userRoles = [
            ['user_id' => $superAdminId, 'role_id' => 1], // Super Admin
            ['user_id' => $warehouseManagerId, 'role_id' => 2], // Warehouse Manager
            ['user_id' => $supervisorId, 'role_id' => 3], // Warehouse Supervisor
            ['user_id' => $receivingStaffId, 'role_id' => 4], // Receiving Staff
            ['user_id' => $operatorId, 'role_id' => 5], // Warehouse Operator
            ['user_id' => $pickerId, 'role_id' => 6], // Picker
            ['user_id' => $packerId, 'role_id' => 7], // Packer
            ['user_id' => $dispatcherId, 'role_id' => 8], // Dispatcher
            ['user_id' => $inventoryControllerId, 'role_id' => 9], // Inventory Controller
            ['user_id' => $qcId, 'role_id' => 10], // Quality Control
            ['user_id' => $csId, 'role_id' => 11], // Customer Service
            ['user_id' => $purchasingId, 'role_id' => 12], // Purchasing Staff
            ['user_id' => $financeId, 'role_id' => 13], // Finance Staff
            ['user_id' => $analystId, 'role_id' => 14], // Reporting Analyst
            ['user_id' => $driverId, 'role_id' => 15], // Driver
        ];

        foreach ($userRoles as $userRole) {
            DB::table('user_roles')->insert([
                'user_id' => $userRole['user_id'],
                'role_id' => $userRole['role_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}