<?php
// database/migrations/2024_01_01_000034_create_stock_opname_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('storage_bin_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('system_quantity');
            $table->integer('physical_quantity')->nullable();
            $table->integer('variance')->nullable();
            $table->decimal('variance_value', 15, 2)->nullable();
            $table->enum('status', ['pending', 'counted', 'recounted', 'adjusted'])->default('pending');
            $table->foreignId('counted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('counted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};