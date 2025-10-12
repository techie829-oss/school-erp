<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_type',
        'user_id',
        'action',
        'model_type',
        'model_id',
        'properties',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'properties' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(AdminUser::class, 'user_id');
    }

    public function model(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByUser($query, $userId, $userType = 'admin')
    {
        return $query->where('user_id', $userId)->where('user_type', $userType);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        return $query;
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public static function log($action, $model = null, $properties = [], $tenantId = null, $userType = 'admin', $userId = null)
    {
        $log = new self([
            'tenant_id' => $tenantId,
            'user_type' => $userType,
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $log->save();
        return $log;
    }

    public function getActionBadgeAttribute()
    {
        return match($this->action) {
            'created' => 'bg-green-100 text-green-800',
            'updated' => 'bg-blue-100 text-blue-800',
            'deleted' => 'bg-red-100 text-red-800',
            'logged_in' => 'bg-green-100 text-green-800',
            'logged_out' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getModelNameAttribute()
    {
        if (!$this->model_type) return 'System';

        $className = class_basename($this->model_type);
        return match($className) {
            'AdminUser' => 'Admin User',
            'Tenant' => 'Tenant',
            'Ticket' => 'Ticket',
            'TicketComment' => 'Ticket Comment',
            default => $className
        };
    }
}
