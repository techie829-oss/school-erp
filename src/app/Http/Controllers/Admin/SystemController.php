<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class SystemController extends Controller
{
    public function overview()
    {
        // Get system statistics
        $stats = [
            // Application Stats
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('data->active', true)->count(),
            'total_users' => AdminUser::count(),
            'active_users' => AdminUser::where('is_active', true)->count(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'db_driver' => config('database.default'),
            'db_host' => config('database.connections.mysql.host'),
            'db_name' => config('database.connections.mysql.database'),
        ];

        // Get system resources
        $stats['memory_usage'] = $this->getMemoryUsage();
        $stats['disk_usage'] = $this->getDiskUsage();
        $stats['cpu_usage'] = $this->getCpuUsage();
        $stats['server_uptime'] = $this->getServerUptime();
        $stats['storage_path_size'] = $this->getDirectorySize(storage_path());
        $stats['cache_size'] = $this->getDirectorySize(storage_path('framework/cache'));
        $stats['logs_size'] = $this->getDirectorySize(storage_path('logs'));

        // Get database table count
        try {
            $tables = DB::select('SHOW TABLES');
            $stats['table_count'] = count($tables);
            $stats['db_status'] = 'Connected';
        } catch (\Exception $e) {
            $stats['table_count'] = 'N/A';
            $stats['db_status'] = 'Error';
        }

        // Get recent logs from Laravel log file
        $logs = $this->getRecentLogs(20);

        return view('admin.system.overview', compact('stats', 'logs'));
    }

    private function getMemoryUsage()
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');

        // Convert memory limit to bytes
        $memoryLimitBytes = $this->convertToBytes($memoryLimit);

        if ($memoryLimitBytes > 0) {
            $percentage = round(($memoryUsage / $memoryLimitBytes) * 100, 1);
        } else {
            $percentage = 0;
        }

        return [
            'used' => $this->formatBytes($memoryUsage),
            'limit' => $memoryLimit,
            'percentage' => $percentage
        ];
    }

    private function getDiskUsage()
    {
        $totalSpace = disk_total_space(base_path());
        $freeSpace = disk_free_space(base_path());
        $usedSpace = $totalSpace - $freeSpace;

        $percentage = $totalSpace > 0 ? round(($usedSpace / $totalSpace) * 100, 1) : 0;

        return [
            'used' => $this->formatBytes($usedSpace),
            'total' => $this->formatBytes($totalSpace),
            'free' => $this->formatBytes($freeSpace),
            'percentage' => $percentage
        ];
    }

    private function getCpuUsage()
    {
        try {
            // Try to get CPU usage (works on Unix/Linux/Mac)
            $load = sys_getloadavg();
            if ($load !== false && isset($load[0])) {
                return round($load[0] * 10, 1); // Approximate percentage
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return 'N/A';
    }

    private function getServerUptime()
    {
        try {
            $uptime = shell_exec('uptime');
            if ($uptime) {
                return trim($uptime);
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return 'N/A';
    }

    private function getDirectorySize($path)
    {
        if (!File::exists($path)) {
            return 'N/A';
        }

        try {
            $size = 0;
            foreach (File::allFiles($path) as $file) {
                $size += $file->getSize();
            }
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function convertToBytes($value)
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value)-1]);
        $value = (int) $value;

        switch($last) {
            case 'g': $value *= 1024;
            case 'm': $value *= 1024;
            case 'k': $value *= 1024;
        }

        return $value;
    }

    private function getRecentLogs($limit = 20)
    {
        try {
            $logPath = storage_path('logs/laravel.log');

            if (!File::exists($logPath)) {
                return [];
            }

            $content = File::get($logPath);
            $lines = explode("\n", $content);
            $lines = array_reverse($lines);

            $logs = [];
            $currentLog = null;
            $count = 0;

            foreach ($lines as $line) {
                if (empty(trim($line))) {
                    continue;
                }

                // Check if this is a new log entry (starts with [YYYY-MM-DD HH:MM:SS])
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+\w+\.(\w+):(.*)/', $line, $matches)) {
                    // Save previous log if exists
                    if ($currentLog !== null) {
                        $logs[] = $currentLog;
                        $count++;
                        if ($count >= $limit) {
                            break;
                        }
                    }

                    // Start new log entry
                    $level = strtoupper($matches[2]);
                    $message = trim($matches[3]);

                    $currentLog = [
                        'timestamp' => $matches[1],
                        'level' => $level,
                        'message' => $message,
                        'full_message' => $message
                    ];
                } elseif ($currentLog !== null) {
                    // Continuation of current log
                    $currentLog['full_message'] .= "\n" . $line;
                }
            }

            // Add last log if exists
            if ($currentLog !== null && $count < $limit) {
                $logs[] = $currentLog;
            }

            return $logs;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');

            return redirect()->route('admin.system.overview')
                ->with('success', 'Cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.system.overview')
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    public function clearRoutes()
    {
        try {
            Artisan::call('route:clear');

            return redirect()->route('admin.system.overview')
                ->with('success', 'Routes cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.system.overview')
                ->with('error', 'Failed to clear routes: ' . $e->getMessage());
        }
    }

    public function clearViews()
    {
        try {
            Artisan::call('view:clear');

            return redirect()->route('admin.system.overview')
                ->with('success', 'Views cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.system.overview')
                ->with('error', 'Failed to clear views: ' . $e->getMessage());
        }
    }

    public function logs(Request $request)
    {
        $level = $request->get('level', 'all');
        $search = $request->get('search', '');
        $limit = $request->get('limit', 100);

        // Get all logs
        $allLogs = $this->getRecentLogs(1000);

        // Filter by level
        if ($level !== 'all') {
            $allLogs = array_filter($allLogs, function($log) use ($level) {
                return strtolower($log['level']) === strtolower($level);
            });
        }

        // Filter by search term
        if (!empty($search)) {
            $allLogs = array_filter($allLogs, function($log) use ($search) {
                return stripos($log['message'], $search) !== false ||
                       stripos($log['full_message'], $search) !== false;
            });
        }

        // Limit results
        $logs = array_slice($allLogs, 0, $limit);

        // Get log statistics
        $stats = [
            'total' => count($allLogs),
            'error' => count(array_filter($allLogs, fn($l) => $l['level'] === 'ERROR')),
            'warning' => count(array_filter($allLogs, fn($l) => $l['level'] === 'WARNING')),
            'info' => count(array_filter($allLogs, fn($l) => $l['level'] === 'INFO')),
            'debug' => count(array_filter($allLogs, fn($l) => $l['level'] === 'DEBUG')),
        ];

        return view('admin.system.logs', compact('logs', 'stats', 'level', 'search', 'limit'));
    }

    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs/laravel.log');

            if (File::exists($logPath)) {
                // Backup current log
                $backupPath = storage_path('logs/laravel.log.backup.' . date('Y-m-d-H-i-s'));
                File::copy($logPath, $backupPath);

                // Clear the log file
                File::put($logPath, '');

                return redirect()->route('admin.system.logs')
                    ->with('success', 'Logs cleared successfully! Backup created.');
            }

            return redirect()->route('admin.system.logs')
                ->with('info', 'No log file found to clear.');
        } catch (\Exception $e) {
            return redirect()->route('admin.system.logs')
                ->with('error', 'Failed to clear logs: ' . $e->getMessage());
        }
    }
}

