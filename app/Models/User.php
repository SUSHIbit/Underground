<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Tournament;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'profile_picture',
        'points',
        'ue_points',
        'theme_preference',
        'is_judge', // Add this to fillable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_judge' => 'boolean', // Add this casting
    ];

    /**
     * Process and save the profile picture.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function saveProfilePicture($file)
    {
        // Delete the old profile picture if it exists
        if ($this->profile_picture) {
            Storage::disk('public')->delete($this->profile_picture);
        }

        $filename = 'profile_' . $this->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Save the image without resizing (simpler approach)
        $path = $file->storeAs('profile-pictures', $filename, 'public');
        
        return $path;
    }

    /**
     * Get the profile picture URL.
     *
     * @return string
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        
        // Return a default avatar if no profile picture is set
        return asset('images/default-avatar.png');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function completedSets()
    {
        return $this->hasManyThrough(
            Set::class,
            QuizAttempt::class,
            'user_id',
            'id',
            'id',
            'set_id'
        )->where('quiz_attempts.completed', true);
    }

    /**
     * Get the rank based on total points.
     *
     * @return string
     */
    public function getRank(): string
    {
        if ($this->points < 50) {
            return 'Unranked';
        } elseif ($this->points < 100) {
            return 'Bronze';
        } elseif ($this->points < 250) {
            return 'Silver';
        } elseif ($this->points < 500) {
            return 'Gold';
        } elseif ($this->points < 750) {
            return 'Master';
        } elseif ($this->points < 1000) {
            return 'Grand Master';
        } else {
            return 'One Above All';
        }
    }

    /**
     * Calculate points to next rank.
     *
     * @return array
     */
    public function getPointsToNextRank(): array
    {
        if ($this->points < 50) {
            return ['next_rank' => 'Bronze', 'points_needed' => 50 - $this->points];
        } elseif ($this->points < 100) {
            return ['next_rank' => 'Silver', 'points_needed' => 100 - $this->points];
        } elseif ($this->points < 250) {
            return ['next_rank' => 'Gold', 'points_needed' => 250 - $this->points];
        } elseif ($this->points < 500) {
            return ['next_rank' => 'Master', 'points_needed' => 500 - $this->points];
        } elseif ($this->points < 750) {
            return ['next_rank' => 'Grand Master', 'points_needed' => 750 - $this->points];
        } elseif ($this->points < 1000) {
            return ['next_rank' => 'One Above All', 'points_needed' => 1000 - $this->points];
        } else {
            return ['next_rank' => 'Maximum Rank Achieved', 'points_needed' => 0];
        }
    }

    /**
     * Add points to user.
     *
     * @param int $points
     * @return void
     */
    public function addPoints(int $points): void
    {
        $this->increment('points', $points);
    }

    // Add to User model
    public function tournamentParticipants()
    {
        return $this->hasMany(TournamentParticipant::class);
    }

    public function participatingTournaments()
    {
        return $this->hasManyThrough(
            Tournament::class,
            TournamentParticipant::class,
            'user_id',
            'id',
            'id',
            'tournament_id'
        );
    }

    /**
     * Add UEPoints to user.
     *
     * @param int $points
     * @return void
     */
    public function addUEPoints(int $points): void
    {
        $this->increment('ue_points', $points);
    }

    /**
     * Deduct UEPoints from user.
     *
     * @param int $points
     * @return bool
     */
    public function deductUEPoints(int $points): bool
    {
        if ($this->ue_points >= $points) {
            $this->decrement('ue_points', $points);
            return true;
        }
        return false;
    }

    /**
     * Check if user has enough UEPoints.
     *
     * @param int $points
     * @return bool
     */
    public function hasEnoughUEPoints(int $points): bool
    {
        return $this->ue_points >= $points;
    }

    /**
     * Get all tournaments where user is assigned as a judge.
     */
    public function judgedTournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_judge_users')
                ->withPivot('role')
                ->withTimestamps();
    }

    /**
     * Get all team invitations for this user
     */
    public function teamInvitations()
    {
        return $this->hasMany(TeamInvitation::class);
    }

    /**
     * Get pending team invitations for this user
     */
    public function pendingTeamInvitations()
    {
        return $this->teamInvitations()
                    ->where('status', 'pending')
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Get tournament teams led by this user
     */
    public function ledTeams()
    {
        return $this->hasMany(TournamentTeam::class, 'leader_id');
    }

    /**
     * Check if user has any pending invitations for a specific tournament
     */
    public function hasPendingInvitationForTournament($tournamentId)
    {
        return $this->pendingTeamInvitations()
                    ->whereHas('team', function ($query) use ($tournamentId) {
                        $query->where('tournament_id', $tournamentId);
                    })
                    ->exists();
    }

    /**
     * Check if user is already part of a team for a specific tournament
     */
    public function isInTournamentTeam($tournamentId)
    {
        return $this->tournamentParticipants()
                    ->where('tournament_id', $tournamentId)
                    ->whereNotNull('team_id')
                    ->exists();
    }



        /**
     * Check if the user is a judge.
     *
     * @return bool
     */
    public function isJudge(): bool
    {
        return $this->is_judge;
    }

    /**
     * Get the display name for the user's roles.
     *
     * @return string
     */
    public function getRoleDisplayName(): string
    {
        $roleName = ucfirst($this->role);
        
        if ($this->is_judge) {
            $roleName .= ' • Judge';
        }
        
        return $roleName;
    }
}