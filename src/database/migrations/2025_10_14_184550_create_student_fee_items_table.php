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
        Schema::create('student_fee_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_fee_card_id');
            $table->unsignedBigInteger('fee_component_id');
            $table->decimal('original_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_reason')->nullable();
            $table->decimal('net_amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', ['unpaid', 'partial', 'paid', 'waived'])->default('unpaid');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('student_fee_card_id')->references('id')->on('student_fee_cards')->onDelete('cascade');
            $table->foreign('fee_component_id')->references('id')->on('fee_components')->onDelete('restrict');

            // Indexes
            $table->index('student_fee_card_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fee_items');
    }
};
