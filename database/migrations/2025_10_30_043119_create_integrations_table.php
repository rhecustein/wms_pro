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
            $table->string('system_name')->unique(); // Contoh: SAP, Xero, TPL-API
            $table->string('type'); // Contoh: ERP, Finance, Logistics
            $table->string('api_endpoint');
            $table->string('api_key')->nullable();
            $table->string('client_secret')->nullable();
            $table->enum('status', ['active', 'inactive', 'error'])->default('inactive');
            $table->text('last_sync_message')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
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