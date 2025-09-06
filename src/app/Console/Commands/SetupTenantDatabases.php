<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Illuminate\Console\Command;

class SetupTenantDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:setup-databases {--force : Force setup even if databases exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create and setup separate databases for tenants that use separate database strategy';

    protected $databaseService;

    public function __construct(TenantDatabaseService $databaseService)
    {
        parent::__construct();
        $this->databaseService = $databaseService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up separate databases for tenants...');

        $tenants = Tenant::whereJsonContains('data->database_strategy', 'separate')->get();

        if ($tenants->isEmpty()) {
            $this->info('No tenants with separate database strategy found.');
            return;
        }

        $this->info("Found {$tenants->count()} tenants with separate database strategy.");

        $bar = $this->output->createProgressBar($tenants->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($tenants as $tenant) {
            try {
                $this->line("\nProcessing: {$tenant->data['name']} ({$tenant->database_name})");

                // Create database
                $result = $this->databaseService->createTenantDatabase($tenant);
                if (!$result['success']) {
                    $this->error("Failed to create database: {$result['message']}");
                    $errorCount++;
                    $bar->advance();
                    continue;
                }

                $this->info("✓ Database created: {$tenant->database_name}");

                // Run migrations
                $migrationResult = $this->databaseService->runTenantMigrations($tenant);
                if (!$migrationResult['success']) {
                    $this->error("Failed to run migrations: {$migrationResult['message']}");
                    $errorCount++;
                    $bar->advance();
                    continue;
                }

                $this->info("✓ Migrations completed");

                // Test connection
                $testResult = $this->databaseService->testTenantConnection($tenant);
                if (!$testResult['success']) {
                    $this->error("Connection test failed: {$testResult['message']}");
                    $errorCount++;
                    $bar->advance();
                    continue;
                }

                $this->info("✓ Connection test passed");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("Error processing {$tenant->data['name']}: " . $e->getMessage());
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("Setup completed!");
        $this->info("✓ Successfully processed: {$successCount} tenants");
        if ($errorCount > 0) {
            $this->error("✗ Failed to process: {$errorCount} tenants");
        }

        if ($successCount > 0) {
            $this->newLine();
            $this->info("Separate database tenants are now ready to use!");
            $this->info("You can test them by visiting their domains:");
            foreach ($tenants as $tenant) {
                if ($successCount > 0) {
                    $this->line("  - {$tenant->data['full_domain']}");
                }
            }
        }
    }
}
