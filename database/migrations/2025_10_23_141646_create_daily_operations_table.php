<?php
// database/migrations/2024_01_01_000040_create_daily_operations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->date('operation_date');
            $table->integer('total_inbound_orders')->default(0);
            $table->integer('total_outbound_orders')->default(0);
            $table->integer('total_items_received')->default(0);
            $table->integer('total_items_picked')->default(0);
            $table->integer('total_items_shipped')->default(0);
            $table->integer('total_pallets_moved')->default(0);
            $table->integer('total_replenishments')->default(0);
            $table->integer('total_transfers')->default(0);
            $table->integer('total_adjustments')->default(0);
            $table->decimal('average_picking_time_minutes', 8, 2)->nullable();
            $table->decimal('average_putaway_time_minutes', 8, 2)->nullable();
            $table->decimal('total_staff_hours', 8, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->unique(['warehouse_id', 'operation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_operations');
    }
};