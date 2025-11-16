<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'setting_key',
        'setting_value',
        'setting_type',
        'group',
        'is_public',
        'description',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get the tenant that owns the setting
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the value cast to the appropriate type
     */
    public function getValue()
    {
        return match($this->setting_type) {
            'boolean' => filter_var($this->setting_value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->setting_value,
            'json' => is_array($this->setting_value) ? $this->setting_value : json_decode($this->setting_value, true),
            'file' => $this->setting_value,
            default => $this->setting_value,
        };
    }

    /**
     * Set the value with the appropriate type
     */
    public function setValue($value)
    {
        $this->setting_value = match($this->setting_type) {
            'boolean' => $value ? 'true' : 'false',
            'integer' => (string) $value,
            'json' => json_encode($value),
            default => (string) $value,
        };

        return $this;
    }

    /**
     * Scope query to a specific tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope query to a specific group
     */
    public function scopeInGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope query to public settings only
     */
    public function scopePublicOnly($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get setting by key for a tenant
     */
    public static function getSetting($tenantId, $key, $default = null)
    {
        $setting = static::where('tenant_id', $tenantId)
            ->where('setting_key', $key)
            ->first();

        return $setting ? $setting->getValue() : $default;
    }

    /**
     * Set or update a setting for a tenant
     */
    public static function setSetting($tenantId, $key, $value, $type = 'string', $group = 'general', $description = null)
    {
        $setting = static::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'setting_key' => $key,
            ],
            [
                'setting_type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );

        $setting->setValue($value);
        $setting->save();

        return $setting;
    }

    /**
     * Get all settings for a tenant as key-value array
     */
    public static function getAllForTenant($tenantId, $group = null)
    {
        $query = static::where('tenant_id', $tenantId);

        if ($group) {
            $query->where('group', $group);
        }

        $settings = $query->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->setting_key] = $setting->getValue();
        }

        return $result;
    }
}
