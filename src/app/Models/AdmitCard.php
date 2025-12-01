<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdmitCard extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'exam_id',
        'student_id',
        'class_id',
        'section_id',
        'hall_ticket_number',
        'qr_code',
        'barcode',
        'student_name',
        'admission_number',
        'roll_number',
        'photo_path',
        'exam_details_json',
        'generated_by',
        'generated_at',
        'is_printed',
        'printed_at',
    ];

    protected $casts = [
        'exam_details_json' => 'array',
        'generated_at' => 'datetime',
        'printed_at' => 'datetime',
        'is_printed' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Scopes
     */
    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopePrinted($query)
    {
        return $query->where('is_printed', true);
    }

    /**
     * Accessors
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path) {
            return Storage::url($this->photo_path);
        }

        // Fallback to student photo if available
        if ($this->student && $this->student->photo) {
            return $this->student->photo_url;
        }

        return null;
    }
}

