<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main super admin user (no tenant_id - global access)
        AdminUser::create([
            'name' => 'Super Admin',
            'email' => 'admin@myschool.test',
            'password' => Hash::make('password'),
            'tenant_id' => null, // Global access
            'admin_type' => AdminUser::TYPE_SUPER_ADMIN,
            'is_active' => true,
            'notes' => 'Main system administrator with global access',
        ]);

        // Create super manager user (no tenant_id - global access)
        AdminUser::create([
            'name' => 'Super Manager',
            'email' => 'manager@myschool.test',
            'password' => Hash::make('password'),
            'tenant_id' => null, // Global access
            'admin_type' => AdminUser::TYPE_SUPER_MANAGER,
            'is_active' => true,
            'notes' => 'System manager with limited global access',
        ]);

        // Create school admin for Delhi Public School
        AdminUser::create([
            'name' => 'Rajesh Kumar',
            'email' => 'admin@dps.edu.in',
            'password' => Hash::make('password'),
            'tenant_id' => 'delhi-public-school-250904',
            'admin_type' => AdminUser::TYPE_SCHOOL_ADMIN,
            'is_active' => true,
            'notes' => 'Principal and Administrator for Delhi Public School',
        ]);

        // Create school admin for Mumbai International School
        AdminUser::create([
            'name' => 'Priya Sharma',
            'email' => 'admin@mis.edu.in',
            'password' => Hash::make('password'),
            'tenant_id' => 'mumbai-international-school-250904',
            'admin_type' => AdminUser::TYPE_SCHOOL_ADMIN,
            'is_active' => true,
            'notes' => 'Principal and Administrator for Mumbai International School',
        ]);

        // Create school admin for Bangalore Tech Academy
        AdminUser::create([
            'name' => 'Arjun Patel',
            'email' => 'admin@bta.edu.in',
            'password' => Hash::make('password'),
            'tenant_id' => 'bangalore-tech-academy-250904',
            'admin_type' => AdminUser::TYPE_SCHOOL_ADMIN,
            'is_active' => true,
            'notes' => 'Principal and Administrator for Bangalore Tech Academy',
        ]);

        // Create school admin for Chennai Central School
        AdminUser::create([
            'name' => 'Suresh Reddy',
            'email' => 'admin@ccs.edu.in',
            'password' => Hash::make('password'),
            'tenant_id' => 'chennai-central-school-250904',
            'admin_type' => AdminUser::TYPE_SCHOOL_ADMIN,
            'is_active' => true,
            'notes' => 'Principal and Administrator for Chennai Central School',
        ]);

        // Create college admin for Pune Engineering College
        AdminUser::create([
            'name' => 'Dr. Meera Joshi',
            'email' => 'admin@pec.edu.in',
            'password' => Hash::make('password'),
            'tenant_id' => 'pune-engineering-college-250904',
            'admin_type' => AdminUser::TYPE_SCHOOL_ADMIN,
            'is_active' => true,
            'notes' => 'Director and Administrator for Pune Engineering College',
        ]);

        // Create university admin for Hyderabad University
        AdminUser::create([
            'name' => 'Prof. Ravi Kumar',
            'email' => 'admin@hu.edu.in',
            'password' => Hash::make('password'),
            'tenant_id' => 'hyderabad-university-250904',
            'admin_type' => AdminUser::TYPE_SCHOOL_ADMIN,
            'is_active' => true,
            'notes' => 'Vice Chancellor and Administrator for Hyderabad University',
        ]);

        // Create additional school admin for Delhi Public School (Deputy Principal)
        AdminUser::create([
            'name' => 'Anita Singh',
            'email' => 'deputy@dps.edu.in',
            'password' => Hash::make('password'),
            'tenant_id' => 'delhi-public-school-250904',
            'admin_type' => AdminUser::TYPE_SCHOOL_ADMIN,
            'is_active' => true,
            'notes' => 'Deputy Principal for Delhi Public School',
        ]);

        // Create additional school admin for Mumbai International School (Academic Head)
        AdminUser::create([
            'name' => 'Dr. Vikram Mehta',
            'email' => 'academic@mis.edu.in',
            'password' => Hash::make('password'),
            'tenant_id' => 'mumbai-international-school-250904',
            'admin_type' => AdminUser::TYPE_SCHOOL_ADMIN,
            'is_active' => true,
            'notes' => 'Academic Head for Mumbai International School',
        ]);
    }
}
