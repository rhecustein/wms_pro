<?php
// database/migrations/2024_01_01_000039_create_equipment_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_number')->unique()->comment('EQ-001');
            $table->enum('equipment_type', ['forklift', 'reach_truck', 'pallet_jack', 'scanner']);
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['available', 'in_use', 'maintenance', 'damaged', 'inactive'])->default('available');
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->integer('operating_hours')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};