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
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            
            // Dates & Times
            $table->dateTime('scheduled_date')->nullable();
            $table->dateTime('shipment_date')->nullable();
            $table->dateTime('arrival_date')->nullable();
            $table->dateTime('unloading_start')->nullable();
            $table->dateTime('unloading_end')->nullable();
            $table->dateTime('completed_at')->nullable();
            
            // Shipment Details
            $table->integer('expected_pallets')->nullable();
            $table->integer('received_pallets')->default(0);
            $table->integer('expected_boxes')->nullable();
            $table->integer('received_boxes')->default(0);
            $table->decimal('expected_weight', 10, 2)->nullable();
            $table->decimal('actual_weight', 10, 2)->nullable();
            
            // Vehicle & Driver Info
            $table->string('vehicle_type')->nullable(); // Truck, Container, etc
            $table->string('vehicle_number')->nullable();
            $table->string('container_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('driver_id_number')->nullable();
            $table->string('seal_number')->nullable();
            
            // Warehouse Operations
            $table->enum('status', ['scheduled', 'in_transit', 'arrived', 'unloading', 'inspection', 'received', 'completed', 'cancelled'])->default('scheduled');
            $table->string('dock_number')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('inspected_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Shipping Documents
            $table->string('bill_of_lading')->nullable();
            $table->string('packing_list')->nullable();
            $table->json('attachments')->nullable(); // Multiple documents
            
            // Quality Check
            $table->enum('inspection_result', ['passed', 'failed', 'partial'])->nullable();
            $table->text('inspection_notes')->nullable();
            $table->boolean('has_damages')->default(false);
            $table->text('damage_description')->nullable();
            
            $table->text('notes')->nullable();
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('arrival_date');
            $table->index(['warehouse_id', 'status']);
            $table->index(['purchase_order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_shipments');
    }
};