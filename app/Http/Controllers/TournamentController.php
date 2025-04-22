<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentParticipant;
use App\Models\TournamentTeam;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TournamentController extends Controller
{
    /**
     * Display a listing of the tournaments.
     */
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
        
        // Get all approved tournaments (published only)
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
    
    /**
     * Display the specified tournament.
     */
    public function show(Tournament $tournament)
    {
        // Load relationships
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
    
    /**
     * Register for a tournament (solo tournaments only).
     */
    public function join(Request $request, Tournament $tournament)
    {
        $user = auth()->user();
        
        // Check if tournament has already ended
        if (Carbon::parse($tournament->date_time)->isPast()) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'This tournament has already ended.');
        }
        
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
        
        try {
            DB::beginTransaction();
            
            if ($tournament->team_size == 1) {
                // Solo tournament - just create participant record
                TournamentParticipant::create([
                    'tournament_id' => $tournament->id,
                    'user_id' => $user->id,
                    'team_id' => null,
                    'role' => 'member'
                ]);
            } else {
                // This route should not be used for team tournaments
                return redirect()->route('tournaments.show', $tournament)
                    ->with('error', 'This tournament requires a team. Please use the team formation form.');
            }
            
            DB::commit();
            
            return redirect()->route('tournaments.show', $tournament)
                ->with('success', 'You have successfully registered for the tournament.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Failed to register for the tournament: ' . $e->getMessage());
        }
    }
    
    /**
     * Submit a project for a tournament.
     */
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
                      ->first();
                      
        if (!$participant) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You are not registered for this tournament.');
        }
        
        // Validate request
        $validated = $request->validate([
            'submission_url' => 'required|url|max:255',
        ]);
        
        // Update submission
        $participant->update([
            'submission_url' => $validated['submission_url'],
        ]);
        
        // Redirect based on whether it's a team or solo tournament
        if ($tournament->team_size > 1 && $participant->team_id) {
            return redirect()->route('tournaments.team', $tournament)
                           ->with('success', 'Your project has been submitted successfully.');
        } else {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('success', 'Your project has been submitted successfully.');
        }
    }
    
    /**
     * Display participants for a tournament.
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
    
    /**
     * Search for users by username for team invitations
     */
    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query) || strlen($query) < 3) {
            return response()->json([
                'users' => []
            ]);
        }
        
        $users = User::where('username', 'like', "%{$query}%")
                    ->where('id', '!=', auth()->id()) // Exclude current user
                    ->where('role', 'student') // Only search for students
                    ->select('id', 'username', 'name', 'profile_picture')
                    ->limit(5)
                    ->get();
        
        // Add rank to each user
        $users->transform(function ($user) {
            $user->rank = $user->getRank();
            return $user;
        });
        
        return response()->json([
            'users' => $users
        ]);
    }
    
    /**
     * Create team and send invitations for a tournament
     */
    public function createTeam(Request $request, Tournament $tournament)
    {
        $user = auth()->user();
        
        // Check if tournament has already ended
        if (Carbon::parse($tournament->date_time)->isPast()) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'This tournament has already ended.');
        }
        
        // Check eligibility
        if (!$tournament->isEligible($user)) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You do not meet the eligibility criteria for this tournament.');
        }
        
        // Check if already participating
        if ($user->isInTournamentTeam($tournament->id)) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You are already part of a team in this tournament.');
        }
        
        // Validate request
        $validated = $request->validate([
            'team_name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('tournament_teams', 'name')->where(function ($query) use ($tournament) {
                    return $query->where('tournament_id', $tournament->id);
                })
            ],
            'invited_user_ids' => [
                'required',
                'array',
                'size:' . ($tournament->team_size - 1) // Must match required team size
            ],
            'invited_user_ids.*' => 'exists:users,id'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create the team
            $team = TournamentTeam::create([
                'tournament_id' => $tournament->id,
                'name' => $validated['team_name'],
                'leader_id' => $user->id
            ]);
            
            // Create participant record for team leader
            TournamentParticipant::create([
                'tournament_id' => $tournament->id,
                'user_id' => $user->id,
                'team_id' => $team->id,
                'role' => 'leader'
            ]);
            
            // Create invitations for team members
            foreach ($validated['invited_user_ids'] as $userId) {
                TeamInvitation::createWithExpiry($team->id, $userId);
                
                // Here you would send notifications to invited users
                // This would use Laravel's notification system
                // User::find($userId)->notify(new TeamInvitationNotification($team));
            }
            
            DB::commit();
            
            return redirect()->route('tournaments.show', $tournament)
                           ->with('success', 'Team created and invitations sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'Failed to create team. ' . $e->getMessage());
        }
    }
    
    /**
     * Display user's pending team invitations
     */
    public function invitations()
    {
        $user = auth()->user();
        
        $pendingInvitations = $user->pendingTeamInvitations()
                                  ->with(['team.tournament', 'team.leader'])
                                  ->get();
        
        return view('tournaments.invitations', compact('pendingInvitations'));
    }
    
    /**
     * Accept a team invitation
     */
    public function acceptInvitation(Request $request, TeamInvitation $invitation)
    {
        $user = auth()->user();
        
        // Make sure the invitation belongs to this user
        if ($invitation->user_id !== $user->id) {
            return redirect()->route('tournaments.invitations')
                           ->with('error', 'Invalid invitation.');
        }
        
        // Accept the invitation
        $result = $invitation->accept();
        
        if ($result) {
            return redirect()->route('tournaments.invitations')
                           ->with('success', 'You have successfully joined the team.');
        } else {
            return redirect()->route('tournaments.invitations')
                           ->with('error', 'Unable to accept invitation. It may have expired or the team is full.');
        }
    }
    
    /**
     * Decline a team invitation
     */
    public function declineInvitation(Request $request, TeamInvitation $invitation)
    {
        $user = auth()->user();
        
        // Make sure the invitation belongs to this user
        if ($invitation->user_id !== $user->id) {
            return redirect()->route('tournaments.invitations')
                           ->with('error', 'Invalid invitation.');
        }
        
        // Decline the invitation
        $result = $invitation->decline();
        
        if ($result) {
            return redirect()->route('tournaments.invitations')
                           ->with('success', 'Invitation declined successfully.');
        } else {
            return redirect()->route('tournaments.invitations')
                           ->with('error', 'Unable to decline invitation. It may have already expired.');
        }
    }
    
    /**
     * View team details for a tournament
     */
    public function team(Tournament $tournament)
    {
        $user = auth()->user();
        
        // Find the user's team for this tournament
        $participant = TournamentParticipant::where('tournament_id', $tournament->id)
                                         ->where('user_id', $user->id)
                                         ->whereNotNull('team_id')
                                         ->with('team.leader', 'team.participants.user')
                                         ->first();
        
        if (!$participant || !$participant->team) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You are not part of a team in this tournament.');
        }
        
        $team = $participant->team;
        $isLeader = $team->leader_id === $user->id;
        $pendingInvitations = $isLeader ? $team->pendingInvitations()->with('user')->get() : null;
        
        return view('tournaments.team', compact('tournament', 'team', 'isLeader', 'pendingInvitations'));
    }
}