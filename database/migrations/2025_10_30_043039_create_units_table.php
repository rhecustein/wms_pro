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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Kilogram, Piece, Box
            $table->string('short_code', 10)->unique(); // KG, PC, BX
            $table->string('type')->default('base'); // base, volume, weight, length
            $table->decimal('base_unit_conversion', 15, 4)->default(1); // Conversion to base unit
            $table->foreignId('base_unit_id')->nullable()->constrained('units')->nullOnDelete(); // Reference to base unit
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};