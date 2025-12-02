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
        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hostel_id');
            $table->string('room_number');
            $table->enum('room_type', ['single', 'double', 'triple', 'dormitory'])->default('dormitory');
            $table->integer('capacity');
            $table->integer('available_beds');
            $table->string('floor')->nullable();
            $table->text('facilities')->nullable(); // JSON or text
            $table->enum('status', ['available', 'occupied', 'maintenance', 'reserved'])->default('available');
            $table->timestamps();

            $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('cascade');
            $table->unique(['hostel_id', 'room_number']);
            $table->index(['hostel_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_rooms');
    }
};

