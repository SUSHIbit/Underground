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
        'profile_picture', // Add this new field
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
     * Get the user's legion membership.
     */
    public function legionMembership()
    {
        return $this->hasOne(LegionMember::class);
    }

    /**
     * Get the legion that the user belongs to.
     */
    public function legion()
    {
        return $this->belongsToMany(Legion::class, 'legion_members')
                    ->withPivot('role', 'is_accepted', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Check if user is in a legion.
     */
    public function isInLegion()
    {
        return $this->legionMembership()->where('is_accepted', true)->exists();
    }

    /**
     * Check if user has a pending legion invitation.
     */
    public function hasPendingLegionInvitation()
    {
        return $this->legionMembership()->where('is_accepted', false)->exists();
    }

    /**
     * Get the user's current legion.
     */
    public function getCurrentLegion()
    {
        $membership = $this->legionMembership()->where('is_accepted', true)->first();
        
        if ($membership) {
            return $membership->legion;
        }
        
        return null;
    }

    /**
     * Check if user is a legion leader.
     */
    public function isLegionLeader()
    {
        return $this->legionMembership()->where('role', 'leader')->exists();
    }

    /**
     * Check if user is a legion officer.
     */
    public function isLegionOfficer()
    {
        return $this->legionMembership()->where('role', 'officer')->exists();
    }

    /**
     * Check if user has legion management privileges.
     */
    public function hasLegionManagementPrivileges()
    {
        return $this->isLegionLeader() || $this->isLegionOfficer();
    }
}