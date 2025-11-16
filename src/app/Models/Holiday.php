<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ForTenant;

class Holiday extends Model
{
    use ForTenant;

    protected $table = 'holidays';

    protected $fillable = [
        'tenant_id',
        'date',
        'title',
        'type',
        'is_full_day',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_full_day' => 'boolean',
    ];
}


