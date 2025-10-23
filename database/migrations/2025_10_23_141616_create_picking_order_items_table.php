<?php
// database/migrations/2024_01_01_000022_create_picking_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('picking_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('picking_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('storage_bin_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity_requested');
            $table->integer('quantity_picked')->default(0);
            $table->string('unit_of_measure');
            $table->integer('pick_sequence')->default(0);
            $table->enum('status', ['pending', 'picked', 'short', 'cancelled'])->default('pending');
            $table->foreignId('picked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('picked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picking_order_items');
    }
};