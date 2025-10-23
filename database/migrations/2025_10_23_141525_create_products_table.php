<?php
// database/migrations/2024_01_01_000009_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->enum('unit_of_measure', ['pcs', 'box', 'pallet', 'kg', 'liter'])->default('pcs');
            $table->decimal('weight_kg', 10, 2)->nullable();
            $table->decimal('length_cm', 8, 2)->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->decimal('height_cm', 8, 2)->nullable();
            $table->enum('packaging_type', ['drum', 'carton', 'pallet', 'bag', 'bulk'])->default('carton');
            $table->boolean('is_batch_tracked')->default(false);
            $table->boolean('is_serial_tracked')->default(false);
            $table->boolean('is_expiry_tracked')->default(false);
            $table->integer('shelf_life_days')->nullable();
            $table->integer('reorder_level')->nullable();
            $table->integer('reorder_quantity')->nullable();
            $table->integer('min_stock_level')->nullable();
            $table->integer('max_stock_level')->nullable();
            $table->boolean('is_hazmat')->default(false);
            $table->decimal('temperature_min', 5, 2)->nullable();
            $table->decimal('temperature_max', 5, 2)->nullable();
            //current_stock will be managed in stock movements
            
            $table->decimal('current_stock', 15, 2)->default(0);
            $table->decimal('stock_value', 15, 2)->default(0);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};