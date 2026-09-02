<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'year', 'is_active'];

    public function sections()
    {
        return $this->hasMany(QuestionSection::class)->orderBy('order');
    }
}
