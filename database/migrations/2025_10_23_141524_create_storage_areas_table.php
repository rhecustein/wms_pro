<?php
// database/migrations/2024_01_01_000005_create_storage_areas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('code')->comment('SPR-001');
            $table->string('name');
            $table->enum('type', ['spr', 'bulky', 'quarantine', 'staging_1', 'staging_2', 'virtual']);
            $table->decimal('area_sqm', 10, 2)->nullable();
            $table->decimal('height_meters', 5, 2)->nullable();
            $table->integer('capacity_pallets')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['warehouse_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_areas');
    }
};