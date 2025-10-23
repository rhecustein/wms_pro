<?php
// database/migrations/2024_01_01_000018_create_putaway_tasks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('putaway_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_number')->unique()->comment('PUT-00001');
            $table->foreignId('good_receiving_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('quantity');
            $table->string('unit_of_measure');
            $table->string('from_location')->comment('staging area');
            $table->foreignId('to_storage_bin_id')->nullable()->constrained('storage_bins')->nullOnDelete();
            $table->foreignId('pallet_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->boolean('suggested_by_system')->default(true);
            $table->string('packaging_type')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('putaway_tasks');
    }
};