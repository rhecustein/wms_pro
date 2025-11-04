<?php
// database/migrations/2024_01_01_000014_create_purchase_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            
            // Product Info Snapshot (untuk history jika product berubah)
            $table->string('product_sku')->nullable();
            $table->string('product_name');
            
            // Quantity
            $table->decimal('quantity_ordered', 15, 2);
            $table->decimal('quantity_received', 15, 2)->default(0);
            $table->decimal('quantity_remaining', 15, 2)->storedAs('quantity_ordered - quantity_received');
            $table->foreignId('unit_id')->constrained('units');
            
            // Pricing
            $table->decimal('unit_price', 15, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2); // qty * unit_price
            $table->decimal('line_total', 15, 2); // subtotal + tax - discount
            
            // Quality Control
            $table->decimal('quantity_rejected', 15, 2)->default(0);
            $table->text('rejection_reason')->nullable();
            
            // Batch & Serial Tracking
            $table->string('batch_number')->nullable();
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['purchase_order_id', 'product_id']);
            $table->index('batch_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};