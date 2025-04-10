<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentParticipant;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        // Update this query to use tournament_judge_users instead of tournament_judges
        $tournaments = Tournament::where('status', 'approved')
                      ->with(['judges']) // This relationship needs to be updated in the Tournament model
                      ->latest('date_time')
                      ->get();
                      
        $user = auth()->user();
        $participatingTournaments = $user->tournamentParticipants()->pluck('tournament_id')->toArray();
        
        // Check eligibility for each tournament
        foreach ($tournaments as $tournament) {
            $tournament->canParticipate = $tournament->isEligible($user);
            $tournament->isParticipating = in_array($tournament->id, $participatingTournaments);
        }
        
        return view('tournaments.index', compact('tournaments'));
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
        
        return view('tournaments.show', compact('tournament', 'isParticipating', 'canParticipate', 'participant'));
    }
    
    public function join(Request $request, Tournament $tournament)
    {
        $user = auth()->user();
        
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

    // Add this method to TournamentController.php:
    public function updateTeam(Request $request, Tournament $tournament)
    {
        $user = auth()->user();
        
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
}