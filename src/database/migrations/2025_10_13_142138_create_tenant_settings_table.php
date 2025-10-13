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
        Schema::create('tenant_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('setting_key')->index();
            $table->text('setting_value')->nullable();
            $table->string('setting_type')->default('string'); // string, boolean, json, integer, file
            $table->string('group')->default('general'); // general, features, academic, branding
            $table->boolean('is_public')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();

            // Composite unique key for tenant_id + setting_key
            $table->unique(['tenant_id', 'setting_key']);

            // Foreign key to tenants table
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_settings');
    }
};
