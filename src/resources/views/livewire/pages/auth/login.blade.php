<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Check if email belongs to a tenant user (called on email input)
     */
    public function checkTenantUser(): void
    {
        $host = request()->getHost();
        $adminDomain = config('all.domains.admin');

        // Only check if we're on admin domain and email is not empty
        if ($host === $adminDomain && !empty($this->form->email)) {
            $this->checkForTenantUser();
        }
    }

    /**
     * Check if the user is a tenant user trying to login from admin domain
     */
    protected function checkForTenantUser(): void
    {
        // Check if user exists in any tenant database
        $user = \App\Models\AdminUser::where('email', $this->form->email)->first();

        if ($user && $user->admin_type === 'school_admin') {
            // Get the tenant for this user
            $tenant = \App\Models\Tenant::find($user->tenant_id);

            if ($tenant) {
                // Build the tenant login URL
                $tenantDomain = $tenant->data['subdomain'] . '.' . config('all.domains.primary');
                $tenantLoginUrl = 'http://' . $tenantDomain . '/login';

                // Store the redirect info in session for display
                session()->flash('tenant_redirect', [
                    'message' => 'This is a school administrator account.',
                    'tenant_name' => $tenant->data['name'] ?? 'Your School',
                    'login_url' => $tenantLoginUrl,
                    'tenant_domain' => $tenantDomain
                ]);

                // Don't throw validation error, just return
                return;
            }
        } else {
            // Clear any existing tenant redirect message if user is not a tenant
            session()->forget('tenant_redirect');
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // Redirect to the appropriate dashboard based on domain
        $redirectRoute = $this->getRedirectRoute();
        $this->redirectIntended(default: $redirectRoute, navigate: true);
    }

    /**
     * Get the appropriate redirect route based on the current domain and user type
     */
    protected function getRedirectRoute(): string
    {
        $host = request()->getHost();
        $adminDomain = config('all.domains.admin');

        // If we're on the admin domain, check if user is school_admin
        if ($host === $adminDomain) {
            $user = auth()->user();

            // If user is school_admin, redirect to their tenant domain
            if ($user && $user->admin_type === 'school_admin') {
                $tenantUrl = $user->getTenantUrl();
                if ($tenantUrl) {
                    return $tenantUrl;
                }
            }

            // For super_admin and super_manager, stay on admin domain
            return route('admin.dashboard', absolute: false);
        }

        // For tenant domains, redirect to tenant admin dashboard
        $tenant = tenant();
        if ($tenant && isset($tenant->data['subdomain'])) {
            return route('tenant.admin.dashboard', ['tenant' => $tenant->data['subdomain']], absolute: false);
        }

        // Fallback to admin dashboard if tenant detection fails
        return route('admin.dashboard', absolute: false);
    }

    /**
     * Get allowed domains for current user
     */
    public function getAllowedDomains(): array
    {
        if (empty($this->form->email)) {
            return [];
        }

        $validationService = new \App\Services\TenantUserValidationService();
        return $validationService->getAllowedDomainsForUser($this->form->email);
    }
}; ?>

<div>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-success/10 border border-success/20 rounded-xl text-success-700 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Tenant Redirect Message -->
    @if (session('tenant_redirect'))
        @php $redirect = session('tenant_redirect'); @endphp
        <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-xl">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-lg font-medium text-blue-900 mb-2">
                        {{ $redirect['message'] }}
                    </h3>
                    <p class="text-blue-800 mb-4">
                        You need to login at your school's specific domain: <strong>{{ $redirect['tenant_domain'] }}</strong>
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ $redirect['login_url'] }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Go to {{ $redirect['tenant_name'] }} Login
                        </a>
                        <button onclick="copyToClipboard('{{ $redirect['login_url'] }}')"
                                class="inline-flex items-center px-4 py-2 bg-white text-blue-600 text-sm font-medium rounded-lg border border-blue-300 hover:bg-blue-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copy URL
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Welcome Message -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-secondary-900 mb-2">Welcome Back</h2>
        <p class="text-secondary-600">Sign in to your account to continue</p>
    </div>

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-secondary-700 mb-2">
                Email Address
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input
                    wire:model.live="form.email"
                    wire:blur="checkTenantUser"
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    autocomplete="username"
                    class="block w-full pl-10 pr-3 py-3 border border-secondary-300 rounded-xl text-secondary-900 placeholder-secondary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200"
                    placeholder="Enter your email"
                />
            </div>
            @error('form.email')
                <p class="mt-2 text-sm text-error-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-secondary-700 mb-2">
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input
                    wire:model="form.password"
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="block w-full pl-10 pr-3 py-3 border border-secondary-300 rounded-xl text-secondary-900 placeholder-secondary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200"
                    placeholder="Enter your password"
                />
            </div>
            @error('form.password')
                <p class="mt-2 text-sm text-error-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember" class="flex items-center">
                <input
                    wire:model="form.remember"
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-secondary-300 rounded"
                />
                <span class="ml-2 text-sm text-secondary-700">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    wire:navigate
                    class="text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200"
                >
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]"
        >
            <svg wire:loading wire:target="login" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="login">Sign In</span>
            <span wire:loading wire:target="login">Signing In...</span>
        </button>


    </form>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show a temporary success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Copied!';
        button.classList.add('bg-green-50', 'text-green-600', 'border-green-300');
        button.classList.remove('bg-white', 'text-blue-600', 'border-blue-300');

        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-50', 'text-green-600', 'border-green-300');
            button.classList.add('bg-white', 'text-blue-600', 'border-blue-300');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Could not copy to clipboard');
    });
}
</script>
