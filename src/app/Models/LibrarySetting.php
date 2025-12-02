<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibrarySetting extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'max_books_per_student',
        'issue_duration_days',
        'fine_per_day',
        'max_renewals',
        'renewal_duration_days',
        'book_lost_fine',
        'book_damage_fine',
        'allow_online_issue',
        'send_overdue_notifications',
        'overdue_notification_days',
        'library_rules',
    ];

    protected $casts = [
        'max_books_per_student' => 'integer',
        'issue_duration_days' => 'integer',
        'fine_per_day' => 'decimal:2',
        'max_renewals' => 'integer',
        'renewal_duration_days' => 'integer',
        'book_lost_fine' => 'decimal:2',
        'book_damage_fine' => 'decimal:2',
        'allow_online_issue' => 'boolean',
        'send_overdue_notifications' => 'boolean',
        'overdue_notification_days' => 'integer',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    // Static method to get or create settings for a tenant
    public static function getForTenant($tenantId)
    {
        return static::firstOrCreate(
            ['tenant_id' => $tenantId],
            [
                'max_books_per_student' => 3,
                'issue_duration_days' => 14,
                'fine_per_day' => 5.00,
                'max_renewals' => 2,
                'renewal_duration_days' => 7,
                'send_overdue_notifications' => true,
                'overdue_notification_days' => 1,
            ]
        );
    }
}
