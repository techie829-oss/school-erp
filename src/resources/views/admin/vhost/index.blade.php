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
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-secondary-900">System Status</h2>
                <p class="text-sm text-secondary-600">Hosting Type: <span class="font-medium text-primary-600">{{ $systemInfo['hosting_type_display'] }}</span></p>
            </div>
            <button onclick="refreshSystemStatus()" class="text-sm px-3 py-1 bg-secondary-100 text-secondary-700 rounded hover:bg-secondary-200 transition-colors">
                <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>
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

    <!-- Service Management -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-secondary-900 mb-4">Service Management</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($systemInfo['hosting_type'] === 'laravel-herd')
            <!-- Herd Controls -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-md font-medium text-secondary-900">Herd Service</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 rounded-full {{ $systemInfo['herd_running'] ? 'bg-success' : 'bg-error' }}"></div>
                        <span class="text-xs text-secondary-600">{{ $systemInfo['herd_running'] ? 'Running' : 'Stopped' }}</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="controlService('herd', 'start')" class="flex-1 px-3 py-2 text-xs bg-success-100 text-success-700 rounded hover:bg-success-200 transition-colors">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Start
                    </button>
                    <button onclick="controlService('herd', 'stop')" class="flex-1 px-3 py-2 text-xs bg-error-100 text-error-700 rounded hover:bg-error-200 transition-colors">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z" />
                        </svg>
                        Stop
                    </button>
                    <button onclick="controlService('herd', 'restart')" class="flex-1 px-3 py-2 text-xs bg-warning-100 text-warning-700 rounded hover:bg-warning-200 transition-colors">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Restart
                    </button>
                </div>
            </div>
            @elseif($systemInfo['hosting_type'] === 'apache')
            <!-- Apache Controls -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-md font-medium text-secondary-900">Apache Service</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 rounded-full {{ $systemInfo['apache_running'] ? 'bg-success' : 'bg-error' }}"></div>
                        <span class="text-xs text-secondary-600">{{ $systemInfo['apache_running'] ? 'Running' : 'Stopped' }}</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="controlService('apache', 'start')" class="flex-1 px-3 py-2 text-xs bg-success-100 text-success-700 rounded hover:bg-success-200 transition-colors">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Start
                    </button>
                    <button onclick="controlService('apache', 'stop')" class="flex-1 px-3 py-2 text-xs bg-error-100 text-error-700 rounded hover:bg-error-200 transition-colors">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z" />
                        </svg>
                        Stop
                    </button>
                    <button onclick="controlService('apache', 'restart')" class="flex-1 px-3 py-2 text-xs bg-warning-100 text-warning-700 rounded hover:bg-warning-200 transition-colors">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Restart
                    </button>
                </div>
            </div>
            @endif

            <!-- Nginx Controls (always show for Apache/Nginx hosting) -->
            @if($systemInfo['hosting_type'] !== 'laravel-herd')
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-md font-medium text-secondary-900">Nginx Service</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 rounded-full {{ $systemInfo['nginx_running'] ? 'bg-success' : 'bg-error' }}"></div>
                        <span class="text-xs text-secondary-600">{{ $systemInfo['nginx_running'] ? 'Running' : 'Stopped' }}</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="controlService('nginx', 'start')" class="flex-1 px-3 py-2 text-xs bg-success-100 text-success-700 rounded hover:bg-success-200 transition-colors">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Start
                    </button>
                    <button onclick="controlService('nginx', 'stop')" class="flex-1 px-3 py-2 text-xs bg-error-100 text-error-700 rounded hover:bg-error-200 transition-colors">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z" />
                        </svg>
                        Stop
                    </button>
                    <button onclick="controlService('nginx', 'restart')" class="flex-1 px-3 py-2 text-xs bg-warning-100 text-warning-700 rounded hover:bg-warning-200 transition-colors">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Restart
                    </button>
                </div>
            </div>
            @endif
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <a href="{{ route('admin.vhost.edit') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-secondary-50 transition-colors">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Edit Vhost</p>
                    <p class="text-xs text-secondary-600">Modify nginx config</p>
                </div>
            </a>

            <a href="{{ route('admin.vhost.herd.edit') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-secondary-50 transition-colors">
                <div class="w-10 h-10 bg-accent-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Edit Herd Config</p>
                    <p class="text-xs text-secondary-600">Modify global Herd config</p>
                </div>
            </a>

            <a href="{{ route('admin.vhost.herd-yml.edit') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-secondary-50 transition-colors">
                <div class="w-10 h-10 bg-warning-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Edit .herd.yml</p>
                    <p class="text-xs text-secondary-600">Modify project config</p>
                </div>
            </a>

            <a href="{{ route('admin.vhost.show') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-secondary-50 transition-colors">
                <div class="w-10 h-10 bg-info-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">View Config</p>
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

function refreshSystemStatus() {
    // Reload the page to refresh system status
    window.location.reload();
}

function controlService(service, action) {
    if (!confirm(`Are you sure you want to ${action} ${service}?`)) {
        return;
    }

    const button = event.target.closest('button');
    const originalText = button.innerHTML;

    // Show loading state
    button.innerHTML = '<svg class="w-3 h-3 mr-1 inline animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>Processing...';
    button.disabled = true;

    // Make API call
    fetch(`{{ route('admin.vhost.index') }}/${service}/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Show result
        if (data.success) {
            showNotification(data.message, 'success');
            // Refresh page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message, 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Service control error:', error);
        showNotification('Failed to control service. Please try again.', 'error');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-success-100 text-success-800 border border-success-200' :
        'bg-error-100 text-error-800 border border-error-200'
    }`;

    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' ?
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                }
            </svg>
            <span class="text-sm font-medium">${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Remove notification after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endsection
