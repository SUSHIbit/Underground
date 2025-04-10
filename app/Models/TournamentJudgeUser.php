<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentJudgeUser extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tournament_id',
        'user_id',
        'role'
    ];
    
    /**
     * Get the tournament associated with this judge relationship.
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
    
    /**
     * Get the user (judge) associated with this relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}