<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TeamInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'status',
        'expires_at',
        'responded_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the team that sent this invitation
     */
    public function team()
    {
        return $this->belongsTo(TournamentTeam::class, 'team_id');
    }

    /**
     * Get the user who was invited
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the invitation is pending
     */
    public function isPending()
    {
        if ($this->status !== 'pending') {
            return false;
        }

        // Check expiration
        return !$this->isExpired();
    }

    /**
     * Check if the invitation is expired
     */
    public function isExpired()
    {
        if ($this->status === 'expired') {
            return true;
        }

        // Check if the invitation has expired based on the expiration date
        if ($this->expires_at && Carbon::now()->greaterThan($this->expires_at)) {
            // Update status to expired
            $this->update(['status' => 'expired']);
            return true;
        }

        return false;
    }

    /**
     * Accept the invitation
     */
    public function accept()
    {
        if (!$this->isPending()) {
            return false;
        }

        // Get the tournament team
        $team = $this->team;
        if (!$team) {
            return false;
        }

        // Check if there's space in the team
        if ($team->isFull()) {
            return false;
        }

        // Update invitation status
        $this->status = 'accepted';
        $this->responded_at = Carbon::now();
        $this->save();

        // Create tournament participant record
        TournamentParticipant::create([
            'tournament_id' => $team->tournament_id,
            'user_id' => $this->user_id,
            'team_id' => $team->id,
            'role' => 'member',
        ]);

        return true;
    }

    /**
     * Decline the invitation
     */
    public function decline()
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->status = 'declined';
        $this->responded_at = Carbon::now();
        $this->save();

        return true;
    }

    /**
     * Create a new invitation with expiry date
     */
    public static function createWithExpiry($teamId, $userId, $expiryDays = 3)
    {
        return self::create([
            'team_id' => $teamId,
            'user_id' => $userId,
            'status' => 'pending',
            'expires_at' => Carbon::now()->addDays($expiryDays),
        ]);
    }
}