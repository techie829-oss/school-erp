<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->command->error('No tenant found! Please create a tenant first.');
            return;
        }

        $this->command->info("Creating students for tenant: {$tenant->id}");

        // Get classes and sections
        $classes = SchoolClass::forTenant($tenant->id)->with('sections')->get();

        if ($classes->isEmpty()) {
            $this->command->error('No classes found! Run ClassSectionSeeder first.');
            return;
        }

        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);
        $totalStudents = 0;

        // Sample first names
        $maleNames = ['Aarav', 'Vivaan', 'Aditya', 'Vihaan', 'Arjun', 'Sai', 'Arnav', 'Ayaan', 'Krishna', 'Ishaan', 'Shaurya', 'Atharv', 'Advait', 'Pranav', 'Ved'];
        $femaleNames = ['Aadhya', 'Ananya', 'Pari', 'Anika', 'Sara', 'Diya', 'Navya', 'Ira', 'Myra', 'Saanvi', 'Kiara', 'Avni', 'Riya', 'Aarohi', 'Shanaya'];
        $lastNames = ['Kumar', 'Sharma', 'Patel', 'Singh', 'Verma', 'Joshi', 'Reddy', 'Mehta', 'Gupta', 'Desai', 'Nair', 'Iyer', 'Malhotra', 'Kapoor', 'Agarwal'];

        // Cities for addresses
        $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Chennai', 'Kolkata', 'Pune', 'Ahmedabad'];
        $states = ['Maharashtra', 'Delhi', 'Karnataka', 'Telangana', 'Tamil Nadu', 'West Bengal', 'Gujarat'];

        // Create students for each class and section
        foreach ($classes as $class) {
            foreach ($class->sections as $section) {
                // Create 8-12 students per section
                $studentsPerSection = rand(8, 12);

                for ($i = 1; $i <= $studentsPerSection; $i++) {
                    $gender = fake()->randomElement(['male', 'female']);
                    $firstName = $gender === 'male'
                        ? $maleNames[array_rand($maleNames)]
                        : $femaleNames[array_rand($femaleNames)];
                    $lastName = $lastNames[array_rand($lastNames)];

                    // Calculate age appropriate for the class
                    $ageForClass = 5 + $class->class_numeric; // Class 1 = ~6 years old
                    $dob = now()->subYears($ageForClass)->subMonths(rand(0, 11))->format('Y-m-d');

                    // Generate unique email
                    $uniqueId = $class->class_numeric . $section->section_name . $i;
                    $email = strtolower($firstName . '.' . $lastName . $uniqueId . '@student.school.com');

                    // Create student
                    $student = Student::create([
                        'tenant_id' => $tenant->id,
                        'admission_number' => Student::generateAdmissionNumber($tenant->id),
                        'admission_date' => now()->subMonths(rand(1, 24))->format('Y-m-d'),
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'full_name' => $firstName . ' ' . $lastName,
                        'date_of_birth' => $dob,
                        'gender' => $gender,
                        'blood_group' => fake()->randomElement(['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-']),
                        'nationality' => 'Indian',
                        'religion' => fake()->randomElement(['Hindu', 'Muslim', 'Christian', 'Sikh', 'Other']),
                        'category' => fake()->randomElement(['general', 'obc', 'sc', 'st']),
                        'email' => $email,
                        'phone' => '+91 ' . fake()->numerify('##########'),
                        'current_address' => [
                            'address' => fake()->streetAddress(),
                            'city' => $cities[array_rand($cities)],
                            'state' => $states[array_rand($states)],
                            'pincode' => fake()->numerify('######'),
                            'country' => 'India',
                        ],
                        'father_name' => fake()->name('male') . ' ' . $lastName,
                        'father_phone' => '+91 ' . fake()->numerify('##########'),
                        'father_email' => strtolower('father.' . $lastName . fake()->numerify('##') . '@email.com'),
                        'father_occupation' => fake()->randomElement(['Engineer', 'Doctor', 'Businessman', 'Teacher', 'Lawyer', 'Accountant']),
                        'mother_name' => fake()->name('female') . ' ' . $lastName,
                        'mother_phone' => '+91 ' . fake()->numerify('##########'),
                        'mother_email' => strtolower('mother.' . $lastName . fake()->numerify('##') . '@email.com'),
                        'mother_occupation' => fake()->randomElement(['Teacher', 'Doctor', 'Homemaker', 'Nurse', 'Engineer', 'Designer']),
                        'emergency_contact_name' => fake()->name() . ' ' . $lastName,
                        'emergency_contact_phone' => '+91 ' . fake()->numerify('##########'),
                        'emergency_contact_relation' => fake()->randomElement(['Uncle', 'Aunt', 'Grandfather', 'Grandmother']),
                        'overall_status' => 'active',
                        'is_active' => true,
                    ]);

                    // Enroll student in this class
                    $student->enrollInClass(
                        $class->id,
                        $section->id,
                        $academicYear,
                        $i // Roll number
                    );

                    $totalStudents++;
                }

                $this->command->info("  ✓ Created {$studentsPerSection} students for {$class->class_name}-{$section->section_name}");
            }
        }

        $this->command->info("\n✅ Successfully created {$totalStudents} students across all classes and sections!");
    }
}

