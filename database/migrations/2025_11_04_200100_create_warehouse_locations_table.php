<?php
// database/migrations/xxxx_xx_xx_create_warehouse_locations_table.php

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
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            
            // Location Code & Name
            $table->string('code')->unique()->comment('A-01-01-01');
            $table->string('name');
            $table->text('description')->nullable();
            
            // Location Hierarchy
            $table->string('zone')->nullable()->comment('Zone A, B, C');
            $table->string('aisle')->nullable()->comment('Aisle 01, 02');
            $table->string('rack')->nullable()->comment('Rack 01, 02');
            $table->string('shelf')->nullable()->comment('Shelf 01, 02');
            $table->string('bin')->nullable()->comment('Bin A, B, C');
            $table->integer('level')->nullable()->comment('Floor level');
            
            // Location Type
            $table->enum('type', [
                'storage',           // Regular storage
                'picking',           // Picking location
                'receiving',         // Receiving area
                'shipping',          // Shipping area
                'staging',           // Staging area
                'quarantine',        // Quality check area
                'return',            // Return area
                'damaged',           // Damaged goods
                'packing',           // Packing area
                'bulk'               // Bulk storage
            ])->default('storage');
            
            // Location Properties
            $table->enum('temperature_zone', ['ambient', 'chilled', 'frozen'])->default('ambient');
            $table->boolean('is_hazmat')->default(false)->comment('Hazardous materials');
            $table->boolean('requires_certification')->default(false);
            
            // Capacity
            $table->decimal('max_weight', 10, 2)->nullable()->comment('Max weight in KG');
            $table->decimal('max_volume', 10, 2)->nullable()->comment('Max volume in M3');
            $table->integer('max_pallets')->nullable();
            $table->decimal('length', 10, 2)->nullable()->comment('Length in CM');
            $table->decimal('width', 10, 2)->nullable()->comment('Width in CM');
            $table->decimal('height', 10, 2)->nullable()->comment('Height in CM');
            
            // Current Usage
            $table->decimal('current_weight', 10, 2)->default(0);
            $table->decimal('current_volume', 10, 2)->default(0);
            $table->integer('current_pallets')->default(0);
            $table->decimal('occupancy_rate', 5, 2)->default(0)->comment('Percentage');
            
            // Access & Priority
            $table->enum('access_type', ['ground', 'forklift', 'crane', 'manual'])->default('manual');
            $table->integer('pick_priority')->default(0)->comment('Lower number = higher priority');
            $table->boolean('is_pick_face')->default(false);
            
            // Status & Restrictions
            $table->boolean('is_active')->default(true);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_mixed_products')->default(false)->comment('Allow multiple products');
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance', 'blocked'])->default('available');
            $table->text('blocked_reason')->nullable();
            
            // Barcode & QR
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            
            // GPS Coordinates (for outdoor locations)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            // Parent Location (for nested locations)
            $table->foreignId('parent_location_id')->nullable()->constrained('warehouse_locations')->nullOnDelete();
            
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['warehouse_id', 'zone', 'aisle']);
            $table->index(['warehouse_id', 'type']);
            $table->index(['warehouse_id', 'status']);
            $table->index('is_active');
            $table->index('pick_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
    }
};