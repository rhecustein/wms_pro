<?php
// database/migrations/2024_01_01_000007_create_storage_bins_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_bins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_area_id')->constrained()->cascadeOnDelete();
            $table->string('bin_code')->unique()->comment('AA0101C');
            $table->string('aisle', 2)->comment('AA');
            $table->string('row', 2)->comment('01');
            $table->string('column', 2)->comment('01');
            $table->string('level', 1)->comment('C: A,B,C,D,E');
            $table->enum('bin_type', ['standard', 'pick_face', 'high_rack', 'bulk', 'quarantine'])->default('standard');
            $table->decimal('max_weight_kg', 10, 2)->nullable();
            $table->integer('max_pallets')->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->decimal('depth_cm', 8, 2)->nullable();
            $table->decimal('height_cm', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_occupied')->default(false);
            $table->integer('current_stock_qty')->default(0);
            $table->enum('packaging_type_restriction', ['drum', 'carton', 'pallet', 'any'])->default('any');
            $table->foreignId('customer_restriction_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->boolean('temperature_controlled')->default(false);
            $table->boolean('hazmat_approved')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_bins');
    }
};