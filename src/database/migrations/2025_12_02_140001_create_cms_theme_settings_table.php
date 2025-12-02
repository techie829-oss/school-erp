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
        Schema::create('cms_theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            
            // Primary Colors
            $table->string('primary_color_50')->default('#eff6ff');
            $table->string('primary_color_100')->default('#dbeafe');
            $table->string('primary_color_500')->default('#3b82f6');
            $table->string('primary_color_600')->default('#2563eb');
            $table->string('primary_color_700')->default('#1d4ed8');
            $table->string('primary_color_900')->default('#1e3a8a');
            
            // Secondary Colors
            $table->string('secondary_color_50')->default('#f8fafc');
            $table->string('secondary_color_100')->default('#f1f5f9');
            $table->string('secondary_color_500')->default('#64748b');
            $table->string('secondary_color_600')->default('#475569');
            $table->string('secondary_color_700')->default('#334155');
            $table->string('secondary_color_900')->default('#0f172a');
            
            // Accent Colors
            $table->string('accent_color_50')->default('#fef3c7');
            $table->string('accent_color_100')->default('#fde68a');
            $table->string('accent_color_500')->default('#f59e0b');
            $table->string('accent_color_600')->default('#d97706');
            $table->string('accent_color_700')->default('#b45309');
            $table->string('accent_color_900')->default('#78350f');
            
            // Status Colors
            $table->string('success_color')->default('#10b981');
            $table->string('warning_color')->default('#f59e0b');
            $table->string('error_color')->default('#ef4444');
            $table->string('info_color')->default('#3b82f6');
            
            // Custom CSS
            $table->text('custom_css')->nullable();
            
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_theme_settings');
    }
};

