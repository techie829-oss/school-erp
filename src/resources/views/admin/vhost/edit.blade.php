@extends('layouts.admin')

@section('title', 'Edit Vhost Configuration')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-secondary-900">Edit Vhost Configuration</h1>
            <p class="text-secondary-600 mt-1">Modify the Herd virtual host configuration file</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.vhost.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Vhost
            </a>
            <button type="button" onclick="validateConfig()" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Validate
            </button>
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

    <!-- Editor Form -->
    <form method="POST" action="{{ route('admin.vhost.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Editor Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <h2 class="text-lg font-semibold text-secondary-900">Configuration Editor</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-1 bg-primary-100 text-primary-700 rounded">nginx</span>
                        <span class="text-xs text-secondary-500" id="line-count">Lines: 0</span>
                        <span class="text-xs text-secondary-500" id="char-count">Chars: 0</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="formatCode()" class="text-xs px-3 py-1 bg-secondary-100 text-secondary-700 rounded hover:bg-secondary-200 transition-colors">
                        Format
                    </button>
                    <button type="button" onclick="insertTemplate()" class="text-xs px-3 py-1 bg-accent-100 text-accent-700 rounded hover:bg-accent-200 transition-colors">
                        Insert Template
                    </button>
                </div>
            </div>

            <!-- Code Editor -->
            <div class="relative">
                <textarea
                    name="content"
                    id="config-editor"
                    rows="30"
                    class="w-full p-4 font-mono text-sm border-0 resize-none focus:ring-0 focus:outline-none"
                    placeholder="Enter your nginx configuration here..."
                    style="min-height: 600px;"
                >{{ old('content', $content) }}</textarea>

                <!-- Line Numbers -->
                <div id="line-numbers" class="absolute left-0 top-0 p-4 font-mono text-sm text-secondary-400 select-none pointer-events-none" style="min-height: 600px;"></div>
            </div>

            <!-- Editor Footer -->
            <div class="flex items-center justify-between p-4 border-t border-gray-200 bg-secondary-50">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs text-secondary-600">Auto-save backup before changes</span>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="resetToOriginal()" class="text-sm px-4 py-2 text-secondary-600 hover:text-secondary-800 transition-colors">
                        Reset
                    </button>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Configuration
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Validation Results -->
    <div id="validation-results" class="hidden">
        <!-- Results will be populated by JavaScript -->
    </div>

    <!-- Help Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-secondary-900 mb-4">Configuration Help</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Required Directives</h3>
                <ul class="text-sm text-secondary-600 space-y-1">
                    <li>• <code class="bg-secondary-100 px-1 rounded">server {}</code> - Server block</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">listen</code> - Port to listen on</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">server_name</code> - Domain names</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">root</code> - Document root</li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Common Patterns</h3>
                <ul class="text-sm text-secondary-600 space-y-1">
                    <li>• <code class="bg-secondary-100 px-1 rounded">*.myschool.test</code> - Wildcard subdomains</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">try_files</code> - Fallback handling</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">fastcgi_pass</code> - PHP processing</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">location</code> - URL matching</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Include CodeMirror for syntax highlighting -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/nginx/nginx.min.js"></script>

<script>
let editor;
let originalContent = @json($content);

document.addEventListener('DOMContentLoaded', function() {
    // Initialize CodeMirror
    editor = CodeMirror.fromTextArea(document.getElementById('config-editor'), {
        mode: 'nginx',
        theme: 'default',
        lineNumbers: true,
        lineWrapping: true,
        indentUnit: 4,
        tabSize: 4,
        autoCloseBrackets: true,
        matchBrackets: true,
        foldGutter: true,
        gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
        extraKeys: {
            'Ctrl-S': function(cm) {
                document.querySelector('form').submit();
            },
            'F11': function(cm) {
                cm.setOption('fullScreen', !cm.getOption('fullScreen'));
            },
            'Esc': function(cm) {
                if (cm.getOption('fullScreen')) cm.setOption('fullScreen', false);
            }
        }
    });

    // Update line and character counts
    editor.on('change', function() {
        updateStats();
    });

    // Initial stats update
    updateStats();
});

function updateStats() {
    const lineCount = editor.lineCount();
    const charCount = editor.getValue().length;
    document.getElementById('line-count').textContent = `Lines: ${lineCount}`;
    document.getElementById('char-count').textContent = `Chars: ${charCount}`;
}

function validateConfig() {
    const content = editor.getValue();

    fetch('{{ route('admin.vhost.validate') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ content: content })
    })
    .then(response => response.json())
    .then(data => {
        showValidationResults(data);
    })
    .catch(error => {
        console.error('Validation error:', error);
        alert('Validation failed. Please try again.');
    });
}

function showValidationResults(data) {
    const resultsDiv = document.getElementById('validation-results');
    resultsDiv.className = 'bg-white rounded-xl shadow-sm border border-gray-200 p-6';

    let html = '<h2 class="text-lg font-semibold text-secondary-900 mb-4">Validation Results</h2>';

    if (data.valid) {
        html += '<div class="flex items-center space-x-2 text-success-600 mb-4">';
        html += '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        html += '<span class="font-medium">Configuration is valid!</span>';
        html += '</div>';
    } else {
        html += '<div class="flex items-center space-x-2 text-error-600 mb-4">';
        html += '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        html += '<span class="font-medium">Configuration has errors</span>';
        html += '</div>';
    }

    if (data.errors && data.errors.length > 0) {
        html += '<div class="mb-4">';
        html += '<h3 class="text-sm font-medium text-error-700 mb-2">Errors:</h3>';
        html += '<ul class="text-sm text-error-600 space-y-1">';
        data.errors.forEach(error => {
            html += `<li>• ${error}</li>`;
        });
        html += '</ul>';
        html += '</div>';
    }

    if (data.warnings && data.warnings.length > 0) {
        html += '<div class="mb-4">';
        html += '<h3 class="text-sm font-medium text-warning-700 mb-2">Warnings:</h3>';
        html += '<ul class="text-sm text-warning-600 space-y-1">';
        data.warnings.forEach(warning => {
            html += `<li>• ${warning}</li>`;
        });
        html += '</ul>';
        html += '</div>';
    }

    resultsDiv.innerHTML = html;
    resultsDiv.scrollIntoView({ behavior: 'smooth' });
}

function formatCode() {
    // Simple formatting - add proper indentation
    const content = editor.getValue();
    const lines = content.split('\n');
    let formatted = '';
    let indentLevel = 0;

    lines.forEach(line => {
        const trimmed = line.trim();
        if (trimmed === '') {
            formatted += '\n';
            return;
        }

        // Decrease indent for closing braces
        if (trimmed === '}') {
            indentLevel = Math.max(0, indentLevel - 1);
        }

        // Add indentation
        formatted += '    '.repeat(indentLevel) + trimmed + '\n';

        // Increase indent for opening braces
        if (trimmed.endsWith('{')) {
            indentLevel++;
        }
    });

    editor.setValue(formatted.trim());
}

function insertTemplate() {
    const template = `# Herd Nginx Configuration
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
    location ~ \\.php$ {
        fastcgi_pass unix:/opt/homebrew/var/run/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Static files caching
    location ~* \\.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to hidden files
    location ~ /\\. {
        deny all;
    }

    # Deny access to sensitive files
    location ~ /(\\.env|composer\\.(json|lock)|package\\.(json|lock)|yarn\\.lock|\\.git) {
        deny all;
    }
}`;

    editor.setValue(template);
}

function resetToOriginal() {
    if (confirm('Are you sure you want to reset to the original configuration? All unsaved changes will be lost.')) {
        editor.setValue(originalContent);
    }
}
</script>
@endsection
