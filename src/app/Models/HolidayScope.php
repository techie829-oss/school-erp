<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ForTenant;

class HolidayScope extends Model
{
    use ForTenant;

    protected $table = 'holiday_scopes';

    protected $fillable = [
        'tenant_id',
        'holiday_id',
        'class_id',
        'section_id',
    ];

    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}


