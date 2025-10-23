<?php
// database/migrations/2024_01_01_000037_create_transfer_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_storage_bin_id')->nullable()->constrained('storage_bins')->nullOnDelete();
            $table->foreignId('to_storage_bin_id')->nullable()->constrained('storage_bins')->nullOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('quantity_requested');
            $table->integer('quantity_shipped')->default(0);
            $table->integer('quantity_received')->default(0);
            $table->string('unit_of_measure');
            $table->enum('status', ['pending', 'shipped', 'received'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_order_items');
    }
};