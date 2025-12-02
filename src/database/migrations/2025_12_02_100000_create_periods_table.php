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
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->integer('period_number');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->enum('break_type', ['none', 'short_break', 'lunch_break', 'assembly'])->default('none');
            $table->string('name')->nullable(); // e.g., "Period 1", "Lunch Break"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'period_number']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};

