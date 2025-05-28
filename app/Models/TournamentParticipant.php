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

    /**
     * Get the rubric scores for this participant.
     */
    public function rubricScores()
    {
        return $this->hasMany(RubricScore::class, 'tournament_participant_id');
    }
}