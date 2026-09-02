<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'code', 'option_text', 'jump_to', 'order'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
