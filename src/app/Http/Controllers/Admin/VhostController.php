<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VhostService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VhostController extends Controller
{
    public function __construct(
        private VhostService $vhostService
    ) {}

    /**
     * Display the vhost management dashboard.
     */
    public function index(): View
    {
        $systemInfo = $this->vhostService->getSystemInfo();
        $backupFiles = $this->vhostService->getBackupFiles();

        return view('admin.vhost.index', [
            'systemInfo' => $systemInfo,
            'backupFiles' => $backupFiles,
        ]);
    }

    /**
     * Show the vhost configuration editor.
     */
    public function edit(): View
    {
        $content = $this->vhostService->getVhostContent();
        $systemInfo = $this->vhostService->getSystemInfo();

        return view('admin.vhost.edit', [
            'content' => $content,
            'systemInfo' => $systemInfo,
        ]);
    }

    /**
     * Update the vhost configuration.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $content = $request->input('content');

        // Validate the configuration
        $validation = $this->vhostService->validateVhostContent($content);

        if (!$validation['valid']) {
            return back()->withErrors([
                'content' => 'Configuration validation failed: ' . implode(', ', $validation['errors'])
            ])->withInput();
        }

        // Update the configuration
        $success = $this->vhostService->updateVhostContent($content);

        if ($success) {
            $message = 'Vhost configuration updated successfully!';

            // Add warnings if any
            if (!empty($validation['warnings'])) {
                $message .= ' Warnings: ' . implode(', ', $validation['warnings']);
            }

            return redirect()->route('admin.vhost.index')
                ->with('success', $message)
                ->with('warnings', $validation['warnings']);
        } else {
            return back()->withErrors([
                'content' => 'Failed to update vhost configuration. Please check file permissions.'
            ])->withInput();
        }
    }

    /**
     * Show the vhost configuration viewer.
     */
    public function show(): View
    {
        $content = $this->vhostService->getVhostContent();
        $systemInfo = $this->vhostService->getSystemInfo();

        return view('admin.vhost.show', [
            'content' => $content,
            'systemInfo' => $systemInfo,
        ]);
    }

    /**
     * Restore from a backup file.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_path' => 'required|string',
        ]);

        $backupPath = $request->input('backup_path');
        $success = $this->vhostService->restoreFromBackup($backupPath);

        if ($success) {
            return redirect()->route('admin.vhost.index')
                ->with('success', 'Vhost configuration restored from backup successfully!');
        } else {
            return back()->withErrors([
                'backup' => 'Failed to restore from backup. Please check file permissions.'
            ]);
        }
    }

    /**
     * Validate vhost configuration.
     */
    public function validate(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $content = $request->input('content');
        $validation = $this->vhostService->validateVhostContent($content);

        return response()->json($validation);
    }

    /**
     * Get system information.
     */
    public function systemInfo(): \Illuminate\Http\JsonResponse
    {
        $systemInfo = $this->vhostService->getSystemInfo();
        return response()->json($systemInfo);
    }

    /**
     * Get backup files.
     */
    public function backups(): \Illuminate\Http\JsonResponse
    {
        $backupFiles = $this->vhostService->getBackupFiles();
        return response()->json($backupFiles);
    }

    /**
     * Show the Herd configuration editor.
     */
    public function editHerd(): View
    {
        $content = $this->vhostService->getHerdConfigContent();
        $systemInfo = $this->vhostService->getSystemInfo();

        return view('admin.vhost.edit-herd', [
            'content' => $content,
            'systemInfo' => $systemInfo,
        ]);
    }

    /**
     * Update the Herd configuration.
     */
    public function updateHerd(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $content = $request->input('content');

        // Validate JSON format
        $decoded = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors([
                'content' => 'Invalid JSON format: ' . json_last_error_msg()
            ])->withInput();
        }

        // Update the configuration
        $success = $this->vhostService->updateHerdConfigContent($content);

        if ($success) {
            return redirect()->route('admin.vhost.index')
                ->with('success', 'Herd configuration updated successfully!');
        } else {
            return back()->withErrors([
                'content' => 'Failed to update Herd configuration. Please check file permissions.'
            ])->withInput();
        }
    }

    /**
     * Show the Herd configuration viewer.
     */
    public function showHerd(): View
    {
        $content = $this->vhostService->getHerdConfigContent();
        $systemInfo = $this->vhostService->getSystemInfo();

        return view('admin.vhost.show-herd', [
            'content' => $content,
            'systemInfo' => $systemInfo,
        ]);
    }

    /**
     * Start Herd service.
     */
    public function startHerd(): \Illuminate\Http\JsonResponse
    {
        $result = $this->vhostService->startHerd();
        return response()->json($result);
    }

    /**
     * Stop Herd service.
     */
    public function stopHerd(): \Illuminate\Http\JsonResponse
    {
        $result = $this->vhostService->stopHerd();
        return response()->json($result);
    }

    /**
     * Restart Herd service.
     */
    public function restartHerd(): \Illuminate\Http\JsonResponse
    {
        $result = $this->vhostService->restartHerd();
        return response()->json($result);
    }

    /**
     * Start Nginx service.
     */
    public function startNginx(): \Illuminate\Http\JsonResponse
    {
        $result = $this->vhostService->startNginx();
        return response()->json($result);
    }

    /**
     * Stop Nginx service.
     */
    public function stopNginx(): \Illuminate\Http\JsonResponse
    {
        $result = $this->vhostService->stopNginx();
        return response()->json($result);
    }

    /**
     * Restart Nginx service.
     */
    public function restartNginx(): \Illuminate\Http\JsonResponse
    {
        $result = $this->vhostService->restartNginx();
        return response()->json($result);
    }

    /**
     * Show the .herd.yml configuration editor.
     */
    public function editHerdYml(): View
    {
        $content = $this->vhostService->getHerdYmlContent();
        $systemInfo = $this->vhostService->getSystemInfo();

        return view('admin.vhost.edit-herd-yml', [
            'content' => $content,
            'systemInfo' => $systemInfo,
        ]);
    }

    /**
     * Update the .herd.yml configuration.
     */
    public function updateHerdYml(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $content = $request->input('content');

        // Basic YAML validation (check for common syntax issues)
        $lines = explode("\n", $content);
        $errors = [];

        foreach ($lines as $lineNum => $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Check for basic YAML structure
            if (str_contains($line, ':') && !str_contains($line, '  -')) {
                // This should be a key-value pair
                if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*\s*:/', $line)) {
                    $errors[] = "Line " . ($lineNum + 1) . ": Invalid key format";
                }
            }
        }

        if (!empty($errors)) {
            return back()->withErrors([
                'content' => 'YAML validation errors: ' . implode(', ', $errors)
            ])->withInput();
        }

        // Update the configuration
        $success = $this->vhostService->updateHerdYmlContent($content);

        if ($success) {
            return redirect()->route('admin.vhost.index')
                ->with('success', '.herd.yml configuration updated successfully!');
        } else {
            return back()->withErrors([
                'content' => 'Failed to update .herd.yml configuration. Please check file permissions.'
            ])->withInput();
        }
    }

    /**
     * Show the .herd.yml configuration viewer.
     */
    public function showHerdYml(): View
    {
        $content = $this->vhostService->getHerdYmlContent();
        $systemInfo = $this->vhostService->getSystemInfo();

        return view('admin.vhost.show-herd-yml', [
            'content' => $content,
            'systemInfo' => $systemInfo,
        ]);
    }
}
