<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Users Management
            'users.view', 'users.create', 'users.edit', 'users.delete',
            
            // Roles Management
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            
            // Warehouses Management
            'warehouses.view', 'warehouses.create', 'warehouses.edit', 'warehouses.delete',
            
            // Storage Areas Management
            'storage_areas.view', 'storage_areas.create', 'storage_areas.edit', 'storage_areas.delete',
            
            // Storage Bins Management
            'storage_bins.view', 'storage_bins.create', 'storage_bins.edit', 'storage_bins.delete',
            
            // Products Management
            'products.view', 'products.create', 'products.edit', 'products.delete',
            
            // Customers Management
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            
            // Vendors Management
            'vendors.view', 'vendors.create', 'vendors.edit', 'vendors.delete',
            
            // Purchase Orders Management
            'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 
            'purchase_orders.delete', 'purchase_orders.approve',
            
            // Sales Orders Management
            'sales_orders.view', 'sales_orders.create', 'sales_orders.edit', 
            'sales_orders.delete', 'sales_orders.approve',
            
            // Inventory Management
            'inventory.view', 'inventory.create', 'inventory.edit', 
            'inventory.delete', 'inventory.adjust',
            
            // Good Receiving Management
            'good_receiving.view', 'good_receiving.create', 'good_receiving.edit', 
            'good_receiving.delete', 'good_receiving.approve',
            
            // Putaway Management
            'putaway.view', 'putaway.create', 'putaway.edit', 
            'putaway.delete', 'putaway.assign',
            
            // Picking Management
            'picking.view', 'picking.create', 'picking.edit', 
            'picking.delete', 'picking.assign',
            
            // Packing Management
            'packing.view', 'packing.create', 'packing.edit', 'packing.delete',
            
            // Delivery Management
            'delivery.view', 'delivery.create', 'delivery.edit', 'delivery.delete',
            
            // Returns Management
            'returns.view', 'returns.create', 'returns.edit', 
            'returns.delete', 'returns.approve',
            
            // Transfers Management
            'transfers.view', 'transfers.create', 'transfers.edit', 
            'transfers.delete', 'transfers.approve',
            
            // Replenishment Management
            'replenishment.view', 'replenishment.create', 'replenishment.edit', 'replenishment.delete',
            
            // Stock Opname Management
            'stock_opname.view', 'stock_opname.create', 'stock_opname.edit', 
            'stock_opname.delete', 'stock_opname.approve',
            
            // Stock Adjustment Management
            'stock_adjustment.view', 'stock_adjustment.create', 'stock_adjustment.edit', 
            'stock_adjustment.delete', 'stock_adjustment.approve',
            
            // Reports Management
            'reports.view', 'reports.export',
            
            // Settings Management
            'settings.view', 'settings.edit',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Define roles with their permissions
        $roles = [
            [
                'name' => 'Super Admin',
                'permissions' => $permissions, // All permissions
            ],
            [
                'name' => 'Warehouse Manager',
                'permissions' => [
                    'users.view',
                    'warehouses.view', 'warehouses.edit',
                    'storage_areas.view', 'storage_areas.create', 'storage_areas.edit',
                    'storage_bins.view', 'storage_bins.create', 'storage_bins.edit',
                    'products.view', 'products.create', 'products.edit',
                    'customers.view',
                    'vendors.view',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.approve',
                    'sales_orders.view', 'sales_orders.create', 'sales_orders.edit', 'sales_orders.approve',
                    'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.adjust',
                    'good_receiving.view', 'good_receiving.create', 'good_receiving.edit', 'good_receiving.approve',
                    'putaway.view', 'putaway.create', 'putaway.edit', 'putaway.assign',
                    'picking.view', 'picking.create', 'picking.edit', 'picking.assign',
                    'packing.view', 'packing.create', 'packing.edit',
                    'delivery.view', 'delivery.create', 'delivery.edit',
                    'returns.view', 'returns.create', 'returns.edit', 'returns.approve',
                    'transfers.view', 'transfers.create', 'transfers.edit', 'transfers.approve',
                    'replenishment.view', 'replenishment.create', 'replenishment.edit',
                    'stock_opname.view', 'stock_opname.create', 'stock_opname.edit', 'stock_opname.approve',
                    'stock_adjustment.view', 'stock_adjustment.create', 'stock_adjustment.edit', 'stock_adjustment.approve',
                    'reports.view', 'reports.export',
                ],
            ],
            [
                'name' => 'Warehouse Supervisor',
                'permissions' => [
                    'warehouses.view',
                    'storage_areas.view',
                    'storage_bins.view', 'storage_bins.edit',
                    'products.view',
                    'customers.view',
                    'vendors.view',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit',
                    'sales_orders.view', 'sales_orders.create', 'sales_orders.edit',
                    'inventory.view', 'inventory.edit',
                    'good_receiving.view', 'good_receiving.create', 'good_receiving.edit',
                    'putaway.view', 'putaway.create', 'putaway.edit', 'putaway.assign',
                    'picking.view', 'picking.create', 'picking.edit', 'picking.assign',
                    'packing.view', 'packing.create', 'packing.edit',
                    'delivery.view', 'delivery.create', 'delivery.edit',
                    'returns.view', 'returns.create', 'returns.edit',
                    'transfers.view', 'transfers.create', 'transfers.edit',
                    'replenishment.view', 'replenishment.create', 'replenishment.edit',
                    'stock_opname.view', 'stock_opname.create', 'stock_opname.edit',
                    'stock_adjustment.view', 'stock_adjustment.create', 'stock_adjustment.edit',
                    'reports.view',
                ],
            ],
            [
                'name' => 'Receiving Staff',
                'permissions' => [
                    'warehouses.view',
                    'storage_bins.view',
                    'products.view',
                    'vendors.view',
                    'purchase_orders.view',
                    'inventory.view',
                    'good_receiving.view', 'good_receiving.create', 'good_receiving.edit',
                    'putaway.view',
                    'returns.view', 'returns.create',
                    'reports.view',
                ],
            ],
            [
                'name' => 'Warehouse Operator',
                'permissions' => [
                    'warehouses.view',
                    'storage_bins.view',
                    'products.view',
                    'inventory.view',
                    'putaway.view', 'putaway.edit',
                    'replenishment.view', 'replenishment.edit',
                    'transfers.view', 'transfers.edit',
                ],
            ],
            [
                'name' => 'Picker',
                'permissions' => [
                    'warehouses.view',
                    'storage_bins.view',
                    'products.view',
                    'inventory.view',
                    'sales_orders.view',
                    'picking.view', 'picking.edit',
                ],
            ],
            [
                'name' => 'Packer',
                'permissions' => [
                    'warehouses.view',
                    'products.view',
                    'sales_orders.view',
                    'picking.view',
                    'packing.view', 'packing.create', 'packing.edit',
                ],
            ],
            [
                'name' => 'Dispatcher',
                'permissions' => [
                    'warehouses.view',
                    'customers.view',
                    'sales_orders.view',
                    'packing.view',
                    'delivery.view', 'delivery.create', 'delivery.edit',
                    'returns.view', 'returns.create',
                    'reports.view',
                ],
            ],
            [
                'name' => 'Inventory Controller',
                'permissions' => [
                    'warehouses.view',
                    'storage_bins.view',
                    'products.view',
                    'inventory.view', 'inventory.edit', 'inventory.adjust',
                    'stock_opname.view', 'stock_opname.create', 'stock_opname.edit',
                    'stock_adjustment.view', 'stock_adjustment.create', 'stock_adjustment.edit',
                    'reports.view', 'reports.export',
                ],
            ],
            [
                'name' => 'Quality Control',
                'permissions' => [
                    'warehouses.view',
                    'products.view',
                    'vendors.view',
                    'good_receiving.view', 'good_receiving.edit',
                    'returns.view', 'returns.edit',
                    'reports.view',
                ],
            ],
            [
                'name' => 'Customer Service',
                'permissions' => [
                    'warehouses.view',
                    'products.view',
                    'customers.view', 'customers.create', 'customers.edit',
                    'sales_orders.view', 'sales_orders.create', 'sales_orders.edit',
                    'inventory.view',
                    'delivery.view',
                    'returns.view', 'returns.create',
                    'reports.view',
                ],
            ],
            [
                'name' => 'Purchasing Staff',
                'permissions' => [
                    'warehouses.view',
                    'products.view', 'products.create', 'products.edit',
                    'vendors.view', 'vendors.create', 'vendors.edit',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit',
                    'inventory.view',
                    'reports.view',
                ],
            ],
            [
                'name' => 'Finance Staff',
                'permissions' => [
                    'purchase_orders.view',
                    'sales_orders.view',
                    'delivery.view',
                    'returns.view',
                    'reports.view', 'reports.export',
                ],
            ],
            [
                'name' => 'Reporting Analyst',
                'permissions' => [
                    'warehouses.view',
                    'products.view',
                    'customers.view',
                    'vendors.view',
                    'inventory.view',
                    'purchase_orders.view',
                    'sales_orders.view',
                    'reports.view', 'reports.export',
                ],
            ],
            [
                'name' => 'Driver',
                'permissions' => [
                    'delivery.view', 'delivery.edit',
                    'returns.view', 'returns.create',
                ],
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleData) {
            $role = Role::create(['name' => $roleData['name']]);
            $role->givePermissionTo($roleData['permissions']);
        }

        $this->command->info('Roles and Permissions seeded successfully!');
    }
}