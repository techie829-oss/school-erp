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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('vehicle_number')->unique();
            $table->enum('vehicle_type', ['bus', 'van', 'car', 'auto', 'other'])->default('bus');
            $table->string('make')->nullable(); // e.g., Tata, Ashok Leyland
            $table->string('model')->nullable();
            $table->year('manufacturing_year')->nullable();
            $table->integer('capacity')->default(0); // Number of seats
            $table->string('color')->nullable();
            $table->string('registration_number')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('permit_expiry')->nullable();
            $table->date('fitness_expiry')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'retired'])->default('active');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('set null');
            
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'vehicle_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
