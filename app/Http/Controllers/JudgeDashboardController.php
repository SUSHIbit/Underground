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
        
        // Check if tournament has ended and waiting period has passed
        $waitingPeriodDays = 2; // Define a 2-day waiting period
        $tournamentDate = Carbon::parse($tournament->date_time);
        $waitingPeriodEnd = $tournamentDate->copy()->addDays($waitingPeriodDays);
        $canJudge = now()->greaterThanOrEqualTo($waitingPeriodEnd);
        
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
            'waitingPeriodEnd'
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
        
        // Check if tournament has ended and waiting period has passed
        $waitingPeriodDays = 2; // Define a 2-day waiting period
        $tournamentDate = Carbon::parse($tournament->date_time);
        $waitingPeriodEnd = $tournamentDate->copy()->addDays($waitingPeriodDays);
        $canJudge = now()->greaterThanOrEqualTo($waitingPeriodEnd);
        
        if (!$canJudge) {
            return redirect()->route('judge.tournament', $tournament)
                           ->with('error', 'Judging is not yet available for this tournament. The waiting period has not ended.');
        }
        
        // Make sure the participant belongs to this tournament
        if ($participant->tournament_id !== $tournament->id) {
            return redirect()->route('judge.tournament', $tournament)->with('error', 'Invalid participant for this tournament.');
        }
        
        // Load related user data
        $participant->load('user');
        
        return view('judge.submission', compact('tournament', 'participant'));
    }
    
    /**
     * Submit score for a tournament participant.
     * Modified to synchronize scores for team members.
     */
    public function submitScore(Request $request, Tournament $tournament, TournamentParticipant $participant)
    {
        // Make sure the authenticated user is a judge for this tournament
        if (!$this->isJudgeForTournament($tournament)) {
            return redirect()->route('judge.dashboard')->with('error', 'You are not assigned as a judge for this tournament.');
        }
        
        // Check if tournament has ended and waiting period has passed
        $waitingPeriodDays = 2; // Define a 2-day waiting period
        $tournamentDate = Carbon::parse($tournament->date_time);
        $waitingPeriodEnd = $tournamentDate->copy()->addDays($waitingPeriodDays);
        $canJudge = now()->greaterThanOrEqualTo($waitingPeriodEnd);
        
        if (!$canJudge) {
            return redirect()->route('judge.tournament', $tournament)
                        ->with('error', 'Judging is not yet available for this tournament. The waiting period has not ended.');
        }
        
        // Make sure the participant belongs to this tournament
        if ($participant->tournament_id !== $tournament->id) {
            return redirect()->route('judge.tournament', $tournament)->with('error', 'Invalid participant for this tournament.');
        }
        
        // Validate the request
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:10',
            'feedback' => 'required|string|max:1000',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update the participant's score and feedback
            $participant->update([
                'score' => $validated['score'],
                'feedback' => $validated['feedback'],
            ]);
            
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