<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop the global unique constraint on admission_number
            $table->dropUnique(['admission_number']);
        });

        // Add tenant-scoped unique constraint
        // Using raw SQL because Laravel doesn't support composite unique constraints directly
        DB::statement('ALTER TABLE students ADD UNIQUE KEY unique_tenant_admission_number (tenant_id, admission_number)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop the tenant-scoped unique constraint
            DB::statement('ALTER TABLE students DROP INDEX unique_tenant_admission_number');

            // Restore global unique constraint
            $table->unique('admission_number');
        });
    }
};
