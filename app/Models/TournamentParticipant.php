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
     * Override the score attribute setter to synchronize scores for team members
     * 
     * @param int|null $value
     * @return void
     */
    public function setScoreAttribute($value)
    {
        $this->attributes['score'] = $value;
        
        // When updating the score, synchronize with team members if this is a team tournament
        if ($this->team_id && $value !== null) {
            // We'll handle the actual synchronization in the JudgeDashboardController
            // This is just a placeholder to show the concept
        }
    }

    /**
     * Get the rubric scores for this participant.
     */
    public function rubricScores()
    {
        return $this->hasMany(RubricScore::class, 'tournament_participant_id');
    }
}