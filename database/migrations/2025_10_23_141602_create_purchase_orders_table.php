<?php
// database/migrations/2024_01_01_000013_create_purchase_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique()->comment('PO-00001');
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            
            // Dates
            $table->date('po_date');
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            
            // Status & Payment
            $table->enum('status', ['draft', 'submitted', 'approved', 'confirmed', 'partial_received', 'received', 'completed', 'cancelled'])->default('draft');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->string('payment_terms')->nullable()->comment('Net 30, Cash, etc');
            $table->integer('payment_due_days')->default(30);
            
            // Financial
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('other_cost', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('IDR');
            
            // Shipping Info
            $table->text('shipping_address')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            
            // Reference
            $table->string('reference_number')->nullable();
            $table->string('supplier_invoice_number')->nullable();
            
            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('po_date');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['warehouse_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};