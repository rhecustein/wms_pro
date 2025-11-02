<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Setting key identifier');
            $table->text('value')->nullable()->comment('Setting value (can store JSON, string, number, etc)');
            $table->enum('type', ['string', 'integer', 'boolean', 'json', 'text', 'file'])->default('string')->comment('Data type of the value');
            $table->string('group', 50)->default('general')->comment('Setting group/category');
            $table->text('description')->nullable()->comment('Description of the setting');
            $table->boolean('is_public')->default(false)->comment('Can be accessed publicly');
            $table->boolean('is_editable')->default(true)->comment('Can be edited from UI');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes for better performance
            $table->index('group');
            $table->index('is_public');
        });

        // Insert default settings
        $this->insertDefaultSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }

    /**
     * Insert default system settings
     */
    private function insertDefaultSettings(): void
    {
        $now = now();
        
        DB::table('settings')->insert([
            // Application Settings
            [
                'key' => 'app_name',
                'value' => 'WMS Pro',
                'type' => 'string',
                'group' => 'application',
                'description' => 'Application name displayed throughout the system',
                'is_public' => true,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'app_description',
                'value' => 'Professional Warehouse Management System',
                'type' => 'text',
                'group' => 'application',
                'description' => 'Application description',
                'is_public' => true,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'type' => 'file',
                'group' => 'application',
                'description' => 'Application logo path',
                'is_public' => true,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'app_favicon',
                'value' => null,
                'type' => 'file',
                'group' => 'application',
                'description' => 'Application favicon path',
                'is_public' => true,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'company_name',
                'value' => 'Your Company Name',
                'type' => 'string',
                'group' => 'application',
                'description' => 'Company name',
                'is_public' => true,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'company_address',
                'value' => null,
                'type' => 'text',
                'group' => 'application',
                'description' => 'Company address',
                'is_public' => true,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'company_phone',
                'value' => null,
                'type' => 'string',
                'group' => 'application',
                'description' => 'Company phone number',
                'is_public' => true,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'company_email',
                'value' => null,
                'type' => 'string',
                'group' => 'application',
                'description' => 'Company email address',
                'is_public' => true,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Warehouse Settings
            [
                'key' => 'default_warehouse_id',
                'value' => '1',
                'type' => 'integer',
                'group' => 'warehouse',
                'description' => 'Default warehouse ID',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_multi_warehouse',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'warehouse',
                'description' => 'Enable multi-warehouse functionality',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_barcode_scanning',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'warehouse',
                'description' => 'Enable barcode scanning feature',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'default_stock_method',
                'value' => 'FIFO',
                'type' => 'string',
                'group' => 'warehouse',
                'description' => 'Default stock method (FIFO, LIFO, FEFO)',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Inventory Settings
            [
                'key' => 'low_stock_threshold',
                'value' => '10',
                'type' => 'integer',
                'group' => 'inventory',
                'description' => 'Low stock alert threshold',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_negative_stock',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Allow negative stock levels',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_batch_tracking',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Enable batch/lot tracking',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_serial_tracking',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Enable serial number tracking',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_expiry_tracking',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Enable expiry date tracking',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Notification Settings
            [
                'key' => 'enable_email_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable email notifications',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_low_stock_alerts',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable low stock alert notifications',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_expiry_alerts',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable expiry date alert notifications',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'expiry_alert_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'notifications',
                'description' => 'Days before expiry to send alerts',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Report Settings
            [
                'key' => 'default_date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'group' => 'report',
                'description' => 'Default date format for reports',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'default_time_format',
                'value' => 'H:i:s',
                'type' => 'string',
                'group' => 'report',
                'description' => 'Default time format for reports',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'default_currency',
                'value' => 'USD',
                'type' => 'string',
                'group' => 'report',
                'description' => 'Default currency code',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'items_per_page',
                'value' => '15',
                'type' => 'integer',
                'group' => 'report',
                'description' => 'Default items per page in tables',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Security Settings
            [
                'key' => 'enable_2fa',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Enable two-factor authentication',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'session_lifetime',
                'value' => '120',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Session lifetime in minutes',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Minimum password length',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Mobile App Settings
            [
                'key' => 'enable_mobile_app',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'mobile',
                'description' => 'Enable mobile app functionality',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_offline_mode',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'mobile',
                'description' => 'Enable offline mode for mobile app',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Integration Settings
            [
                'key' => 'enable_api',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'integration',
                'description' => 'Enable REST API',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'api_rate_limit',
                'value' => '60',
                'type' => 'integer',
                'group' => 'integration',
                'description' => 'API rate limit per minute',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // System Settings
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable maintenance mode',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_debug_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable debug mode',
                'is_public' => false,
                'is_editable' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'timezone',
                'value' => 'UTC',
                'type' => 'string',
                'group' => 'system',
                'description' => 'System timezone',
                'is_public' => false,
                'is_editable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
};