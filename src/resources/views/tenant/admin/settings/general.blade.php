<form action="{{ url('/admin/settings/general') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 gap-6">
        <!-- Institution Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
                Institution Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $tenantData['name'] ?? $tenant->id) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('name') border-red-300 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Platform Type -->
        <div>
            <label for="platform_type" class="block text-sm font-medium text-gray-700">
                Platform Type <span class="text-red-500">*</span>
            </label>
            <select name="platform_type" id="platform_type" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('platform_type') border-red-300 @enderror">
                <option value="school" {{ old('platform_type', $tenantData['platform_type'] ?? 'school') == 'school' ? 'selected' : '' }}>School Only</option>
                <option value="college" {{ old('platform_type', $tenantData['platform_type'] ?? '') == 'college' ? 'selected' : '' }}>College Only</option>
                <option value="both" {{ old('platform_type', $tenantData['platform_type'] ?? '') == 'both' ? 'selected' : '' }}>Both (School + College)</option>
            </select>
            <p class="mt-1 text-sm text-gray-500">Select the type of educational institution</p>
            @error('platform_type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Logo -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Institution Logo
            </label>

            @if(isset($tenantData['logo']) && $tenantData['logo'])
                <div class="mt-2 flex items-center space-x-4">
                    <img src="{{ Storage::url($tenantData['logo']) }}" alt="Logo" class="h-20 w-20 object-contain rounded-lg border border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500">Current logo</p>
                        <form action="{{ url('/admin/settings/logo') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete the logo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="mt-1 text-sm text-red-600 hover:text-red-800">
                                Remove logo
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="mt-2">
                <input type="file" name="logo" id="logo" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('logo') border-red-300 @enderror">
                <p class="mt-1 text-sm text-gray-500">PNG, JPG, SVG up to 2MB. Recommended size: 200x200px</p>
            </div>
            @error('logo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Contact Email -->
        <div>
            <label for="contact_email" class="block text-sm font-medium text-gray-700">
                Contact Email
            </label>
            <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $tenantData['contact_email'] ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('contact_email') border-red-300 @enderror">
            @error('contact_email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Contact Phone -->
        <div>
            <label for="contact_phone" class="block text-sm font-medium text-gray-700">
                Contact Phone
            </label>
            <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $tenantData['contact_phone'] ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('contact_phone') border-red-300 @enderror">
            @error('contact_phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address -->
        <div>
            <label for="address" class="block text-sm font-medium text-gray-700">
                Address
            </label>
            <textarea name="address" id="address" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('address') border-red-300 @enderror">{{ old('address', $tenantData['address'] ?? '') }}</textarea>
            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            Save General Settings
        </button>
    </div>
</form>

