<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first tenant
        $tenantId = \App\Models\Tenant::first()?->id;
        if (!$tenantId) {
            $this->command->error('No tenant found. Please create a tenant first.');
            return;
        }

        // Get admin user for marking attendance
        $adminUser = User::where('user_type', 'school_admin')->first();
        if (!$adminUser) {
            $adminUser = User::where('tenant_id', $tenantId)->first();
        }
        if (!$adminUser) {
            $this->command->error('No user found for tenant. Please create a user first.');
            return;
        }

        $this->command->info('Generating attendance data for last 30 days...');

        // Generate student attendance for last 30 days
        $students = Student::forTenant($tenantId)->active()->with('currentEnrollment')->get();
        $days = 30;

        $studentCount = 0;
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($i);

            // Skip weekends (Sunday)
            if ($date->dayOfWeek == 0) {
                continue;
            }

            foreach ($students as $student) {
                if (!$student->currentEnrollment) continue;

                // 90% chance of present, 8% absent, 2% late
                $rand = rand(1, 100);
                if ($rand <= 90) {
                    $status = 'present';
                } elseif ($rand <= 98) {
                    $status = 'absent';
                } else {
                    $status = 'late';
                }

                StudentAttendance::updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'student_id' => $student->id,
                        'attendance_date' => $date->format('Y-m-d'),
                    ],
                    [
                        'class_id' => $student->currentEnrollment->class_id,
                        'section_id' => $student->currentEnrollment->section_id,
                        'status' => $status,
                        'remarks' => $status == 'absent' ? 'Sick' : null,
                        'marked_by' => $adminUser->id,
                        'marked_at' => $date->setTime(9, 0),
                    ]
                );

                $studentCount++;
            }
        }

        $this->command->info("Created {$studentCount} student attendance records");

        // Generate teacher attendance for last 30 days
        $teachers = Teacher::forTenant($tenantId)->active()->get();
        $teacherCount = 0;

        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($i);

            // Skip weekends
            if ($date->dayOfWeek == 0) {
                continue;
            }

            foreach ($teachers as $teacher) {
                // 95% chance of present, 3% absent, 2% on leave
                $rand = rand(1, 100);
                if ($rand <= 95) {
                    $status = 'present';
                    $checkIn = '09:00:00';
                    $checkOut = '17:00:00';
                    $hours = 8.0;
                } elseif ($rand <= 98) {
                    $status = 'absent';
                    $checkIn = null;
                    $checkOut = null;
                    $hours = null;
                } else {
                    $status = 'on_leave';
                    $checkIn = null;
                    $checkOut = null;
                    $hours = null;
                }

                TeacherAttendance::updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'teacher_id' => $teacher->id,
                        'attendance_date' => $date->format('Y-m-d'),
                    ],
                    [
                        'status' => $status,
                        'check_in_time' => $checkIn,
                        'check_out_time' => $checkOut,
                        'total_hours' => $hours,
                        'remarks' => $status == 'absent' ? 'Sick leave' : null,
                        'marked_by' => $adminUser->id,
                        'marked_at' => $date->setTime(9, 0),
                    ]
                );

                $teacherCount++;
            }
        }

        $this->command->info("Created {$teacherCount} teacher attendance records");
        $this->command->info('✅ Attendance data seeding complete!');
    }
}

