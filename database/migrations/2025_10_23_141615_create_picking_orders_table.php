<?php
// database/migrations/2024_01_01_000021_create_picking_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('picking_orders', function (Blueprint $table) {
            $table->id();
            $table->string('picking_number')->unique()->comment('PICK-00001');
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->dateTime('picking_date');
            $table->enum('picking_type', ['single_order', 'batch', 'wave', 'zone'])->default('single_order');
            $table->enum('priority', ['urgent', 'high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('total_items')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picking_orders');
    }
};