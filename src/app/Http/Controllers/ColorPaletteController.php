<?php

namespace App\Http\Controllers;

use App\Models\TenantColorPalette;
use App\Services\ColorPaletteService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColorPaletteController extends Controller
{
    public function __construct(
        private ColorPaletteService $colorService
    ) {}

    /**
     * Show the color palette management page.
     */
    public function index(): View
    {
        $tenantId = 1; // TODO: Get from actual tenant context
        $palettes = TenantColorPalette::where('tenant_id', $tenantId)->get();
        $activePalette = $this->colorService->getActivePalette();
        $predefinedSchemes = $this->colorService->getPredefinedSchemes();

        return view('admin.color-palettes.index', compact('palettes', 'activePalette', 'predefinedSchemes'));
    }

    /**
     * Show the color palette editor.
     */
    public function edit(TenantColorPalette $palette): View
    {
        return view('admin.color-palettes.edit', compact('palette'));
    }

    /**
     * Update a color palette.
     */
    public function update(Request $request, TenantColorPalette $palette)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'primary_50' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_100' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_500' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_600' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_700' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_900' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_50' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_100' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_500' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_600' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_700' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_900' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_50' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_100' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_500' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_600' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_700' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_900' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'success' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'warning' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'error' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'info' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $this->colorService->updatePalette($palette->tenant_id, $validated);

        return redirect()->route('admin.color-palettes.index')
            ->with('success', 'Color palette updated successfully!');
    }

    /**
     * Apply a predefined color scheme.
     */
    public function applyScheme(Request $request)
    {
        $validated = $request->validate([
            'scheme' => 'required|string|in:blue,green,purple,red,indigo',
        ]);

        $tenantId = 1; // TODO: Get from actual tenant context
        $schemes = $this->colorService->getPredefinedSchemes();
        $selectedScheme = $schemes[$validated['scheme']];

        // Create a new palette with the selected scheme
        $this->colorService->createPalette(
            $tenantId,
            $selectedScheme['name'],
            array_merge($selectedScheme, ['is_active' => true])
        );

        return redirect()->route('admin.color-palettes.index')
            ->with('success', 'Color scheme applied successfully!');
    }

    /**
     * Create a new color palette.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'primary_500' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_600' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_700' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_500' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_600' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $tenantId = 1; // TODO: Get from actual tenant context

        // Use default values for other colors
        $defaultPalette = TenantColorPalette::getDefaultForTenant($tenantId);
        $colors = array_merge($defaultPalette->toArray(), $validated);

        $this->colorService->createPalette($tenantId, $validated['name'], $colors);

        return redirect()->route('admin.color-palettes.index')
            ->with('success', 'Color palette created successfully!');
    }

    /**
     * Delete a color palette.
     */
    public function destroy(TenantColorPalette $palette)
    {
        if ($palette->is_active) {
            return back()->with('error', 'Cannot delete the active color palette.');
        }

        $palette->delete();

        return redirect()->route('admin.color-palettes.index')
            ->with('success', 'Color palette deleted successfully!');
    }
}
