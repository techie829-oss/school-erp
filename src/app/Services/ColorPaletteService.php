<?php

namespace App\Services;

use App\Models\TenantColorPalette;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ColorPaletteService
{
    public function __construct(
        private TenantService $tenantService
    ) {}

    /**
     * Get the active color palette for the current tenant.
     */
    public function getActivePalette(Request $request): ?TenantColorPalette
    {
        $tenantId = $this->tenantService->getCurrentTenantId($request);

        // If no tenant found, return null
        if (!$tenantId) {
            return null;
        }

        return Cache::remember("color_palette_tenant_{$tenantId}", 3600, function () use ($tenantId) {
            $palette = TenantColorPalette::active()
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$palette) {
                return TenantColorPalette::getDefaultForTenant($tenantId);
            }

            return $palette;
        });
    }

    /**
     * Get all colors for the current tenant.
     */
    public function getAllColors(Request $request): array
    {
        $palette = $this->getActivePalette($request);

        if (!$palette) {
            return $this->getDefaultColors();
        }

        return $palette->colors;
    }

    /**
     * Get default colors when no tenant is found.
     */
    private function getDefaultColors(): array
    {
        return [
            'primary' => [
                '50' => '#eff6ff',
                '100' => '#dbeafe',
                '500' => '#3b82f6',
                '600' => '#2563eb',
                '700' => '#1d4ed8',
                '900' => '#1e3a8a',
            ],
            'secondary' => [
                '50' => '#f8fafc',
                '100' => '#f1f5f9',
                '500' => '#64748b',
                '600' => '#475569',
                '700' => '#334155',
                '900' => '#0f172a',
            ],
            'accent' => [
                '50' => '#fef3c7',
                '100' => '#fde68a',
                '500' => '#f59e0b',
                '600' => '#d97706',
                '700' => '#b45309',
                '900' => '#78350f',
            ],
            'success' => '#10b981',
            'warning' => '#f59e0b',
            'error' => '#ef4444',
            'info' => '#3b82f6',
        ];
    }

    /**
     * Generate CSS custom properties for the current tenant's color palette.
     */
    public function generateCSSVariables(Request $request): string
    {
        $colors = $this->getAllColors($request);

        $css = ":root {\n";

        // Primary colors
        if (isset($colors['primary'])) {
            foreach ($colors['primary'] as $shade => $color) {
                $css .= "    --color-primary-{$shade}: {$color};\n";
            }
        }

        // Secondary colors
        if (isset($colors['secondary'])) {
            foreach ($colors['secondary'] as $shade => $color) {
                $css .= "    --color-secondary-{$shade}: {$color};\n";
            }
        }

        // Accent colors
        if (isset($colors['accent'])) {
            foreach ($colors['accent'] as $shade => $color) {
                $css .= "    --color-accent-{$shade}: {$color};\n";
            }
        }

        // Status colors
        if (isset($colors['success'])) {
            $css .= "    --color-success: {$colors['success']};\n";
        }
        if (isset($colors['warning'])) {
            $css .= "    --color-warning: {$colors['warning']};\n";
        }
        if (isset($colors['error'])) {
            $css .= "    --color-error: {$colors['error']};\n";
        }
        if (isset($colors['info'])) {
            $css .= "    --color-info: {$colors['info']};\n";
        }

        $css .= "}\n\n";

        // Add CSS rules to ensure Tailwind classes use our variables
        $css .= "/* Ensure Tailwind classes use our CSS variables */\n";
        $css .= ".text-primary-600 { color: var(--color-primary-600) !important; }\n";
        $css .= ".bg-primary-600 { background-color: var(--color-primary-600) !important; }\n";
        $css .= ".text-secondary-600 { color: var(--color-secondary-600) !important; }\n";
        $css .= ".bg-secondary-100 { background-color: var(--color-secondary-100) !important; }\n";
        $css .= ".hover\\:text-primary-600:hover { color: var(--color-primary-600) !important; }\n";
        $css .= ".hover\\:bg-primary-700:hover { background-color: var(--color-primary-700) !important; }\n";
        $css .= ".hover\\:bg-secondary-200:hover { background-color: var(--color-secondary-200) !important; }\n";
        $css .= ".border-primary-600 { border-color: var(--color-primary-600) !important; }\n";
        $css .= ".bg-primary-50 { background-color: var(--color-primary-50) !important; }\n";

        return $css;
    }

    /**
     * Generate inline CSS for the current tenant.
     */
    public function generateInlineCSS(Request $request): string
    {
        return "<style>\n" . $this->generateCSSVariables($request) . "</style>";
    }

    /**
     * Update color palette for a tenant.
     */
    public function updatePalette(string $tenantId, array $colors): TenantColorPalette
    {
        $palette = TenantColorPalette::getDefaultForTenant($tenantId);
        $palette->update(['colors' => $colors]);

        // Clear cache
        Cache::forget("color_palette_tenant_{$tenantId}");

        return $palette;
    }

    /**
     * Create a new color palette for a tenant.
     */
    public function createPalette(string $tenantId, string $name, array $colors): TenantColorPalette
    {
        // Deactivate other palettes if this one should be active
        if (($colors['is_active'] ?? false) === true) {
            TenantColorPalette::where('tenant_id', $tenantId)
                ->update(['is_active' => false]);
        }

        $palette = TenantColorPalette::create([
            'tenant_id' => $tenantId,
            'name' => $name,
            'is_active' => $colors['is_active'] ?? false,
            'colors' => $colors,
        ]);

        // Clear cache
        Cache::forget("color_palette_tenant_{$tenantId}");

        return $palette;
    }

    /**
     * Get predefined color schemes.
     */
    public function getPredefinedSchemes(): array
    {
        return [
            'blue' => [
                'name' => 'Blue Theme',
                'primary' => [
                    '500' => '#3b82f6',
                    '600' => '#2563eb',
                    '700' => '#1d4ed8',
                ],
                'accent' => [
                    '500' => '#f59e0b',
                    '600' => '#d97706',
                ],
            ],
            'green' => [
                'name' => 'Green Theme',
                'primary' => [
                    '500' => '#22c55e',
                    '600' => '#16a34a',
                    '700' => '#15803d',
                ],
                'accent' => [
                    '500' => '#f59e0b',
                    '600' => '#d97706',
                ],
            ],
            'purple' => [
                'name' => 'Purple Theme',
                'primary' => [
                    '500' => '#a855f7',
                    '600' => '#9333ea',
                    '700' => '#7c3aed',
                ],
                'accent' => [
                    '500' => '#f59e0b',
                    '600' => '#d97706',
                ],
            ],
            'red' => [
                'name' => 'Red Theme',
                'primary' => [
                    '500' => '#ef4444',
                    '600' => '#dc2626',
                    '700' => '#b91c1c',
                ],
                'accent' => [
                    '500' => '#f59e0b',
                    '600' => '#d97706',
                ],
            ],
            'indigo' => [
                'name' => 'Indigo Theme',
                'primary' => [
                    '500' => '#6366f1',
                    '600' => '#4f46e5',
                    '700' => '#4338ca',
                ],
                'accent' => [
                    '500' => '#f59e0b',
                    '600' => '#d97706',
                ],
            ],
        ];
    }

    /**
     * Get the current tenant ID from the request.
     */
    private function getCurrentTenantId(Request $request): ?string
    {
        return $this->tenantService->getCurrentTenantId($request);
    }
}
