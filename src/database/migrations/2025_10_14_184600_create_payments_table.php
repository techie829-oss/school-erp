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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('payment_number')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'cheque', 'card', 'upi', 'net_banking', 'online', 'demand_draft'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('success');
            $table->json('gateway_response')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('collected_by')->nullable(); // User who collected
            $table->timestamps();

            // Foreign Keys
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
            $table->foreign('collected_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('tenant_id');
            $table->index('student_id');
            $table->index('payment_number');
            $table->index('status');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
