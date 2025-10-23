<?php
// database/migrations/2024_01_01_000038_create_cross_docking_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cross_docking_orders', function (Blueprint $table) {
            $table->id();
            $table->string('cross_dock_number')->unique()->comment('CD-00001');
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inbound_shipment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('outbound_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->string('unit_of_measure');
            $table->enum('status', ['scheduled', 'receiving', 'sorting', 'loading', 'completed', 'cancelled'])->default('scheduled');
            $table->dateTime('scheduled_date');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->string('dock_in')->nullable();
            $table->string('dock_out')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cross_docking_orders');
    }
};