<form action="{{ url('/admin/settings/academic') }}" method="POST" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <!-- Academic Year Start -->
        <div>
            <label for="academic_year_start" class="block text-sm font-medium text-gray-700">
                Academic Year Start Date
            </label>
            <input type="date" name="academic_year_start" id="academic_year_start"
                value="{{ old('academic_year_start', $academicSettings['academic_year_start'] ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('academic_year_start') border-red-300 @enderror">
            @error('academic_year_start')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Academic Year End -->
        <div>
            <label for="academic_year_end" class="block text-sm font-medium text-gray-700">
                Academic Year End Date
            </label>
            <input type="date" name="academic_year_end" id="academic_year_end"
                value="{{ old('academic_year_end', $academicSettings['academic_year_end'] ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('academic_year_end') border-red-300 @enderror">
            @error('academic_year_end')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Default Session -->
        <div>
            <label for="default_session" class="block text-sm font-medium text-gray-700">
                Default Session/Term
            </label>
            <input type="text" name="default_session" id="default_session"
                value="{{ old('default_session', $academicSettings['default_session'] ?? '') }}"
                placeholder="e.g., 2024-2025, Spring 2024"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('default_session') border-red-300 @enderror">
            <p class="mt-1 text-sm text-gray-500">Current academic session name</p>
            @error('default_session')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Week Start Day -->
        <div>
            <label for="week_start_day" class="block text-sm font-medium text-gray-700">
                Week Start Day
            </label>
            <select name="week_start_day" id="week_start_day"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('week_start_day') border-red-300 @enderror">
                <option value="sunday" {{ old('week_start_day', $academicSettings['week_start_day'] ?? 'monday') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                <option value="monday" {{ old('week_start_day', $academicSettings['week_start_day'] ?? 'monday') == 'monday' ? 'selected' : '' }}>Monday</option>
                <option value="tuesday" {{ old('week_start_day', $academicSettings['week_start_day'] ?? 'monday') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                <option value="wednesday" {{ old('week_start_day', $academicSettings['week_start_day'] ?? 'monday') == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                <option value="thursday" {{ old('week_start_day', $academicSettings['week_start_day'] ?? 'monday') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                <option value="friday" {{ old('week_start_day', $academicSettings['week_start_day'] ?? 'monday') == 'friday' ? 'selected' : '' }}>Friday</option>
                <option value="saturday" {{ old('week_start_day', $academicSettings['week_start_day'] ?? 'monday') == 'saturday' ? 'selected' : '' }}>Saturday</option>
            </select>
            <p class="mt-1 text-sm text-gray-500">First day of the week for timetables and calendars</p>
            @error('week_start_day')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
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
                <h3 class="text-sm font-medium text-blue-800">Academic Settings Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>These settings control the academic calendar and session information. Make sure to set the correct academic year dates to properly track student progress and generate reports.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            Save Academic Settings
        </button>
    </div>
</form>

