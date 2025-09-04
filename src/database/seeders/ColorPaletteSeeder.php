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
        // Delhi Public School - Green Theme (Shared DB)
        TenantColorPalette::create([
            'tenant_id' => 'delhi-public-school-250904',
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

        // Mumbai International School - Purple Theme (Separate DB)
        TenantColorPalette::create([
            'tenant_id' => 'mumbai-international-school-250904',
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

        // Bangalore Tech Academy - Blue Theme (Separate DB)
        TenantColorPalette::create([
            'tenant_id' => 'bangalore-tech-academy-250904',
            'name' => 'Bangalore Blue',
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

        // Chennai Central School - Orange Theme (Shared DB)
        TenantColorPalette::create([
            'tenant_id' => 'chennai-central-school-250904',
            'name' => 'Chennai Orange',
            'is_active' => true,
            'colors' => [
                'primary' => [
                    '50' => '#fff7ed',
                    '100' => '#ffedd5',
                    '500' => '#f97316',
                    '600' => '#ea580c',
                    '700' => '#c2410c',
                    '900' => '#9a3412',
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

        // Pune Engineering College - Indigo Theme (Separate DB)
        TenantColorPalette::create([
            'tenant_id' => 'pune-engineering-college-250904',
            'name' => 'Pune Indigo',
            'is_active' => true,
            'colors' => [
                'primary' => [
                    '50' => '#eef2ff',
                    '100' => '#e0e7ff',
                    '500' => '#6366f1',
                    '600' => '#4f46e5',
                    '700' => '#4338ca',
                    '900' => '#312e81',
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

        // Hyderabad University - Teal Theme (Separate DB)
        TenantColorPalette::create([
            'tenant_id' => 'hyderabad-university-250904',
            'name' => 'Hyderabad Teal',
            'is_active' => true,
            'colors' => [
                'primary' => [
                    '50' => '#f0fdfa',
                    '100' => '#ccfbf1',
                    '500' => '#14b8a6',
                    '600' => '#0d9488',
                    '700' => '#0f766e',
                    '900' => '#134e4a',
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
