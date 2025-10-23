<?php
// database/migrations/2024_01_01_000028_create_return_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_orders', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique()->comment('RET-00001');
            $table->foreignId('delivery_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->dateTime('return_date');
            $table->enum('return_type', ['customer_return', 'damaged', 'expired', 'wrong_item'])->default('customer_return');
            $table->enum('status', ['pending', 'received', 'inspected', 'restocked', 'disposed', 'cancelled'])->default('pending');
            $table->integer('total_items')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->foreignId('inspected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('inspected_at')->nullable();
            $table->enum('disposition', ['restock', 'quarantine', 'dispose', 'rework'])->nullable();
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->enum('refund_status', ['pending', 'approved', 'processed'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_orders');
    }
};