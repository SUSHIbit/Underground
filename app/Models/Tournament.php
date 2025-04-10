<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'description', 
        'date_time', 
        'location', 
        'eligibility', 
        'minimum_rank', 
        'team_size', 
        'deadline', 
        'rules', 
        'judging_criteria', 
        'project_submission',
        'created_by',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'review_notes'
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'deadline' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Updated to use tournament_judge_users table
    public function judges()
    {
        return $this->belongsToMany(User::class, 'tournament_judge_users')
                   ->withPivot('role')
                   ->withTimestamps();
    }

    public function participants()
    {
        return $this->hasMany(TournamentParticipant::class);
    }

    public function comments()
    {
        return $this->morphMany(SetComment::class, 'commentable');
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isPendingApproval()
    {
        return $this->status === 'pending_approval';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function submitForApproval()
    {
        $this->status = 'pending_approval';
        $this->submitted_at = now();
        $this->save();
        
        return $this;
    }

    public function approve(User $accessor, $notes = null)
    {
        $this->status = 'approved';
        $this->reviewed_at = now();
        $this->reviewed_by = $accessor->id;
        $this->review_notes = $notes;
        $this->save();
        
        return $this;
    }

    public function reject(User $accessor, $notes = null)
    {
        $this->status = 'rejected';
        $this->reviewed_at = now();
        $this->reviewed_by = $accessor->id;
        $this->review_notes = $notes;
        $this->save();
        
        return $this;
    }

    public function isEligible(User $user)
    {
        // Get user's rank, ensuring it's not null
        $userRank = $user->getRank();
        if (!$userRank) {
            return false;
        }
    
        // Define ranks in order from lowest to highest
        $ranks = ['Unranked', 'Bronze', 'Silver', 'Gold', 'Master', 'Grand Master', 'One Above All'];
        
        // Get index of user's rank and minimum required rank
        $userRankIndex = array_search($userRank, $ranks);
        $minRankIndex = array_search($this->minimum_rank, $ranks);
        
        // If either rank isn't found in our array, handle the error
        if ($userRankIndex === false) {
            \Log::error("User rank '{$userRank}' not found in ranks array.");
            return false;
        }
        
        if ($minRankIndex === false) {
            \Log::error("Tournament minimum rank '{$this->minimum_rank}' not found in ranks array.");
            // Default to allowing participation if rank requirement is invalid
            return true;
        }
        
        // Check if user's rank meets or exceeds the minimum rank
        return $userRankIndex >= $minRankIndex;
    }
}