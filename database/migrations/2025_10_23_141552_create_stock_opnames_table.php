<?php
// database/migrations/2024_01_01_000033_create_stock_opnames_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number')->unique()->comment('OPN-00001');
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('storage_area_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('opname_date');
            $table->enum('opname_type', ['full', 'cycle', 'spot'])->default('cycle');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->foreignId('scheduled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('total_items_planned')->default(0);
            $table->integer('total_items_counted')->default(0);
            $table->integer('variance_count')->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};