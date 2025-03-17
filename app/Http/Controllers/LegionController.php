<?php

namespace App\Http\Controllers;

use App\Models\Legion;
use App\Models\LegionMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LegionController extends Controller
{
    /**
     * Display a listing of legions.
     */
    public function index()
    {
        $user = auth()->user();
        $userLegion = $user->getCurrentLegion();
        
        // Get top legions by power
        $topLegions = Legion::withCount('acceptedMembers')
                        ->having('accepted_members_count', '>', 0)
                        ->get()
                        ->sortByDesc(function ($legion) {
                            return $legion->power;
                        })
                        ->take(10);
        
        return view('legions.index', compact('user', 'userLegion', 'topLegions'));
    }

    /**
     * Show the form for creating a new legion.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Check if user is already in a legion
        if ($user->isInLegion()) {
            return redirect()->route('legions.index')
                             ->with('error', 'You are already in a legion. Leave your current legion first to create a new one.');
        }
        
        return view('legions.create', compact('user'));
    }

    /**
     * Store a newly created legion in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check if user is already in a legion
        if ($user->isInLegion()) {
            return redirect()->route('legions.index')
                             ->with('error', 'You are already in a legion. Leave your current legion first to create a new one.');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:legions'],
            'description' => ['nullable', 'string', 'max:500'],
            'emblem' => ['nullable', 'image', 'max:2048'],
        ]);
        
        // Handle emblem upload
        if ($request->hasFile('emblem')) {
            $path = $request->file('emblem')->store('legion-emblems', 'public');
            $validated['emblem'] = $path;
        }
        
        // Create the legion
        $legion = Legion::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'emblem' => $validated['emblem'] ?? null,
            'leader_id' => $user->id,
        ]);
        
        // Add creator as leader
        LegionMember::create([
            'legion_id' => $legion->id,
            'user_id' => $user->id,
            'role' => 'leader',
            'is_accepted' => true,
            'joined_at' => now(),
        ]);
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'Legion created successfully!');
    }

    /**
     * Display the specified legion.
     */
    public function show(Legion $legion)
    {
        $legion->load(['members.user', 'leader']);
        
        $user = auth()->user();
        $isMember = $legion->members()->where('user_id', $user->id)->where('is_accepted', true)->exists();
        $isPending = $legion->members()->where('user_id', $user->id)->where('is_accepted', false)->exists();
        $membership = $legion->members()->where('user_id', $user->id)->first();
        
        return view('legions.show', compact('legion', 'user', 'isMember', 'isPending', 'membership'));
    }

    /**
     * Show the form for editing the specified legion.
     */
    public function edit(Legion $legion)
    {
        $user = auth()->user();
        
        // Check if user is leader or officer
        $membership = $legion->members()->where('user_id', $user->id)->first();
        
        if (!$membership || !($membership->isLeader() || $membership->isOfficer())) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'You do not have permission to edit this legion.');
        }
        
        return view('legions.edit', compact('legion', 'user', 'membership'));
    }

    /**
     * Update the specified legion in storage.
     */
    public function update(Request $request, Legion $legion)
    {
        $user = auth()->user();
        
        // Check if user is leader or officer
        $membership = $legion->members()->where('user_id', $user->id)->first();
        
        if (!$membership || !($membership->isLeader() || $membership->isOfficer())) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'You do not have permission to edit this legion.');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('legions')->ignore($legion->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'emblem' => ['nullable', 'image', 'max:2048'],
        ]);
        
        // Handle emblem upload
        if ($request->hasFile('emblem')) {
            // Delete old emblem if exists
            if ($legion->emblem) {
                Storage::disk('public')->delete($legion->emblem);
            }
            
            $path = $request->file('emblem')->store('legion-emblems', 'public');
            $validated['emblem'] = $path;
        }
        
        $legion->update($validated);
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'Legion updated successfully!');
    }

    /**
     * Apply to join a legion.
     */
    public function apply(Request $request, Legion $legion)
    {
        $user = auth()->user();
        
        // Check if user is already in a legion
        if ($user->isInLegion()) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'You are already in a legion. Leave your current legion first to join another.');
        }
        
        // Check if legion is full
        if ($legion->isLegionFull()) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'This legion is full. Please try another one.');
        }
        
        // Check if user already has a pending application
        if ($legion->members()->where('user_id', $user->id)->exists()) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'You have already applied or been invited to this legion.');
        }
        
        // Create the application
        LegionMember::create([
            'legion_id' => $legion->id,
            'user_id' => $user->id,
            'role' => 'member',
            'is_accepted' => false,
        ]);
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'Your application has been submitted successfully.');
    }

    /**
     * Invite a user to the legion.
     */
    public function invite(Request $request, Legion $legion)
    {
        $user = auth()->user();
        
        // Check if user is leader or officer
        $membership = $legion->members()->where('user_id', $user->id)->first();
        
        if (!$membership || !($membership->isLeader() || $membership->isOfficer())) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'You do not have permission to invite users to this legion.');
        }
        
        // Check if legion is full
        if ($legion->isLegionFull()) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'This legion is full. Cannot invite more members.');
        }
        
        $validated = $request->validate([
            'username' => ['required', 'string', 'exists:users,username'],
        ]);
        
        $invitedUser = User::where('username', $validated['username'])->first();
        
        // Check if user is already in a legion
        if ($invitedUser->isInLegion()) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'This user is already in a legion.');
        }
        
        // Check if user already has a pending invitation
        if ($legion->members()->where('user_id', $invitedUser->id)->exists()) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'This user already has a pending invitation or application.');
        }
        
        // Create the invitation
        LegionMember::create([
            'legion_id' => $legion->id,
            'user_id' => $invitedUser->id,
            'role' => 'member',
            'is_accepted' => false,
        ]);
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'Invitation sent successfully.');
    }

    /**
     * Accept an application to join the legion.
     */
    public function acceptApplication(Request $request, Legion $legion, User $user)
    {
        $currentUser = auth()->user();
        
        // Check if current user is leader or officer
        $membership = $legion->members()->where('user_id', $currentUser->id)->first();
        
        if (!$membership || !($membership->isLeader() || $membership->isOfficer())) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'You do not have permission to accept applications.');
        }
        
        // Check if legion is full
        if ($legion->isLegionFull()) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'The legion is full. Cannot accept more members.');
        }
        
        // Get the application
        $application = $legion->members()->where('user_id', $user->id)->where('is_accepted', false)->first();
        
        if (!$application) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Application not found or already processed.');
        }
        
        // Accept the application
        $application->update([
            'is_accepted' => true,
            'joined_at' => now(),
        ]);
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'Application accepted successfully.');
    }

    /**
     * Reject an application to join the legion.
     */
    public function rejectApplication(Request $request, Legion $legion, User $user)
    {
        $currentUser = auth()->user();
        
        // Check if current user is leader or officer
        $membership = $legion->members()->where('user_id', $currentUser->id)->first();
        
        if (!$membership || !($membership->isLeader() || $membership->isOfficer())) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'You do not have permission to reject applications.');
        }
        
        // Get the application
        $application = $legion->members()->where('user_id', $user->id)->where('is_accepted', false)->first();
        
        if (!$application) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Application not found or already processed.');
        }
        
        // Delete the application
        $application->delete();
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'Application rejected successfully.');
    }

    /**
     * Accept an invitation to join the legion.
     */
    public function acceptInvitation(Request $request, Legion $legion)
    {
        $user = auth()->user();
        
        // Check if user is already in a legion
        if ($user->isInLegion()) {
            return redirect()->route('legions.index')
                             ->with('error', 'You are already in a legion. Leave your current legion first to join another.');
        }
        
        // Get the invitation
        $invitation = $legion->members()->where('user_id', $user->id)->where('is_accepted', false)->first();
        
        if (!$invitation) {
            return redirect()->route('legions.index')
                             ->with('error', 'Invitation not found or already processed.');
        }
        
        // Accept the invitation
        $invitation->update([
            'is_accepted' => true,
            'joined_at' => now(),
        ]);
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'You have joined the legion successfully.');
    }

    /**
     * Reject an invitation to join the legion.
     */
    public function rejectInvitation(Request $request, Legion $legion)
    {
        $user = auth()->user();
        
        // Get the invitation
        $invitation = $legion->members()->where('user_id', $user->id)->where('is_accepted', false)->first();
        
        if (!$invitation) {
            return redirect()->route('legions.index')
                             ->with('error', 'Invitation not found or already processed.');
        }
        
        // Delete the invitation
        $invitation->delete();
        
        return redirect()->route('legions.index')
                         ->with('success', 'Invitation rejected successfully.');
    }

    /**
     * Leave the legion.
     */
    public function leave(Request $request, Legion $legion)
    {
        $user = auth()->user();
        
        // Get the membership
        $membership = $legion->members()->where('user_id', $user->id)->where('is_accepted', true)->first();
        
        if (!$membership) {
            return redirect()->route('legions.index')
                             ->with('error', 'You are not a member of this legion.');
        }
        
        // Check if user is leader
        if ($membership->isLeader()) {
            // Check if there are other members
            if ($legion->acceptedMembers()->where('user_id', '!=', $user->id)->count() > 0) {
                return redirect()->route('legions.show', $legion)
                                 ->with('error', 'You cannot leave the legion as the leader while other members exist. Promote someone else to leader first or disband the legion.');
            }
            
            // If leader is the only member, disband the legion
            $legion->delete();
            
            return redirect()->route('legions.index')
                             ->with('success', 'Legion disbanded successfully as you were the only member.');
        }
        
        // Delete the membership
        $membership->delete();
        
        return redirect()->route('legions.index')
                         ->with('success', 'You have left the legion successfully.');
    }

    /**
     * Promote a member to officer.
     */
    public function promote(Request $request, Legion $legion, User $user)
    {
        $currentUser = auth()->user();
        
        // Check if current user is leader
        $leaderMembership = $legion->members()->where('user_id', $currentUser->id)->where('role', 'leader')->first();
        
        if (!$leaderMembership) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Only the legion leader can promote members.');
        }
        
        // Get the member to promote
        $memberToPromote = $legion->members()->where('user_id', $user->id)->where('is_accepted', true)->first();
        
        if (!$memberToPromote || $memberToPromote->role !== 'member') {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Member not found or already an officer/leader.');
        }
        
        // Promote the member
        $memberToPromote->update([
            'role' => 'officer',
        ]);
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'Member promoted to officer successfully.');
    }

    /**
     * Demote an officer to member.
     */
    public function demote(Request $request, Legion $legion, User $user)
    {
        $currentUser = auth()->user();
        
        // Check if current user is leader
        $leaderMembership = $legion->members()->where('user_id', $currentUser->id)->where('role', 'leader')->first();
        
        if (!$leaderMembership) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Only the legion leader can demote officers.');
        }
        
        // Get the officer to demote
        $officerToDemote = $legion->members()->where('user_id', $user->id)->where('role', 'officer')->where('is_accepted', true)->first();
        
        if (!$officerToDemote) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Officer not found or already a regular member.');
        }
        
        // Demote the officer
        $officerToDemote->update([
            'role' => 'member',
        ]);
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'Officer demoted to member successfully.');
    }

    /**
     * Transfer leadership to another member.
     */
    public function transferLeadership(Request $request, Legion $legion, User $user)
    {
        $currentUser = auth()->user();
        
        // Check if current user is leader
        $leaderMembership = $legion->members()->where('user_id', $currentUser->id)->where('role', 'leader')->first();
        
        if (!$leaderMembership) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Only the legion leader can transfer leadership.');
        }
        
        // Get the member to promote
        $memberToPromote = $legion->members()->where('user_id', $user->id)->where('is_accepted', true)->first();
        
        if (!$memberToPromote) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Member not found or not accepted.');
        }
        
        // Update legion leader
        $legion->update([
            'leader_id' => $user->id,
        ]);
        
        // Update role of new leader
        $memberToPromote->update([
            'role' => 'leader',
        ]);
        
        // Update role of old leader to officer
        $leaderMembership->update([
            'role' => 'officer',
        ]);
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'Leadership transferred successfully.');
    }

    /**
     * Remove a member from the legion.
     */
    public function removeMember(Request $request, Legion $legion, User $user)
    {
        $currentUser = auth()->user();
        
        // Check if current user is leader or officer
        $membership = $legion->members()->where('user_id', $currentUser->id)->first();
        
        if (!$membership || !($membership->isLeader() || $membership->isOfficer())) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'You do not have permission to remove members.');
        }
        
        // Get the member to remove
        $memberToRemove = $legion->members()->where('user_id', $user->id)->where('is_accepted', true)->first();
        
        if (!$memberToRemove) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Member not found.');
        }
        
        // Cannot remove the leader
        if ($memberToRemove->isLeader()) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Cannot remove the legion leader.');
        }
        
        // Officers can only remove regular members
        if ($membership->isOfficer() && $memberToRemove->isOfficer()) {
            return redirect()->route('legions.show', $legion)
                             ->with('error', 'Officers can only remove regular members, not other officers.');
        }
        
        // Remove the member
        $memberToRemove->delete();
        
        return redirect()->route('legions.show', $legion)
                         ->with('success', 'Member removed successfully.');
    }

    /**
     * Show the legion leaderboard.
     */
    public function leaderboard()
    {
        // Get all legions with at least one member
        $legions = Legion::withCount('acceptedMembers')
                    ->having('accepted_members_count', '>', 0)
                    ->get()
                    ->sortByDesc(function ($legion) {
                        return $legion->power;
                    });
        
        return view('legions.leaderboard', compact('legions'));
    }
}