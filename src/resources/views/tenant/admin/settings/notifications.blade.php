<form action="{{ url('/admin/settings/notifications') }}" method="POST" class="space-y-6">
    @csrf

    <div class="space-y-6">
        <!-- Email Configuration -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Email Configuration</h3>

            <div class="grid grid-cols-1 gap-6">
                <!-- Enable Email Notifications -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="email_enabled" id="email_enabled" value="1"
                            {{ old('email_enabled', $notificationSettings['email_enabled'] ?? false) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                            onchange="toggleEmailSettings()">
                    </div>
                    <div class="ml-3">
                        <label for="email_enabled" class="font-medium text-gray-700">
                            Enable Email Notifications
                        </label>
                        <p class="text-sm text-gray-500">Send emails for payments, reminders, and other notifications</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email SMTP Settings -->
        <div id="email-settings" class="pt-6 border-t border-gray-200" style="display: {{ old('email_enabled', $notificationSettings['email_enabled'] ?? false) ? 'block' : 'none' }};">
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">SMTP Configuration</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Configure your email provider's SMTP settings. Common providers: Gmail, SendGrid, Mailgun, Amazon SES</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mail Driver -->
                <div>
                    <label for="mail_mailer" class="block text-sm font-medium text-gray-700">
                        Mail Driver
                    </label>
                    <select name="mail_mailer" id="mail_mailer"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="smtp" {{ old('mail_mailer', $notificationSettings['mail_mailer'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ old('mail_mailer', $notificationSettings['mail_mailer'] ?? 'smtp') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="mailgun" {{ old('mail_mailer', $notificationSettings['mail_mailer'] ?? 'smtp') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        <option value="ses" {{ old('mail_mailer', $notificationSettings['mail_mailer'] ?? 'smtp') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                    </select>
                </div>

                <!-- SMTP Host -->
                <div>
                    <label for="mail_host" class="block text-sm font-medium text-gray-700">
                        SMTP Host <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="mail_host" id="mail_host"
                        value="{{ old('mail_host', $notificationSettings['mail_host'] ?? '') }}"
                        placeholder="smtp.gmail.com"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('mail_host') border-red-300 @enderror">
                    @error('mail_host')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SMTP Port -->
                <div>
                    <label for="mail_port" class="block text-sm font-medium text-gray-700">
                        SMTP Port <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="mail_port" id="mail_port"
                        value="{{ old('mail_port', $notificationSettings['mail_port'] ?? '587') }}"
                        placeholder="587"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('mail_port') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Common: 587 (TLS), 465 (SSL), 25 (No encryption)</p>
                    @error('mail_port')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Encryption -->
                <div>
                    <label for="mail_encryption" class="block text-sm font-medium text-gray-700">
                        Encryption
                    </label>
                    <select name="mail_encryption" id="mail_encryption"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="tls" {{ old('mail_encryption', $notificationSettings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                        <option value="ssl" {{ old('mail_encryption', $notificationSettings['mail_encryption'] ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="" {{ old('mail_encryption', $notificationSettings['mail_encryption'] ?? 'tls') == '' ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                <!-- Username -->
                <div>
                    <label for="mail_username" class="block text-sm font-medium text-gray-700">
                        SMTP Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="mail_username" id="mail_username"
                        value="{{ old('mail_username', $notificationSettings['mail_username'] ?? '') }}"
                        placeholder="your-email@gmail.com"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('mail_username') border-red-300 @enderror">
                    @error('mail_username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="mail_password" class="block text-sm font-medium text-gray-700">
                        SMTP Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="mail_password" id="mail_password"
                        value="{{ old('mail_password', $notificationSettings['mail_password'] ?? '') }}"
                        placeholder="••••••••••••••••"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('mail_password') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">For Gmail, use App Password instead of regular password</p>
                    @error('mail_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- From Address -->
                <div>
                    <label for="mail_from_address" class="block text-sm font-medium text-gray-700">
                        From Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="mail_from_address" id="mail_from_address"
                        value="{{ old('mail_from_address', $notificationSettings['mail_from_address'] ?? '') }}"
                        placeholder="noreply@yourschool.com"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('mail_from_address') border-red-300 @enderror">
                    @error('mail_from_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- From Name -->
                <div>
                    <label for="mail_from_name" class="block text-sm font-medium text-gray-700">
                        From Name
                    </label>
                    <input type="text" name="mail_from_name" id="mail_from_name"
                        value="{{ old('mail_from_name', $notificationSettings['mail_from_name'] ?? 'School ERP') }}"
                        placeholder="School Name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>

            <!-- Test Email Button -->
            <div class="mt-4">
                <button type="button" onclick="sendTestEmail()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Send Test Email
                </button>
                <p class="mt-1 text-sm text-gray-500">Verify your email configuration by sending a test email</p>
            </div>
        </div>

        <!-- SMS Configuration -->
        <div class="pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">SMS Configuration</h3>

            <div class="grid grid-cols-1 gap-6">
                <!-- Enable SMS Notifications -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="sms_enabled" id="sms_enabled" value="1"
                            {{ old('sms_enabled', $notificationSettings['sms_enabled'] ?? false) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                            onchange="toggleSmsSettings()">
                    </div>
                    <div class="ml-3">
                        <label for="sms_enabled" class="font-medium text-gray-700">
                            Enable SMS Notifications
                        </label>
                        <p class="text-sm text-gray-500">Send SMS for payment confirmations, reminders, and alerts</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMS Gateway Settings -->
        <div id="sms-settings" class="pt-6 border-t border-gray-200" style="display: {{ old('sms_enabled', $notificationSettings['sms_enabled'] ?? false) ? 'block' : 'none' }};">
            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">MSG91 Configuration</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>Sign up at <a href="https://msg91.com/" target="_blank" class="underline">msg91.com</a> to get your Auth Key and configure SMS templates</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SMS Provider -->
                <div>
                    <label for="sms_provider" class="block text-sm font-medium text-gray-700">
                        SMS Provider
                    </label>
                    <select name="sms_provider" id="sms_provider"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        onchange="toggleSmsProviderFields()">
                        <option value="msg91" {{ old('sms_provider', $notificationSettings['sms_provider'] ?? 'msg91') == 'msg91' ? 'selected' : '' }}>MSG91</option>
                        <option value="twilio" {{ old('sms_provider', $notificationSettings['sms_provider'] ?? 'msg91') == 'twilio' ? 'selected' : '' }}>Twilio (Coming Soon)</option>
                    </select>
                </div>

                <div></div>

                <!-- MSG91 Settings -->
                <div id="msg91-settings" style="display: {{ old('sms_provider', $notificationSettings['sms_provider'] ?? 'msg91') == 'msg91' ? 'contents' : 'none' }};">
                    <!-- MSG91 Auth Key -->
                    <div>
                        <label for="msg91_auth_key" class="block text-sm font-medium text-gray-700">
                            MSG91 Auth Key <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="msg91_auth_key" id="msg91_auth_key"
                            value="{{ old('msg91_auth_key', $notificationSettings['msg91_auth_key'] ?? '') }}"
                            placeholder="Your MSG91 Auth Key"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('msg91_auth_key') border-red-300 @enderror">
                        @error('msg91_auth_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- MSG91 Sender ID -->
                    <div>
                        <label for="msg91_sender_id" class="block text-sm font-medium text-gray-700">
                            MSG91 Sender ID
                        </label>
                        <input type="text" name="msg91_sender_id" id="msg91_sender_id"
                            value="{{ old('msg91_sender_id', $notificationSettings['msg91_sender_id'] ?? 'SCHOOL') }}"
                            placeholder="SCHOOL"
                            maxlength="6"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm uppercase">
                        <p class="mt-1 text-sm text-gray-500">6 characters max, uppercase letters only</p>
                    </div>

                    <!-- MSG91 Route -->
                    <div>
                        <label for="msg91_route" class="block text-sm font-medium text-gray-700">
                            MSG91 Route
                        </label>
                        <select name="msg91_route" id="msg91_route"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="4" {{ old('msg91_route', $notificationSettings['msg91_route'] ?? '4') == '4' ? 'selected' : '' }}>Transactional (Route 4)</option>
                            <option value="1" {{ old('msg91_route', $notificationSettings['msg91_route'] ?? '4') == '1' ? 'selected' : '' }}>Promotional (Route 1)</option>
                        </select>
                    </div>

                    <!-- MSG91 DLT Template IDs -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            DLT Template IDs (as per Indian TRAI guidelines)
                        </label>
                        <p class="text-xs text-gray-500 mb-3">
                            Configure separate approved DLT template IDs for each SMS type. Content and variables must match exactly with your DLT-registered templates.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="msg91_dlt_template_payment_confirmation" class="block text-xs font-medium text-gray-700">
                                    Payment Confirmation Template ID
                                </label>
                                <input type="text" name="msg91_dlt_template_payment_confirmation" id="msg91_dlt_template_payment_confirmation"
                                    value="{{ old('msg91_dlt_template_payment_confirmation', $notificationSettings['msg91_dlt_template_payment_confirmation'] ?? '') }}"
                                    placeholder="1207XXXXXXXXXXXX"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-xs">
                            </div>
                            <div>
                                <label for="msg91_dlt_template_payment_reminder" class="block text-xs font-medium text-gray-700">
                                    Payment Reminder Template ID
                                </label>
                                <input type="text" name="msg91_dlt_template_payment_reminder" id="msg91_dlt_template_payment_reminder"
                                    value="{{ old('msg91_dlt_template_payment_reminder', $notificationSettings['msg91_dlt_template_payment_reminder'] ?? '') }}"
                                    placeholder="1207XXXXXXXXXXXX"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-xs">
                            </div>
                            <div>
                                <label for="msg91_dlt_template_fee_due" class="block text-xs font-medium text-gray-700">
                                    Fee Due Alert Template ID
                                </label>
                                <input type="text" name="msg91_dlt_template_fee_due" id="msg91_dlt_template_fee_due"
                                    value="{{ old('msg91_dlt_template_fee_due', $notificationSettings['msg91_dlt_template_fee_due'] ?? '') }}"
                                    placeholder="1207XXXXXXXXXXXX"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-xs">
                            </div>
                            <div>
                                <label for="msg91_dlt_template_attendance" class="block text-xs font-medium text-gray-700">
                                    Attendance Alert Template ID
                                </label>
                                <input type="text" name="msg91_dlt_template_attendance" id="msg91_dlt_template_attendance"
                                    value="{{ old('msg91_dlt_template_attendance', $notificationSettings['msg91_dlt_template_attendance'] ?? '') }}"
                                    placeholder="1207XXXXXXXXXXXX"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-xs">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SMS Template Guidelines (DLT Variables) -->
            <div class="mt-6 bg-white border border-dashed border-gray-300 rounded-md p-4">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Recommended SMS Templates & DLT Variables</h4>
                <p class="text-xs text-gray-500 mb-3">
                    Use these sample formats when creating templates on your DLT portal / MSG91. Replace <code>{#var#}</code> with DLT variables
                    (e.g. <code>{#var1#}</code>, <code>{#var2#}</code>) as per your provider's syntax.
                </p>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-700">Use Case</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-700">Sample Content</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-700">Variables (DLT)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr>
                                <td class="px-3 py-2 align-top font-medium text-gray-800">Payment Confirmation</td>
                                <td class="px-3 py-2 align-top text-gray-700">
                                    Dear Parent, fee payment of Rs.{#var1#} for {#var2#} (Adm No: {#var3#}) received on {#var4#}. Receipt: {#var5#}. {#var6#}.
                                </td>
                                <td class="px-3 py-2 align-top text-gray-600">
                                    var1 = Amount, var2 = Student Name, var3 = Admission No, var4 = Date, var5 = Receipt No, var6 = School Name
                                </td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 align-top font-medium text-gray-800">Payment Reminder</td>
                                <td class="px-3 py-2 align-top text-gray-700">
                                    Dear Parent, fee of Rs.{#var1#} for {#var2#} (Adm No: {#var3#}) is due on {#var4#}. Kindly pay at the earliest. {#var5#}.
                                </td>
                                <td class="px-3 py-2 align-top text-gray-600">
                                    var1 = Due Amount, var2 = Student Name, var3 = Admission No, var4 = Due Date, var5 = School Name
                                </td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 align-top font-medium text-gray-800">Fee Due Alert</td>
                                <td class="px-3 py-2 align-top text-gray-700">
                                    Dear Parent, total outstanding fee of Rs.{#var1#} for {#var2#} (Adm No: {#var3#}) is pending. Please clear dues. {#var4#}.
                                </td>
                                <td class="px-3 py-2 align-top text-gray-600">
                                    var1 = Outstanding Amount, var2 = Student Name, var3 = Admission No, var4 = School Name
                                </td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 align-top font-medium text-gray-800">Attendance Alert</td>
                                <td class="px-3 py-2 align-top text-gray-700">
                                    Dear Parent, {#var1#} (Adm No: {#var2#}) was marked {#var3#} on {#var4#}. In case of any query, contact school office. {#var5#}.
                                </td>
                                <td class="px-3 py-2 align-top text-gray-600">
                                    var1 = Student Name, var2 = Admission No, var3 = Status (Absent/Late), var4 = Date, var5 = School Name
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-2 text-[11px] text-gray-500">
                    Note: Exact variable notation (<code>{#var1#}</code> / <code>@{{var1}}</code>) depends on your DLT/MSG91 configuration. Make sure the text and variable positions in your DLT-approved template
                    match the messages sent from the application.
                </p>
            </div>

            <!-- Test SMS Button -->
            <div class="mt-4">
                <div class="flex items-center gap-4">
                    <input type="tel" id="test_phone" placeholder="Enter phone number (10 digits)"
                        class="block w-64 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        pattern="[0-9]{10}" maxlength="10">
                    <button type="button" onclick="sendTestSms()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Send Test SMS
                    </button>
                </div>
                <p class="mt-1 text-sm text-gray-500">Verify your SMS configuration by sending a test message</p>
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Preferences</h3>

            <div class="space-y-4">
                <p class="text-sm text-gray-600">Choose which events should trigger notifications:</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Payment Confirmation -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_payment_confirmation" id="notify_payment_confirmation" value="1"
                                {{ old('notify_payment_confirmation', $notificationSettings['notify_payment_confirmation'] ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="notify_payment_confirmation" class="text-sm font-medium text-gray-700">
                                Payment Confirmation
                            </label>
                        </div>
                    </div>

                    <!-- Payment Reminder -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_payment_reminder" id="notify_payment_reminder" value="1"
                                {{ old('notify_payment_reminder', $notificationSettings['notify_payment_reminder'] ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="notify_payment_reminder" class="text-sm font-medium text-gray-700">
                                Payment Reminder
                            </label>
                        </div>
                    </div>

                    <!-- Fee Due Alert -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_fee_due" id="notify_fee_due" value="1"
                                {{ old('notify_fee_due', $notificationSettings['notify_fee_due'] ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="notify_fee_due" class="text-sm font-medium text-gray-700">
                                Fee Due Alert
                            </label>
                        </div>
                    </div>

                    <!-- Attendance Alert -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_attendance" id="notify_attendance" value="1"
                                {{ old('notify_attendance', $notificationSettings['notify_attendance'] ?? false) ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="notify_attendance" class="text-sm font-medium text-gray-700">
                                Attendance Alert
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Important Security Note</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>All sensitive information (passwords, API keys) is encrypted before storage. Never share your credentials with anyone.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            Save Notification Settings
        </button>
    </div>
</form>

<script>
function toggleEmailSettings() {
    const enabled = document.getElementById('email_enabled').checked;
    document.getElementById('email-settings').style.display = enabled ? 'block' : 'none';
}

function toggleSmsSettings() {
    const enabled = document.getElementById('sms_enabled').checked;
    document.getElementById('sms-settings').style.display = enabled ? 'block' : 'none';
}

function toggleSmsProviderFields() {
    const provider = document.getElementById('sms_provider').value;
    document.getElementById('msg91-settings').style.display = provider === 'msg91' ? 'contents' : 'none';
}

function sendTestEmail() {
    // Implementation for test email
    alert('Test email functionality will be implemented in the backend');
}

function sendTestSms() {
    const phone = document.getElementById('test_phone').value;
    if (!phone || phone.length !== 10) {
        alert('Please enter a valid 10-digit phone number');
        return;
    }
    // Implementation for test SMS
    alert('Test SMS functionality will be implemented in the backend');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleEmailSettings();
    toggleSmsSettings();
    toggleSmsProviderFields();
});
</script>

