<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentParticipant;
use App\Models\JudgeScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JudgeDashboardController extends Controller
{
    /**
     * Display the judge's dashboard with tournaments they are judging.
     */
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        
        // Get tournaments where the user is assigned as a judge
        $tournaments = $user->judgedTournaments()->get();
        
        // Empty collections for each category
        $readyToJudgeTournaments = collect();
        $waitingPeriodTournaments = collect();
        $upcomingTournaments = collect();
        
        // Process each tournament and place it in the correct category
        foreach ($tournaments as $tournament) {
            $judgingDate = Carbon::parse($tournament->judging_date);
            
            // If the judging date is in the past
            if ($now->greaterThanOrEqualTo($judgingDate)) {
                // It's either ready to judge or in waiting period
                $readyToJudgeTournaments->push($tournament);
            } 
            // If judging date is in the future but within 48 hours
            else if ($judgingDate->diffInHours($now) <= 48) {
                // It's in waiting period (coming up soon)
                $waitingPeriodTournaments->push($tournament);
            } 
            // If judging date is more than 48 hours in the future
            else {
                // It's an upcoming tournament
                $upcomingTournaments->push($tournament);
            }
        }
        
        return view('judge.dashboard', compact('readyToJudgeTournaments', 'waitingPeriodTournaments', 'upcomingTournaments'));
    }
    
    public function tournament(Tournament $tournament)
    {
        // Make sure the authenticated user is a judge for this tournament
        if (!$this->isJudgeForTournament($tournament)) {
            return redirect()->route('judge.dashboard')->with('error', 'You are not assigned as a judge for this tournament.');
        }
        
        // Get current time in Malaysia timezone
        $now = Carbon::now('Asia/Kuala_Lumpur');
        
        // Parse the judging date in Malaysia timezone
        $judgingDate = Carbon::parse($tournament->judging_date)->setTimezone('Asia/Kuala_Lumpur');
        
        // If the judging date has passed, judging should be available immediately
        $canJudge = $now->greaterThanOrEqualTo($judgingDate);
        
        // No waiting period needed if judging date has already passed
        $waitingPeriodEnd = $judgingDate;
        
        // Get all participants with their submissions and judge scores
        $participants = $tournament->participants()
                                ->with(['user', 'judgeScores.judge'])
                                ->orderBy('created_at')
                                ->get();
        
        // Get the current judge's user ID
        $currentJudgeId = auth()->id();
        
        // Add judge-specific information to participants
        $participants->each(function ($participant) use ($currentJudgeId) {
            // Get this judge's score for this participant
            $participant->currentJudgeScore = $participant->getJudgeScore($currentJudgeId);
            
            // Count how many judges have scored this participant
            $participant->judgeCount = $participant->judgeScores->count();
            
            // Get total number of judges for this tournament
            $participant->totalJudges = $participant->tournament->judges->count();
        });
        
        // Get counts for different submission statuses
        $totalParticipants = $participants->count();
        $submittedCount = $participants->where('submission_url', '!=', null)->count();
        
        // Count participants graded by current judge
        $gradedByCurrentJudgeCount = $participants->filter(function ($participant) {
            return $participant->currentJudgeScore !== null;
        })->count();
        
        // Count participants fully graded by all judges
        $fullyGradedCount = $participants->filter(function ($participant) {
            return $participant->judgeCount >= $participant->totalJudges;
        })->count();
        
        // Grading completion status
        $isCurrentJudgeComplete = $tournament->isJudgeGradingComplete($currentJudgeId);
        $canCompleteGrading = $tournament->canJudgeCompleteGrading($currentJudgeId) && !$isCurrentJudgeComplete;
        $completedJudgesCount = $tournament->getCompletedJudgesCount();
        $totalJudgesCount = $tournament->judges()->count();
        $isAllGradingComplete = $tournament->isGradingComplete();
        
        return view('judge.tournament', compact(
            'tournament', 
            'participants', 
            'totalParticipants', 
            'submittedCount', 
            'gradedByCurrentJudgeCount',
            'fullyGradedCount',
            'canJudge',
            'waitingPeriodEnd',
            'judgingDate',
            'isCurrentJudgeComplete',
            'canCompleteGrading',
            'completedJudgesCount',
            'totalJudgesCount',
            'isAllGradingComplete'
        ));
    }
    
    /**
     * Display submission details for grading.
     */
    public function submission(Tournament $tournament, TournamentParticipant $participant)
    {
        // Make sure the authenticated user is a judge for this tournament
        if (!$this->isJudgeForTournament($tournament)) {
            return redirect()->route('judge.dashboard')->with('error', 'You are not assigned as a judge for this tournament.');
        }
        
        // Get current time in Malaysia timezone
        $now = Carbon::now('Asia/Kuala_Lumpur');
        
        // Parse the judging date in Malaysia timezone
        $judgingDate = Carbon::parse($tournament->judging_date)->setTimezone('Asia/Kuala_Lumpur');
        
        // If the judging date has passed, judging should be available immediately
        $canJudge = $now->greaterThanOrEqualTo($judgingDate);
        
        if (!$canJudge) {
            return redirect()->route('judge.tournament', $tournament)
                        ->with('error', 'Judging is not yet available for this tournament. Please wait until the judging date.');
        }
        
        // Make sure the participant belongs to this tournament
        if ($participant->tournament_id !== $tournament->id) {
            return redirect()->route('judge.tournament', $tournament)->with('error', 'Invalid participant for this tournament.');
        }
        
        // Load related user data and get current judge's score
        $participant->load('user', 'team.participants.user');
        
        $currentJudgeId = auth()->id();
        $existingJudgeScore = $participant->getJudgeScore($currentJudgeId);
        
        // Get existing rubric scores from judge's individual scoring
        $rubricScores = [];
        if ($existingJudgeScore && $existingJudgeScore->rubric_scores) {
            $rubricScores = $existingJudgeScore->rubric_scores;
        }
        
        // Get all judge scores for this participant (for display)
        $allJudgeScores = $participant->judgeScores()->with('judge')->get();
        
        return view('judge.submission', compact(
            'tournament', 
            'participant', 
            'rubricScores', 
            'existingJudgeScore',
            'allJudgeScores'
        ));
    }
    
    /**
     * Submit score for a tournament participant.
     */
    public function submitScore(Request $request, Tournament $tournament, TournamentParticipant $participant)
    {
        // Make sure the authenticated user is a judge for this tournament
        if (!$this->isJudgeForTournament($tournament)) {
            return redirect()->route('judge.dashboard')->with('error', 'You are not assigned as a judge for this tournament.');
        }
        
        // Get current time in Malaysia timezone
        $now = Carbon::now('Asia/Kuala_Lumpur');
        
        // Parse the judging date in Malaysia timezone
        $judgingDate = Carbon::parse($tournament->judging_date)->setTimezone('Asia/Kuala_Lumpur');
        
        // If the judging date has passed, judging should be available immediately
        $canJudge = $now->greaterThanOrEqualTo($judgingDate);
        
        if (!$canJudge) {
            return redirect()->route('judge.tournament', $tournament)
                        ->with('error', 'Judging is not yet available for this tournament. Please wait until the judging date.');
        }
        
        // Make sure the participant belongs to this tournament
        if ($participant->tournament_id !== $tournament->id) {
            return redirect()->route('judge.tournament', $tournament)->with('error', 'Invalid participant for this tournament.');
        }
        
        // Validate the request based on whether rubrics exist
        if ($tournament->rubrics->count() > 0) {
            // Dynamic validation for rubric scores
            $rubricRules = [];
            foreach ($tournament->rubrics as $rubric) {
                $rubricRules["rubric_scores.{$rubric->id}"] = 'required|numeric|min:0|max:10';
            }
            
            $validated = $request->validate(array_merge([
                'score' => 'required|numeric|min:0|max:10',
                'feedback' => 'nullable|string|max:1000', // Made nullable
                'rubric_scores' => 'required|array',
            ], $rubricRules));
        } else {
            // Simple validation without rubrics
            $validated = $request->validate([
                'score' => 'required|numeric|min:0|max:10',
                'feedback' => 'nullable|string|max:1000', // Made nullable
            ]);
        }
        
        try {
            DB::beginTransaction();
            
            $currentJudgeId = auth()->id();
            
            // Create or update the judge's individual score for the main participant
            $judgeScore = JudgeScore::updateOrCreate(
                [
                    'tournament_participant_id' => $participant->id,
                    'judge_user_id' => $currentJudgeId,
                ],
                [
                    'score' => $validated['score'],
                    'feedback' => $validated['feedback'] ?? null,
                    'rubric_scores' => isset($validated['rubric_scores']) ? $validated['rubric_scores'] : null,
                ]
            );
            
            // **NEW: Team Scoring Synchronization**
            // If this is a team tournament and participant has a team, sync scores across all team members
            if ($tournament->team_size > 1 && $participant->team_id) {
                // Get all other team members (excluding the current participant)
                $teamMembers = TournamentParticipant::where('team_id', $participant->team_id)
                                                  ->where('id', '!=', $participant->id)
                                                  ->get();
                
                foreach ($teamMembers as $member) {
                    // Create/update judge score for each team member with identical scoring
                    JudgeScore::updateOrCreate(
                        [
                            'tournament_participant_id' => $member->id,
                            'judge_user_id' => $currentJudgeId,
                        ],
                        [
                            'score' => $validated['score'],
                            'feedback' => $validated['feedback'] ?? null,
                            'rubric_scores' => isset($validated['rubric_scores']) ? $validated['rubric_scores'] : null,
                        ]
                    );
                    
                    // Update the average score for each team member
                    $member->updateAverageScore();
                }
            }
            
            // Update the participant's average score (main participant)
            $participant->updateAverageScore();
            
            // Award points only when the FIRST judge grades (to avoid duplicate awards)
            $judgeScoresCount = $participant->judgeScores()->count();
            if ($judgeScoresCount == 1 && ($participant->points_awarded === null || $participant->points_awarded === 0)) {
                // Calculate points based on average score
                $pointsToAward = 0;
                $averageScore = $participant->score;
                
                if ($averageScore >= 9) {
                    $pointsToAward = 20; // Excellent submission
                } elseif ($averageScore >= 7) {
                    $pointsToAward = 15; // Great submission
                } elseif ($averageScore >= 5) {
                    $pointsToAward = 10; // Good submission
                } elseif ($averageScore >= 3) {
                    $pointsToAward = 5;  // Average submission
                } else {
                    $pointsToAward = 2;  // Participation points
                }
                
                $participant->user->addPoints($pointsToAward);
                $participant->update(['points_awarded' => $pointsToAward]);
                
                // For team tournaments, award the same points to all team members
                if ($tournament->team_size > 1 && $participant->team_id) {
                    $teamMembers = TournamentParticipant::where('team_id', $participant->team_id)
                                                    ->where('id', '!=', $participant->id)
                                                    ->get();
                    
                    foreach ($teamMembers as $member) {
                        // Update average score and feedback (already done above)
                        // Award the same points
                        if ($member->points_awarded === null || $member->points_awarded === 0) {
                            $member->user->addPoints($pointsToAward);
                            $member->update(['points_awarded' => $pointsToAward]);
                        }
                    }
                }
            } else {
                // If this is not the first judge, just update average for team members (already done above)
                // No additional point awarding needed
            }
            
            DB::commit();
            
            // Determine success message based on team vs individual
            $successMessage = 'Your score has been submitted successfully.';
            if ($tournament->team_size > 1 && $participant->team_id) {
                $successMessage = 'Your score has been submitted successfully for the entire team.';
            }
            
            return redirect()->route('judge.tournament', $tournament)
                        ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if the authenticated user is a judge for the given tournament.
     */
    private function isJudgeForTournament(Tournament $tournament)
    {
        $user = Auth::user();
        
        return $tournament->judges()->where('user_id', $user->id)->exists();
    }

    /**
 * Mark judge as done with grading for this tournament
 */
public function completeGrading(Tournament $tournament)
{
    $user = Auth::user();
    
    // Make sure the authenticated user is a judge for this tournament
    if (!$this->isJudgeForTournament($tournament)) {
        return redirect()->route('judge.dashboard')->with('error', 'You are not assigned as a judge for this tournament.');
    }
    
    // Check if judge can complete grading (has graded all submissions)
    if (!$tournament->canJudgeCompleteGrading($user->id)) {
        return redirect()->route('judge.tournament', $tournament)
                    ->with('error', 'You must grade all submitted participants before marking grading as complete.');
    }
    
    // Check if already completed
    if ($tournament->isJudgeGradingComplete($user->id)) {
        return redirect()->route('judge.tournament', $tournament)
                    ->with('info', 'You have already marked your grading as complete for this tournament.');
    }
    
    try {
        // Mark this judge as completed
        $tournament->markJudgeGradingComplete($user->id);
        
        $message = 'You have successfully marked your grading as complete for this tournament.';
        
        // Check if all judges are now done
        if ($tournament->isGradingComplete()) {
            $message .= ' All judges have now completed grading - participants can view their results.';
        } else {
            $remainingJudges = $tournament->judges()->count() - $tournament->getCompletedJudgesCount();
            $message .= " Waiting for {$remainingJudges} more judge(s) to complete grading.";
        }
        
        return redirect()->route('judge.tournament', $tournament)
                    ->with('success', $message);
        
    } catch (\Exception $e) {
        return redirect()->route('judge.tournament', $tournament)
                    ->with('error', 'Failed to mark grading as complete: ' . $e->getMessage());
    }
}
}