<?php
// database/migrations/2024_01_01_000036_create_transfer_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_orders', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique()->comment('TRF-00001');
            $table->foreignId('from_warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('to_warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->enum('transfer_type', ['inter_warehouse', 'internal_bin', 'consolidation'])->default('inter_warehouse');
            $table->dateTime('transfer_date');
            $table->dateTime('expected_arrival_date')->nullable();
            $table->enum('status', ['draft', 'approved', 'in_transit', 'received', 'completed', 'cancelled'])->default('draft');
            $table->integer('total_items')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_orders');
    }
};