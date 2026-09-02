<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionSection extends Model
{
    use HasFactory;

    protected $fillable = ['questionnaire_id', 'title', 'order'];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }
}
