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
        Schema::create('hostel_fees', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('hostel_id');
            $table->string('fee_type'); // e.g., 'monthly', 'semester', 'annual', 'security_deposit'
            $table->string('name'); // e.g., 'Monthly Hostel Fee', 'Security Deposit'
            $table->decimal('amount', 10, 2);
            $table->enum('frequency', ['one_time', 'monthly', 'quarterly', 'semester', 'annual'])->default('monthly');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('cascade');
            $table->index(['tenant_id', 'hostel_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_fees');
    }
};

