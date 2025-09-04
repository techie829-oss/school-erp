@extends('layouts.admin')

@section('title', 'Vhost Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-secondary-900">Vhost Management</h1>
            <p class="text-secondary-600 mt-1">Manage Herd virtual host configuration files</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.vhost.show') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Config
            </a>
            <a href="{{ route('admin.vhost.edit') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Config
            </a>
        </div>
    </div>

    <!-- System Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-secondary-900 mb-4">System Status</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['vhost_exists'] ? 'bg-success' : 'bg-error' }}"></div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Vhost File</p>
                    <p class="text-xs text-secondary-600">{{ $systemInfo['vhost_exists'] ? 'Exists' : 'Not Found' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['vhost_writable'] ? 'bg-success' : 'bg-error' }}"></div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Writable</p>
                    <p class="text-xs text-secondary-600">{{ $systemInfo['vhost_writable'] ? 'Yes' : 'No' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['herd_running'] ? 'bg-success' : 'bg-warning' }}"></div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Herd</p>
                    <p class="text-xs text-secondary-600">{{ $systemInfo['herd_running'] ? 'Running' : 'Stopped' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['nginx_running'] ? 'bg-success' : 'bg-warning' }}"></div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Nginx</p>
                    <p class="text-xs text-secondary-600">{{ $systemInfo['nginx_running'] ? 'Running' : 'Stopped' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-secondary-900 mb-4">System Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">File Path</h3>
                <p class="text-sm text-secondary-600 font-mono bg-secondary-50 p-2 rounded">{{ $systemInfo['vhost_path'] }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">PHP Version</h3>
                <p class="text-sm text-secondary-600">{{ $systemInfo['php_version'] }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Laravel Version</h3>
                <p class="text-sm text-secondary-600">{{ $systemInfo['laravel_version'] }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Last Updated</h3>
                <p class="text-sm text-secondary-600">{{ $systemInfo['vhost_exists'] ? 'Check file timestamp' : 'Never' }}</p>
            </div>
        </div>
    </div>

    <!-- Backup Files -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-secondary-900">Backup Files</h2>
            <span class="text-sm text-secondary-600">{{ count($backupFiles) }} backup(s)</span>
        </div>

        @if(count($backupFiles) > 0)
            <div class="space-y-3">
                @foreach($backupFiles as $backup)
                    <div class="flex items-center justify-between p-3 bg-secondary-50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-secondary-900">{{ $backup['name'] }}</p>
                            <p class="text-xs text-secondary-600">{{ $backup['date'] }} • {{ number_format($backup['size'] / 1024, 1) }} KB</p>
                        </div>
                        <div class="flex space-x-2">
                            <form method="POST" action="{{ route('admin.vhost.restore') }}" class="inline">
                                @csrf
                                <input type="hidden" name="backup_path" value="{{ $backup['path'] }}">
                                <button type="submit" class="text-xs px-3 py-1 bg-primary-100 text-primary-700 rounded hover:bg-primary-200 transition-colors" onclick="return confirm('Are you sure you want to restore this backup? This will overwrite the current configuration.')">
                                    Restore
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-secondary-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-secondary-600">No backup files found</p>
                <p class="text-sm text-secondary-500">Backups are created automatically when you update the configuration</p>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-secondary-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.vhost.edit') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-secondary-50 transition-colors">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Edit Configuration</p>
                    <p class="text-xs text-secondary-600">Modify vhost settings</p>
                </div>
            </a>

            <a href="{{ route('admin.vhost.show') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-secondary-50 transition-colors">
                <div class="w-10 h-10 bg-accent-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">View Configuration</p>
                    <p class="text-xs text-secondary-600">Read-only view</p>
                </div>
            </a>

            <button onclick="refreshSystemInfo()" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-secondary-50 transition-colors">
                <div class="w-10 h-10 bg-success-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Refresh Status</p>
                    <p class="text-xs text-secondary-600">Update system info</p>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
function refreshSystemInfo() {
    // Reload the page to refresh system information
    window.location.reload();
}
</script>
@endsection
