<?php
// database/migrations/2024_01_01_000029_create_return_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('return_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            
            // Item Identification
            $table->string('batch_number')->nullable()->comment('Batch/Lot number');
            $table->string('serial_number')->nullable()->comment('Serial number for tracking');
            
            // Quantities
            $table->integer('quantity_returned')->default(0)->comment('Quantity being returned');
            $table->integer('quantity_restocked')->default(0)->comment('Quantity restocked to inventory');
            $table->integer('quantity_disposed')->default(0)->comment('Quantity disposed/destroyed');
            
            // Item Condition & Disposition
            $table->enum('condition', ['good', 'damaged', 'expired', 'defective'])
                ->default('good')
                ->comment('Physical condition of returned item');
            $table->enum('disposition', ['restock', 'quarantine', 'dispose', 'rework', 'return_to_supplier'])
                ->nullable()
                ->comment('Action decided after inspection');
            
            // Location Tracking
            $table->foreignId('restocked_to_bin_id')->nullable()->constrained('storage_bins')->nullOnDelete();
            $table->foreignId('quarantine_bin_id')->nullable()->constrained('storage_bins')->nullOnDelete();
            
            // Return Details
            $table->text('return_reason')->nullable()->comment('Reason for return');
            $table->text('inspection_notes')->nullable()->comment('Notes from inspection');
            $table->text('notes')->nullable()->comment('Additional notes');
            
            // Pricing (for refund calculation)
            $table->decimal('unit_price', 15, 2)->default(0)->comment('Original unit price');
            $table->decimal('refund_amount', 15, 2)->default(0)->comment('Refund amount for this item');
            
            // Timestamps
            $table->timestamp('inspected_at')->nullable();
            $table->timestamp('restocked_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('return_order_id');
            $table->index('product_id');
            $table->index('batch_number');
            $table->index('serial_number');
            $table->index('condition');
            $table->index('disposition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_order_items');
    }
};