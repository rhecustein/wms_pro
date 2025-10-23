<?php
// database/migrations/2024_01_01_000024_create_packing_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packing_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('picking_order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('quantity_packed');
            $table->string('box_number')->nullable();
            $table->decimal('box_weight_kg', 10, 2)->nullable();
            $table->foreignId('packed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('packed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing_order_items');
    }
};