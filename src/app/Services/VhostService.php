<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class VhostService
{
    /**
     * Get the path to the Herd vhost configuration file.
     */
    public function getVhostPath(): string
    {
        $hostingType = config('all.hosting.type', 'laravel-herd');
        $hostingConfig = config("all.hosting.server.{$hostingType}");

        if ($hostingType === 'laravel-herd') {
            $path = $hostingConfig['vhost_path'] ?? '~/.config/herd/config/nginx/valet.conf';
            // Expand ~ to home directory
            if (str_starts_with($path, '~')) {
                $homeDir = $_SERVER['HOME'] ?? getenv('HOME');
                return str_replace('~', $homeDir, $path);
            }
            return $path;
        }

        return $hostingConfig['vhost_path'] ?? '/etc/nginx/sites-available/default';
    }

    /**
     * Get the path to the Herd configuration directory.
     */
    public function getHerdConfigPath(): string
    {
        $hostingType = config('all.hosting.type', 'laravel-herd');
        $hostingConfig = config("all.hosting.server.{$hostingType}");

        if ($hostingType === 'laravel-herd') {
            $path = $hostingConfig['config_path'] ?? '~/.config/herd';
            // Expand ~ to home directory
            if (str_starts_with($path, '~')) {
                $homeDir = $_SERVER['HOME'] ?? getenv('HOME');
                return str_replace('~', $homeDir, $path);
            }
            return $path;
        }

        return $hostingConfig['config_path'] ?? '/etc/nginx';
    }

    /**
     * Get the path to the Herd main configuration file.
     */
    public function getHerdConfigFile(): string
    {
        return $this->getHerdConfigPath() . '/config.json';
    }

    /**
     * Get the path to the project .herd.yml file.
     */
    public function getHerdYmlPath(): string
    {
        return base_path('.herd.yml');
    }

    /**
     * Get the backup directory path.
     */
    public function getBackupDirectory(): string
    {
        return base_path('storage/backups/herd');
    }

    /**
     * Ensure backup directory exists.
     */
    private function ensureBackupDirectory(): string
    {
        $backupDir = $this->getBackupDirectory();

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        return $backupDir;
    }

            /**
     * Generate backup filename with timestamp.
     */
    private function generateBackupFilename(string $originalFilename): string
    {
        $timestamp = date('Y-m-d-H-i-s');
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $basename = pathinfo($originalFilename, PATHINFO_FILENAME);

        // Always use the full original filename as prefix for backup files
        return "{$originalFilename}.backup.{$timestamp}";
    }

    /**
     * Get the current vhost configuration content.
     */
    public function getVhostContent(): string
    {
        $path = $this->getVhostPath();

        if (!File::exists($path)) {
            return $this->getDefaultVhostContent();
        }

        return File::get($path);
    }

    /**
     * Update the vhost configuration content.
     */
    public function updateVhostContent(string $content): bool
    {
        try {
            $path = $this->getVhostPath();

            // Create directory if it doesn't exist
            $directory = dirname($path);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Backup the current file
            if (File::exists($path)) {
                $backupDir = $this->ensureBackupDirectory();
                $backupFilename = $this->generateBackupFilename(basename($path));
                $backupPath = $backupDir . '/' . $backupFilename;
                File::copy($path, $backupPath);

                Log::info('Vhost configuration backed up', [
                    'original_path' => $path,
                    'backup_path' => $backupPath
                ]);
            }

            // Write the new content
            File::put($path, $content);

            // Log the change
            Log::info('Vhost configuration updated', [
                'path' => $path,
                'size' => strlen($content)
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update vhost configuration', [
                'error' => $e->getMessage(),
                'path' => $this->getVhostPath()
            ]);

            return false;
        }
    }

    /**
     * Get backup files for the vhost configuration.
     */
    public function getBackupFiles(): array
    {
        $backupDir = $this->getBackupDirectory();
        $filename = basename($this->getVhostPath());

        if (!File::exists($backupDir)) {
            return [];
        }

                $files = scandir($backupDir);
        $backups = [];

        foreach ($files as $name) {
            if ($name !== '.' && $name !== '..') {
                $filePath = $backupDir . '/' . $name;
                if (is_file($filePath)) {
                    // Look for backup files with pattern: filename.backup.timestamp
                    // Handle both old pattern (.herd.backup.) and new pattern (.herd.yml.backup.)
                    if (str_contains($name, '.backup.') &&
                        (str_starts_with($name, $filename) || str_starts_with($name, str_replace('.yml', '', $filename)))) {
                        $backups[] = [
                            'name' => $name,
                            'path' => $filePath,
                            'size' => filesize($filePath),
                            'modified' => filemtime($filePath),
                            'date' => date('Y-m-d H:i:s', filemtime($filePath))
                        ];
                    }
                }
            }
        }

        // Sort by modification time (newest first)
        usort($backups, fn($a, $b) => $b['modified'] <=> $a['modified']);

        return $backups;
    }

    /**
     * Get backup files for Herd configuration.
     */
    public function getHerdConfigBackupFiles(): array
    {
        $backupDir = $this->getBackupDirectory();
        $filename = basename($this->getHerdConfigFile());

        if (!File::exists($backupDir)) {
            return [];
        }

                $files = scandir($backupDir);
        $backups = [];

        foreach ($files as $name) {
            if ($name !== '.' && $name !== '..') {
                $filePath = $backupDir . '/' . $name;
                if (is_file($filePath)) {
                    // Look for backup files with pattern: filename.backup.timestamp
                    // Handle both old pattern (.herd.backup.) and new pattern (.herd.yml.backup.)
                    if (str_contains($name, '.backup.') &&
                        (str_starts_with($name, $filename) || str_starts_with($name, str_replace('.yml', '', $filename)))) {
                        $backups[] = [
                            'name' => $name,
                            'path' => $filePath,
                            'size' => filesize($filePath),
                            'modified' => filemtime($filePath),
                            'date' => date('Y-m-d H:i:s', filemtime($filePath))
                        ];
                    }
                }
            }
        }

        // Sort by modification time (newest first)
        usort($backups, fn($a, $b) => $b['modified'] <=> $a['modified']);

        return $backups;
    }

    /**
     * Get backup files for .herd.yml configuration.
     */
    public function getHerdYmlBackupFiles(): array
    {
        $backupDir = $this->getBackupDirectory();
        $filename = basename($this->getHerdYmlPath());

        if (!File::exists($backupDir)) {
            return [];
        }

                $files = scandir($backupDir);
        $backups = [];

        foreach ($files as $name) {
            if ($name !== '.' && $name !== '..') {
                $filePath = $backupDir . '/' . $name;
                if (is_file($filePath)) {
                    // Look for backup files with pattern: filename.backup.timestamp
                    // Handle both old pattern (.herd.backup.) and new pattern (.herd.yml.backup.)
                    if (str_contains($name, '.backup.') &&
                        (str_starts_with($name, $filename) || str_starts_with($name, str_replace('.yml', '', $filename)))) {
                        $backups[] = [
                            'name' => $name,
                            'path' => $filePath,
                            'size' => filesize($filePath),
                            'modified' => filemtime($filePath),
                            'date' => date('Y-m-d H:i:s', filemtime($filePath))
                        ];
                    }
                }
            }
        }

        // Sort by modification time (newest first)
        usort($backups, fn($a, $b) => $b['modified'] <=> $a['modified']);

        return $backups;
    }

    /**
     * Clean up old backup files (keep only last 10 backups per file type).
     */
    public function cleanupOldBackups(): array
    {
        $backupDir = $this->getBackupDirectory();
        $cleaned = [];

        if (!File::exists($backupDir)) {
            return $cleaned;
        }

        $files = File::files($backupDir);
        $backupGroups = [];

        // Group backups by file type
        foreach ($files as $file) {
            $name = $file->getFilename();
            if (str_contains($name, '.backup.')) {
                $baseName = explode('.backup.', $name)[0];
                if (!isset($backupGroups[$baseName])) {
                    $backupGroups[$baseName] = [];
                }
                $backupGroups[$baseName][] = [
                    'file' => $file,
                    'modified' => $file->getMTime()
                ];
            }
        }

        // Clean up each group (keep only last 10)
        foreach ($backupGroups as $baseName => $backups) {
            if (count($backups) > 10) {
                // Sort by modification time (newest first)
                usort($backups, fn($a, $b) => $b['modified'] <=> $a['modified']);

                // Remove old backups (keep first 10)
                $toRemove = array_slice($backups, 10);

                foreach ($toRemove as $backup) {
                    $file = $backup['file'];
                    if (File::delete($file->getPathname())) {
                        $cleaned[] = $file->getFilename();
                    }
                }
            }
        }

        if (!empty($cleaned)) {
            Log::info('Old backup files cleaned up', [
                'backup_dir' => $backupDir,
                'cleaned_files' => $cleaned
            ]);
        }

        return $cleaned;
    }

    /**
     * Restore from a backup file.
     */
    public function restoreFromBackup(string $backupPath): bool
    {
        try {
            $currentPath = $this->getVhostPath();

            if (!File::exists($backupPath)) {
                return false;
            }

            // Create directory if it doesn't exist
            $directory = dirname($currentPath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Copy backup to current file
            File::copy($backupPath, $currentPath);

            Log::info('Vhost configuration restored from backup', [
                'backup_path' => $backupPath,
                'current_path' => $currentPath
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to restore vhost configuration from backup', [
                'error' => $e->getMessage(),
                'backup_path' => $backupPath
            ]);

            return false;
        }
    }

    /**
     * Get default vhost configuration content.
     */
    private function getDefaultVhostContent(): string
    {
        return <<<'NGINX'
# Herd Nginx Configuration
# This file is managed by School ERP Admin Panel

server {
    listen 80;
    server_name myschool.test *.myschool.test;
    root /Users/rohitk/react/lara/school-erp/src/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Handle tenant subdomains
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/opt/homebrew/var/run/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Deny access to sensitive files
    location ~ /(\.env|composer\.(json|lock)|package\.(json|lock)|yarn\.lock|\.git) {
        deny all;
    }
}
NGINX;
    }

    /**
     * Validate vhost configuration syntax.
     */
    public function validateVhostContent(string $content): array
    {
        $errors = [];
        $warnings = [];

        // Basic validation checks
        if (empty(trim($content))) {
            $errors[] = 'Configuration content cannot be empty';
        }

        if (!str_contains($content, 'server {')) {
            $errors[] = 'Missing server block';
        }

        if (!str_contains($content, 'listen')) {
            $errors[] = 'Missing listen directive';
        }

        if (!str_contains($content, 'server_name')) {
            $errors[] = 'Missing server_name directive';
        }

        if (!str_contains($content, 'root')) {
            $errors[] = 'Missing root directive';
        }

        // Check for common issues
        if (str_contains($content, 'server_name myschool.test')) {
            $warnings[] = 'Make sure myschool.test domain is properly configured';
        }

        if (!str_contains($content, '*.myschool.test')) {
            $warnings[] = 'Wildcard subdomain (*.myschool.test) not found - tenant subdomains may not work';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Get Herd configuration content.
     */
    public function getHerdConfigContent(): string
    {
        $path = $this->getHerdConfigFile();

        if (!File::exists($path)) {
            return $this->getDefaultHerdConfig();
        }

        return File::get($path);
    }

    /**
     * Update Herd configuration content.
     */
    public function updateHerdConfigContent(string $content): bool
    {
        try {
            $path = $this->getHerdConfigFile();

            // Create directory if it doesn't exist
            $directory = dirname($path);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Backup the current file
            if (File::exists($path)) {
                $backupDir = $this->ensureBackupDirectory();
                $backupFilename = $this->generateBackupFilename(basename($path));
                $backupPath = $backupDir . '/' . $backupFilename;
                File::copy($path, $backupPath);

                Log::info('Herd configuration backed up', [
                    'original_path' => $path,
                    'backup_path' => $backupPath
                ]);
            }

            // Write the new content
            File::put($path, $content);

            // Log the change
            Log::info('Herd configuration updated', [
                'path' => $path,
                'size' => strlen($content)
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update Herd configuration', [
                'error' => $e->getMessage(),
                'path' => $this->getHerdConfigFile()
            ]);

            return false;
        }
    }

    /**
     * Get default Herd configuration.
     */
    private function getDefaultHerdConfig(): string
    {
        return json_encode([
            'tld' => 'test',
            'loopback' => '127.0.0.1',
            'paths' => [
                '/Users/rohitk/react/lara/school-erp/src'
            ],
            'nginx' => [
                'config' => '/Users/rohitk/.config/herd/config/nginx/valet.conf'
            ],
            'php' => [
                'version' => '8.3'
            ]
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Get .herd.yml configuration content.
     */
    public function getHerdYmlContent(): string
    {
        $path = $this->getHerdYmlPath();

        if (!File::exists($path)) {
            return $this->getDefaultHerdYml();
        }

        return File::get($path);
    }

    /**
     * Update .herd.yml configuration content.
     */
    public function updateHerdYmlContent(string $content): bool
    {
        try {
            $path = $this->getHerdYmlPath();

            // Backup the current file
            if (File::exists($path)) {
                $backupDir = $this->ensureBackupDirectory();
                $backupFilename = $this->generateBackupFilename(basename($path));
                $backupPath = $backupDir . '/' . $backupFilename;
                File::copy($path, $backupPath);

                Log::info('.herd.yml configuration backed up', [
                    'original_path' => $path,
                    'backup_path' => $backupPath
                ]);
            }

            // Write the new content
            File::put($path, $content);

            // Log the change
            Log::info('.herd.yml configuration updated', [
                'path' => $path,
                'size' => strlen($content)
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update .herd.yml configuration', [
                'error' => $e->getMessage(),
                'path' => $this->getHerdYmlPath()
            ]);

            return false;
        }
    }

    /**
     * Get default .herd.yml configuration.
     */
    private function getDefaultHerdYml(): string
    {
        return "name: school-erp
domain: myschool.test
subdomains:
  - app
  - schoola
  - schoolb
  - schoolc

php: 8.3
mysql: 8.0
redis: 7.0
";
    }

    /**
     * Start Herd service.
     */
    public function startHerd(): array
    {
        try {
            $output = shell_exec('herd start 2>&1');
            $success = $this->isHerdRunning();

            Log::info('Herd start command executed', [
                'output' => $output,
                'success' => $success
            ]);

            return [
                'success' => $success,
                'output' => $output,
                'message' => $success ? 'Herd started successfully' : 'Failed to start Herd'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to start Herd', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'output' => $e->getMessage(),
                'message' => 'Error starting Herd: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Stop Herd service.
     */
    public function stopHerd(): array
    {
        try {
            $output = shell_exec('herd stop 2>&1');
            $stopped = !$this->isHerdRunning();

            Log::info('Herd stop command executed', [
                'output' => $output,
                'stopped' => $stopped
            ]);

            return [
                'success' => $stopped,
                'output' => $output,
                'message' => $stopped ? 'Herd stopped successfully' : 'Failed to stop Herd'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to stop Herd', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'output' => $e->getMessage(),
                'message' => 'Error stopping Herd: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Restart Herd service.
     */
    public function restartHerd(): array
    {
        try {
            $output = shell_exec('herd restart 2>&1');
            $running = $this->isHerdRunning();

            Log::info('Herd restart command executed', [
                'output' => $output,
                'running' => $running
            ]);

            return [
                'success' => $running,
                'output' => $output,
                'message' => $running ? 'Herd restarted successfully' : 'Failed to restart Herd'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to restart Herd', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'output' => $e->getMessage(),
                'message' => 'Error restarting Herd: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Start Nginx service.
     */
    public function startNginx(): array
    {
        try {
            $output = shell_exec('sudo nginx 2>&1');
            $running = $this->isNginxRunning();

            Log::info('Nginx start command executed', [
                'output' => $output,
                'running' => $running
            ]);

            return [
                'success' => $running,
                'output' => $output,
                'message' => $running ? 'Nginx started successfully' : 'Failed to start Nginx'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to start Nginx', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'output' => $e->getMessage(),
                'message' => 'Error starting Nginx: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Stop Nginx service.
     */
    public function stopNginx(): array
    {
        try {
            $output = shell_exec('sudo nginx -s stop 2>&1');
            $stopped = !$this->isNginxRunning();

            Log::info('Nginx stop command executed', [
                'output' => $output,
                'stopped' => $stopped
            ]);

            return [
                'success' => $stopped,
                'output' => $output,
                'message' => $stopped ? 'Nginx stopped successfully' : 'Failed to stop Nginx'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to stop Nginx', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'output' => $e->getMessage(),
                'message' => 'Error stopping Nginx: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Restart Nginx service.
     */
    public function restartNginx(): array
    {
        try {
            $output = shell_exec('sudo nginx -s reload 2>&1');
            $running = $this->isNginxRunning();

            Log::info('Nginx restart command executed', [
                'output' => $output,
                'running' => $running
            ]);

            return [
                'success' => $running,
                'output' => $output,
                'message' => $running ? 'Nginx restarted successfully' : 'Failed to restart Nginx'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to restart Nginx', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'output' => $e->getMessage(),
                'message' => 'Error restarting Nginx: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get the current hosting type.
     */
    public function getHostingType(): string
    {
        return config('all.hosting.type', 'laravel-herd');
    }

    /**
     * Get hosting configuration for the current type.
     */
    public function getHostingConfig(): array
    {
        $hostingType = $this->getHostingType();
        return config("all.hosting.server.{$hostingType}", []);
    }

    /**
     * Check if vhost management is enabled.
     */
    public function isVhostManagementEnabled(): bool
    {
        return config('all.features.vhost_management', true);
    }

    /**
     * Get system information for vhost management.
     */
    public function getSystemInfo(): array
    {
        $hostingType = $this->getHostingType();
        $hostingConfig = $this->getHostingConfig();

        return [
            'hosting_type' => $hostingType,
            'hosting_config' => $hostingConfig,
            'vhost_management_enabled' => $this->isVhostManagementEnabled(),
            'vhost_path' => $this->getVhostPath(),
            'vhost_exists' => File::exists($this->getVhostPath()),
            'vhost_writable' => File::isWritable(dirname($this->getVhostPath())),
            'herd_config_path' => $this->getHerdConfigPath(),
            'herd_config_exists' => File::exists($this->getHerdConfigFile()),
            'herd_config_writable' => File::isWritable($this->getHerdConfigPath()),
            'herd_yml_path' => $this->getHerdYmlPath(),
            'herd_yml_exists' => File::exists($this->getHerdYmlPath()),
            'herd_yml_writable' => File::isWritable(dirname($this->getHerdYmlPath())),
            'herd_running' => $this->isHerdRunning(),
            'nginx_running' => $this->isNginxRunning(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
    }

    /**
     * Check if Herd is running.
     */
    private function isHerdRunning(): bool
    {
        try {
            $output = shell_exec('ps aux | grep -i herd | grep -v grep');
            return !empty(trim($output));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if Nginx is running.
     */
    private function isNginxRunning(): bool
    {
        try {
            $output = shell_exec('ps aux | grep -i nginx | grep -v grep');
            return !empty(trim($output));
        } catch (\Exception $e) {
            return false;
        }
    }
}
