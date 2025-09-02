<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Internal/Admin Tenant (Shared Database)
        Tenant::create([
            'id' => 'internal',
            'data' => [
                'name' => 'Internal Admin',
                'domain' => 'app.myschool.test',
                'database_strategy' => 'shared',
                'database' => 'school_erp',
                'status' => 'active',
                'type' => 'internal',
                'description' => 'Internal administration and super admin access',
            ],
        ]);

        // 2. School A (Shared Database)
        Tenant::create([
            'id' => 'school-a',
            'data' => [
                'name' => 'Delhi Public School',
                'domain' => 'schoola.myschool.test',
                'database_strategy' => 'shared',
                'database' => 'school_erp',
                'status' => 'active',
                'type' => 'school',
                'description' => 'Premium school using shared database',
                'student_count' => 1200,
                'location' => 'Delhi, India',
            ],
        ]);

        // 3. School B (Separate Database)
        Tenant::create([
            'id' => 'school-b',
            'data' => [
                'name' => 'Mumbai International School',
                'domain' => 'schoolb.myschool.test',
                'database_strategy' => 'separate',
                'database' => 'school_erp_school_b',
                'status' => 'active',
                'type' => 'school',
                'description' => 'International school with separate database',
                'student_count' => 800,
                'location' => 'Mumbai, India',
            ],
        ]);

        // 4. School C (Separate Database)
        Tenant::create([
            'id' => 'school-c',
            'data' => [
                'name' => 'Bangalore Tech Academy',
                'domain' => 'schoolc.myschool.test',
                'database_strategy' => 'separate',
                'database' => 'school_erp_school_c',
                'status' => 'active',
                'type' => 'school',
                'description' => 'Technology-focused academy with separate database',
                'student_count' => 600,
                'location' => 'Bangalore, India',
            ],
        ]);

        // 5. Landing Page Tenant (Shared Database)
        Tenant::create([
            'id' => 'landing',
            'data' => [
                'name' => 'Landing Page',
                'domain' => 'myschool.test',
                'database_strategy' => 'shared',
                'database' => 'school_erp',
                'status' => 'active',
                'type' => 'landing',
                'description' => 'Marketing and landing page tenant',
            ],
        ]);
    }
}
