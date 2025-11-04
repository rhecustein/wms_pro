// database/migrations/xxxx_create_storage_bins_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_bins', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('storage_area_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            
            // Location identifiers
            $table->string('code')->unique()->comment('Unique bin code: AA0101A'); // AA0101A
            $table->string('aisle', 10)->comment('Aisle identifier'); // AA
            $table->string('row', 10)->comment('Row number'); // 01
            $table->string('column', 10)->comment('Column number'); // 01
            $table->string('level', 10)->comment('Level/Height identifier'); // A
            
            // Status & Type
            $table->enum('status', ['available', 'occupied', 'reserved', 'blocked', 'maintenance'])->default('available');
            $table->enum('bin_type', ['pick_face', 'high_rack', 'staging', 'quarantine'])->default('high_rack');
            $table->enum('packaging_restriction', ['none', 'drum', 'carton', 'pallet'])->nullable();
            
            // Capacity - Weight
            $table->decimal('max_weight_kg', 10, 2)->nullable()->comment('Maximum weight capacity in KG');
            $table->decimal('current_weight_kg', 10, 2)->default(0)->comment('Current total weight in KG');
            
            // Capacity - Volume
            $table->decimal('max_volume_cbm', 10, 3)->nullable()->comment('Maximum volume capacity in CBM');
            $table->decimal('current_volume_cbm', 10, 3)->default(0)->comment('Current total volume in CBM');
            
            // Capacity - Quantity
            $table->decimal('current_quantity', 10, 2)->default(0)->comment('Current total quantity');
            $table->decimal('min_quantity', 10, 2)->default(0)->comment('Minimum quantity threshold');
            $table->decimal('max_quantity', 10, 2)->nullable()->comment('Maximum quantity capacity');
            
            // Physical dimensions of the bin itself
            $table->decimal('bin_length_cm', 10, 2)->nullable()->comment('Physical length of bin in CM');
            $table->decimal('bin_width_cm', 10, 2)->nullable()->comment('Physical width of bin in CM');
            $table->decimal('bin_height_cm', 10, 2)->nullable()->comment('Physical height of bin in CM');
            
            // Flags
            $table->boolean('is_occupied')->default(false)->comment('Quick check if bin has stock');
            $table->boolean('is_hazmat')->default(false)->comment('For hazardous materials storage');
            $table->boolean('is_active')->default(true)->comment('Active status');
            $table->boolean('is_temperature_controlled')->default(false)->comment('Requires temperature control');
            $table->boolean('is_locked')->default(false)->comment('Locked for counting or maintenance');
            
            // Temperature control (if applicable)
            $table->decimal('min_temperature_c', 5, 2)->nullable()->comment('Minimum temperature in Celsius');
            $table->decimal('max_temperature_c', 5, 2)->nullable()->comment('Maximum temperature in Celsius');
            
            // Additional info
            $table->integer('picking_priority')->default(0)->comment('Priority for picking operations (higher = priority)');
            $table->string('barcode')->nullable()->unique()->comment('Barcode for bin identification');
            $table->string('rfid_tag')->nullable()->unique()->comment('RFID tag for bin identification');
            $table->text('notes')->nullable();
            
            // Audit fields
            $table->timestamp('last_count_date')->nullable()->comment('Last physical count date');
            $table->foreignId('last_count_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_movement_date')->nullable()->comment('Last stock movement date');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['warehouse_id', 'status']);
            $table->index(['warehouse_id', 'is_active']);
            $table->index(['aisle', 'row', 'column', 'level']);
            $table->index(['status', 'is_occupied']);
            $table->index('code');
            $table->index('barcode');
            $table->index('rfid_tag');
            $table->index(['current_weight_kg', 'max_weight_kg']);
            $table->index(['current_volume_cbm', 'max_volume_cbm']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_bins');
    }
};