<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get basic statistics
            $stats = [
                'total_students' => $this->getTotalStudents(),
                'total_teachers' => $this->getTotalTeachers(),
                'total_classes' => $this->getTotalClasses(),
                'active_students' => $this->getActiveStudents(),
                'attendance_today' => $this->getTodayAttendance(),
                'recent_enrollments' => $this->getRecentEnrollments(),
            ];

            // Get recent activities
            $recent_activities = $this->getRecentActivities();

            // Get upcoming events
            $upcoming_events = $this->getUpcomingEvents();

            // Get current tenant information
            $tenant = $this->resolveTenantFromSubdomain();
            $tenantSubdomain = $tenant ? $tenant->data['subdomain'] ?? 'default' : 'default';

            return view('tenant.admin.dashboard', compact('stats', 'recent_activities', 'upcoming_events', 'tenantSubdomain'));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Dashboard Controller Error: ' . $e->getMessage());

            // Return a fallback view with default stats
            $stats = [
                'total_students' => 0,
                'total_teachers' => 0,
                'total_classes' => 0,
                'active_students' => 0,
                'attendance_today' => 0,
                'recent_enrollments' => 0,
            ];

            $recent_activities = [];
            $upcoming_events = [];

            // Get current tenant information
            $tenant = $this->resolveTenantFromSubdomain();
            $tenantSubdomain = $tenant ? $tenant->data['subdomain'] ?? 'default' : 'default';

            return view('tenant.admin.dashboard', compact('stats', 'recent_activities', 'upcoming_events', 'tenantSubdomain'));
        }
    }

    private function getTotalStudents()
    {
        try {
            return DB::table('students')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalTeachers()
    {
        try {
            return DB::table('teachers')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalClasses()
    {
        try {
            return DB::table('classes')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getActiveStudents()
    {
        try {
            return DB::table('students')->where('status', 'active')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTodayAttendance()
    {
        try {
            $today = now()->format('Y-m-d');
            return DB::table('attendance')
                ->whereDate('date', $today)
                ->where('status', 'present')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getRecentEnrollments()
    {
        try {
            return DB::table('students')
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getRecentActivities()
    {
        // This would typically come from an activities log table
        return [
            [
                'type' => 'enrollment',
                'message' => 'New student enrolled',
                'time' => now()->subHours(2),
                'icon' => 'user-plus',
                'color' => 'green'
            ],
            [
                'type' => 'attendance',
                'message' => 'Attendance marked for Class 10A',
                'time' => now()->subHours(4),
                'icon' => 'check-circle',
                'color' => 'blue'
            ],
            [
                'type' => 'grade',
                'message' => 'Grades updated for Mathematics',
                'time' => now()->subHours(6),
                'icon' => 'academic-cap',
                'color' => 'purple'
            ],
        ];
    }

    private function getUpcomingEvents()
    {
        // This would typically come from an events table
        return [
            [
                'title' => 'Parent-Teacher Meeting',
                'date' => now()->addDays(3),
                'type' => 'meeting'
            ],
            [
                'title' => 'Annual Sports Day',
                'date' => now()->addDays(7),
                'type' => 'event'
            ],
            [
                'title' => 'Mid-term Examinations',
                'date' => now()->addDays(14),
                'type' => 'exam'
            ],
        ];
    }

    /**
     * Resolve tenant from subdomain manually
     */
    protected function resolveTenantFromSubdomain()
    {
        $host = request()->getHost();
        $subdomain = $this->extractSubdomain($host);

        if (!$subdomain) {
            return null;
        }

        // Find tenant by subdomain
        return \App\Models\Tenant::where('data->subdomain', $subdomain)->first();
    }

    /**
     * Extract subdomain from host
     */
    protected function extractSubdomain(string $host): ?string
    {
        $primaryDomain = config('all.domains.primary');

        if (str_ends_with($host, '.' . $primaryDomain)) {
            return str_replace('.' . $primaryDomain, '', $host);
        }

        return null;
    }
}
