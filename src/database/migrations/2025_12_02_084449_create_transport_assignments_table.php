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
        Schema::create('transport_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('route_id');
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('pickup_stop_id')->nullable(); // Pickup stop
            $table->unsignedBigInteger('drop_stop_id')->nullable(); // Drop stop
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('booking_date')->nullable();
            $table->enum('booking_status', ['pending', 'confirmed', 'active', 'cancelled', 'completed'])->default('pending');
            $table->decimal('monthly_fare', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'cancelled'])->default('active');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->foreign('pickup_stop_id')->references('id')->on('route_stops')->onDelete('set null');
            $table->foreign('drop_stop_id')->references('id')->on('route_stops')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'route_id']);
            $table->index(['tenant_id', 'vehicle_id']);
            $table->index(['tenant_id', 'status']);
            $table->index(['booking_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_assignments');
    }
};
