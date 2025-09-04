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
        // 1. Delhi Public School (Shared Database)
        Tenant::create([
            'id' => 'delhi-public-school-250904',
            'data' => [
                'name' => 'Delhi Public School',
                'email' => 'admin@dps.edu.in',
                'type' => 'school',
                'database_strategy' => 'shared',
                'subdomain' => 'dps',
                'full_domain' => 'dps.myschool.test',
                'custom_domain' => null,
                'active' => true,
                'description' => 'Premium school using shared database',
                'student_count' => 1200,
                'location' => 'Delhi, India',
                'established' => '1985',
                'curriculum' => 'CBSE',
            ],
        ]);

        // 2. Mumbai International School (Separate Database)
        Tenant::create([
            'id' => 'mumbai-international-school-250904',
            'data' => [
                'name' => 'Mumbai International School',
                'email' => 'admin@mis.edu.in',
                'type' => 'school',
                'database_strategy' => 'separate',
                'subdomain' => 'mis',
                'full_domain' => 'mis.myschool.test',
                'custom_domain' => null,
                'active' => true,
                'description' => 'International school with separate database',
                'student_count' => 800,
                'location' => 'Mumbai, India',
                'established' => '1992',
                'curriculum' => 'IB',
            ],
        ]);

        // 3. Bangalore Tech Academy (Separate Database)
        Tenant::create([
            'id' => 'bangalore-tech-academy-250904',
            'data' => [
                'name' => 'Bangalore Tech Academy',
                'email' => 'admin@bta.edu.in',
                'type' => 'school',
                'database_strategy' => 'separate',
                'subdomain' => 'bta',
                'full_domain' => 'bta.myschool.test',
                'custom_domain' => null,
                'active' => true,
                'description' => 'Technology-focused academy with separate database',
                'student_count' => 600,
                'location' => 'Bangalore, India',
                'established' => '2000',
                'curriculum' => 'ICSE',
            ],
        ]);

        // 4. Chennai Central School (Shared Database)
        Tenant::create([
            'id' => 'chennai-central-school-250904',
            'data' => [
                'name' => 'Chennai Central School',
                'email' => 'admin@ccs.edu.in',
                'type' => 'school',
                'database_strategy' => 'shared',
                'subdomain' => 'ccs',
                'full_domain' => 'ccs.myschool.test',
                'custom_domain' => null,
                'active' => true,
                'description' => 'Central school with shared database',
                'student_count' => 900,
                'location' => 'Chennai, India',
                'established' => '1988',
                'curriculum' => 'CBSE',
            ],
        ]);

        // 5. Pune Engineering College (Separate Database)
        Tenant::create([
            'id' => 'pune-engineering-college-250904',
            'data' => [
                'name' => 'Pune Engineering College',
                'email' => 'admin@pec.edu.in',
                'type' => 'college',
                'database_strategy' => 'separate',
                'subdomain' => 'pec',
                'full_domain' => 'pec.myschool.test',
                'custom_domain' => null,
                'active' => true,
                'description' => 'Engineering college with separate database',
                'student_count' => 1500,
                'location' => 'Pune, India',
                'established' => '1995',
                'curriculum' => 'Engineering',
            ],
        ]);

        // 6. Hyderabad University (Separate Database)
        Tenant::create([
            'id' => 'hyderabad-university-250904',
            'data' => [
                'name' => 'Hyderabad University',
                'email' => 'admin@hu.edu.in',
                'type' => 'university',
                'database_strategy' => 'separate',
                'subdomain' => 'hu',
                'full_domain' => 'hu.myschool.test',
                'custom_domain' => null,
                'active' => true,
                'description' => 'University with separate database',
                'student_count' => 2500,
                'location' => 'Hyderabad, India',
                'established' => '1980',
                'curriculum' => 'University',
            ],
        ]);

    }
}
