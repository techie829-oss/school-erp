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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable(); // For tenant-wise filtering
            $table->string('user_type')->default('admin'); // admin, tenant_admin, etc.
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); // created, updated, deleted, logged_in, etc.
            $table->string('model_type')->nullable(); // Model class name
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('properties')->nullable(); // Additional data
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
            $table->index(['user_type', 'user_id']);
            $table->index(['action']);
            $table->index(['model_type', 'model_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
