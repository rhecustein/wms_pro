<?php
// database/migrations/2024_01_01_000017_create_good_receiving_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('good_receiving_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('good_receiving_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity_expected')->default(0);
            $table->integer('quantity_received')->default(0);
            $table->integer('quantity_accepted')->default(0);
            $table->integer('quantity_rejected')->default(0);
            $table->string('unit_of_measure');
            $table->foreignId('pallet_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('quality_status', ['passed', 'failed', 'pending'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('good_receiving_items');
    }
};