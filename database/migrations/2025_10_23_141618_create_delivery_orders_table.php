<?php
// database/migrations/2024_01_01_000026_create_delivery_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('do_number')->unique()->comment('DO-00001');
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('packing_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->dateTime('delivery_date');
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['prepared', 'loaded', 'in_transit', 'delivered', 'returned', 'cancelled'])->default('prepared');
            $table->integer('total_boxes')->default(0);
            $table->decimal('total_weight_kg', 10, 2)->default(0);
            $table->text('shipping_address')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->dateTime('loaded_at')->nullable();
            $table->dateTime('departed_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->string('received_by_name')->nullable();
            $table->text('received_by_signature')->nullable();
            $table->string('delivery_proof_image')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};