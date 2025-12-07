<form action="{{ url('/admin/settings/attendance') }}" method="POST" class="space-y-6">
    @csrf

    <div class="space-y-6">
        <!-- School Timings Section -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">School Timings</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <!-- School Start Time -->
                <div>
                    <label for="school_start_time" class="block text-sm font-medium text-gray-700">
                        School Start Time
                    </label>
                    <input type="time" name="school_start_time" id="school_start_time"
                        value="{{ old('school_start_time', substr($attendanceSettings->school_start_time ?? '09:00:00', 0, 5)) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('school_start_time') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Default check-in time for teachers</p>
                    @error('school_start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- School End Time -->
                <div>
                    <label for="school_end_time" class="block text-sm font-medium text-gray-700">
                        School End Time
                    </label>
                    <input type="time" name="school_end_time" id="school_end_time"
                        value="{{ old('school_end_time', substr($attendanceSettings->school_end_time ?? '17:00:00', 0, 5)) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('school_end_time') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Default check-out time for teachers</p>
                    @error('school_end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Late Arrival After -->
                <div>
                    <label for="late_arrival_after" class="block text-sm font-medium text-gray-700">
                        Late Arrival After
                    </label>
                    <input type="time" name="late_arrival_after" id="late_arrival_after"
                        value="{{ old('late_arrival_after', substr($attendanceSettings->late_arrival_after ?? '09:15:00', 0, 5)) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('late_arrival_after') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Time after which arrival is considered late</p>
                    @error('late_arrival_after')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grace Period -->
                <div>
                    <label for="grace_period_minutes" class="block text-sm font-medium text-gray-700">
                        Grace Period (minutes)
                    </label>
                    <input type="number" name="grace_period_minutes" id="grace_period_minutes"
                        value="{{ old('grace_period_minutes', $attendanceSettings->grace_period_minutes ?? 15) }}"
                        min="0" max="60"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('grace_period_minutes') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Grace period for late arrivals (in minutes)</p>
                    @error('grace_period_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Attendance Policies Section -->
        <div class="pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Attendance Policies</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <!-- Minimum Working Hours -->
                <div>
                    <label for="minimum_working_hours" class="block text-sm font-medium text-gray-700">
                        Minimum Working Hours (per day)
                    </label>
                    <input type="number" name="minimum_working_hours" id="minimum_working_hours"
                        value="{{ old('minimum_working_hours', $attendanceSettings->minimum_working_hours ?? 8.0) }}"
                        min="0" max="24" step="0.5"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('minimum_working_hours') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Required working hours for full day</p>
                    @error('minimum_working_hours')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Half Day Threshold -->
                <div>
                    <label for="half_day_threshold_hours" class="block text-sm font-medium text-gray-700">
                        Half Day Threshold (hours)
                    </label>
                    <input type="number" name="half_day_threshold_hours" id="half_day_threshold_hours"
                        value="{{ old('half_day_threshold_hours', $attendanceSettings->half_day_threshold_hours ?? 4.0) }}"
                        min="0" max="12" step="0.5"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('half_day_threshold_hours') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Minimum hours for half day attendance</p>
                    @error('half_day_threshold_hours')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Weekend Days -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Weekend Days
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @php
                            $weekendDays = old('weekend_days', $attendanceSettings->weekend_days ?? ['sunday']);
                            // Handle if weekend_days is stored as JSON string
                            if (is_string($weekendDays)) {
                                $weekendDays = json_decode($weekendDays, true) ?? ['sunday'];
                            }
                            $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                        @endphp
                        @foreach($days as $day)
                        <div class="flex items-center">
                            <input type="checkbox" name="weekend_days[]" value="{{ $day }}"
                                id="weekend_{{ $day }}"
                                {{ in_array($day, $weekendDays ?? []) ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="weekend_{{ $day }}" class="ml-2 block text-sm text-gray-700 capitalize">
                                {{ $day }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Select days that are considered weekends</p>
                    @error('weekend_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Settings</h3>

            <div class="space-y-4">
                <!-- Auto-mark Absent -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="auto_mark_absent" id="auto_mark_absent" value="1"
                            {{ old('auto_mark_absent', $attendanceSettings->auto_mark_absent ?? false) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3">
                        <label for="auto_mark_absent" class="font-medium text-gray-700">
                            Auto-mark Absent
                        </label>
                        <p class="text-sm text-gray-500">Automatically mark as absent if no attendance is recorded by end of day</p>
                    </div>
                </div>

                <!-- Require Remarks for Absent -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="require_remarks_for_absent" id="require_remarks_for_absent" value="1"
                            {{ old('require_remarks_for_absent', $attendanceSettings->require_remarks_for_absent ?? false) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3">
                        <label for="require_remarks_for_absent" class="font-medium text-gray-700">
                            Require Remarks for Absent
                        </label>
                        <p class="text-sm text-gray-500">Make remarks mandatory when marking as absent</p>
                    </div>
                </div>

                <!-- Allow Edit After Days -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="allow_edit_after_days" class="block text-sm font-medium text-gray-700">
                            Allow Edit After (days)
                        </label>
                        <input type="number" name="allow_edit_after_days" id="allow_edit_after_days"
                            value="{{ old('allow_edit_after_days', $attendanceSettings->allow_edit_after_days ?? 7) }}"
                            min="0" max="30"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('allow_edit_after_days') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Number of days after which attendance cannot be edited</p>
                        @error('allow_edit_after_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3 flex-1 min-w-0">
                <h3 class="text-sm font-medium text-blue-800">Attendance Settings Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>These settings control attendance policies, default timings, and notifications. Changes will apply to all future attendance records.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            Save Attendance Settings
        </button>
    </div>
</form>

