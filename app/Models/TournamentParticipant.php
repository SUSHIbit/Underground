<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentParticipant extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tournament_id', 
        'user_id', 
        'team_id',
        'role',
        'submission_url', 
        'score', 
        'feedback',
        'points_awarded'
    ];
    
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function team()
    {
        return $this->belongsTo(TournamentTeam::class);
    }

    /**
     * Get all judge scores for this participant.
     */
    public function judgeScores()
    {
        return $this->hasMany(JudgeScore::class, 'tournament_participant_id');
    }

    /**
     * Get the judge score for a specific judge.
     */
    public function getJudgeScore($judgeUserId)
    {
        return $this->judgeScores()->where('judge_user_id', $judgeUserId)->first();
    }

    /**
     * Calculate and update the average score from all judge scores.
     */
    public function updateAverageScore()
    {
        $judgeScores = $this->judgeScores;
        
        if ($judgeScores->count() > 0) {
            $averageScore = $judgeScores->avg('score');
            $this->score = round($averageScore, 1);
            
            // For feedback, we could concatenate all feedback or just use the most recent
            // For now, let's use the most recent feedback
            $latestFeedback = $judgeScores->sortByDesc('created_at')->first()->feedback;
            $this->feedback = $latestFeedback;
            
            $this->save();
        }
    }

    /**
     * Get the rubric scores for this participant.
     */
    public function rubricScores()
    {
        return $this->hasMany(RubricScore::class, 'tournament_participant_id');
    }

    /**
     * Override the score attribute setter to synchronize scores for team members
     * 
     * @param float|null $value
     * @return void
     */
    public function setScoreAttribute($value)
    {
        $this->attributes['score'] = $value;
        
        // Only synchronize if this is a team tournament and the score is being set
        if ($this->team_id && $value !== null && !$this->isDirty('score')) {
            // Use a flag to prevent infinite recursion
            if (!isset($this->syncingScore)) {
                $this->syncingScore = true;
                
                // Synchronize with other team members
                static::where('team_id', $this->team_id)
                    ->where('id', '!=', $this->id)
                    ->update(['score' => $value]);
                
                unset($this->syncingScore);
            }
        }
    }
}