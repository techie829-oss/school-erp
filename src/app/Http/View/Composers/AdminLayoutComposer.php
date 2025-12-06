<?php

namespace App\Http\View\Composers;

use App\Models\TenantSetting;
use App\Services\TenantService;
use Illuminate\View\View;

class AdminLayoutComposer
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $tenant = $this->tenantService->getCurrentTenant(request());

        if ($tenant) {
            $featureSettings = TenantSetting::getAllForTenant($tenant->id, 'features');

            // Set defaults to true if not set (backward compatibility)
            $features = [
                'students' => $featureSettings['feature_students'] ?? true,
                'teachers' => $featureSettings['feature_teachers'] ?? true,
                'classes' => $featureSettings['feature_classes'] ?? true,
                'attendance' => $featureSettings['feature_attendance'] ?? true,
                'exams' => $featureSettings['feature_exams'] ?? true,
                'grades' => $featureSettings['feature_grades'] ?? true,
                'fees' => $featureSettings['feature_fees'] ?? true,
                'library' => $featureSettings['feature_library'] ?? false,
                'transport' => $featureSettings['feature_transport'] ?? false,
                'hostel' => $featureSettings['feature_hostel'] ?? false,
                'assignments' => $featureSettings['feature_assignments'] ?? true,
                'timetable' => $featureSettings['feature_timetable'] ?? true,
                'events' => $featureSettings['feature_events'] ?? true,
                'notice_board' => $featureSettings['feature_notice_board'] ?? true,
                'communication' => $featureSettings['feature_communication'] ?? true,
                'reports' => $featureSettings['feature_reports'] ?? true,
                'cms' => $featureSettings['feature_cms'] ?? false,
            ];
        } else {
            // Default features if no tenant
            $features = [
                'students' => true,
                'teachers' => true,
                'classes' => true,
                'attendance' => true,
                'exams' => true,
                'grades' => true,
                'fees' => true,
                'library' => false,
                'transport' => false,
                'hostel' => false,
                'assignments' => true,
                'timetable' => true,
                'events' => true,
                'notice_board' => true,
                'communication' => true,
                'reports' => true,
                'cms' => false,
            ];
        }

        $view->with('featureSettings', $features);
    }
}

