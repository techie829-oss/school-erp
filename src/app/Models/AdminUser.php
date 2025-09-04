<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'admin_type', // super_admin, super_manager, school_admin
        'is_active',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Admin type constants
     */
    const TYPE_SUPER_ADMIN = 'super_admin';
    const TYPE_SUPER_MANAGER = 'super_manager';
    const TYPE_SCHOOL_ADMIN = 'school_admin';

    /**
     * Get the tenant this admin manages
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if admin is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get the active attribute (alias for is_active)
     */
    public function getActiveAttribute(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if admin is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->admin_type === self::TYPE_SUPER_ADMIN;
    }

    /**
     * Check if admin is super manager
     */
    public function isSuperManager(): bool
    {
        return $this->admin_type === self::TYPE_SUPER_MANAGER;
    }

    /**
     * Check if admin is school admin
     */
    public function isSchoolAdmin(): bool
    {
        return $this->admin_type === self::TYPE_SCHOOL_ADMIN;
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get all admin types
     */
    public static function getAdminTypes(): array
    {
        return [
            self::TYPE_SUPER_ADMIN => 'Super Admin',
            self::TYPE_SUPER_MANAGER => 'Super Manager',
            self::TYPE_SCHOOL_ADMIN => 'School Admin',
        ];
    }

    /**
     * Get the tenant domain for school admin users
     */
    public function getTenantDomain(): ?string
    {
        if ($this->admin_type !== self::TYPE_SCHOOL_ADMIN || !$this->tenant_id) {
            return null;
        }

        $tenant = $this->tenant;
        if (!$tenant || !isset($tenant->data['full_domain'])) {
            return null;
        }

        return $tenant->data['full_domain'];
    }

    /**
     * Get the full tenant URL for school admin users
     */
    public function getTenantUrl(string $path = '/admin'): ?string
    {
        $domain = $this->getTenantDomain();
        if (!$domain) {
            return null;
        }

        $protocol = request()->secure() ? 'https' : 'http';
        return $protocol . '://' . $domain . $path;
    }
}
