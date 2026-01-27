<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating Demo Tenant...');

        // 1. Create Demo Tenant
        $tenant = Tenant::firstOrCreate(
            ['id' => 'demo-school'],
            [
                'data' => [
                    'name' => 'Demo Public School',
                    'email' => 'admin@demo.com',
                    'type' => 'school',
                    'database_strategy' => 'shared',
                    'subdomain' => 'demo',
                    'full_domain' => 'demo.myschool.test', // This will likely be overridden by app logic or Nginx
                    'custom_domain' => null,
                    'active' => true,
                    'description' => 'Demo school for testing and showcase',
                    'student_count' => 500,
                    'location' => 'Demo City, Cloud',
                    'established' => '2025',
                    'curriculum' => 'CBSE',
                    'is_active' => true,
                ]
            ]
        );

        $this->command->info("Tenant '{$tenant->name}' created/found.");

        // 2. Create Admin User for this Tenant
        $this->command->info('Creating Admin User...');
        $admin = AdminUser::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'admin_type' => AdminUser::TYPE_SCHOOL_ADMIN,
                'is_active' => true,
                'notes' => 'Admin for Demo Tenant',
            ]
        );

        $this->command->info("Admin '{$admin->email}' created with password 'password'.");

        // 3. Run Complete School Seeder
        // This relies on Tenant::first() returning our demo tenant, which is true if DB was empty
        $this->command->info('Seeding Demo School Data...');
        $this->call(CompleteSchoolSeeder::class);
    }
}
