<?php

namespace Database\Seeders\Demo;

use App\Models\Teacher;
use App\Models\Department;
use App\Models\Subject;
use App\Models\Tenant;
use App\Models\TeacherQualification;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first tenant
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->command->error('No tenant found! Please create a tenant first.');
            return;
        }

        $this->command->info("Creating teachers for tenant: {$tenant->id}");

        // Get departments and subjects
        $departments = Department::forTenant($tenant->id)->get();
        $subjects = Subject::forTenant($tenant->id)->get();

        if ($departments->isEmpty()) {
            $this->command->error('No departments found! Run DepartmentSeeder first.');
            return;
        }

        if ($subjects->isEmpty()) {
            $this->command->error('No subjects found! Run SubjectSeeder first.');
            return;
        }

        $teachers = [
            [
                'first_name' => 'Rajesh',
                'last_name' => 'Kumar',
                'gender' => 'male',
                'dob' => '1975-05-15',
                'email' => 'rajesh.kumar@school.com',
                'phone' => '+91 9876543210',
                'department' => 'Mathematics',
                'designation' => 'Head Teacher',
                'qualification' => 'M.Sc Mathematics',
                'experience' => 20,
                'subjects' => ['Mathematics'],
                'employment_type' => 'permanent',
                'salary' => 75000,
            ],
            [
                'first_name' => 'Priya',
                'last_name' => 'Sharma',
                'gender' => 'female',
                'dob' => '1982-08-22',
                'email' => 'priya.sharma@school.com',
                'phone' => '+91 9876543211',
                'department' => 'Science',
                'designation' => 'Senior Teacher',
                'qualification' => 'M.Sc Physics',
                'experience' => 15,
                'subjects' => ['Physics'],
                'employment_type' => 'permanent',
                'salary' => 65000,
            ],
            [
                'first_name' => 'Amit',
                'last_name' => 'Patel',
                'gender' => 'male',
                'dob' => '1985-03-10',
                'email' => 'amit.patel@school.com',
                'phone' => '+91 9876543212',
                'department' => 'Science',
                'designation' => 'Teacher',
                'qualification' => 'M.Sc Chemistry',
                'experience' => 12,
                'subjects' => ['Chemistry'],
                'employment_type' => 'permanent',
                'salary' => 60000,
            ],
            [
                'first_name' => 'Sneha',
                'last_name' => 'Reddy',
                'gender' => 'female',
                'dob' => '1988-11-18',
                'email' => 'sneha.reddy@school.com',
                'phone' => '+91 9876543213',
                'department' => 'Science',
                'designation' => 'Teacher',
                'qualification' => 'M.Sc Biology',
                'experience' => 10,
                'subjects' => ['Biology'],
                'employment_type' => 'permanent',
                'salary' => 58000,
            ],
            [
                'first_name' => 'Arjun',
                'last_name' => 'Singh',
                'gender' => 'male',
                'dob' => '1990-06-25',
                'email' => 'arjun.singh@school.com',
                'phone' => '+91 9876543214',
                'department' => 'English',
                'designation' => 'Teacher',
                'qualification' => 'M.A English',
                'experience' => 8,
                'subjects' => ['English'],
                'employment_type' => 'permanent',
                'salary' => 55000,
            ],
            [
                'first_name' => 'Kavita',
                'last_name' => 'Verma',
                'gender' => 'female',
                'dob' => '1987-09-14',
                'email' => 'kavita.verma@school.com',
                'phone' => '+91 9876543215',
                'department' => 'Languages',
                'designation' => 'Teacher',
                'qualification' => 'M.A Hindi',
                'experience' => 11,
                'subjects' => ['Hindi'],
                'employment_type' => 'permanent',
                'salary' => 56000,
            ],
            [
                'first_name' => 'Vikram',
                'last_name' => 'Desai',
                'gender' => 'male',
                'dob' => '1983-12-05',
                'email' => 'vikram.desai@school.com',
                'phone' => '+91 9876543216',
                'department' => 'Social Studies',
                'designation' => 'Senior Teacher',
                'qualification' => 'M.A History',
                'experience' => 14,
                'subjects' => ['History', 'Geography'],
                'employment_type' => 'permanent',
                'salary' => 62000,
            ],
            [
                'first_name' => 'Anita',
                'last_name' => 'Mehta',
                'gender' => 'female',
                'dob' => '1992-04-30',
                'email' => 'anita.mehta@school.com',
                'phone' => '+91 9876543217',
                'department' => 'Computer Science',
                'designation' => 'Teacher',
                'qualification' => 'B.Tech Computer Science',
                'experience' => 6,
                'subjects' => ['Computer Science', 'Information Technology'],
                'employment_type' => 'contract',
                'salary' => 52000,
            ],
            [
                'first_name' => 'Rahul',
                'last_name' => 'Joshi',
                'gender' => 'male',
                'dob' => '1989-07-08',
                'email' => 'rahul.joshi@school.com',
                'phone' => '+91 9876543218',
                'department' => 'Arts',
                'designation' => 'Teacher',
                'qualification' => 'B.F.A',
                'experience' => 9,
                'subjects' => ['Art & Craft', 'Music'],
                'employment_type' => 'permanent',
                'salary' => 50000,
            ],
            [
                'first_name' => 'Deepa',
                'last_name' => 'Nair',
                'gender' => 'female',
                'dob' => '1991-02-20',
                'email' => 'deepa.nair@school.com',
                'phone' => '+91 9876543219',
                'department' => 'Physical Education',
                'designation' => 'Teacher',
                'qualification' => 'B.P.Ed',
                'experience' => 7,
                'subjects' => ['Physical Education'],
                'employment_type' => 'permanent',
                'salary' => 48000,
            ],
        ];

        // Get all existing employee IDs globally (employee_id is unique across all tenants)
        $year = now()->year;
        $maxNumber = Teacher::where('employee_id', 'like', "TCH-{$year}-%")
            ->pluck('employee_id')
            ->map(function($id) {
                return (int) substr($id, -3);
            })
            ->max();

        // Start counter from highest existing number + 1, or 1 if none exist
        $nextEmployeeNumber = $maxNumber ? $maxNumber + 1 : 1;

        $this->command->info("Starting employee ID generation from: TCH-{$year}-" . str_pad($nextEmployeeNumber, 3, '0', STR_PAD_LEFT));

        foreach ($teachers as $teacherData) {
            // Check if teacher already exists by email (email is globally unique)
            $existingTeacher = Teacher::where('email', $teacherData['email'])->first();

            if ($existingTeacher) {
                $this->command->info("⊘ Exists: {$teacherData['first_name']} {$teacherData['last_name']} (email: {$teacherData['email']})");
                continue;
            }

            // Find department
            $department = $departments->where('department_name', $teacherData['department'])->first();

            // Generate unique employee ID - find next available number
            // Note: employee_id has GLOBAL unique constraint, so check all tenants
            $employeeId = null;
            $attempts = 0;
            do {
                $employeeId = sprintf('TCH-%d-%03d', $year, $nextEmployeeNumber);
                // Check globally (employee_id is unique across all tenants)
                $exists = Teacher::where('employee_id', $employeeId)->exists();
                if ($exists) {
                    $nextEmployeeNumber++;
                }
                $attempts++;
                if ($attempts > 100) {
                    $this->command->error("Unable to generate unique employee ID for {$teacherData['first_name']} {$teacherData['last_name']}");
                    break;
                }
            } while ($exists);

            if ($attempts > 100) {
                continue;
            }

            // Increment for next teacher
            $nextEmployeeNumber++;

            // Create teacher
            try {
                $teacher = Teacher::create([
                    'tenant_id' => $tenant->id,
                    'employee_id' => $employeeId,
                    'first_name' => $teacherData['first_name'],
                    'last_name' => $teacherData['last_name'],
                    'gender' => $teacherData['gender'],
                    'date_of_birth' => $teacherData['dob'],
                    'blood_group' => ['A+', 'B+', 'O+', 'AB+'][array_rand(['A+', 'B+', 'O+', 'AB+'])],
                    'nationality' => 'Indian',
                    'category' => 'general',
                    'email' => $teacherData['email'],
                    'phone' => $teacherData['phone'],
                    'current_address' => [
                        'address' => fake()->streetAddress(),
                        'city' => fake()->city(),
                        'state' => fake()->state(),
                        'pincode' => fake()->postcode(),
                        'country' => 'India',
                    ],
                    'department_id' => $department?->id,
                    'designation' => $teacherData['designation'],
                    'employment_type' => $teacherData['employment_type'],
                    'date_of_joining' => now()->subYears($teacherData['experience'])->format('Y-m-d'),
                    'highest_qualification' => $teacherData['qualification'],
                    'experience_years' => $teacherData['experience'],
                    'salary_amount' => $teacherData['salary'],
                    'bank_name' => 'State Bank of India',
                    'bank_account_number' => fake()->numerify('##########'),
                    'bank_ifsc_code' => 'SBIN' . fake()->numerify('0######'),
                    'pan_number' => strtoupper(fake()->bothify('?????####?')),
                    'aadhar_number' => fake()->numerify('############'),
                    'is_active' => true,
                    'status' => 'active',
                ]);

                $this->command->info("✓ Created: {$teacherData['first_name']} {$teacherData['last_name']} ({$employeeId})");

            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == 23000) {
                    $message = $e->getMessage();
                    $this->command->error("SQL Error for {$teacherData['first_name']} {$teacherData['last_name']}: " . substr($message, 0, 200));
                    if (str_contains($message, 'employee_id')) {
                        $this->command->warn("⊘ Skipped (duplicate employee_id): {$teacherData['first_name']} {$teacherData['last_name']} - {$employeeId}");
                    } elseif (str_contains($message, 'email')) {
                        $this->command->warn("⊘ Skipped (duplicate email): {$teacherData['first_name']} {$teacherData['last_name']}");
                    } else {
                        $this->command->warn("⊘ Skipped (duplicate): {$teacherData['first_name']} {$teacherData['last_name']}");
                    }
                    continue;
                }
                throw $e;
            }

            // Assign subjects
            foreach ($teacherData['subjects'] as $subjectName) {
                $subject = $subjects->where('subject_name', $subjectName)->first();
                if ($subject) {
                    $teacher->subjects()->attach($subject->id, [
                        'tenant_id' => $tenant->id,
                        'is_primary' => true,
                    ]);
                }
            }

            // Add a qualification (if not exists)
            TeacherQualification::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'teacher_id' => $teacher->id,
                    'degree_name' => $teacherData['qualification'],
                ],
                [
                    'qualification_type' => 'academic',
                    'specialization' => $teacherData['subjects'][0] ?? null,
                    'institution_name' => fake()->company() . ' University',
                    'university_board' => fake()->randomElement(['Delhi University', 'Mumbai University', 'Bangalore University', 'Pune University']),
                    'year_of_passing' => now()->subYears($teacherData['experience'] + 2)->year,
                    'grade_percentage' => fake()->randomElement(['First Class', '75%', '80%', '85%']),
                    'is_verified' => true,
                ]
            );

        }

        $this->command->info("\n✅ Successfully created " . count($teachers) . " teachers with qualifications!");
    }
}

