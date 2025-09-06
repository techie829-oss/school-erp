<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TenantUserValidationService;
use App\Models\Tenant;
use App\Models\AdminUser;

class TestTenantDomainValidation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:tenant-domain-validation {email} {subdomain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test tenant domain validation for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $subdomain = $this->argument('subdomain');

        $this->info("Testing domain validation for user: {$email} on subdomain: {$subdomain}");

        $validationService = new TenantUserValidationService();

        // Test domain access
        $hasAccess = $validationService->validateDomainAccess($email, $subdomain);
        
        if ($hasAccess) {
            $this->info("✅ User has access to this domain");
        } else {
            $this->error("❌ User does NOT have access to this domain");
        }

        // Show allowed domains for this user
        $allowedDomains = $validationService->getAllowedDomainsForUser($email);
        
        if (!empty($allowedDomains)) {
            $this->info("Allowed domains for this user:");
            foreach ($allowedDomains as $domain) {
                $this->line("  - {$domain}");
            }
        } else {
            $this->warn("No allowed domains found for this user");
        }

        // Show tenant information
        $tenant = $validationService->getTenantBySubdomain($subdomain);
        if ($tenant) {
            $this->info("Tenant found:");
            $this->line("  - ID: {$tenant->id}");
            $this->line("  - Name: " . ($tenant->data['name'] ?? 'Unknown'));
            $this->line("  - Database Strategy: " . ($tenant->data['database_strategy'] ?? 'Unknown'));
        } else {
            $this->error("Tenant not found for subdomain: {$subdomain}");
        }

        return 0;
    }
}
