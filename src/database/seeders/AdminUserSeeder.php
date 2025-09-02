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
        // Create main super admin user
        AdminUser::create([
            'name' => 'Super Admin',
            'email' => 'admin@myschool.test',
            'password' => Hash::make('password'),
            'tenant_id' => 'internal', // Internal tenant
            'admin_type' => AdminUser::TYPE_SUPER_ADMIN,
            'is_active' => true,
            'notes' => 'Main system administrator',
        ]);

        // Create super manager user
        AdminUser::create([
            'name' => 'Super Manager',
            'email' => 'manager@myschool.test',
            'password' => Hash::make('password'),
            'tenant_id' => 'internal', // Internal tenant
            'admin_type' => AdminUser::TYPE_SUPER_MANAGER,
            'is_active' => true,
            'notes' => 'System manager with limited access',
        ]);

        // Create school admin for School A
        AdminUser::create([
            'name' => 'School A Admin',
            'email' => 'admin@schoola.myschool.test',
            'password' => Hash::make('password'),
            'tenant_id' => 'school-a',
            'admin_type' => AdminUser::TYPE_SCHOOL_ADMIN,
            'is_active' => true,
            'notes' => 'Administrator for Delhi Public School',
        ]);
    }
}
