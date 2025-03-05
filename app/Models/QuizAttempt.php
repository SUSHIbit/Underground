<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'set_id', 
        'score', 
        'total_questions', 
        'completed'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function getScorePercentageAttribute()
    {
        if ($this->total_questions === 0) {
            return 0;
        }
        
        return round(($this->score / $this->total_questions) * 100);
    }
}
