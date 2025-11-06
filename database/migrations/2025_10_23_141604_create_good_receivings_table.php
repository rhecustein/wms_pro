<?php
// database/migrations/2024_01_01_000016_create_good_receivings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('good_receivings', function (Blueprint $table) {
            $table->id();
            $table->string('gr_number')->unique()->comment('GR-00001');
            $table->foreignId('inbound_shipment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->dateTime('receiving_date');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['draft', 'in_progress', 'quality_check', 'completed', 'partial', 'cancelled'])->default('draft');
            $table->integer('total_items')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->integer('total_pallets')->default(0);
            $table->enum('quality_status', ['pending', 'passed', 'failed', 'partial'])->default('pending');
            $table->foreignId('quality_checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('quality_checked_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('good_receivings');
    }
};