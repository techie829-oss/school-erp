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
        // This migration updates existing tenants to include platform_type in data JSON
        // Default platform_type will be 'school'

        $tenants = DB::table('tenants')->get();

        foreach ($tenants as $tenant) {
            $data = json_decode($tenant->data, true) ?? [];

            // Add platform_type if not exists
            if (!isset($data['platform_type'])) {
                $data['platform_type'] = 'school'; // default: school, college, both
            }

            // Add logo if not exists
            if (!isset($data['logo'])) {
                $data['logo'] = null;
            }

            // Add contact info if not exists
            if (!isset($data['contact_email'])) {
                $data['contact_email'] = null;
            }

            if (!isset($data['contact_phone'])) {
                $data['contact_phone'] = null;
            }

            if (!isset($data['address'])) {
                $data['address'] = null;
            }

            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['data' => json_encode($data)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove platform_type from data JSON
        $tenants = DB::table('tenants')->get();

        foreach ($tenants as $tenant) {
            $data = json_decode($tenant->data, true) ?? [];

            unset($data['platform_type']);
            unset($data['logo']);
            unset($data['contact_email']);
            unset($data['contact_phone']);
            unset($data['address']);

            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['data' => json_encode($data)]);
        }
    }
};
