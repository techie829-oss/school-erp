<?php

namespace App\Models;

use App\Models\Traits\ForTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory, ForTenant;

    protected $fillable = [
        'tenant_id',
        'quiz_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'marks',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
