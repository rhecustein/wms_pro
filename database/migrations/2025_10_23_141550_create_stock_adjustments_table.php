<?php
// database/migrations/2024_01_01_000031_create_stock_adjustments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique()->comment('ADJ-00001');
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->dateTime('adjustment_date');
            $table->enum('adjustment_type', ['addition', 'reduction', 'correction'])->default('correction');
            $table->enum('reason', ['damaged', 'expired', 'lost', 'found', 'count_correction'])->default('count_correction');
            $table->enum('status', ['draft', 'approved', 'posted', 'cancelled'])->default('draft');
            $table->integer('total_items')->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};