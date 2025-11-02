<?php

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
        Schema::create('inbounds', function (Blueprint $table) {
            $table->id();
            $table->string('inbound_number')->unique(); // INB-2025-001
            $table->string('reference_number')->nullable(); // PO Number, DO Number, etc
            
            // Relations
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('integration_id')->nullable()->constrained('integrations')->nullOnDelete();
            
            // Dates
            $table->date('inbound_date');
            $table->date('expected_date')->nullable();
            $table->timestamp('received_at')->nullable();
            
            // Status
            $table->enum('status', [
                'draft',
                'pending',
                'in_progress',
                'received',
                'completed',
                'cancelled'
            ])->default('draft');
            
            $table->enum('type', [
                'purchase_order',
                'return',
                'transfer',
                'production',
                'adjustment',
                'other'
            ])->default('purchase_order');
            
            // Amounts
            $table->decimal('total_quantity', 15, 2)->default(0);
            $table->decimal('received_quantity', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            
            // Shipping Information
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            
            // Documents
            $table->string('document_file')->nullable(); // PDF, Image
            $table->json('attachments')->nullable();
            
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Received By
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('inbound_date');
            $table->index('status');
            $table->index('warehouse_id');
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbounds');
    }
};