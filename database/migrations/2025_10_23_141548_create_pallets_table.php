<?php
// database/migrations/2024_01_01_000011_create_pallets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pallets', function (Blueprint $table) {
            $table->id();
            $table->string('pallet_number')->unique()->comment('PLT-00001');
            $table->enum('pallet_type', ['standard', 'euro', 'custom'])->default('standard');
            $table->string('barcode')->unique()->nullable();
            $table->string('qr_code')->unique()->nullable();
            $table->decimal('width_cm', 8, 2)->default(120);
            $table->decimal('depth_cm', 8, 2)->default(120);
            $table->decimal('height_cm', 8, 2)->default(16);
            $table->decimal('max_weight_kg', 10, 2)->default(1200);
            $table->decimal('current_weight_kg', 10, 2)->default(0);
            $table->foreignId('storage_bin_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['empty', 'loaded', 'in_transit', 'damaged'])->default('empty');
            $table->boolean('is_available')->default(true);
            $table->timestamp('last_used_date')->nullable();
            $table->enum('condition', ['good', 'fair', 'poor', 'damaged'])->default('good');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pallets');
    }
};