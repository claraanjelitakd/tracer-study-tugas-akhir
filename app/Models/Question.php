<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_section_id', 'code', 'question_text', 'type', 'is_required', 'jump_logic', 'order'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'jump_logic' => 'array',
    ];

    public function section()
    {
        return $this->belongsTo(QuestionSection::class, 'question_section_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }
}
