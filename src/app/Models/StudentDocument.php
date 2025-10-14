<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StudentDocument extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'student_id',
        'tenant_id',
        'document_type',
        'document_name',
        'file_path',
        'file_size',
        'file_type',
        'uploaded_by',
        'remarks',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Get the student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the user who uploaded
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    /**
     * Get human-readable file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Get document type label
     */
    public function getDocumentTypeLabelAttribute()
    {
        $labels = [
            'birth_certificate' => 'Birth Certificate',
            'tc' => 'Transfer Certificate',
            'id_proof' => 'ID Proof',
            'photo' => 'Photo',
            'medical' => 'Medical Certificate',
            'caste' => 'Caste Certificate',
            'income' => 'Income Certificate',
            'other' => 'Other Document',
        ];

        return $labels[$this->document_type] ?? $this->document_type;
    }

    /**
     * Delete file from storage
     */
    public function deleteFile()
    {
        if ($this->file_path && Storage::exists($this->file_path)) {
            return Storage::delete($this->file_path);
        }
        return false;
    }

    /**
     * Scope to filter by tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope to filter by document type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Boot method to delete file when document is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }
}
