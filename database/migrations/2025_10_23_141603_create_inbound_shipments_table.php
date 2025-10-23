<?php
// database/migrations/2024_01_01_000015_create_inbound_shipments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique()->comment('ISH-00001');
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->dateTime('arrival_date')->nullable();
            $table->integer('expected_pallets')->nullable();
            $table->integer('received_pallets')->default(0);
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('seal_number')->nullable();
            $table->enum('status', ['scheduled', 'arrived', 'unloading', 'received', 'completed'])->default('scheduled');
            $table->string('dock_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_shipments');
    }
};