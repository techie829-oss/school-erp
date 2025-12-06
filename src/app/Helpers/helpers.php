<?php

if (!function_exists('cms_field')) {
    /**
     * Get CMS field value based on status
     * If enabled, return CMS data; if disabled, return default value
     *
     * @param string $pageSlug Page slug (empty for home)
     * @param string $fieldName Field name
     * @param mixed $defaultValue Default value if field is disabled or not found (optional, will use config if not provided)
     * @param string|null $tenantId Tenant ID (optional, will use current tenant if not provided)
     * @return mixed
     */
function cms_field(string $pageSlug, string $fieldName, $defaultValue = null, ?string $tenantId = null, ?string $language = null)
{
    return \App\Helpers\CmsHelper::getFieldValue($pageSlug, $fieldName, $defaultValue, $tenantId, $language);
}

/**
 * Get CMS components for a page
 *
 * @param string $pageSlug Page slug (empty for home)
 * @param string $componentType Component type (features, programs, testimonials, quick_links)
 * @param string|null $tenantId Tenant ID
 * @param string|null $language Language code
 * @return array
 */
function cms_components(string $pageSlug, string $componentType, ?string $tenantId = null, ?string $language = null): array
{
    return \App\Helpers\CmsHelper::getComponents($pageSlug, $componentType, $tenantId, $language);
}
}
