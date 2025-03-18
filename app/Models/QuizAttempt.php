<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'set_id', 
        'score', 
        'total_questions', 
        'completed',
        'started_at',
        'time_expires_at'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'started_at' => 'datetime',
        'time_expires_at' => 'datetime'
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
    
    /**
     * Get the remaining time in seconds
     */
    public function getRemainingTimeAttribute()
    {
        if (!$this->time_expires_at) {
            return null;
        }
        
        $now = Carbon::now();
        if ($now->gt($this->time_expires_at)) {
            return 0;
        }
        
        return $this->time_expires_at->diffInSeconds($now);
    }
    
    /**
     * Check if the timer has expired
     */
    public function hasTimerExpired()
    {
        if (!$this->time_expires_at) {
            return false;
        }
        
        return Carbon::now()->gt($this->time_expires_at);
    }
    
    /**
     * Start the timer based on set timer_minutes
     */
    public function startTimer($timer_minutes)
    {
        if (!$timer_minutes || !is_numeric($timer_minutes) || $this->started_at) {
            return;
        }
        
        $this->started_at = Carbon::now();
        $this->time_expires_at = Carbon::now()->addMinutes($timer_minutes);
        $this->save();
    }
}