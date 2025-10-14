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
        Schema::create('fee_plan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_plan_id');
            $table->unsignedBigInteger('fee_component_id');
            $table->decimal('amount', 10, 2);
            $table->boolean('is_mandatory')->default(true);
            $table->date('due_date')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('fee_plan_id')->references('id')->on('fee_plans')->onDelete('cascade');
            $table->foreign('fee_component_id')->references('id')->on('fee_components')->onDelete('cascade');

            // Indexes
            $table->index('fee_plan_id');
            $table->index('fee_component_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_plan_items');
    }
};
