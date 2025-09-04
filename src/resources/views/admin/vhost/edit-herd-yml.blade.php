@extends('layouts.admin')

@section('title', 'Edit .herd.yml Configuration')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-secondary-900">Edit .herd.yml Configuration</h1>
            <p class="text-secondary-600 mt-1">Modify the project Herd configuration file</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.vhost.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Vhost
            </a>
            <button type="button" onclick="validateYAML()" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Validate YAML
            </button>
        </div>
    </div>

    <!-- System Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['herd_yml_exists'] ? 'bg-success' : 'bg-error' }}"></div>
                <span class="text-sm text-secondary-600">File: {{ $systemInfo['herd_yml_exists'] ? 'Exists' : 'Not Found' }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['herd_yml_writable'] ? 'bg-success' : 'bg-error' }}"></div>
                <span class="text-sm text-secondary-600">Writable: {{ $systemInfo['herd_yml_writable'] ? 'Yes' : 'No' }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full {{ $systemInfo['herd_running'] ? 'bg-success' : 'bg-warning' }}"></div>
                <span class="text-sm text-secondary-600">Herd: {{ $systemInfo['herd_running'] ? 'Running' : 'Stopped' }}</span>
            </div>
            <div class="text-sm text-secondary-500 font-mono">{{ $systemInfo['herd_yml_path'] }}</div>
        </div>
    </div>

    <!-- Editor Form -->
    <form method="POST" action="{{ route('admin.vhost.herd-yml.update') }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Editor Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <h2 class="text-lg font-semibold text-secondary-900">.herd.yml Editor</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-1 bg-success-100 text-success-700 rounded">YAML</span>
                        <span class="text-xs text-secondary-500" id="line-count">Lines: 0</span>
                        <span class="text-xs text-secondary-500" id="char-count">Chars: 0</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="formatYAML()" class="text-xs px-3 py-1 bg-secondary-100 text-secondary-700 rounded hover:bg-secondary-200 transition-colors">
                        Format YAML
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
                    id="herd-yml-editor" 
                    rows="30" 
                    class="w-full p-4 font-mono text-sm border-0 resize-none focus:ring-0 focus:outline-none"
                    placeholder="Enter your .herd.yml configuration here..."
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
        <h2 class="text-lg font-semibold text-secondary-900 mb-4">.herd.yml Configuration Help</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Required Fields</h3>
                <ul class="text-sm text-secondary-600 space-y-1">
                    <li>• <code class="bg-secondary-100 px-1 rounded">name</code> - Project name</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">domain</code> - Main domain (e.g., myschool.test)</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">subdomains</code> - Array of subdomains</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">php</code> - PHP version</li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-medium text-secondary-700 mb-2">Optional Fields</h3>
                <ul class="text-sm text-secondary-600 space-y-1">
                    <li>• <code class="bg-secondary-100 px-1 rounded">mysql</code> - MySQL version</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">redis</code> - Redis version</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">services</code> - Additional services</li>
                    <li>• <code class="bg-secondary-100 px-1 rounded">environment</code> - Environment variables</li>
                </ul>
            </div>
        </div>
        
        <div class="mt-6">
            <h3 class="text-sm font-medium text-secondary-700 mb-2">Example Configuration</h3>
            <pre class="text-xs bg-secondary-100 p-3 rounded overflow-x-auto"><code>name: school-erp
domain: myschool.test
subdomains:
  - app
  - schoola
  - schoolb
  - schoolc

php: 8.3
mysql: 8.0
redis: 7.0</code></pre>
        </div>
    </div>
</div>

<!-- Include CodeMirror for YAML syntax highlighting -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/yaml/yaml.min.js"></script>

<script>
let editor;
let originalContent = @json($content);

document.addEventListener('DOMContentLoaded', function() {
    // Initialize CodeMirror
    editor = CodeMirror.fromTextArea(document.getElementById('herd-yml-editor'), {
        mode: 'yaml',
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

function validateYAML() {
    const content = editor.getValue();
    
    // Basic YAML validation
    const lines = content.split('\n');
    const errors = [];
    const warnings = [];
    
    let inSubdomains = false;
    let hasName = false;
    let hasDomain = false;
    let hasSubdomains = false;
    let hasPhp = false;
    
    lines.forEach((line, index) => {
        const trimmed = line.trim();
        const lineNum = index + 1;
        
        if (trimmed === '' || trimmed.startsWith('#')) {
            return;
        }
        
        // Check for required fields
        if (trimmed.startsWith('name:')) {
            hasName = true;
            if (trimmed.split(':')[1].trim() === '') {
                errors.push(`Line ${lineNum}: Name cannot be empty`);
            }
        }
        
        if (trimmed.startsWith('domain:')) {
            hasDomain = true;
            const domain = trimmed.split(':')[1].trim();
            if (domain === '') {
                errors.push(`Line ${lineNum}: Domain cannot be empty`);
            } else if (!domain.includes('.')) {
                warnings.push(`Line ${lineNum}: Domain should include a TLD (e.g., .test, .local)`);
            }
        }
        
        if (trimmed === 'subdomains:') {
            hasSubdomains = true;
            inSubdomains = true;
        }
        
        if (trimmed.startsWith('php:')) {
            hasPhp = true;
            const version = trimmed.split(':')[1].trim();
            if (version === '') {
                errors.push(`Line ${lineNum}: PHP version cannot be empty`);
            }
        }
        
        // Check subdomain format
        if (inSubdomains && trimmed.startsWith('- ')) {
            const subdomain = trimmed.substring(2).trim();
            if (subdomain === '') {
                errors.push(`Line ${lineNum}: Subdomain cannot be empty`);
            }
        }
        
        // Check indentation
        if (trimmed.startsWith('  ') && !trimmed.startsWith('  -')) {
            if (!trimmed.includes(':')) {
                errors.push(`Line ${lineNum}: Invalid indentation or missing colon`);
            }
        }
        
        // Reset subdomains context
        if (trimmed !== '' && !trimmed.startsWith('  ') && !trimmed.startsWith('-')) {
            inSubdomains = false;
        }
    });
    
    // Check for missing required fields
    if (!hasName) {
        errors.push('Missing required field: name');
    }
    if (!hasDomain) {
        errors.push('Missing required field: domain');
    }
    if (!hasSubdomains) {
        errors.push('Missing required field: subdomains');
    }
    if (!hasPhp) {
        errors.push('Missing required field: php');
    }
    
    showValidationResults({
        valid: errors.length === 0,
        errors: errors,
        warnings: warnings
    });
}

function showValidationResults(data) {
    const resultsDiv = document.getElementById('validation-results');
    resultsDiv.className = 'bg-white rounded-xl shadow-sm border border-gray-200 p-6';
    
    let html = '<h2 class="text-lg font-semibold text-secondary-900 mb-4">Validation Results</h2>';
    
    if (data.valid) {
        html += '<div class="flex items-center space-x-2 text-success-600 mb-4">';
        html += '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        html += '<span class="font-medium">YAML configuration is valid!</span>';
        html += '</div>';
    } else {
        html += '<div class="flex items-center space-x-2 text-error-600 mb-4">';
        html += '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        html += '<span class="font-medium">YAML configuration has errors</span>';
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

function formatYAML() {
    // Simple YAML formatting - fix indentation
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
        
        // Decrease indent for top-level items
        if (!line.startsWith(' ') && !line.startsWith('-')) {
            indentLevel = 0;
        }
        
        // Add proper indentation
        if (line.startsWith('- ')) {
            formatted += '  '.repeat(indentLevel) + trimmed + '\n';
        } else if (line.includes(':')) {
            formatted += '  '.repeat(indLevel) + trimmed + '\n';
            if (trimmed.endsWith(':')) {
                indentLevel++;
            }
        } else {
            formatted += '  '.repeat(indentLevel) + trimmed + '\n';
        }
    });
    
    editor.setValue(formatted.trim());
}

function insertDefaultConfig() {
    const defaultConfig = `name: school-erp
domain: myschool.test
subdomains:
  - app
  - schoola
  - schoolb
  - schoolc

php: 8.3
mysql: 8.0
redis: 7.0`;
    
    editor.setValue(defaultConfig);
}

function resetToOriginal() {
    if (confirm('Are you sure you want to reset to the original configuration? All unsaved changes will be lost.')) {
        editor.setValue(originalContent);
    }
}
</script>
@endsection
