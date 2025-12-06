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
        Schema::table('teachers', function (Blueprint $table) {
            // Drop the global unique constraint on employee_id
            $table->dropUnique(['employee_id']);
        });

        // Add tenant-scoped unique constraint
        // Using raw SQL because Laravel doesn't support composite unique constraints directly
        DB::statement('ALTER TABLE teachers ADD UNIQUE KEY unique_tenant_employee_id (tenant_id, employee_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Drop the tenant-scoped unique constraint
            DB::statement('ALTER TABLE teachers DROP INDEX unique_tenant_employee_id');

            // Restore global unique constraint
            $table->unique('employee_id');
        });
    }
};
