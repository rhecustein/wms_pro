<?php
// database/migrations/2024_01_01_000035_create_replenishment_tasks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replenishment_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_number')->unique()->comment('REP-00001');
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_storage_bin_id')->constrained('storage_bins')->cascadeOnDelete()->comment('high rack');
            $table->foreignId('to_storage_bin_id')->constrained('storage_bins')->cascadeOnDelete()->comment('pick face');
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('quantity_suggested');
            $table->integer('quantity_moved')->default(0);
            $table->string('unit_of_measure');
            $table->enum('priority', ['urgent', 'high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('trigger_type', ['min_level', 'empty_pick_face', 'manual'])->default('min_level');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replenishment_tasks');
    }
};