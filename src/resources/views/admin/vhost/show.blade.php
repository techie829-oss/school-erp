@extends('layouts.admin')

@section('title', 'View Vhost Configuration')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-secondary-900">Vhost Configuration</h1>
            <p class="text-secondary-600 mt-1">Current Herd virtual host configuration file</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.vhost.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Vhost
            </a>
            <a href="{{ route('admin.vhost.edit') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Configuration
            </a>
        </div>
    </div>

    <!-- System Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['vhost_exists'] ? 'bg-success' : 'bg-error' }}"></div>
                <span class="text-sm text-secondary-600">File: {{ $systemInfo['vhost_exists'] ? 'Exists' : 'Not Found' }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['vhost_writable'] ? 'bg-success' : 'bg-error' }}"></div>
                <span class="text-sm text-secondary-600">Writable: {{ $systemInfo['vhost_writable'] ? 'Yes' : 'No' }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['herd_running'] ? 'bg-success' : 'bg-warning' }}"></div>
                <span class="text-sm text-secondary-600">Herd: {{ $systemInfo['herd_running'] ? 'Running' : 'Stopped' }}</span>
            </div>
            <div class="text-sm text-secondary-500 font-mono">{{ $systemInfo['vhost_path'] }}</div>
        </div>
    </div>

    <!-- Configuration Viewer -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Viewer Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <h2 class="text-lg font-semibold text-secondary-900">Configuration File</h2>
                <div class="flex items-center space-x-2">
                    <span class="text-xs px-2 py-1 bg-primary-100 text-primary-700 rounded">nginx</span>
                    <span class="text-xs text-secondary-500" id="line-count">Lines: {{ substr_count($content, "\n") + 1 }}</span>
                    <span class="text-xs text-secondary-500" id="char-count">Chars: {{ strlen($content) }}</span>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="copyToClipboard()" class="text-xs px-3 py-1 bg-secondary-100 text-secondary-700 rounded hover:bg-secondary-200 transition-colors">
                    <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Copy
                </button>
                <button onclick="downloadConfig()" class="text-xs px-3 py-1 bg-accent-100 text-accent-700 rounded hover:bg-accent-200 transition-colors">
                    <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download
                </button>
            </div>
        </div>

        <!-- Code Display -->
        <div class="relative">
            <pre id="config-content" class="p-4 font-mono text-sm bg-secondary-50 overflow-x-auto" style="min-height: 600px; white-space: pre-wrap;">{{ $content }}</pre>
        </div>

        <!-- Viewer Footer -->
        <div class="flex items-center justify-between p-4 border-t border-gray-200 bg-secondary-50">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs text-secondary-600">Read-only view</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="refreshContent()" class="text-sm px-4 py-2 text-secondary-600 hover:text-secondary-800 transition-colors">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Configuration Analysis -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-secondary-900 mb-4">Configuration Analysis</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Server Blocks</h3>
                <p class="text-2xl font-bold text-primary-600">{{ substr_count($content, 'server {') }}</p>
                <p class="text-xs text-secondary-600">Active server configurations</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Listen Ports</h3>
                <p class="text-2xl font-bold text-accent-600">{{ preg_match_all('/listen\s+(\d+)/', $content, $matches) ? count(array_unique($matches[1])) : 0 }}</p>
                <p class="text-xs text-secondary-600">Unique port configurations</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Location Blocks</h3>
                <p class="text-2xl font-bold text-success-600">{{ substr_count($content, 'location ') }}</p>
                <p class="text-xs text-secondary-600">URL routing rules</p>
            </div>
        </div>
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

            <button onclick="copyToClipboard()" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-secondary-50 transition-colors">
                <div class="w-10 h-10 bg-accent-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Copy to Clipboard</p>
                    <p class="text-xs text-secondary-600">Copy configuration</p>
                </div>
            </button>

            <button onclick="downloadConfig()" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-secondary-50 transition-colors">
                <div class="w-10 h-10 bg-success-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-secondary-900">Download File</p>
                    <p class="text-xs text-secondary-600">Save as .conf file</p>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const content = document.getElementById('config-content').textContent;

    navigator.clipboard.writeText(content).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Copied!';
        button.classList.add('bg-success-100', 'text-success-700');
        button.classList.remove('bg-secondary-100', 'text-secondary-700');

        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-success-100', 'text-success-700');
            button.classList.add('bg-secondary-100', 'text-secondary-700');
        }, 2000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
        alert('Failed to copy to clipboard');
    });
}

function downloadConfig() {
    const content = document.getElementById('config-content').textContent;
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'valet.conf';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function refreshContent() {
    window.location.reload();
}
</script>
@endsection
