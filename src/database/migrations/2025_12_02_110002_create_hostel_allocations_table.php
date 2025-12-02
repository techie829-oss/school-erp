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
        Schema::create('hostel_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('hostel_id');
            $table->unsignedBigInteger('room_id');
            $table->integer('bed_number')->nullable();
            $table->date('allocation_date');
            $table->date('release_date')->nullable();
            $table->enum('status', ['active', 'released', 'transferred'])->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('allocated_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('hostel_rooms')->onDelete('cascade');
            $table->foreign('allocated_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['student_id', 'status'], 'unique_active_allocation');
            $table->index(['tenant_id', 'hostel_id', 'status']);
            $table->index(['room_id', 'bed_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_allocations');
    }
};

