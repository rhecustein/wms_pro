<?php
// database/migrations/2024_01_01_000016_create_inbound_shipment_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_shipment_id')->constrained('inbound_shipments')->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained('purchase_order_items')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            
            // Quantity
            $table->decimal('quantity_expected', 15, 2);
            $table->decimal('quantity_received', 15, 2)->default(0);
            $table->decimal('quantity_rejected', 15, 2)->default(0);
            $table->decimal('quantity_accepted', 15, 2)->storedAs('quantity_received - quantity_rejected');
            $table->foreignId('unit_id')->constrained('units');
            
            // Batch & Serial
            $table->string('batch_number')->nullable();
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->json('serial_numbers')->nullable(); // For serialized products
            
            // Location
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations')->nullOnDelete();
            
            // Quality Control
            $table->enum('quality_status', ['pending', 'passed', 'failed', 'quarantine'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('qc_notes')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['inbound_shipment_id', 'product_id']);
            $table->index('batch_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_shipment_items');
    }
};