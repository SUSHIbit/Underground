<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // Add this line
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

    /**
     * Calculate rankings and award both regular points and UEPoints for tournament participants
     * FIXED VERSION: Properly handles both team and individual tournaments
     */
    public function calculateRankingsAndAwardUEPoints()
    {
        // Only calculate if grading is complete and rankings haven't been calculated
        if (!$this->isGradingComplete()) {
            return false;
        }

        // Check if rankings have already been calculated
        $alreadyCalculated = $this->participants()
                                ->where('ranking_calculated', true)
                                ->exists();
        
        if ($alreadyCalculated) {
            return false;
        }

        DB::beginTransaction();
        
        try {
            if ($this->team_size > 1) {
                // TEAM TOURNAMENT RANKING
                return $this->calculateTeamRankings();
            } else {
                // INDIVIDUAL TOURNAMENT RANKING
                return $this->calculateIndividualRankings();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to calculate tournament rankings: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate rankings for team tournaments
     * Teams are ranked, and all team members get the same rank
     */
    private function calculateTeamRankings()
    {
        // Get all teams with at least one participant that has a score
        $teamsWithScores = DB::table('tournament_teams')
            ->join('tournament_participants', 'tournament_teams.id', '=', 'tournament_participants.team_id')
            ->where('tournament_teams.tournament_id', $this->id)
            ->whereNotNull('tournament_participants.score')
            ->select('tournament_teams.id as team_id', 'tournament_teams.name as team_name')
            ->groupBy('tournament_teams.id', 'tournament_teams.name')
            ->get();

        if ($teamsWithScores->isEmpty()) {
            DB::rollBack();
            return false;
        }

        // Get team scores (should be identical for all team members)
        $teamRankings = [];
        foreach ($teamsWithScores as $team) {
            $teamScore = $this->participants()
                           ->where('team_id', $team->team_id)
                           ->whereNotNull('score')
                           ->first();
            
            if ($teamScore) {
                $teamRankings[] = [
                    'team_id' => $team->team_id,
                    'team_name' => $team->team_name,
                    'score' => $teamScore->score,
                    'created_at' => $teamScore->created_at // For tiebreaker
                ];
            }
        }

        // Sort teams by score (highest first), then by creation time (earlier first) for tiebreaker
        usort($teamRankings, function($a, $b) {
            if ($a['score'] == $b['score']) {
                return $a['created_at'] <=> $b['created_at']; // Earlier team wins tiebreaker
            }
            return $b['score'] <=> $a['score']; // Higher score wins
        });

        // Assign ranks to teams and their members
        $currentRank = 1;
        $previousScore = null;

        foreach ($teamRankings as $index => $teamData) {
            // Handle ties - if score is different from previous, update rank
            if ($previousScore !== null && $teamData['score'] < $previousScore) {
                $currentRank = $index + 1;
            }

            // Calculate points based on team rank
            $pointsForRank = $this->getPointsForRank($currentRank);

            // Get all team members
            $teamMembers = $this->participants()->where('team_id', $teamData['team_id'])->get();

            // Update all team members with the same rank and points
            foreach ($teamMembers as $member) {
                $member->update([
                    'tournament_rank' => $currentRank,
                    'points_awarded' => $pointsForRank,      // Regular points for leaderboard
                    'ue_points_awarded' => $pointsForRank,   // UEPoints for retakes (same amount)
                    'ranking_calculated' => true
                ]);

                // Award both point types to user
                $member->user->addPoints($pointsForRank);      // For leaderboard ranking
                $member->user->addUEPoints($pointsForRank);    // For quiz/challenge retakes
            }

            $previousScore = $teamData['score'];

            \Log::info("Team tournament ranking: Team '{$teamData['team_name']}' (ID: {$teamData['team_id']}) ranked #{$currentRank} with score {$teamData['score']} - {$teamMembers->count()} members awarded {$pointsForRank} points each");
        }

        DB::commit();
        \Log::info("Team tournament rankings calculated successfully for tournament ID: {$this->id}");
        return true;
    }

    /**
     * Calculate rankings for individual tournaments
     * Each participant is ranked individually
     */
    private function calculateIndividualRankings()
    {
        // Get all participants with scores, ordered by score (highest first)
        $participants = $this->participants()
                          ->whereNotNull('score')
                          ->orderBy('score', 'desc')
                          ->orderBy('created_at', 'asc') // Tiebreaker: earlier registration
                          ->get();

        if ($participants->isEmpty()) {
            DB::rollBack();
            return false;
        }

        $currentRank = 1;
        $previousScore = null;

        foreach ($participants as $index => $participant) {
            // Handle ties - if score is different from previous, update rank
            if ($previousScore !== null && $participant->score < $previousScore) {
                $currentRank = $index + 1;
            }

            // Calculate points based on individual rank
            $pointsForRank = $this->getPointsForRank($currentRank);

            // Update participant with rank and points
            $participant->update([
                'tournament_rank' => $currentRank,
                'points_awarded' => $pointsForRank,      // Regular points for leaderboard
                'ue_points_awarded' => $pointsForRank,   // UEPoints for retakes (same amount)
                'ranking_calculated' => true
            ]);

            // Award both point types to user
            $participant->user->addPoints($pointsForRank);      // For leaderboard ranking
            $participant->user->addUEPoints($pointsForRank);    // For quiz/challenge retakes

            $previousScore = $participant->score;

            \Log::info("Individual tournament ranking: Participant '{$participant->user->name}' (ID: {$participant->id}) ranked #{$currentRank} with score {$participant->score} - awarded {$pointsForRank} points");
        }

        DB::commit();
        \Log::info("Individual tournament rankings calculated successfully for tournament ID: {$this->id}");
        return true;
    }

    /**
     * Get points award amount based on rank (unified for both regular points and UEPoints)
     */
    private function getPointsForRank($rank)
    {
        switch ($rank) {
            case 1:
                return 50; // First place: 50 points + 50 UEPoints
            case 2:
                return 30; // Second place: 30 points + 30 UEPoints
            case 3:
                return 20; // Third place: 20 points + 20 UEPoints
            default:
                return 5; // All others: 5 points + 5 UEPoints
        }
    }

    /**
     * Get the top 3 participants with their ranks
     * UPDATED: Works correctly for both team and individual tournaments
     */
    public function getTopThreeParticipants()
    {
        if ($this->team_size > 1) {
            // For team tournaments, get one representative from each of the top 3 teams
            return $this->participants()
                       ->whereNotNull('tournament_rank')
                       ->where('tournament_rank', '<=', 3)
                       ->with('user', 'team')
                       ->get()
                       ->groupBy('tournament_rank')
                       ->map(function($participants) {
                           // Return the team leader as representative, or first member if no leader specified
                           return $participants->sortBy(function($participant) {
                               return $participant->role === 'leader' ? 0 : 1;
                           })->first();
                       })
                       ->sortBy('tournament_rank')
                       ->values();
        } else {
            // For individual tournaments, get top 3 individuals
            return $this->participants()
                       ->whereNotNull('tournament_rank')
                       ->where('tournament_rank', '<=', 3)
                       ->orderBy('tournament_rank')
                       ->with('user')
                       ->get();
        }
    }

    /**
     * Get all ranked participants ordered by rank
     * UPDATED: Works correctly for both team and individual tournaments
     */
    public function getRankedParticipants()
    {
        return $this->participants()
                   ->whereNotNull('tournament_rank')
                   ->orderBy('tournament_rank')
                   ->orderBy('score', 'desc')
                   ->with('user', 'team')
                   ->get();
    }
}