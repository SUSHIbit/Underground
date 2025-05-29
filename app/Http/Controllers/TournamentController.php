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
        $tournament->load(['judges', 'creator', 'rubrics']);
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
                
                // Award 2 UEPoints for successfully joining the tournament
                $user->addUEPoints(2);
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
        
        // Check if user is a team member
        if ($tournament->team_size > 1) {
            if (!$participant->team_id) {
                return redirect()->route('tournaments.show', $tournament)
                              ->with('error', 'You need to be part of a team to submit for this tournament.');
            }
            
            // For team tournaments, only the leader can submit
            $team = TournamentTeam::find($participant->team_id);
            if ($team->leader_id !== $user->id) {
                return redirect()->route('tournaments.team', $tournament)
                              ->with('error', 'Only the team leader can submit the project.');
            }
            
            // Make sure team is complete
            $teamMemberCount = TournamentParticipant::where('team_id', $team->id)->count();
            if ($teamMemberCount < $tournament->team_size) {
                return redirect()->route('tournaments.team', $tournament)
                              ->with('error', 'Your team must have all ' . $tournament->team_size . ' members before submitting.');
            }
        }
        
        // Validate request
        $validated = $request->validate([
            'submission_url' => 'required|url|max:255',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update submission for the participant
            $participant->update([
                'submission_url' => $validated['submission_url'],
            ]);
            
            // For team tournaments, synchronize the URL across all team members
            if ($tournament->team_size > 1 && $participant->team_id) {
                TournamentParticipant::where('team_id', $participant->team_id)
                                   ->where('id', '!=', $participant->id)
                                   ->update([
                                       'submission_url' => $validated['submission_url']
                                   ]);
            }
            
            DB::commit();
            
            // Redirect based on whether it's a team or solo tournament
            if ($tournament->team_size > 1 && $participant->team_id) {
                return redirect()->route('tournaments.team', $tournament)
                              ->with('success', 'Your team\'s project has been submitted successfully.');
            } else {
                return redirect()->route('tournaments.show', $tournament)
                              ->with('success', 'Your project has been submitted successfully.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
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
     * Show the form for creating a new team with user selection.
     */
    public function createTeamForm(Tournament $tournament, Request $request)
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
        $existingParticipation = TournamentParticipant::where('tournament_id', $tournament->id)
                                                    ->where('user_id', $user->id)
                                                    ->first();
                                                    
        if ($existingParticipation) {
            if ($existingParticipation->team_id) {
                return redirect()->route('tournaments.team', $tournament)
                            ->with('error', 'You are already part of a team in this tournament.');
            } else {
                return redirect()->route('tournaments.show', $tournament)
                            ->with('error', 'You are already registered for this tournament as an individual participant.');
            }
        }
        
        // Get search parameters
        $searchQuery = $request->input('search', '');
        $selectedUserIds = $request->session()->get('selected_team_members', []);
        
        // Handle adding/removing users from selection
        if ($request->has('add_user_id')) {
            $userIdToAdd = $request->input('add_user_id');
            if (count($selectedUserIds) < ($tournament->team_size - 1)) {
                if (!in_array($userIdToAdd, $selectedUserIds)) {
                    $selectedUserIds[] = $userIdToAdd;
                    $request->session()->put('selected_team_members', $selectedUserIds);
                }
            }
        }
        
        if ($request->has('remove_user_id')) {
            $userIdToRemove = $request->input('remove_user_id');
            $selectedUserIds = array_diff($selectedUserIds, [$userIdToRemove]);
            $request->session()->put('selected_team_members', $selectedUserIds);
        }
        
        if ($request->has('clear_selection')) {
            $selectedUserIds = [];
            $request->session()->put('selected_team_members', $selectedUserIds);
        }
        
        // Get eligible users
        $eligibleUsersQuery = User::where('id', '!=', $user->id)
            ->where('role', 'student')
            ->whereNotIn('id', $selectedUserIds)
            ->whereNotIn('id', function($query) use ($tournament) {
                $query->select('user_id')
                      ->from('tournament_participants')
                      ->where('tournament_id', $tournament->id)
                      ->whereNotNull('team_id');
            });
        
        if (!empty($searchQuery)) {
            $eligibleUsersQuery->where(function($query) use ($searchQuery) {
                $query->where('username', 'like', "%{$searchQuery}%")
                    ->orWhere('name', 'like', "%{$searchQuery}%");
            });
        }
        
        $eligibleUsers = $eligibleUsersQuery->limit(5)->get()
            ->filter(function($potentialMember) use ($tournament) {
                return $tournament->isEligible($potentialMember);
            });
        
        // Get selected users' details
        $selectedUsers = [];
        if (!empty($selectedUserIds)) {
            $selectedUsers = User::whereIn('id', $selectedUserIds)->get();
        }
        
        return view('tournaments.create-team', compact(
            'tournament', 
            'eligibleUsers', 
            'searchQuery', 
            'selectedUsers',
            'selectedUserIds'
        ));
    }

    /**
     * Create team and add selected members.
     */
    public function createTeam(Request $request, Tournament $tournament)
    {
        $user = auth()->user();
        
        // Basic checks
        if (Carbon::parse($tournament->date_time)->isPast()) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'This tournament has already ended.');
        }
        
        if (!$tournament->isEligible($user)) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You do not meet the eligibility criteria for this tournament.');
        }
        
        if ($user->isInTournamentTeam($tournament->id)) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You are already part of a team in this tournament.');
        }
        
        // Check if this is adding members to existing team or creating new team
        if ($request->has('user_ids')) {
            // This is adding members to existing team
            return $this->addTeamMembers($request, $tournament);
        }
        
        // This is creating a new team
        // Get selected team members from session
        $selectedUserIds = $request->session()->get('selected_team_members', []);
        
        // Validate request
        $validated = $request->validate([
            'team_name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('tournament_teams', 'name')->where(function ($query) use ($tournament) {
                    return $query->where('tournament_id', $tournament->id);
                })
            ]
        ]);
        
        // Validate team member count
        if (count($selectedUserIds) != ($tournament->team_size - 1)) {
            return redirect()->route('tournaments.create-team-form', $tournament)
                ->with('error', 'You need to select exactly ' . ($tournament->team_size - 1) . ' team members.');
        }
        
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
            
            // Create participant records for selected team members
            foreach ($selectedUserIds as $memberId) {
                TournamentParticipant::create([
                    'tournament_id' => $tournament->id,
                    'user_id' => $memberId,
                    'team_id' => $team->id,
                    'role' => 'member'
                ]);
            }
            
            DB::commit();
            
            // Clear session data
            $request->session()->forget('selected_team_members');
            
            return redirect()->route('tournaments.team', $tournament)
                           ->with('success', 'Team created successfully with all selected members.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'Failed to create team. ' . $e->getMessage());
        }
    }

    /**
     * Add members to existing team.
     */
    private function addTeamMembers(Request $request, Tournament $tournament)
    {
        $user = auth()->user();
        
        // Get the user's team for this tournament
        $participant = TournamentParticipant::where('tournament_id', $tournament->id)
                                          ->where('user_id', $user->id)
                                          ->whereNotNull('team_id')
                                          ->with('team')
                                          ->first();
        
        if (!$participant || !$participant->team) {
            return redirect()->route('tournaments.show', $tournament)
                        ->with('error', 'You are not part of a team in this tournament.');
        }
        
        $team = $participant->team;
        
        // Check if user is team leader
        if ($team->leader_id !== $user->id) {
            return redirect()->route('tournaments.team', $tournament)
                        ->with('error', 'Only team leaders can add members to the team.');
        }
        
        // Check team capacity
        $currentMemberCount = TournamentParticipant::where('team_id', $team->id)->count();
        if ($currentMemberCount >= $tournament->team_size) {
            return redirect()->route('tournaments.team', $tournament)
                        ->with('error', 'Team is already full.');
        }
        
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|exists:users,id',
        ]);
        
        $usersToAdd = User::whereIn('id', $validated['user_ids'])->get();
        $addedCount = 0;
        $errors = [];
        
        try {
            DB::beginTransaction();
            
            foreach ($usersToAdd as $userToAdd) {
                // Check if we've reached team capacity
                if ($currentMemberCount + $addedCount >= $tournament->team_size) {
                    $errors[] = "Team capacity reached. Could not add {$userToAdd->name}.";
                    break;
                }
                
                // Check if user is eligible for tournament
                if (!$tournament->isEligible($userToAdd)) {
                    $errors[] = "{$userToAdd->name} does not meet the tournament eligibility requirements.";
                    continue;
                }
                
                // Check if user is already in a team for this tournament
                $existingParticipation = TournamentParticipant::where('tournament_id', $tournament->id)
                                                            ->where('user_id', $userToAdd->id)
                                                            ->first();
                
                if ($existingParticipation) {
                    if ($existingParticipation->team_id) {
                        $errors[] = "{$userToAdd->name} is already part of another team.";
                    } else {
                        $errors[] = "{$userToAdd->name} is already registered as individual participant.";
                    }
                    continue;
                }
                
                // Add user to team
                TournamentParticipant::create([
                    'tournament_id' => $tournament->id,
                    'user_id' => $userToAdd->id,
                    'team_id' => $team->id,
                    'role' => 'member'
                ]);
                
                $addedCount++;
            }
            
            DB::commit();
            
            $message = '';
            if ($addedCount > 0) {
                $message = "Successfully added {$addedCount} member(s) to the team.";
            }
            
            if (!empty($errors)) {
                $message .= ' However, some users could not be added: ' . implode(' ', $errors);
            }
            
            if ($addedCount === 0 && !empty($errors)) {
                return redirect()->route('tournaments.team', $tournament)
                            ->with('error', 'No users could be added. ' . implode(' ', $errors));
            }
            
            return redirect()->route('tournaments.team', $tournament)
                        ->with('success', $message);
                        
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tournaments.team', $tournament)
                        ->with('error', 'Failed to add team members: ' . $e->getMessage());
        }
    }
    
    /**
     * View team details for a tournament.
     */
    public function team(Tournament $tournament)
    {
        $user = Auth::user();
        
        // Find the user's team for this tournament
        $participant = TournamentParticipant::where('tournament_id', $tournament->id)
                                        ->where('user_id', $user->id)
                                        ->whereNotNull('team_id')
                                        ->with('team.leader')
                                        ->first();
        
        if (!$participant || !$participant->team) {
            $soloParticipant = TournamentParticipant::where('tournament_id', $tournament->id)
                                                  ->where('user_id', $user->id)
                                                  ->whereNull('team_id')
                                                  ->first();
            
            if ($soloParticipant) {
                return redirect()->route('tournaments.show', $tournament)
                            ->with('error', 'You are registered as an individual participant, not part of a team.');
            }
            
            return redirect()->route('tournaments.show', $tournament)
                        ->with('error', 'You are not part of a team in this tournament.');
        }
        
        $team = $participant->team;
        $isLeader = $team->leader_id === $user->id;
        
        // Get all team members
        $teamMembers = TournamentParticipant::where('team_id', $team->id)
                                        ->with('user')
                                        ->get();
        
        $isTeamComplete = $teamMembers->count() >= $tournament->team_size;
        
        // Format team members for display
        $allTeamMembers = collect();
        
        // Add the team leader first
        $leaderParticipant = $teamMembers->where('user_id', $team->leader_id)->first();
        if ($leaderParticipant) {
            $allTeamMembers->push([
                'user' => $leaderParticipant->user,
                'status' => 'member',
                'is_leader' => true,
                'is_current_user' => $leaderParticipant->user_id === $user->id,
                'participant_id' => $leaderParticipant->id
            ]);
        }
        
        // Add other members
        foreach ($teamMembers->where('user_id', '!=', $team->leader_id) as $member) {
            $allTeamMembers->push([
                'user' => $member->user,
                'status' => 'member',
                'is_leader' => false,
                'is_current_user' => $member->user_id === $user->id,
                'participant_id' => $member->id
            ]);
        }
        
        return view('tournaments.team', compact(
            'tournament', 
            'team', 
            'isLeader', 
            'allTeamMembers', 
            'isTeamComplete'
        ));
    }
    
    /**
     * Remove a member from a team (leader only).
     */
    public function removeMember(Request $request, Tournament $tournament, TournamentParticipant $participant)
    {
        $user = Auth::user();
        
        $userParticipant = TournamentParticipant::where('tournament_id', $tournament->id)
                                           ->where('user_id', $user->id)
                                           ->whereNotNull('team_id')
                                           ->first();
        
        if (!$userParticipant) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You are not part of a team in this tournament.');
        }
        
        $team = TournamentTeam::find($userParticipant->team_id);
        
        if ($team->leader_id !== $user->id) {
            return redirect()->route('tournaments.team', $tournament)
                           ->with('error', 'Only the team leader can remove members.');
        }
        
        if ($participant->user_id === $user->id) {
            return redirect()->route('tournaments.team', $tournament)
                           ->with('error', 'As a leader, you cannot remove yourself from the team.');
        }
        
        if ($participant->team_id !== $team->id) {
            return redirect()->route('tournaments.team', $tournament)
                           ->with('error', 'This member is not part of your team.');
        }
        
        if (Carbon::parse($tournament->date_time)->isPast()) {
            return redirect()->route('tournaments.team', $tournament)
                           ->with('error', 'You cannot modify the team after the tournament has started.');
        }
        
        $participant->delete();
        
        return redirect()->route('tournaments.team', $tournament)
                       ->with('success', 'Team member has been removed successfully.');
    }
    
    /**
     * Leave a team (member only).
     */
    public function leaveTeam(Request $request, Tournament $tournament)
    {
        $user = Auth::user();
        
        $participant = TournamentParticipant::where('tournament_id', $tournament->id)
                                      ->where('user_id', $user->id)
                                      ->whereNotNull('team_id')
                                      ->first();
        
        if (!$participant) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You are not part of a team in this tournament.');
        }
        
        $team = TournamentTeam::find($participant->team_id);
        
        if ($team->leader_id === $user->id) {
            return redirect()->route('tournaments.team', $tournament)
                           ->with('error', 'As the team leader, you cannot leave the team. You must either find a new leader or disband the team.');
        }
        
        if (Carbon::parse($tournament->date_time)->isPast()) {
            return redirect()->route('tournaments.team', $tournament)
                           ->with('error', 'You cannot leave the team after the tournament has started.');
        }
        
        $participant->delete();
        
        return redirect()->route('tournaments.show', $tournament)
                       ->with('success', 'You have successfully left the team.');
    }
    
    /**
     * Disband a team (leader only).
     */
    public function disbandTeam(Request $request, Tournament $tournament)
    {
        $user = Auth::user();
        
        $participant = TournamentParticipant::where('tournament_id', $tournament->id)
                                      ->where('user_id', $user->id)
                                      ->whereNotNull('team_id')
                                      ->first();
        
        if (!$participant) {
            return redirect()->route('tournaments.show', $tournament)
                           ->with('error', 'You are not part of a team in this tournament.');
        }
        
        $team = TournamentTeam::find($participant->team_id);
        
        if ($team->leader_id !== $user->id) {
            return redirect()->route('tournaments.team', $tournament)
                           ->with('error', 'Only the team leader can disband the team.');
        }
        
        if (Carbon::parse($tournament->date_time)->isPast()) {
            return redirect()->route('tournaments.team', $tournament)
                           ->with('error', 'You cannot disband the team after the tournament has started.');
        }
        
        try {
            DB::beginTransaction();
            
            TournamentParticipant::where('team_id', $team->id)->delete();
            $team->delete();
            
            DB::commit();
            
            return redirect()->route('tournaments.show', $tournament)
                           ->with('success', 'Team has been disbanded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('tournaments.team', $tournament)
                           ->with('error', 'Failed to disband team: ' . $e->getMessage());
        }
    }

    /**
     * View team results for a tournament.
     */
    public function teamResults(Tournament $tournament)
    {
        $user = Auth::user();
        
        $participant = TournamentParticipant::where('tournament_id', $tournament->id)
                                        ->where('user_id', $user->id)
                                        ->whereNotNull('team_id')
                                        ->with('team.leader')
                                        ->first();
        
        if (!$participant || !$participant->team) {
            return redirect()->route('tournaments.show', $tournament)
                        ->with('error', 'You are not part of a team in this tournament.');
        }
        
        $team = $participant->team;
        
        $teamMembers = TournamentParticipant::where('team_id', $team->id)
                                        ->with('user')
                                        ->get();
        
        return view('tournaments.team-results', compact(
            'tournament', 
            'team', 
            'teamMembers'
        ));
    }

    /**
     * Search for eligible users that can be added to a team.
     */
    public function searchEligibleUsers(Request $request, Tournament $tournament)
    {
        $user = Auth::user();
        $searchQuery = $request->input('search', '');
        
        if (strlen($searchQuery) < 2) {
            return response()->json(['users' => []]);
        }
        
        // Get the user's team for this tournament
        $participant = TournamentParticipant::where('tournament_id', $tournament->id)
                                          ->where('user_id', $user->id)
                                          ->whereNotNull('team_id')
                                          ->with('team')
                                          ->first();
        
        if (!$participant || !$participant->team) {
            return response()->json(['error' => 'You are not part of a team in this tournament.'], 403);
        }
        
        $team = $participant->team;
        
        // Check if user is team leader
        if ($team->leader_id !== $user->id) {
            return response()->json(['error' => 'Only team leaders can search for members.'], 403);
        }
        
        // Query for eligible student users
        $eligibleUsers = User::where('id', '!=', $user->id)
            ->where('role', 'student')
            ->where(function($query) use ($searchQuery) {
                $query->where('username', 'like', "%{$searchQuery}%")
                      ->orWhere('name', 'like', "%{$searchQuery}%");
            })
            ->whereNotIn('id', function($query) use ($tournament) {
                $query->select('user_id')
                      ->from('tournament_participants')
                      ->where('tournament_id', $tournament->id)
                      ->whereNotNull('team_id');
            })
            ->limit(10)
            ->get()
            ->filter(function($potentialMember) use ($tournament) {
                return $tournament->isEligible($potentialMember);
            })
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'rank' => $user->getRank()
                ];
            })
            ->values();
        
        return response()->json(['users' => $eligibleUsers]);
    }
}