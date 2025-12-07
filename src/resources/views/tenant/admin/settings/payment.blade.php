<form action="{{ url('/admin/settings/payment') }}" method="POST" class="space-y-6">
    @csrf

    <div class="space-y-6">
        <!-- Payment Gateway Configuration -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Gateway Settings</h3>

            <div class="grid grid-cols-1 gap-6">
                <!-- Enable Online Payments -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="enable_online_payments" id="enable_online_payments" value="1"
                            {{ old('enable_online_payments', $paymentSettings['enable_online_payments'] ?? false) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                            onchange="togglePaymentGateway()">
                    </div>
                    <div class="ml-3">
                        <label for="enable_online_payments" class="font-medium text-gray-700">
                            Enable Online Payments
                        </label>
                        <p class="text-sm text-gray-500">Allow parents to pay fees online through payment gateway</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Gateway Selection -->
        <div id="gateway-settings" class="pt-6 border-t border-gray-200" style="display: {{ old('enable_online_payments', $paymentSettings['enable_online_payments'] ?? false) ? 'block' : 'none' }};">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Gateway Configuration</h3>

            <div class="grid grid-cols-1 gap-6">
                <!-- Gateway Provider -->
                <div>
                    <label for="payment_gateway" class="block text-sm font-medium text-gray-700">
                        Payment Gateway Provider
                    </label>
                    <select name="payment_gateway" id="payment_gateway"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('payment_gateway') border-red-300 @enderror"
                        onchange="toggleGatewayFields()">
                        <option value="">Select Gateway</option>
                        <option value="razorpay" {{ old('payment_gateway', $paymentSettings['payment_gateway'] ?? '') == 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                        <option value="stripe" {{ old('payment_gateway', $paymentSettings['payment_gateway'] ?? '') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                        <option value="payu" {{ old('payment_gateway', $paymentSettings['payment_gateway'] ?? '') == 'payu' ? 'selected' : '' }}>PayU</option>
                        <option value="phonepe" {{ old('payment_gateway', $paymentSettings['payment_gateway'] ?? '') == 'phonepe' ? 'selected' : '' }}>PhonePe</option>
                    </select>
                    @error('payment_gateway')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Razorpay Settings -->
                <div id="razorpay-settings" style="display: {{ old('payment_gateway', $paymentSettings['payment_gateway'] ?? '') == 'razorpay' ? 'block' : 'none' }};">
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-blue-800">Razorpay Configuration</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Get your API keys from Razorpay Dashboard: <a href="https://dashboard.razorpay.com/" target="_blank" class="underline">dashboard.razorpay.com</a></p>
                                    <p class="mt-1">Use Test keys for testing and Live keys for production.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Razorpay Key ID -->
                        <div>
                            <label for="razorpay_key_id" class="block text-sm font-medium text-gray-700">
                                Razorpay Key ID
                            </label>
                            <input type="text" name="razorpay_key_id" id="razorpay_key_id"
                                value="{{ old('razorpay_key_id', $paymentSettings['razorpay_key_id'] ?? '') }}"
                                placeholder="rzp_test_xxxxxxxxxx or rzp_live_xxxxxxxxxx"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('razorpay_key_id') border-red-300 @enderror">
                            @error('razorpay_key_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Razorpay Key Secret -->
                        <div>
                            <label for="razorpay_key_secret" class="block text-sm font-medium text-gray-700">
                                Razorpay Key Secret
                            </label>
                            <input type="password" name="razorpay_key_secret" id="razorpay_key_secret"
                                value="{{ old('razorpay_key_secret', $paymentSettings['razorpay_key_secret'] ?? '') }}"
                                placeholder="••••••••••••••••"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('razorpay_key_secret') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Keep this secret and secure</p>
                            @error('razorpay_key_secret')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Webhook Secret -->
                        <div>
                            <label for="razorpay_webhook_secret" class="block text-sm font-medium text-gray-700">
                                Webhook Secret (Optional)
                            </label>
                            <input type="password" name="razorpay_webhook_secret" id="razorpay_webhook_secret"
                                value="{{ old('razorpay_webhook_secret', $paymentSettings['razorpay_webhook_secret'] ?? '') }}"
                                placeholder="••••••••••••••••"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <p class="mt-1 text-sm text-gray-500">For payment verification webhooks</p>
                        </div>

                        <!-- Test Mode -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5 mt-1">
                                <input type="checkbox" name="razorpay_test_mode" id="razorpay_test_mode" value="1"
                                    {{ old('razorpay_test_mode', $paymentSettings['razorpay_test_mode'] ?? true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label for="razorpay_test_mode" class="font-medium text-gray-700">
                                    Test Mode
                                </label>
                                <p class="text-sm text-gray-500">Use test API keys for testing (recommended until production)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stripe Settings (Hidden by default) -->
                <div id="stripe-settings" style="display: {{ old('payment_gateway', $paymentSettings['payment_gateway'] ?? '') == 'stripe' ? 'block' : 'none' }};">
                    <div class="bg-purple-50 border border-purple-200 rounded-md p-4 mb-4">
                        <p class="text-sm text-purple-700">Stripe configuration (Coming Soon)</p>
                    </div>
                </div>

                <!-- PayU Settings (Hidden by default) -->
                <div id="payu-settings" style="display: {{ old('payment_gateway', $paymentSettings['payment_gateway'] ?? '') == 'payu' ? 'block' : 'none' }};">
                    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                        <p class="text-sm text-green-700">PayU configuration (Coming Soon)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offline Payment Settings -->
        <div class="pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Offline Payment Settings</h3>

            <div class="space-y-4">
                <!-- Payment Methods -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Accepted Payment Methods
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @php
                            $paymentMethods = old('payment_methods', $paymentSettings['payment_methods'] ?? ['cash', 'cheque', 'card', 'upi']);
                            // Handle if payment_methods is stored as JSON string
                            if (is_string($paymentMethods)) {
                                $paymentMethods = json_decode($paymentMethods, true) ?? ['cash', 'cheque', 'card', 'upi'];
                            }
                            $methods = ['cash' => 'Cash', 'cheque' => 'Cheque', 'card' => 'Card/POS', 'upi' => 'UPI', 'net_banking' => 'Net Banking', 'demand_draft' => 'Demand Draft'];
                        @endphp
                        @foreach($methods as $value => $label)
                        <div class="flex items-center">
                            <input type="checkbox" name="payment_methods[]" value="{{ $value }}"
                                id="payment_{{ $value }}"
                                {{ in_array($value, $paymentMethods ?? []) ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="payment_{{ $value }}" class="ml-2 block text-sm text-gray-700">
                                {{ $label }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Auto-generate Receipts -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="auto_generate_receipt" id="auto_generate_receipt" value="1"
                            {{ old('auto_generate_receipt', $paymentSettings['auto_generate_receipt'] ?? true) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3">
                        <label for="auto_generate_receipt" class="font-medium text-gray-700">
                            Auto-generate Receipts
                        </label>
                        <p class="text-sm text-gray-500">Automatically generate receipt on payment confirmation</p>
                    </div>
                </div>

                <!-- Payment Reminder Days -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="payment_reminder_days" class="block text-sm font-medium text-gray-700">
                            Payment Reminder (days before due date)
                        </label>
                        <input type="number" name="payment_reminder_days" id="payment_reminder_days"
                            value="{{ old('payment_reminder_days', $paymentSettings['payment_reminder_days'] ?? 7) }}"
                            min="0" max="30"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-sm text-gray-500">Send reminder X days before due date</p>
                    </div>

                    <div>
                        <label for="late_fee_percentage" class="block text-sm font-medium text-gray-700">
                            Late Fee Percentage (%)
                        </label>
                        <input type="number" name="late_fee_percentage" id="late_fee_percentage"
                            value="{{ old('late_fee_percentage', $paymentSettings['late_fee_percentage'] ?? 0) }}"
                            min="0" max="100" step="0.1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-sm text-gray-500">Late fee as percentage of due amount (0 = disabled)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Settings -->
        <div class="pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Receipt Settings</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Receipt Prefix -->
                <div>
                    <label for="receipt_prefix" class="block text-sm font-medium text-gray-700">
                        Receipt Number Prefix
                    </label>
                    <input type="text" name="receipt_prefix" id="receipt_prefix"
                        value="{{ old('receipt_prefix', $paymentSettings['receipt_prefix'] ?? 'REC') }}"
                        maxlength="10"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">e.g., REC-2025-0001</p>
                </div>

                <!-- Invoice Prefix -->
                <div>
                    <label for="invoice_prefix" class="block text-sm font-medium text-gray-700">
                        Invoice Number Prefix
                    </label>
                    <input type="text" name="invoice_prefix" id="invoice_prefix"
                        value="{{ old('invoice_prefix', $paymentSettings['invoice_prefix'] ?? 'INV') }}"
                        maxlength="10"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">e.g., INV-2025-0001</p>
                </div>

                <!-- Currency -->
                <div>
                    <label for="currency_code" class="block text-sm font-medium text-gray-700">
                        Currency
                    </label>
                    <select name="currency_code" id="currency_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="INR" {{ old('currency_code', $paymentSettings['currency_code'] ?? 'INR') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                        <option value="USD" {{ old('currency_code', $paymentSettings['currency_code'] ?? 'INR') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="EUR" {{ old('currency_code', $paymentSettings['currency_code'] ?? 'INR') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                        <option value="GBP" {{ old('currency_code', $paymentSettings['currency_code'] ?? 'INR') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                    </select>
                </div>

                <!-- Tax/GST -->
                <div>
                    <label for="tax_percentage" class="block text-sm font-medium text-gray-700">
                        Tax/GST Percentage (%)
                    </label>
                    <input type="number" name="tax_percentage" id="tax_percentage"
                        value="{{ old('tax_percentage', $paymentSettings['tax_percentage'] ?? 0) }}"
                        min="0" max="100" step="0.1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">Tax to be added on fee amounts (0 = no tax)</p>
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Settings</h3>

            <div class="space-y-4">
                <!-- Email Receipts -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="email_receipts" id="email_receipts" value="1"
                            {{ old('email_receipts', $paymentSettings['email_receipts'] ?? true) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3">
                        <label for="email_receipts" class="font-medium text-gray-700">
                            Email Receipts to Parents
                        </label>
                        <p class="text-sm text-gray-500">Send payment receipt via email automatically</p>
                    </div>
                </div>

                <!-- SMS Notifications -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="sms_payment_confirmation" id="sms_payment_confirmation" value="1"
                            {{ old('sms_payment_confirmation', $paymentSettings['sms_payment_confirmation'] ?? false) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3">
                        <label for="sms_payment_confirmation" class="font-medium text-gray-700">
                            SMS Payment Confirmation
                        </label>
                        <p class="text-sm text-gray-500">Send SMS on successful payment (requires SMS gateway)</p>
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
                <h3 class="text-sm font-medium text-blue-800">Payment Settings Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Configure payment gateway and collection settings. API keys are stored securely and encrypted.</p>
                    <p class="mt-1">Always use test keys in development and live keys only in production.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            Save Payment Settings
        </button>
    </div>
</form>

<script>
function togglePaymentGateway() {
    const enabled = document.getElementById('enable_online_payments').checked;
    document.getElementById('gateway-settings').style.display = enabled ? 'block' : 'none';
}

function toggleGatewayFields() {
    const gateway = document.getElementById('payment_gateway').value;

    // Hide all gateway-specific settings
    document.getElementById('razorpay-settings').style.display = 'none';
    document.getElementById('stripe-settings').style.display = 'none';
    document.getElementById('payu-settings').style.display = 'none';

    // Show selected gateway settings
    if (gateway === 'razorpay') {
        document.getElementById('razorpay-settings').style.display = 'block';
    } else if (gateway === 'stripe') {
        document.getElementById('stripe-settings').style.display = 'block';
    } else if (gateway === 'payu') {
        document.getElementById('payu-settings').style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePaymentGateway();
    toggleGatewayFields();
});
</script>

