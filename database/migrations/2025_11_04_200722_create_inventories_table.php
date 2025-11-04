<?php
// database/migrations/xxxx_xx_xx_create_inventories_table.php

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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            
            // Quantity Management
            $table->decimal('quantity_on_hand', 15, 2)->default(0)->comment('Physical stock');
            $table->decimal('quantity_available', 15, 2)->default(0)->comment('Available for sale');
            $table->decimal('quantity_reserved', 15, 2)->default(0)->comment('Reserved for orders');
            $table->decimal('quantity_allocated', 15, 2)->default(0)->comment('Allocated to picking');
            $table->decimal('quantity_in_transit', 15, 2)->default(0)->comment('On the way');
            $table->decimal('quantity_damaged', 15, 2)->default(0)->comment('Damaged stock');
            $table->decimal('quantity_quarantine', 15, 2)->default(0)->comment('Quality check');
            
            // Unit
            $table->foreignId('unit_id')->constrained('units');
            
            // Batch & Serial Tracking
            $table->string('batch_number')->nullable()->index();
            $table->string('lot_number')->nullable()->index();
            $table->string('serial_number')->nullable()->unique();
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('best_before_date')->nullable();
            
            // Costing (for inventory valuation)
            $table->decimal('unit_cost', 15, 2)->default(0)->comment('Average/FIFO cost');
            $table->decimal('total_cost', 15, 2)->default(0)->comment('Total inventory value');
            $table->enum('costing_method', ['fifo', 'lifo', 'average', 'specific'])->default('fifo');
            
            // Stock Status
            $table->enum('stock_status', [
                'in_stock',
                'low_stock',
                'out_of_stock',
                'overstock',
                'discontinued'
            ])->default('in_stock');
            
            // Quality Status
            $table->enum('quality_status', [
                'good',
                'damaged',
                'expired',
                'quarantine',
                'returned'
            ])->default('good');
            
            // Physical Properties (for storage optimization)
            $table->decimal('weight', 10, 2)->nullable()->comment('Total weight in KG');
            $table->decimal('volume', 10, 2)->nullable()->comment('Total volume in M3');
            $table->integer('pallet_count')->default(0);
            
            // Supplier Information
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier_batch_number')->nullable();
            
            // Last Activity
            $table->dateTime('last_received_at')->nullable();
            $table->dateTime('last_picked_at')->nullable();
            $table->dateTime('last_counted_at')->nullable();
            $table->dateTime('last_adjusted_at')->nullable();
            
            // Cycle Count
            $table->integer('cycle_count_frequency')->default(30)->comment('Days between counts');
            $table->date('next_count_date')->nullable();
            $table->decimal('count_variance', 15, 2)->default(0)->comment('Last count difference');
            
            // Alerts & Flags
            $table->boolean('is_below_minimum')->default(false);
            $table->boolean('is_near_expiry')->default(false)->comment('Within 30 days');
            $table->boolean('is_expired')->default(false);
            $table->boolean('requires_attention')->default(false);
            
            // Bin/Zone for easy access
            $table->string('bin_location')->nullable()->comment('Quick reference');
            
            $table->text('notes')->nullable();
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['warehouse_id', 'product_id']);
            $table->index(['warehouse_id', 'location_id']);
            $table->index(['product_id', 'batch_number']);
            $table->index(['warehouse_id', 'stock_status']);
            $table->index(['warehouse_id', 'quality_status']);
            $table->index('expiry_date');
            $table->index('next_count_date');
            $table->index(['is_below_minimum', 'is_near_expiry', 'is_expired']);
            
            // Unique constraint: One product per location per batch
            $table->unique(['warehouse_id', 'location_id', 'product_id', 'batch_number'], 'inventory_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};