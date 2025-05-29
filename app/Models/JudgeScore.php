<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudgeScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_participant_id',
        'judge_user_id',
        'score',
        'feedback',
        'rubric_scores',
    ];

    protected $casts = [
        'rubric_scores' => 'array',
        'score' => 'decimal:1',
    ];

    /**
     * Get the participant that this score belongs to.
     */
    public function participant()
    {
        return $this->belongsTo(TournamentParticipant::class, 'tournament_participant_id');
    }

    /**
     * Get the judge who gave this score.
     */
    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_user_id');
    }

    /**
     * Get the tournament through the participant.
     */
    public function tournament()
    {
        return $this->hasOneThrough(Tournament::class, TournamentParticipant::class, 'id', 'id', 'tournament_participant_id', 'tournament_id');
    }
}