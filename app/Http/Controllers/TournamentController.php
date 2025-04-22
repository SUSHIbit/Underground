<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentParticipant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TournamentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $participatingTournaments = $user->tournamentParticipants()->pluck('tournament_id')->toArray();
        
        // Define tournament types with display names for the view
        $tournamentTypes = [
            'web_design' => 'Web Design',
            'hackathon' => 'Hackathon',
            'coup_detat' => 'Coup d\'État',
            'coding_competition' => 'Coding Competition',
            'mobile' => 'Mobile Development'
        ];
        
        // Get all approved tournaments - FIXED to only show status='approved' (published)
        // not 'approved_unpublished' which are still waiting for lecturer to publish
        $allTournaments = Tournament::where('status', 'approved')
                          ->with(['judges'])
                          ->latest('date_time')
                          ->get();
        
        // Current date for comparison
        $now = Carbon::now();
        
        // Separate tournaments into upcoming and completed
        $upcomingTournaments = $allTournaments->filter(function($tournament) use ($now) {
            return Carbon::parse($tournament->date_time)->greaterThan($now);
        });
        
        $completedTournaments = $allTournaments->filter(function($tournament) use ($now) {
            return Carbon::parse($tournament->date_time)->lessThan($now);
        });
        
        // Group upcoming tournaments by type
        $upcomingGrouped = [];
        foreach ($tournamentTypes as $type => $displayName) {
            $tournamentsOfType = $upcomingTournaments->where('tournament_type', $type);
            if ($tournamentsOfType->count() > 0) {
                $upcomingGrouped[$type] = [
                    'display_name' => $displayName,
                    'tournaments' => $tournamentsOfType
                ];
            }
        }
        
        // Handle "other" upcoming tournaments
        $otherUpcoming = $upcomingTournaments->filter(function($tournament) use ($tournamentTypes) {
            return !array_key_exists($tournament->tournament_type, $tournamentTypes);
        });
        
        if ($otherUpcoming->count() > 0) {
            $upcomingGrouped['other'] = [
                'display_name' => 'Other Tournaments',
                'tournaments' => $otherUpcoming
            ];
        }
        
        // Group completed tournaments by type
        $completedGrouped = [];
        foreach ($tournamentTypes as $type => $displayName) {
            $tournamentsOfType = $completedTournaments->where('tournament_type', $type);
            if ($tournamentsOfType->count() > 0) {
                $completedGrouped[$type] = [
                    'display_name' => $displayName,
                    'tournaments' => $tournamentsOfType
                ];
            }
        }
        
        // Handle "other" completed tournaments
        $otherCompleted = $completedTournaments->filter(function($tournament) use ($tournamentTypes) {
            return !array_key_exists($tournament->tournament_type, $tournamentTypes);
        });
        
        if ($otherCompleted->count() > 0) {
            $completedGrouped['other'] = [
                'display_name' => 'Other Tournaments',
                'tournaments' => $otherCompleted
            ];
        }
        
        // Check eligibility for each tournament
        foreach ($allTournaments as $tournament) {
            $tournament->canParticipate = $tournament->isEligible($user);
            $tournament->isParticipating = in_array($tournament->id, $participatingTournaments);
        }
        
        return view('tournaments.index', compact('upcomingGrouped', 'completedGrouped'));
    }
    
    public function show(Tournament $tournament)
    {
        // Update this to use tournament_judge_users instead of tournament_judges
        $tournament->load(['judges', 'creator']);
        $user = auth()->user();
        
        // Check if the user is already participating
        $participant = TournamentParticipant::where('tournament_id', $tournament->id)
                      ->where('user_id', $user->id)
                      ->first();
                          
        $isParticipating = !is_null($participant);
           
        // Check eligibility
        $canParticipate = $tournament->isEligible($user);
        
        // Check if tournament has ended
        $hasEnded = Carbon::parse($tournament->date_time)->isPast();
        
        // Check if submission deadline has passed
        $deadlinePassed = Carbon::parse($tournament->deadline)->isPast();
        
        return view('tournaments.show', compact(
            'tournament', 
            'isParticipating', 
            'canParticipate', 
            'participant', 
            'hasEnded',
            'deadlinePassed'
        ));
    }
    
    public function join(Request $request, Tournament $tournament)
    {
        $user = auth()->user();
        
        // Check if tournament has already ended
        if (Carbon::parse($tournament->date_time)->isPast()) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'This tournament has already ended.');
        }
        
        // Validate request
        $validated = $request->validate([
            'team_name' => 'required_if:team_size,>,1|nullable|string|max:255',
            'team_members' => 'required_if:team_size,>,1|nullable|array',
            'team_members.*' => 'nullable|string|max:255',
        ]);
        
        // Check eligibility
        if (!$tournament->isEligible($user)) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You do not meet the eligibility criteria for this tournament.');
        }
        
        // Check if already participating
        $existing = TournamentParticipant::where('tournament_id', $tournament->id)
                   ->where('user_id', $user->id)
                   ->first();
                   
        if ($existing) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You are already participating in this tournament.');
        }
        
        // Create participation record
        TournamentParticipant::create([
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
            'team_name' => $validated['team_name'] ?? null,
            'team_members' => $validated['team_members'] ?? null,
        ]);
        
        return redirect()->route('tournaments.show', $tournament)
                       ->with('success', 'You have successfully joined the tournament.');
    }
    
    public function submit(Request $request, Tournament $tournament)
    {
        $user = auth()->user();
        
        // Check if submission deadline has passed
        if (Carbon::parse($tournament->deadline)->isPast()) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'The submission deadline for this tournament has passed.');
        }
        
        // Find participant record
        $participant = TournamentParticipant::where('tournament_id', $tournament->id)
                      ->where('user_id', $user->id)
                      ->firstOrFail();
        
        // Validate request
        $validated = $request->validate([
            'submission_url' => 'required|url|max:255',
        ]);
        
        // Update submission
        $participant->update([
            'submission_url' => $validated['submission_url'],
        ]);
        
        return redirect()->route('tournaments.show', $tournament)
                       ->with('success', 'Your project has been submitted successfully.');
    }

    public function updateTeam(Request $request, Tournament $tournament)
    {
        $user = auth()->user();
        
        // Check if tournament has already ended
        if (Carbon::parse($tournament->date_time)->isPast()) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'This tournament has already ended. Team cannot be updated.');
        }
        
        // Find participant record
        $participant = TournamentParticipant::where('tournament_id', $tournament->id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
        
        // Validate request
        $validated = $request->validate([
            'team_name' => 'required_if:team_size,>,1|nullable|string|max:255',
            'team_members' => 'required_if:team_size,>,1|nullable|array',
            'team_members.*' => 'nullable|string|max:255',
        ]);
        
        // Update team information
        $participant->update([
            'team_name' => $validated['team_name'] ?? null,
            'team_members' => $validated['team_members'] ?? null,
        ]);
        
        return redirect()->route('tournaments.show', $tournament)
                    ->with('success', 'Team information updated successfully.');
    }

    /**
     * Display all participants for a tournament.
     *
     * @param  \App\Models\Tournament  $tournament
     * @return \Illuminate\View\View
     */
    public function participants(Tournament $tournament)
    {
        // Load all participants with their users
        $participants = $tournament->participants()
                    ->with('user')
                    ->get();
        
        // Get the current user's participant record if they're participating
        $userParticipant = $participants->where('user_id', auth()->id())->first();
        
        // Determine if tournament has ended
        $hasEnded = Carbon::parse($tournament->date_time)->isPast();
        
        return view('tournaments.participants', compact(
            'tournament', 
            'participants', 
            'userParticipant', 
            'hasEnded'
        ));
    }
}