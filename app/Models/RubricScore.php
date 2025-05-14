<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RubricScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_participant_id',
        'tournament_rubric_id',
        'score',
    ];

    /**
     * Get the participant that owns this score.
     */
    public function participant()
    {
        return $this->belongsTo(TournamentParticipant::class, 'tournament_participant_id');
    }

    /**
     * Get the rubric that this score is for.
     */
    public function rubric()
    {
        return $this->belongsTo(TournamentRubric::class, 'tournament_rubric_id');
    }
}