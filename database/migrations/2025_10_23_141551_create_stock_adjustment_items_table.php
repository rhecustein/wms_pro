<?php
// database/migrations/2024_01_01_000032_create_stock_adjustment_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('storage_bin_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('current_quantity');
            $table->integer('adjusted_quantity');
            $table->integer('difference')->virtualAs('adjusted_quantity - current_quantity');
            $table->string('unit_of_measure');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
    }
};