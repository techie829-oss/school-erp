<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('holiday_scopes', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('holiday_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'holiday_id']);

            $table->foreign('holiday_id')->references('id')->on('holidays')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_scopes');
    }
};


