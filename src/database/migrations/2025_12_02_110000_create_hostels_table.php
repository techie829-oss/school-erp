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
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('name');
            $table->text('address')->nullable();
            $table->integer('capacity')->default(0);
            $table->integer('available_beds')->default(0);
            $table->unsignedBigInteger('warden_id')->nullable(); // Teacher/Staff ID
            $table->string('contact_number')->nullable();
            $table->text('description')->nullable();
            $table->enum('gender', ['male', 'female', 'mixed'])->default('mixed');
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('warden_id')->references('id')->on('teachers')->onDelete('set null');
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostels');
    }
};

