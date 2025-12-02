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
        Schema::create('transport_payments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('bill_id')->nullable();
            $table->string('payment_number')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'cheque', 'bank_transfer', 'online', 'card', 'other'])->default('cash');
            $table->string('payment_type')->nullable(); // e.g., 'monthly_fare', 'route_fare'
            $table->string('transaction_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('cheque_number')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->enum('status', ['success', 'pending', 'failed', 'cancelled'])->default('success');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('collected_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('bill_id')->references('id')->on('transport_bills')->onDelete('set null');
            $table->foreign('collected_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'bill_id']);
            $table->index(['tenant_id', 'payment_date']);
            $table->index(['tenant_id', 'status']);
            $table->index('payment_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_payments');
    }
};
