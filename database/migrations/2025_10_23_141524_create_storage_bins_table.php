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
            $table->boolean('is_occupied')->default(false);
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('storage_area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique(); // AA0101A
            $table->string('aisle', 10); // AA
            $table->string('row', 10); // 01
            $table->string('column', 10); // 01
            $table->string('level', 10); // A
            $table->enum('status', ['available', 'occupied', 'reserved', 'blocked', 'maintenance'])->default('available');
            $table->decimal('max_weight_kg', 10, 2)->nullable();
            $table->decimal('current_weight_kg', 10, 2)->default(0);
            $table->decimal('max_volume_cbm', 10, 2)->nullable();
            $table->decimal('current_volume_cbm', 10, 2)->default(0);
            $table->decimal('current_quantity', 10, 2)->default(0);
            $table->decimal('min_quantity', 10, 2)->default(0);
            $table->decimal('max_quantity', 10, 2)->nullable();
            $table->enum('bin_type', ['pick_face', 'high_rack', 'staging', 'quarantine'])->default('high_rack');
            $table->enum('packaging_restriction', ['none', 'drum', 'carton', 'pallet'])->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_hazmat')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['warehouse_id', 'status']);
            $table->index(['aisle', 'row', 'column', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_bins');
    }
};