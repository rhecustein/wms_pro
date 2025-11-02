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
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('system_name')->unique(); // SAP, Xero, TPL-API
            $table->string('type'); // ERP, Finance, Logistics, Accounting
            $table->string('api_endpoint');
            $table->string('api_key')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('access_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'error', 'maintenance'])->default('inactive');
            $table->text('configuration')->nullable(); // JSON config
            $table->text('last_sync_message')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->integer('sync_frequency_minutes')->default(60); // Auto sync every X minutes
            $table->boolean('auto_sync_enabled')->default(false);
            $table->text('notes')->nullable();
            
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
        Schema::dropIfExists('integrations');
    }
};