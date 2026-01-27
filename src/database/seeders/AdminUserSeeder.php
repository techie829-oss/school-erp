<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin (Global System Owner)
        AdminUser::create([
            'name' => 'Super Admin',
            'email' => 'rohitkmarch96@gmail.com',
            'password' => Hash::make('password123'),
            'tenant_id' => null, // Global access
            'admin_type' => AdminUser::TYPE_SUPER_ADMIN,
            'is_active' => true,
            'notes' => 'Main system administrator with global access',
        ]);

        // 2. Super Manager (Global Manager)
        AdminUser::create([
            'name' => 'Super Manager',
            'email' => 'lmprkmkp@gmail.com',
            'password' => Hash::make('password123'),
            'tenant_id' => null, // Global access
            'admin_type' => AdminUser::TYPE_SUPER_MANAGER,
            'is_active' => true,
            'notes' => 'System manager with limited global access',
        ]);

        $this->command->info('Created Global Admins: [EMAIL_ADDRESS] / [EMAIL_ADDRESS]');
    }
}
