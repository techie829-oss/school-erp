<?php

namespace Database\Seeders\Demo;

use App\Models\Department;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first tenant (or you can specify your tenant ID)
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->command->error('No tenant found! Please create a tenant first.');
            return;
        }

        $this->command->info("Creating departments for tenant: {$tenant->id}");

        $departments = [
            [
                'department_name' => 'Science',
                'department_code' => 'SCI',
                'description' => 'Department of Science - Physics, Chemistry, Biology',
            ],
            [
                'department_name' => 'Mathematics',
                'department_code' => 'MATH',
                'description' => 'Department of Mathematics and Statistics',
            ],
            [
                'department_name' => 'English',
                'department_code' => 'ENG',
                'description' => 'Department of English Language and Literature',
            ],
            [
                'department_name' => 'Social Studies',
                'department_code' => 'SST',
                'description' => 'Department of Social Studies - History, Geography, Civics',
            ],
            [
                'department_name' => 'Arts',
                'department_code' => 'ART',
                'description' => 'Department of Fine Arts and Performing Arts',
            ],
            [
                'department_name' => 'Physical Education',
                'department_code' => 'PE',
                'description' => 'Department of Physical Education and Sports',
            ],
            [
                'department_name' => 'Computer Science',
                'department_code' => 'CS',
                'description' => 'Department of Computer Science and Information Technology',
            ],
            [
                'department_name' => 'Languages',
                'department_code' => 'LANG',
                'description' => 'Department of Foreign Languages and Regional Languages',
            ],
        ];

        foreach ($departments as $dept) {
            $department = Department::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'department_name' => $dept['department_name'],
                ],
                [
                    'department_code' => $dept['department_code'],
                    'description' => $dept['description'],
                    'is_active' => true,
                ]
            );

            if ($department->wasRecentlyCreated) {
                $this->command->info("✓ Created: {$dept['department_name']}");
            } else {
                $this->command->info("⊘ Exists: {$dept['department_name']}");
            }
        }

        $this->command->info("\n✅ Successfully created " . count($departments) . " departments!");
    }
}

