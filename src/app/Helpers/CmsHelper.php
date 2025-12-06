<?php

namespace App\Helpers;

use App\Models\CmsPage;

class CmsHelper
{
    /**
     * Get CMS field value based on status
     * If enabled, return CMS data; if disabled, return default value
     *
     * @param string $pageSlug Page slug (empty for home)
     * @param string $fieldName Field name
     * @param mixed $defaultValue Default value if field is disabled or not found (optional, will use config if not provided)
     * @param string|null $tenantId Tenant ID (optional, will use current tenant if not provided)
     * @param string|null $language Language code (optional, will use current locale if not provided)
     * @return mixed
     */
    public static function getFieldValue(string $pageSlug, string $fieldName, $defaultValue = null, ?string $tenantId = null, ?string $language = null)
    {
        // Normalize page slug
        $configSlug = $pageSlug === '' ? 'home' : $pageSlug;

        // Get current language
        if ($language === null) {
            // First priority: Session language (user selected)
            $language = session('website_language');

            // Second priority: App locale
            if (!$language) {
                $language = app()->getLocale();
            }

            // Third priority: CMS settings for tenant (only if no language set yet)
            if (!$language) {
                try {
                    if (!$tenantId) {
                        $tenant = request()->attributes->get('current_tenant');
                        if ($tenant) {
                            $tenantId = is_object($tenant) ? $tenant->id : (is_array($tenant) ? ($tenant['id'] ?? null) : null);
                        }
                    }

                    if ($tenantId) {
                        $cmsSettings = \App\Models\CmsSettings::forTenant($tenantId)->first();
                        if ($cmsSettings && $cmsSettings->default_language) {
                            $language = $cmsSettings->default_language;
                        }
                    }
                } catch (\Exception $e) {
                    // Silently fail
                }
            }

            // Final fallback to config default
            if (!$language) {
                $language = config('content.pages.default_language', 'en');
            }

            // Ensure language is valid
            $availableLanguages = array_keys(config('content.pages.languages', ['en' => 'English']));
            if (!in_array($language, $availableLanguages)) {
                $language = config('content.pages.default_language', 'en');
            }
        }

        // Get default value from config if not provided
        if ($defaultValue === null) {
            $defaultValue = config("content.pages.pages.{$configSlug}.{$language}.{$fieldName}", '');

            // Fallback to tenant's default language if translation not found
            if (empty($defaultValue)) {
                $defaultLang = config('content.pages.default_language', 'en');

                // Try to get tenant's default language from CMS settings
                try {
                    if ($tenantId) {
                        $cmsSettings = \App\Models\CmsSettings::forTenant($tenantId)->first();
                        if ($cmsSettings && $cmsSettings->default_language) {
                            $defaultLang = $cmsSettings->default_language;
                        }
                    } else {
                        // Try to get tenant ID and then settings
                        $tenant = request()->attributes->get('current_tenant');
                        if ($tenant) {
                            $tempTenantId = is_object($tenant) ? $tenant->id : (is_array($tenant) ? ($tenant['id'] ?? null) : null);
                            if ($tempTenantId) {
                                $cmsSettings = \App\Models\CmsSettings::forTenant($tempTenantId)->first();
                                if ($cmsSettings && $cmsSettings->default_language) {
                                    $defaultLang = $cmsSettings->default_language;
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Silently fail, use config default
                }

                $defaultValue = config("content.pages.pages.{$configSlug}.{$defaultLang}.{$fieldName}", '');
            }

            // Handle dynamic placeholders in default values
            // For example: "Welcome to {tenant_name}" or "Welcome to {tenant_name} - {tenant_description}"
            if (is_string($defaultValue) && (str_contains($defaultValue, '{tenant_') || str_contains($defaultValue, '{tenant['))) {
                // Try to get tenant info for replacements
                try {
                    $tenantService = app(\App\Services\TenantService::class);
                    $tenantInfo = $tenantService->getTenantInfo(request());

                    // Replace placeholders
                    $defaultValue = str_replace('{tenant_name}', $tenantInfo['name'] ?? 'Our School', $defaultValue);
                    $defaultValue = str_replace('{tenant_description}', $tenantInfo['description'] ?? 'Excellence in Education', $defaultValue);
                    $defaultValue = str_replace('{tenant_student_count}', $tenantInfo['student_count'] ?? '500+', $defaultValue);
                } catch (\Exception $e) {
                    // If replacement fails, use as-is
                }
            }
        }

        // Get field config
        $fields = config("all.cms_fields.{$configSlug}", []);

        // Find field
        $field = collect($fields)->firstWhere('name', $fieldName);

        if (!$field) {
            return $defaultValue;
        }

        // If disabled, return default
        if ($field['status'] === 'disabled') {
            return $defaultValue;
        }

        // If enabled, get CMS data
        if ($field['status'] === 'enabled') {
            // Get tenant ID from multiple sources
            if (!$tenantId) {
                // Try to get from request attributes (set by middleware)
                $tenant = request()->attributes->get('current_tenant');
                if ($tenant) {
                    $tenantId = is_object($tenant) ? $tenant->id : (is_array($tenant) ? ($tenant['id'] ?? null) : null);
                }

                // If still not found, try to get from TenantService
                if (!$tenantId) {
                    try {
                        $tenantService = app(\App\Services\TenantService::class);
                        $currentTenant = $tenantService->getCurrentTenant(request());
                        if ($currentTenant) {
                            $tenantId = is_object($currentTenant) ? $currentTenant->id : (is_array($currentTenant) ? ($currentTenant['id'] ?? null) : null);
                        }

                        // If still not found, try getCurrentTenantId
                        if (!$tenantId) {
                            $tenantId = $tenantService->getCurrentTenantId(request());
                        }
                    } catch (\Exception $e) {
                        // Silently fail
                    }
                }
            }

            if (!$tenantId) {
                return $defaultValue;
            }

            // Get page - use pageSlug for database (empty string for home page)
            // Database stores home page with slug '' (empty string)
            $page = CmsPage::forTenant($tenantId)
                ->where('slug', $pageSlug === '' ? '' : $pageSlug)
                ->first();

            if (!$page || !$page->content) {
                return $defaultValue;
            }

            // Ensure content is an array (handle both array and JSON string)
            $content = $page->content;
            if (is_string($content)) {
                $content = json_decode($content, true) ?? [];
            }
            if (!is_array($content)) {
                return $defaultValue;
            }

            $fieldValues = $content['fields'] ?? [];

            // Check for language-specific field value
            $langKey = "{$fieldName}_{$language}";
            if (isset($fieldValues[$langKey])) {
                return $fieldValues[$langKey];
            }

            // Fallback to tenant's default language from CMS settings
            $defaultLang = config('content.pages.default_language', 'en');
            try {
                $cmsSettings = \App\Models\CmsSettings::forTenant($tenantId)->first();
                if ($cmsSettings && $cmsSettings->default_language) {
                    $defaultLang = $cmsSettings->default_language;
                }
            } catch (\Exception $e) {
                // Silently fail, use config default
            }

            if ($language !== $defaultLang) {
                $defaultLangKey = "{$fieldName}_{$defaultLang}";
                if (isset($fieldValues[$defaultLangKey])) {
                    return $fieldValues[$defaultLangKey];
                }
            }

            // Fallback to field without language suffix (backward compatibility)
            if (isset($fieldValues[$fieldName])) {
                return $fieldValues[$fieldName];
            }

            return $defaultValue;
        }

        return $defaultValue;
    }

    /**
     * Get all enabled fields for a page
     *
     * @param string $pageSlug Page slug
     * @param string|null $tenantId Tenant ID
     * @return array
     */
    public static function getEnabledFields(string $pageSlug, ?string $tenantId = null): array
    {
        $configSlug = $pageSlug === '' ? 'home' : $pageSlug;
        $fields = config("all.cms_fields.{$configSlug}", []);

        $enabledFields = collect($fields)
            ->where('status', 'enabled')
            ->pluck('name')
            ->toArray();

        if (empty($enabledFields)) {
            return [];
        }

        if (!$tenantId) {
            // Try to get from request attributes (set by middleware)
            $tenant = request()->attributes->get('current_tenant');
            if ($tenant) {
                $tenantId = is_object($tenant) ? $tenant->id : (is_array($tenant) ? ($tenant['id'] ?? null) : null);
            }

            // If still not found, try to get from TenantService
            if (!$tenantId) {
                try {
                    $tenantService = app(\App\Services\TenantService::class);
                    $tenant = $tenantService->getCurrentTenant(request());
                    if ($tenant) {
                        $tenantId = is_object($tenant) ? $tenant->id : (is_array($tenant) ? ($tenant['id'] ?? null) : null);
                    }
                } catch (\Exception $e) {
                    // Silently fail
                }
            }
        }

        if (!$tenantId) {
            return [];
        }

        $page = CmsPage::forTenant($tenantId)
            ->where('slug', $pageSlug)
            ->first();

        if (!$page || !$page->content) {
            return [];
        }

        // Ensure content is an array (handle both array and JSON string)
        $content = $page->content;
        if (is_string($content)) {
            $content = json_decode($content, true) ?? [];
        }
        if (!is_array($content)) {
            return [];
        }

        $fieldValues = $content['fields'] ?? [];

        return array_intersect_key($fieldValues, array_flip($enabledFields));
    }

    /**
     * Get components for a page (Features, Programs, Testimonials, Quick Links)
     *
     * @param string $pageSlug Page slug (empty for home)
     * @param string $componentType Component type (features, programs, testimonials, quick_links)
     * @param string|null $tenantId Tenant ID
     * @param string|null $language Language code
     * @return array
     */
    public static function getComponents(string $pageSlug, string $componentType, ?string $tenantId = null, ?string $language = null): array
    {
        // Normalize page slug
        $configSlug = $pageSlug === '' ? 'home' : $pageSlug;

        // Get current language
        if ($language === null) {
            $language = session('website_language') ?? app()->getLocale();
            if (!$language) {
                $language = config('content.pages.default_language', 'en');
            }

            // Ensure language is valid
            $availableLanguages = array_keys(config('content.pages.languages', ['en' => 'English']));
            if (!in_array($language, $availableLanguages)) {
                $language = config('content.pages.default_language', 'en');
            }
        }

        // Get tenant ID
        if (!$tenantId) {
            // Try to get from request attributes (set by middleware)
            $tenant = request()->attributes->get('current_tenant');
            if ($tenant) {
                $tenantId = is_object($tenant) ? $tenant->id : (is_array($tenant) ? ($tenant['id'] ?? null) : null);
            }

            // If still not found, try to get from TenantService
            if (!$tenantId) {
                try {
                    $tenantService = app(\App\Services\TenantService::class);
                    $currentTenant = $tenantService->getCurrentTenant(request());
                    if ($currentTenant) {
                        $tenantId = is_object($currentTenant) ? $currentTenant->id : (is_array($currentTenant) ? ($currentTenant['id'] ?? null) : null);
                    }

                    // If still not found, try getCurrentTenantId
                    if (!$tenantId) {
                        $tenantId = $tenantService->getCurrentTenantId(request());
                    }
                } catch (\Exception $e) {
                    // Silently fail
                }
            }
        }

        if (!$tenantId) {
            return [];
        }

        // Get page
        $page = CmsPage::forTenant($tenantId)
            ->where('slug', $pageSlug === '' ? '' : $pageSlug)
            ->first();

        if (!$page || !$page->content) {
            return [];
        }

        // Ensure content is an array
        $content = $page->content;
        if (is_string($content)) {
            $content = json_decode($content, true) ?? [];
        }
        if (!is_array($content)) {
            return [];
        }

        $components = $content['components'] ?? [];
        $componentList = $components[$componentType] ?? [];

        // Process components to return language-specific content
        $processedComponents = [];
        foreach ($componentList as $component) {
            $processed = [];

            // Get title and description in current language
            $processed['title'] = $component['title'][$language] ?? $component['title']['en'] ?? $component['title'][array_key_first($component['title'] ?? [])] ?? '';
            $processed['description'] = $component['description'][$language] ?? $component['description']['en'] ?? $component['description'][array_key_first($component['description'] ?? [])] ?? '';

            // Copy other fields
            if (isset($component['icon'])) $processed['icon'] = $component['icon'];
            if (isset($component['color'])) {
                $processed['color'] = $component['color'];
                // Map color to Tailwind classes
                $colorMap = [
                    'primary' => 'primary',
                    'green' => 'green',
                    'blue' => 'blue',
                    'purple' => 'purple',
                    'red' => 'red',
                    'yellow' => 'yellow',
                    'orange' => 'orange',
                    'indigo' => 'indigo',
                    'pink' => 'pink',
                    'teal' => 'teal',
                    'cyan' => 'cyan',
                ];
                $processed['color_class'] = $colorMap[$component['color']] ?? 'primary';
            } else {
                $processed['color'] = 'primary';
                $processed['color_class'] = 'primary';
            }
            if (isset($component['url'])) $processed['url'] = $component['url'];
            if (isset($component['subtitle'])) {
                $processed['subtitle'] = $component['subtitle'][$language] ?? $component['subtitle']['en'] ?? $component['subtitle'][array_key_first($component['subtitle'] ?? [])] ?? '';
            }
            // Handle features list (for program cards and facility cards)
            if (isset($component['features'])) {
                $processed['features'] = $component['features'][$language] ?? $component['features']['en'] ?? $component['features'][array_key_first($component['features'] ?? [])] ?? [];
            }
            // Handle items list (for requirement cards)
            if (isset($component['items'])) {
                $processed['items'] = $component['items'][$language] ?? $component['items']['en'] ?? $component['items'][array_key_first($component['items'] ?? [])] ?? [];
            }
            // Handle step_number (for process steps)
            if (isset($component['step_number'])) $processed['step_number'] = $component['step_number'];
            // Handle label, date, description (for date cards)
            if (isset($component['label']) && is_array($component['label'])) {
                $processed['label'] = $component['label'][$language] ?? $component['label']['en'] ?? $component['label'][array_key_first($component['label'] ?? [])] ?? '';
            }
            if (isset($component['date']) && is_array($component['date'])) {
                $processed['date'] = $component['date'][$language] ?? $component['date']['en'] ?? $component['date'][array_key_first($component['date'] ?? [])] ?? '';
            }
            // Handle question and answer (for FAQ items)
            if (isset($component['question']) && is_array($component['question'])) {
                $processed['question'] = $component['question'][$language] ?? $component['question']['en'] ?? $component['question'][array_key_first($component['question'] ?? [])] ?? '';
            }
            if (isset($component['answer']) && is_array($component['answer'])) {
                $processed['answer'] = $component['answer'][$language] ?? $component['answer']['en'] ?? $component['answer'][array_key_first($component['answer'] ?? [])] ?? '';
            }
            if (isset($component['author_name'])) $processed['author_name'] = $component['author_name'];
            if (isset($component['author_role'])) $processed['author_role'] = $component['author_role'];
            if (isset($component['author_initials'])) $processed['author_initials'] = $component['author_initials'];
            if (isset($component['rating'])) $processed['rating'] = $component['rating'];

            $processedComponents[] = $processed;
        }

        return $processedComponents;
    }
}
