<?php
// database/migrations/2024_01_01_000027_create_delivery_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('quantity_delivered');
            $table->integer('quantity_returned')->default(0);
            $table->string('unit_of_measure');
            $table->enum('condition', ['good', 'damaged', 'returned'])->default('good');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_order_items');
    }
};