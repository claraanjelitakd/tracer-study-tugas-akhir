<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionMapping extends Model
{
    use HasFactory;

    protected $fillable = ['table_name', 'column_name', 'question_id'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
