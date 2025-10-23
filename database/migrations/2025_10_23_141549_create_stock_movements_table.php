<?php
// database/migrations/2024_01_01_000030_create_stock_movements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_bin_id')->nullable()->constrained('storage_bins')->nullOnDelete();
            $table->foreignId('to_bin_id')->nullable()->constrained('storage_bins')->nullOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('quantity');
            $table->string('unit_of_measure');
            $table->enum('movement_type', ['inbound', 'outbound', 'transfer', 'adjustment', 'putaway', 'picking', 'replenishment']);
            $table->enum('reference_type', ['purchase_order', 'sales_order', 'transfer', 'adjustment'])->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->dateTime('movement_date');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['warehouse_id', 'product_id', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};