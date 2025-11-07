<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        
        DB::table('settings')->insert([
            // License Settings
            [
                'key' => 'purchase_code',
                'value' => null,
                'type' => 'string',
                'group' => 'license',
                'description' => 'CodeCanyon purchase code',
                'is_public' => false,
                'is_editable' => false,
                'order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'buyer_email',
                'value' => null,
                'type' => 'email',
                'group' => 'license',
                'description' => 'Buyer email from Envato',
                'is_public' => false,
                'is_editable' => false,
                'order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'license_type',
                'value' => 'regular',
                'type' => 'string',
                'group' => 'license',
                'description' => 'License type (regular, extended)',
                'is_public' => false,
                'is_editable' => false,
                'order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'installed_at',
                'value' => null,
                'type' => 'string',
                'group' => 'license',
                'description' => 'Installation date and time',
                'is_public' => false,
                'is_editable' => false,
                'order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'installed_domain',
                'value' => null,
                'type' => 'url',
                'group' => 'license',
                'description' => 'Domain where app is installed',
                'is_public' => false,
                'is_editable' => false,
                'order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Additional System Settings
            [
                'key' => 'app_version',
                'value' => '1.0.0',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Current application version',
                'is_public' => false,
                'is_editable' => false,
                'order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_activity_log',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable activity logging',
                'is_public' => false,
                'is_editable' => true,
                'order' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Backup Settings
            [
                'key' => 'auto_backup_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'backup',
                'description' => 'Enable automatic backups',
                'is_public' => false,
                'is_editable' => true,
                'order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Document Settings
            [
                'key' => 'invoice_prefix',
                'value' => 'INV',
                'type' => 'string',
                'group' => 'document',
                'description' => 'Invoice number prefix',
                'is_public' => false,
                'is_editable' => true,
                'order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'purchase_code',
            'buyer_email',
            'license_type',
            'installed_at',
            'installed_domain',
            'app_version',
            'enable_activity_log',
            'auto_backup_enabled',
            'invoice_prefix',
        ])->delete();
    }
};