<form action="{{ url('/admin/settings/features') }}" method="POST" class="space-y-6">
    @csrf

    <div class="space-y-4">
        <p class="text-sm text-gray-600">Enable or disable specific modules for your institution. Disabled modules will not be accessible to users.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Students Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_students" name="enable_students" type="checkbox" value="1"
                        {{ ($featureSettings['feature_students'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_students" class="font-medium text-gray-700">Student Management</label>
                    <p class="text-gray-500">Manage student enrollment, profiles, and records</p>
                </div>
            </div>

            <!-- Teachers Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_teachers" name="enable_teachers" type="checkbox" value="1"
                        {{ ($featureSettings['feature_teachers'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_teachers" class="font-medium text-gray-700">Teacher Management</label>
                    <p class="text-gray-500">Manage teacher profiles and assignments</p>
                </div>
            </div>

            <!-- Classes Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_classes" name="enable_classes" type="checkbox" value="1"
                        {{ ($featureSettings['feature_classes'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_classes" class="font-medium text-gray-700">Class Management</label>
                    <p class="text-gray-500">Manage classes, sections, and subjects</p>
                </div>
            </div>

            <!-- Attendance Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_attendance" name="enable_attendance" type="checkbox" value="1"
                        {{ ($featureSettings['feature_attendance'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_attendance" class="font-medium text-gray-700">Attendance System</label>
                    <p class="text-gray-500">Track student and staff attendance</p>
                </div>
            </div>

            <!-- Holidays Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_holidays" name="enable_holidays" type="checkbox" value="1"
                        {{ ($featureSettings['feature_holidays'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_holidays" class="font-medium text-gray-700">Holiday Management</label>
                    <p class="text-gray-500">Manage school holidays and calendar</p>
                </div>
            </div>

            <!-- Exams Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_exams" name="enable_exams" type="checkbox" value="1"
                        {{ ($featureSettings['feature_exams'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_exams" class="font-medium text-gray-700">Exam Management</label>
                    <p class="text-gray-500">Manage exams, schedules, and results</p>
                </div>
            </div>

            <!-- Grades Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_grades" name="enable_grades" type="checkbox" value="1"
                        {{ ($featureSettings['feature_grades'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_grades" class="font-medium text-gray-700">Grades & Marks</label>
                    <p class="text-gray-500">Record and manage student grades</p>
                </div>
            </div>

            <!-- Fees Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_fees" name="enable_fees" type="checkbox" value="1"
                        {{ ($featureSettings['feature_fees'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_fees" class="font-medium text-gray-700">Fee Management</label>
                    <p class="text-gray-500">Manage fee structure, invoices, and payments</p>
                </div>
            </div>

            <!-- Library Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_library" name="enable_library" type="checkbox" value="1"
                        {{ ($featureSettings['feature_library'] ?? false) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_library" class="font-medium text-gray-700">Library Management</label>
                    <p class="text-gray-500">Manage books, issues, and returns</p>
                </div>
            </div>

            <!-- Transport Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_transport" name="enable_transport" type="checkbox" value="1"
                        {{ ($featureSettings['feature_transport'] ?? false) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_transport" class="font-medium text-gray-700">Transport Management</label>
                    <p class="text-gray-500">Manage routes, vehicles, and drivers</p>
                </div>
            </div>

            <!-- Hostel Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_hostel" name="enable_hostel" type="checkbox" value="1"
                        {{ ($featureSettings['feature_hostel'] ?? false) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_hostel" class="font-medium text-gray-700">Hostel Management</label>
                    <p class="text-gray-500">Manage hostel rooms and allocations</p>
                </div>
            </div>

            <!-- Assignments Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_assignments" name="enable_assignments" type="checkbox" value="1"
                        {{ ($featureSettings['feature_assignments'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_assignments" class="font-medium text-gray-700">Assignments</label>
                    <p class="text-gray-500">Create and manage homework assignments</p>
                </div>
            </div>

            <!-- Timetable Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_timetable" name="enable_timetable" type="checkbox" value="1"
                        {{ ($featureSettings['feature_timetable'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_timetable" class="font-medium text-gray-700">Timetable</label>
                    <p class="text-gray-500">Manage class and exam timetables</p>
                </div>
            </div>

            <!-- Events Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_events" name="enable_events" type="checkbox" value="1"
                        {{ ($featureSettings['feature_events'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_events" class="font-medium text-gray-700">Events & Calendar</label>
                    <p class="text-gray-500">Manage school events and activities</p>
                </div>
            </div>

            <!-- Notice Board Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_notice_board" name="enable_notice_board" type="checkbox" value="1"
                        {{ ($featureSettings['feature_notice_board'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_notice_board" class="font-medium text-gray-700">Notice Board</label>
                    <p class="text-gray-500">Post announcements and notices</p>
                </div>
            </div>

            <!-- Communication Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_communication" name="enable_communication" type="checkbox" value="1"
                        {{ ($featureSettings['feature_communication'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_communication" class="font-medium text-gray-700">Communication</label>
                    <p class="text-gray-500">SMS and email notifications</p>
                </div>
            </div>

            <!-- Reports Module -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_reports" name="enable_reports" type="checkbox" value="1"
                        {{ ($featureSettings['feature_reports'] ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_reports" class="font-medium text-gray-700">Reports & Analytics</label>
                    <p class="text-gray-500">Generate various reports and analytics</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end pt-4 border-t border-gray-200">
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            Save Feature Settings
        </button>
    </div>
</form>

