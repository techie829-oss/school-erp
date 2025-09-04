<?php

namespace Database\Seeders;

use App\Models\TenantColorPalette;
use Illuminate\Database\Seeder;

class ColorPaletteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Internal Admin - Professional Blue Theme
        TenantColorPalette::create([
            'tenant_id' => 'internal',
            'name' => 'Professional Blue',
            'is_active' => true,
            'colors' => [
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
            ],
        ]);

        // School A - Green Theme (Shared DB)
        TenantColorPalette::create([
            'tenant_id' => 'school-a',
            'name' => 'Delhi Green',
            'is_active' => true,
            'colors' => [
                'primary' => [
                    '50' => '#f0fdf4',
                    '100' => '#dcfce7',
                    '500' => '#22c55e',
                    '600' => '#16a34a',
                    '700' => '#15803d',
                    '900' => '#14532d',
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
            ],
        ]);

        // School B - Purple Theme (Separate DB)
        TenantColorPalette::create([
            'tenant_id' => 'school-b',
            'name' => 'Mumbai Purple',
            'is_active' => true,
            'colors' => [
                'primary' => [
                    '50' => '#faf5ff',
                    '100' => '#f3e8ff',
                    '500' => '#a855f7',
                    '600' => '#9333ea',
                    '700' => '#7c3aed',
                    '900' => '#581c87',
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
            ],
        ]);

        // School C - Red Theme (Separate DB)
        TenantColorPalette::create([
            'tenant_id' => 'school-c',
            'name' => 'Bangalore Red',
            'is_active' => true,
            'colors' => [
                'primary' => [
                    '50' => '#fef2f2',
                    '100' => '#fee2e2',
                    '500' => '#ef4444',
                    '600' => '#dc2626',
                    '700' => '#b91c1c',
                    '900' => '#7f1d1d',
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
            ],
        ]);

    }
}
