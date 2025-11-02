<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique(); // Stock Keeping Unit
            $table->string('barcode')->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Category & Brand
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('brand')->nullable();
            
            // Unit
            $table->foreignId('unit_id')->constrained('units');
            
            // Supplier
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            
            // Pricing
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('minimum_selling_price', 15, 2)->default(0);
            
            // Stock Management
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->integer('current_stock')->default(0);
            
            // Physical Properties
            $table->decimal('weight', 10, 2)->nullable(); // in KG
            $table->decimal('length', 10, 2)->nullable(); // in CM
            $table->decimal('width', 10, 2)->nullable(); // in CM
            $table->decimal('height', 10, 2)->nullable(); // in CM
            
            // Tax
            $table->boolean('is_taxable')->default(true);
            $table->decimal('tax_rate', 5, 2)->default(0); // Percentage
            
            // Status
            $table->enum('type', ['raw_material', 'finished_goods', 'spare_parts', 'consumable'])->default('finished_goods');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_serialized')->default(false); // Track by serial number
            $table->boolean('is_batch_tracked')->default(false); // Track by batch/lot
            
            // Images
            $table->string('image')->nullable();
            $table->json('images')->nullable(); // Multiple images
            
            $table->text('notes')->nullable();
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};