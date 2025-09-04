@extends('layouts.admin')

@section('title', 'Edit Herd Configuration')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-secondary-900">Edit Herd Configuration</h1>
            <p class="text-secondary-600 mt-1">Modify the Herd main configuration file</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.vhost.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Vhost
            </a>
            <button type="button" onclick="validateJSON()" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Validate JSON
            </button>
        </div>
    </div>

    <!-- System Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['herd_config_exists'] ? 'bg-success' : 'bg-error' }}"></div>
                <span class="text-sm text-secondary-600">Config: {{ $systemInfo['herd_config_exists'] ? 'Exists' : 'Not Found' }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['herd_config_writable'] ? 'bg-success' : 'bg-error' }}"></div>
                <span class="text-sm text-secondary-600">Writable: {{ $systemInfo['herd_config_writable'] ? 'Yes' : 'No' }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['herd_running'] ? 'bg-success' : 'bg-warning' }}"></div>
                <span class="text-sm text-secondary-600">Herd: {{ $systemInfo['herd_running'] ? 'Running' : 'Stopped' }}</span>
            </div>
            <div class="text-sm text-secondary-500 font-mono">{{ $systemInfo['herd_config_path'] }}/config.json</div>
        </div>
    </div>

    <!-- Editor Form -->
    <form method="POST" action="{{ route('admin.vhost.herd.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Editor Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <h2 class="text-lg font-semibold text-secondary-900">Herd Configuration Editor</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-1 bg-accent-100 text-accent-700 rounded">JSON</span>
                        <span class="text-xs text-secondary-500" id="line-count">Lines: 0</span>
                        <span class="text-xs text-secondary-500" id="char-count">Chars: 0</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="formatJSON()" class="text-xs px-3 py-1 bg-secondary-100 text-secondary-700 rounded hover:bg-secondary-200 transition-colors">
                        Format JSON
                    </button>
                    <button type="button" onclick="insertDefaultConfig()" class="text-xs px-3 py-1 bg-accent-100 text-accent-700 rounded hover:bg-accent-200 transition-colors">
                        Insert Default
                    </button>
                </div>
            </div>

            <!-- Code Editor -->
            <div class="relative">
                <textarea
                    name="content"
                    id="herd-config-editor"
                    rows="30"
                    class="w-full p-4 font-mono text-sm border-0 resize-none focus:ring-0 focus:outline-none"
                    placeholder="Enter your Herd configuration JSON here..."
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
        <h2 class="text-lg font-semibold text-secondary-900 mb-4">Herd Configuration Help</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Required Fields</h3>
                <ul class="text-sm text-secondary-600 space-y-1">
                    <li>• <code class="bg-secondary-100 px-1 rounded">tld</code> - Top level domain (e.g., "test")</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">loopback</code> - Loopback IP address</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">paths</code> - Array of project paths</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">nginx</code> - Nginx configuration</li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Optional Fields</h3>
                <ul class="text-sm text-secondary-600 space-y-1">
                    <li>• <code class="bg-secondary-100 px-1 rounded">php</code> - PHP version settings</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">dns</code> - DNS configuration</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">ssl</code> - SSL certificate settings</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">features</code> - Feature flags</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Include CodeMirror for JSON syntax highlighting -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>

<script>
let editor;
let originalContent = @json($content);

document.addEventListener('DOMContentLoaded', function() {
    // Initialize CodeMirror
    editor = CodeMirror.fromTextArea(document.getElementById('herd-config-editor'), {
        mode: 'application/json',
        theme: 'default',
        lineNumbers: true,
        lineWrapping: true,
        indentUnit: 2,
        tabSize: 2,
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

function validateJSON() {
    const content = editor.getValue();

    try {
        const parsed = JSON.parse(content);
        showValidationResults({
            valid: true,
            errors: [],
            warnings: []
        });
    } catch (error) {
        showValidationResults({
            valid: false,
            errors: [`Invalid JSON: ${error.message}`],
            warnings: []
        });
    }
}

function showValidationResults(data) {
    const resultsDiv = document.getElementById('validation-results');
    resultsDiv.className = 'bg-white rounded-xl shadow-sm border border-gray-200 p-6';

    let html = '<h2 class="text-lg font-semibold text-secondary-900 mb-4">Validation Results</h2>';

    if (data.valid) {
        html += '<div class="flex items-center space-x-2 text-success-600 mb-4">';
        html += '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        html += '<span class="font-medium">JSON is valid!</span>';
        html += '</div>';
    } else {
        html += '<div class="flex items-center space-x-2 text-error-600 mb-4">';
        html += '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        html += '<span class="font-medium">JSON has errors</span>';
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

function formatJSON() {
    try {
        const content = editor.getValue();
        const parsed = JSON.parse(content);
        const formatted = JSON.stringify(parsed, null, 2);
        editor.setValue(formatted);
    } catch (error) {
        alert('Invalid JSON format. Please fix errors before formatting.');
    }
}

function insertDefaultConfig() {
    const defaultConfig = {
        "tld": "test",
        "loopback": "127.0.0.1",
        "paths": [
            "/Users/rohitk/react/lara/school-erp/src"
        ],
        "nginx": {
            "config": "/Users/rohitk/.config/herd/config/nginx/valet.conf"
        },
        "php": {
            "version": "8.3"
        }
    };

    editor.setValue(JSON.stringify(defaultConfig, null, 2));
}

function resetToOriginal() {
    if (confirm('Are you sure you want to reset to the original configuration? All unsaved changes will be lost.')) {
        editor.setValue(originalContent);
    }
}
</script>
@endsection
