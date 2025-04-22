<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'name',
        'leader_id',
    ];

    /**
     * Get the tournament that this team belongs to
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Get the user who is the team leader
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Get all participants in this team
     */
    public function participants()
    {
        return $this->hasMany(TournamentParticipant::class, 'team_id');
    }

    /**
     * Get all members of this team (excluding the leader)
     */
    public function members()
    {
        return $this->participants()->where('role', 'member');
    }

    /**
     * Get all invitations for this team
     */
    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class, 'team_id');
    }

    /**
     * Get pending invitations for this team
     */
    public function pendingInvitations()
    {
        return $this->invitations()->where('status', 'pending');
    }

    /**
     * Check if the team is full
     */
    public function isFull()
    {
        $teamSize = $this->tournament->team_size;
        $currentMemberCount = $this->participants()->count(); // includes leader and members
        
        return $currentMemberCount >= $teamSize;
    }

    /**
     * Get number of available spots in the team
     */
    public function availableSpots()
    {
        $teamSize = $this->tournament->team_size;
        $currentMemberCount = $this->participants()->count();
        
        return max(0, $teamSize - $currentMemberCount);
    }
}