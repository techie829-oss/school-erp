<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticeAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'notice_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
    ];

    // Relationships
    public function notice()
    {
        return $this->belongsTo(Notice::class);
    }

    // Accessors
    public function getFormattedSizeAttribute()
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = is_numeric($this->file_size) ? $this->file_size : 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

