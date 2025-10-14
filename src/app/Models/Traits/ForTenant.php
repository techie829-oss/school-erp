<?php

namespace App\Models\Traits;

trait ForTenant
{
    /**
     * Scope a query to only include models for a specific tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

