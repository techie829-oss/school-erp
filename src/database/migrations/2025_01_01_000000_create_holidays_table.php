<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            // Keep tenant_id type consistent with existing tenant-scoped tables (string slug)
            $table->string('tenant_id');
            $table->date('date');
            $table->string('title');
            $table->string('type')->nullable(); // national, school, exam, etc.
            $table->boolean('is_full_day')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};


