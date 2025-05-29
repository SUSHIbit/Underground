<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'judging_date',
        'rules', 
        'judging_criteria', 
        'project_submission',
        'created_by',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'review_notes',
        'published_at',
        'tournament_type'
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'deadline' => 'datetime',
        'judging_date' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Check if the tournament has ended
     * 
     * @return bool
     */
    public function hasEnded()
    {
        return Carbon::parse($this->date_time)->isPast();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

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
    
    /**
     * Check if this tournament is approved but not published.
     */
    public function isApprovedUnpublished()
    {
        return $this->status === 'approved_unpublished';
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
        // Set status to approved_unpublished instead of approved
        $this->status = 'approved_unpublished';
        $this->reviewed_at = now();
        $this->reviewed_by = $accessor->id;
        $this->review_notes = $notes;
        $this->save();
        
        return $this;
    }
    
    /**
     * Publish this tournament.
     */
    public function publish()
    {
        if ($this->status === 'approved_unpublished') {
            $this->status = 'approved';
            $this->published_at = now();
            $this->save();
        }
        
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

    /**
     * Get the rubrics for this tournament
     */
    public function rubrics()
    {
        return $this->hasMany(TournamentRubric::class);
    }

    /**
     * Get total weight of all rubrics
     * 
     * @return int
     */
    public function getTotalRubricWeight()
    {
        return $this->rubrics()->sum('score_weight');
    }

    /**
     * Check if tournament has valid rubrics (at least 3 and sum to 100)
     * 
     * @return bool
     */
    public function hasValidRubrics()
    {
        return $this->rubrics()->count() >= 3 && $this->getTotalRubricWeight() == 100;
    }

    /**
     * Check if all judges have completed grading for this tournament
     */
    public function isGradingComplete()
    {
        $totalJudges = $this->judges()->count();
        $completedJudges = $this->judges()->wherePivot('grading_completed', true)->count();
        
        return $totalJudges > 0 && $completedJudges === $totalJudges;
    }

    /**
     * Check if a specific judge has completed grading
     */
    public function isJudgeGradingComplete($judgeUserId)
    {
        return $this->judges()
                    ->where('user_id', $judgeUserId)
                    ->wherePivot('grading_completed', true)
                    ->exists();
    }

    /**
     * Get the count of judges who have completed grading
     */
    public function getCompletedJudgesCount()
    {
        return $this->judges()->wherePivot('grading_completed', true)->count();
    }

    /**
     * Check if a judge can mark grading as complete
     * They must have graded all submitted participants
     */
    public function canJudgeCompleteGrading($judgeUserId)
    {
        // Get all participants with submissions
        $participantsWithSubmissions = $this->participants()
                                        ->whereNotNull('submission_url')
                                        ->pluck('id');
        
        if ($participantsWithSubmissions->isEmpty()) {
            return false;
        }
        
        // Check if judge has graded all submitted participants
        $gradedByJudge = \App\Models\JudgeScore::where('judge_user_id', $judgeUserId)
                                            ->whereIn('tournament_participant_id', $participantsWithSubmissions)
                                            ->count();
        
        return $gradedByJudge === $participantsWithSubmissions->count();
    }

    /**
     * Mark a judge as having completed grading
     */
    public function markJudgeGradingComplete($judgeUserId)
    {
        $this->judges()->updateExistingPivot($judgeUserId, [
            'grading_completed' => true,
            'grading_completed_at' => now()
        ]);
    }
}