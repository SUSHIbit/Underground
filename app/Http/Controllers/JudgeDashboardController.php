<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentParticipant;
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
    
        /**
     * Display the tournament's details and submissions.
     */
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
        
        // Get all participants with their submissions
        $participants = $tournament->participants()
                                 ->with('user')
                                 ->orderBy('created_at')
                                 ->get();
        
        // Get counts for different submission statuses
        $totalParticipants = $participants->count();
        $submittedCount = $participants->where('submission_url', '!=', null)->count();
        $gradedCount = $participants->where('score', '!=', null)->count();
        
        return view('judge.tournament', compact(
            'tournament', 
            'participants', 
            'totalParticipants', 
            'submittedCount', 
            'gradedCount',
            'canJudge',
            'waitingPeriodEnd',
            'judgingDate'
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
        
        // Load related user data and rubric scores
        $participant->load('user', 'team.participants.user');
        
        // Get existing rubric scores if available
        $rubricScores = [];
        $existingScores = $participant->rubricScores()->get();
        
        foreach ($existingScores as $score) {
            $rubricScores[$score->tournament_rubric_id] = $score->score;
        }
        
        return view('judge.submission', compact('tournament', 'participant', 'rubricScores'));
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
                'feedback' => 'required|string|max:1000',
                'rubric_scores' => 'required|array',
            ], $rubricRules));
        } else {
            // Simple validation without rubrics
            $validated = $request->validate([
                'score' => 'required|numeric|min:0|max:10',
                'feedback' => 'required|string|max:1000',
            ]);
        }
        
        try {
            DB::beginTransaction();
            
            // Update the participant's score and feedback
            $participant->update([
                'score' => $validated['score'],
                'feedback' => $validated['feedback'],
            ]);
            
            // Save individual rubric scores if they exist
            if (isset($validated['rubric_scores']) && is_array($validated['rubric_scores'])) {
                // Remove old scores first
                $participant->rubricScores()->delete();
                
                // Add new scores
                foreach ($validated['rubric_scores'] as $rubricId => $score) {
                    $participant->rubricScores()->create([
                        'tournament_rubric_id' => $rubricId,
                        'score' => $score,
                    ]);
                }
            }
            
            // Award points to the user based on their score
            // Calculate points based on score out of 10
            $pointsToAward = 0;
            if ($validated['score'] >= 9) {
                $pointsToAward = 20; // Excellent submission
            } elseif ($validated['score'] >= 7) {
                $pointsToAward = 15; // Great submission
            } elseif ($validated['score'] >= 5) {
                $pointsToAward = 10; // Good submission
            } elseif ($validated['score'] >= 3) {
                $pointsToAward = 5;  // Average submission
            } else {
                $pointsToAward = 2;  // Participation points
            }
            
            // Only award points if they haven't already been awarded
            if ($participant->points_awarded === null || $participant->points_awarded === 0) {
                $participant->user->addPoints($pointsToAward);
                $participant->update(['points_awarded' => $pointsToAward]);
                
                // For team tournaments, synchronize the score, feedback, and points for all team members
                if ($participant->team_id) {
                    $teamMembers = TournamentParticipant::where('team_id', $participant->team_id)
                                                    ->where('id', '!=', $participant->id)
                                                    ->get();
                    
                    foreach ($teamMembers as $member) {
                        // Update score and feedback
                        $member->update([
                            'score' => $validated['score'],
                            'feedback' => $validated['feedback'],
                        ]);
                        
                        // Award the same points
                        if ($member->points_awarded === null || $member->points_awarded === 0) {
                            $member->user->addPoints($pointsToAward);
                            $member->update(['points_awarded' => $pointsToAward]);
                        }
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('judge.tournament', $tournament)
                        ->with('success', 'Score and feedback submitted successfully.');
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
}