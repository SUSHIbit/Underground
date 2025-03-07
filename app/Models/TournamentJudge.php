<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentJudge extends Model
{
    use HasFactory;
    
    protected $fillable = ['tournament_id', 'name', 'role'];
    
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}