<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CmsPageController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    protected function getTenant(Request $request)
    {
        $tenant = $request->attributes->get('current_tenant');
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        return $tenant;
    }

    /**
     * Display a listing of fixed pages.
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        // Get all fixed pages for this tenant
        $pages = CmsPage::forTenant($tenant->id)
            ->orderByRaw("FIELD(slug, '', 'about', 'programs', 'facilities', 'admission', 'contact')")
            ->get();

        return view('tenant.admin.cms.pages.index', compact('tenant', 'pages'));
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $page = CmsPage::forTenant($tenant->id)->findOrFail($id);

        // Get fields config for this page
        $pageSlug = $page->slug === '' ? 'home' : $page->slug;
        $fields = config("all.cms_fields.{$pageSlug}", []);

        // Get current content values
        // Ensure content is an array (handle both array and JSON string)
        $content = $page->content ?? [];
        if (is_string($content)) {
            $content = json_decode($content, true) ?? [];
        }
        if (!is_array($content)) {
            $content = [];
        }
        $fieldValues = $content['fields'] ?? [];

        // Get available languages
        $languages = config('content.pages.languages', ['en' => 'English']);
        $defaultLang = config('content.pages.default_language', 'en');

        // Get default values from config for placeholders (for all languages)
        $defaultValues = [];
        foreach ($fields as $field) {
            foreach ($languages as $langCode => $langName) {
                $defaultValue = config("content.pages.pages.{$pageSlug}.{$langCode}.{$field['name']}", '');
                // Replace tenant placeholders in default values
                if (is_string($defaultValue) && str_contains($defaultValue, '{tenant_')) {
                    $tenantData = $tenant->data ?? [];
                    $defaultValue = str_replace('{tenant_name}', $tenantData['name'] ?? 'Our School', $defaultValue);
                    $defaultValue = str_replace('{tenant_description}', $tenantData['description'] ?? 'Excellence in Education', $defaultValue);
                    $defaultValue = str_replace('{tenant_student_count}', $tenantData['student_count'] ?? '500+', $defaultValue);
                }
                $defaultValues[$field['name']][$langCode] = $defaultValue;
            }
        }

        return view('tenant.admin.cms.pages.edit', compact('tenant', 'page', 'fields', 'fieldValues', 'defaultValues', 'languages', 'defaultLang'));
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $page = CmsPage::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Get fields config for this page
        $pageSlug = $page->slug === '' ? 'home' : $page->slug;
        $fields = config("all.cms_fields.{$pageSlug}", []);

        // Collect field values from request (language-specific)
        $fieldValues = [];
        $languages = config('content.pages.languages', ['en' => 'English']);

        foreach ($fields as $field) {
            $fieldName = $field['name'];
            foreach ($languages as $langCode => $langName) {
                $langKey = "field_{$fieldName}_{$langCode}";
                if ($request->has($langKey)) {
                    $fieldValues["{$fieldName}_{$langCode}"] = $request->input($langKey);
                }
            }
        }

        // Update content with field values
        // Ensure content is an array (handle both array and JSON string)
        $content = $page->content ?? [];
        if (is_string($content)) {
            $content = json_decode($content, true) ?? [];
        }
        if (!is_array($content)) {
            $content = [];
        }
        $content['fields'] = $fieldValues;

        $page->update([
            'title' => $request->title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'content' => $content,
            'is_published' => $request->boolean('is_published', $page->is_published),
        ]);

        return redirect()->to('/admin/cms/pages')
            ->with('success', 'Page updated successfully!');
    }

    /**
     * Manage components for a page (Features, Programs, Testimonials, Quick Links)
     */
    public function manageComponents(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $page = CmsPage::forTenant($tenant->id)->findOrFail($id);

        // Get page slug for config lookup
        $pageSlug = $page->slug === '' ? 'home' : $page->slug;

        // Get current content
        $content = $page->content ?? [];
        if (is_string($content)) {
            $content = json_decode($content, true) ?? [];
        }
        if (!is_array($content)) {
            $content = [];
        }

        // Get components
        $components = $content['components'] ?? [
            'features' => [],
            'programs' => [],
            'testimonials' => [],
            'quick_links' => [],
            'program_cards' => [],
            'facility_cards' => [],
            'amenity_cards' => [],
            'process_steps' => [],
            'requirement_cards' => [],
            'date_cards' => [],
            'faq_items' => [],
        ];

        // Get available languages
        $languages = config('content.pages.languages', ['en' => 'English']);
        $defaultLang = config('content.pages.default_language', 'en');

        return view('tenant.admin.cms.pages.components', compact('tenant', 'page', 'components', 'languages', 'defaultLang', 'pageSlug'));
    }

    /**
     * Add or update a component
     */
    public function saveComponent(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $page = CmsPage::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'component_type' => 'required|in:features,programs,testimonials,quick_links,program_cards,facility_cards,amenity_cards,process_steps,requirement_cards,date_cards,faq_items',
            'component_index' => 'nullable|integer',
            'title_en' => 'required|string|max:255',
            'title_hi' => 'nullable|string|max:255',
            'title_kn' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_hi' => 'nullable|string',
            'description_kn' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'url' => 'nullable|url|max:255',
            'author_name' => 'nullable|string|max:255',
            'author_role' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Get current content
        $content = $page->content ?? [];
        if (is_string($content)) {
            $content = json_decode($content, true) ?? [];
        }
        if (!is_array($content)) {
            $content = [];
        }

        if (!isset($content['components'])) {
            $content['components'] = [
                'features' => [],
                'programs' => [],
                'testimonials' => [],
                'quick_links' => [],
                'program_cards' => [],
                'facility_cards' => [],
                'amenity_cards' => [],
            ];
        }

        $componentType = $request->input('component_type');
        $componentIndex = $request->input('component_index');

        // Build component data
        $languages = config('content.pages.languages', ['en' => 'English']);
        $component = [
            'title' => [],
            'description' => [],
        ];

        foreach ($languages as $langCode => $langName) {
            $component['title'][$langCode] = $request->input("title_{$langCode}", '');
            $component['description'][$langCode] = $request->input("description_{$langCode}", '');
        }

        // Add type-specific fields
        if ($componentType === 'features') {
            $component['icon'] = $request->input('icon', 'book');
            $component['color'] = $request->input('color', 'primary');
        } elseif ($componentType === 'programs') {
            $component['subtitle'] = [];
            foreach ($languages as $langCode => $langName) {
                $component['subtitle'][$langCode] = $request->input("subtitle_{$langCode}", '');
            }
            $component['icon'] = $request->input('icon', 'book');
            $component['color'] = $request->input('color', 'primary');
        } elseif ($componentType === 'testimonials') {
            $component['author_name'] = $request->input('author_name', '');
            $component['author_role'] = $request->input('author_role', '');
            $component['author_initials'] = $request->input('author_initials', '');
            $component['rating'] = $request->input('rating', 5);
            $component['color'] = $request->input('color', 'primary');
        } elseif ($componentType === 'quick_links') {
            $component['url'] = $request->input('url', '');
            $component['icon'] = $request->input('icon', 'link');
            $component['color'] = $request->input('color', 'primary');
        } elseif ($componentType === 'program_cards' || $componentType === 'facility_cards') {
            $component['icon'] = $request->input('icon', 'book');
            $component['color'] = $request->input('color', 'blue');
            $component['url'] = $request->input('url', '');
            // Handle features list
            $component['features'] = [];
            foreach ($languages as $langCode => $langName) {
                $featuresInput = $request->input("features_{$langCode}", '');
                if ($featuresInput) {
                    // Split by newline or comma
                    $features = preg_split('/[\n,]+/', $featuresInput);
                    $features = array_map('trim', $features);
                    $features = array_filter($features); // Remove empty
                    $component['features'][$langCode] = array_values($features);
                } else {
                    $component['features'][$langCode] = [];
                }
            }
        } elseif ($componentType === 'amenity_cards') {
            $component['icon'] = $request->input('icon', 'device');
            $component['color'] = $request->input('color', 'blue');
        } elseif ($componentType === 'process_steps') {
            $component['step_number'] = $request->input('step_number', 1);
            $component['icon'] = $request->input('icon', 'document');
            $component['color'] = $request->input('color', 'blue');
        } elseif ($componentType === 'requirement_cards') {
            $component['icon'] = $request->input('icon', 'document');
            $component['color'] = $request->input('color', 'blue');
            // Handle items list
            $component['items'] = [];
            foreach ($languages as $langCode => $langName) {
                $itemsInput = $request->input("items_{$langCode}", '');
                if ($itemsInput) {
                    // Split by newline or comma
                    $items = preg_split('/[\n,]+/', $itemsInput);
                    $items = array_map('trim', $items);
                    $items = array_filter($items); // Remove empty
                    $component['items'][$langCode] = array_values($items);
                } else {
                    $component['items'][$langCode] = [];
                }
            }
        } elseif ($componentType === 'date_cards') {
            $component['label'] = [];
            $component['date'] = [];
            $component['description'] = [];
            foreach ($languages as $langCode => $langName) {
                $component['label'][$langCode] = $request->input("label_{$langCode}", '');
                $component['date'][$langCode] = $request->input("date_{$langCode}", '');
                $component['description'][$langCode] = $request->input("description_{$langCode}", '');
            }
            $component['color'] = $request->input('color', 'blue');
        } elseif ($componentType === 'faq_items') {
            $component['question'] = [];
            $component['answer'] = [];
            foreach ($languages as $langCode => $langName) {
                $component['question'][$langCode] = $request->input("question_{$langCode}", '');
                $component['answer'][$langCode] = $request->input("answer_{$langCode}", '');
            }
        }

        // Add or update component
        if ($componentIndex !== null && isset($content['components'][$componentType][$componentIndex])) {
            $content['components'][$componentType][$componentIndex] = $component;
        } else {
            $content['components'][$componentType][] = $component;
        }

        $page->update(['content' => $content]);

        return redirect()->route('tenant.admin.cms.pages.components', $id)
            ->with('success', 'Component saved successfully!');
    }

    /**
     * Delete a component
     */
    public function deleteComponent(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $page = CmsPage::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'component_type' => 'required|in:features,programs,testimonials,quick_links,program_cards,facility_cards,amenity_cards,process_steps,requirement_cards,date_cards,faq_items',
            'component_index' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Get current content
        $content = $page->content ?? [];
        if (is_string($content)) {
            $content = json_decode($content, true) ?? [];
        }
        if (!is_array($content)) {
            $content = [];
        }

        if (isset($content['components'][$request->input('component_type')][$request->input('component_index')])) {
            unset($content['components'][$request->input('component_type')][$request->input('component_index')]);
            // Re-index array
            $content['components'][$request->input('component_type')] = array_values($content['components'][$request->input('component_type')]);
            $page->update(['content' => $content]);
        }

        return redirect()->to('/admin/cms/pages/' . $id . '/components')
            ->with('success', 'Component deleted successfully!');
    }
}
