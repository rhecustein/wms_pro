<?php
// database/migrations/2024_01_01_000028_create_return_orders_table.php

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
        Schema::create('return_orders', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique()->comment('RET-20241201-0001');
            
            // Related Orders
            $table->foreignId('delivery_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_order_id')->nullable()->constrained()->nullOnDelete();
            
            // Warehouse & Customer
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            
            // Return Details
            $table->dateTime('return_date');
            $table->enum('return_type', ['customer_return', 'damaged', 'expired', 'wrong_item'])
                ->default('customer_return')
                ->comment('Type of return');
            $table->enum('status', ['pending', 'received', 'inspected', 'restocked', 'disposed', 'cancelled'])
                ->default('pending')
                ->comment('Current status of return order');
            
            // Summary
            $table->integer('total_items')->default(0)->comment('Total number of different items');
            $table->integer('total_quantity')->default(0)->comment('Total quantity of all items');
            
            // Inspection Details
            $table->foreignId('inspected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('inspected_at')->nullable();
            $table->enum('disposition', ['restock', 'quarantine', 'dispose', 'rework'])
                ->nullable()
                ->comment('Decision after inspection');
            
            // Refund Information
            $table->decimal('refund_amount', 15, 2)->default(0)->comment('Total refund amount');
            $table->enum('refund_status', ['pending', 'approved', 'processed', 'rejected'])
                ->default('pending')
                ->comment('Refund processing status');
            $table->dateTime('refund_processed_at')->nullable();
            
            // Receiving Information
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('received_at')->nullable();
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable()->comment('Reason if refund rejected');
            
            // Audit Trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('return_number');
            $table->index('status');
            $table->index('return_type');
            $table->index('return_date');
            $table->index(['warehouse_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_orders');
    }
};