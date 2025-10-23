<?php
// database/migrations/2024_01_01_000029_create_return_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('quantity_returned');
            $table->integer('quantity_restocked')->default(0);
            $table->text('return_reason')->nullable();
            $table->enum('condition', ['good', 'damaged', 'expired'])->default('good');
            $table->enum('disposition', ['restock', 'quarantine', 'dispose', 'rework'])->nullable();
            $table->foreignId('restocked_to_bin_id')->nullable()->constrained('storage_bins')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_order_items');
    }
};