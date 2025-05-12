<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentRubric extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'title',
        'score_weight'
    ];

    /**
     * Get the tournament that this rubric belongs to
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}