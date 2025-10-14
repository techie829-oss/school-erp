<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeacherDocument extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'teacher_id',
        'document_name',
        'document_type',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_at',
        'uploaded_by',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Accessors
     */
    public function getFileUrlAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function getFileSizeHumanAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Scopes
     */
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }
}

