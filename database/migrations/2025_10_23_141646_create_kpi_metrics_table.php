<?php
// database/migrations/2024_01_01_000041_create_kpi_metrics_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->date('metric_date');
            $table->enum('metric_type', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->decimal('order_fulfillment_rate', 5, 2)->nullable();
            $table->decimal('picking_accuracy_rate', 5, 2)->nullable();
            $table->decimal('on_time_shipment_rate', 5, 2)->nullable();
            $table->decimal('inventory_accuracy_rate', 5, 2)->nullable();
            $table->decimal('space_utilization_rate', 5, 2)->nullable();
            $table->decimal('dock_door_utilization_rate', 5, 2)->nullable();
            $table->decimal('labor_productivity_rate', 5, 2)->nullable();
            $table->decimal('cost_per_order', 15, 2)->nullable();
            $table->decimal('average_order_cycle_time', 8, 2)->nullable();
            $table->decimal('stock_turnover_ratio', 8, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['warehouse_id', 'metric_date', 'metric_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_metrics');
    }
};