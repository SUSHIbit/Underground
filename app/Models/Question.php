<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'set_id', 
        'question_number', 
        'question_text', 
        'options', 
        'correct_answer', 
        'reason'
    ];

    protected $casts = [
        'options' => 'json',
    ];

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
