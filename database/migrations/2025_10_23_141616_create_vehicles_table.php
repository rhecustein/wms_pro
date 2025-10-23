<?php
// database/migrations/2024_01_01_000025_create_vehicles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->unique()->comment('VEH-001');
            $table->string('license_plate')->unique();
            $table->enum('vehicle_type', ['truck', 'van', 'forklift', 'reach_truck'])->default('truck');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->year('year')->nullable();
            $table->decimal('capacity_kg', 10, 2)->nullable();
            $table->decimal('capacity_cbm', 10, 2)->nullable();
            $table->enum('status', ['available', 'in_use', 'maintenance', 'inactive'])->default('available');
            $table->enum('ownership', ['owned', 'rented', 'leased'])->default('owned');
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->integer('odometer_km')->default(0);
            $table->string('fuel_type')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};