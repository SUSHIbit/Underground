<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Tournament Team') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    @if(session('error'))
                        <div class="bg-red-900/20 border-l-4 border-red-500 text-red-400 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-900/20 border-l-4 border-green-500 text-green-400 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <div class="mb-6">
                        <a href="{{ route('tournaments.show', $tournament) }}" class="text-amber-400 hover:text-amber-300">
                            &larr; Back to Tournament
                        </a>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-2 text-amber-400">{{ $team->name }}</h3>
                        <p class="text-gray-400 mb-2">Team for: {{ $tournament->title }}</p>
                        @if($tournament->hasEnded())
                            <span class="inline-block px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-sm">Tournament Completed</span>
                        @endif
                    </div>
                    
                    <!-- Team Information -->
                    <div class="bg-gray-700/20 rounded-lg p-6 mb-8 border border-amber-800/20">
                        <h4 class="font-semibold text-lg mb-4 text-amber-400">Team Members</h4>
                        
                        <div class="space-y-4">
                            @foreach($allTeamMembers as $memberData)
                                <div class="flex items-center justify-between p-3 rounded-lg {{ $memberData['is_leader'] ? 'bg-amber-900/10' : 'bg-gray-800' }}">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-full {{ $memberData['is_leader'] ? 'bg-amber-600' : 'bg-gray-700' }} flex items-center justify-center text-white font-bold mr-4">
                                            {{ substr($memberData['user']->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium {{ $memberData['is_leader'] ? 'text-amber-400' : 'text-white' }}">
                                                {{ $memberData['user']->name }} ({{ $memberData['user']->username }})
                                            </p>
                                            <div class="flex items-center">
                                                <p class="text-sm text-gray-400">
                                                    {{ $memberData['is_leader'] ? 'Team Leader' : 'Team Member' }} • {{ $memberData['user']->getRank() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        @if($memberData['is_current_user'])
                                            <span class="px-2 py-1 bg-gray-700/50 text-gray-300 text-xs rounded-full">You</span>
                                        @endif
                                        
                                        <!-- Only show management buttons if tournament hasn't ended -->
                                        @if(!$tournament->hasEnded())
                                            @if($isLeader && !$memberData['is_leader'])
                                                <form action="{{ route('tournaments.team.remove-member', ['tournament' => $tournament, 'participant' => $memberData['participant_id']]) }}" method="POST" class="ml-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded-full" onclick="return confirm('Are you sure you want to remove this member from the team?')">
                                                        Remove
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if(!$isLeader && $memberData['is_current_user'])
                                                <form action="{{ route('tournaments.team.leave', $tournament) }}" method="POST" class="ml-2">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded-full" onclick="return confirm('Are you sure you want to leave this team?')">
                                                        Leave Team
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Team Status Summary -->
                        <div class="mt-6 p-4 bg-gray-800/50 rounded-lg border border-amber-800/20">
                            <h5 class="font-medium text-white mb-2">Team Status</h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                                <div>
                                    <p class="text-sm text-gray-400">Team Size: <span class="text-amber-400">{{ $tournament->team_size }} members</span></p>
                                    <p class="text-sm text-gray-400">Current Members: <span class="text-amber-400">{{ $allTeamMembers->count() }}</span></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Team Leader: <span class="text-amber-400">{{ $team->leader->name }}</span></p>
                                    
                                    @if($isTeamComplete)
                                        <p class="text-sm text-green-400">✓ Team is complete!</p>
                                    @else
                                        <p class="text-sm text-amber-400">⚠ Team needs {{ $tournament->team_size - $allTeamMembers->count() }} more members</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Only show team management actions if tournament hasn't ended -->
                            @if($isLeader && !$tournament->hasEnded())
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if(!$isTeamComplete)
                                        <button onclick="toggleAddMembersForm()" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded transition-colors">
                                            Add Members
                                        </button>
                                    @endif
                                    
                                    <form action="{{ route('tournaments.team.disband', $tournament) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-2 px-4 rounded transition-colors" onclick="return confirm('Are you sure you want to disband this team? This action cannot be undone.')">
                                            Disband Team
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Add Members Section - CONSISTENT UI WITH CREATE TEAM -->
                    @if($isLeader && !$tournament->hasEnded() && !$isTeamComplete)
                        <div id="add-members" class="bg-blue-900/10 rounded-lg p-6 mb-8 border border-blue-800/20" style="display: none;">
                            <h4 class="font-semibold text-lg mb-4 text-blue-400">Add Team Members</h4>
                            
                            <div class="bg-gray-700/50 p-4 rounded-lg mb-6">
                                <p class="mb-2">Search and add eligible users to your team.</p>
                                <p class="mb-2">You can add up to <span class="text-blue-400 font-medium">{{ $tournament->team_size - $allTeamMembers->count() }}</span> more members.</p>
                            </div>
                            
                            <!-- Search and Add Members Form -->
                            <div class="mb-4" x-data="{ 
                                searchQuery: '', 
                                selectedUsers: [],
                                availableUsers: [],
                                maxMembers: {{ $tournament->team_size - $allTeamMembers->count() }},
                                async searchUsers() {
                                    if (this.searchQuery.length < 2) {
                                        this.availableUsers = [];
                                        return;
                                    }
                                    
                                    try {
                                        const response = await fetch(`/tournaments/{{ $tournament->id }}/search-eligible-users?search=${encodeURIComponent(this.searchQuery)}`);
                                        const data = await response.json();
                                        this.availableUsers = data.users || [];
                                    } catch (error) {
                                        console.error('Search failed:', error);
                                        this.availableUsers = [];
                                    }
                                },
                                addUser(user) {
                                    if (this.selectedUsers.length >= this.maxMembers) return;
                                    if (this.selectedUsers.find(u => u.id === user.id)) return;
                                    
                                    this.selectedUsers.push(user);
                                    this.availableUsers = this.availableUsers.filter(u => u.id !== user.id);
                                },
                                removeUser(userId) {
                                    this.selectedUsers = this.selectedUsers.filter(u => u.id !== userId);
                                },
                                clearSelection() {
                                    this.selectedUsers = [];
                                }
                            }">
                                
                                <!-- Search input -->
                                <div class="mb-4">
                                    <input 
                                        type="text" 
                                        x-model="searchQuery"
                                        @input.debounce.500ms="searchUsers()"
                                        placeholder="Search users by username or name" 
                                        class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                    >
                                </div>
                                
                                <!-- Available Users Section -->
                                <div x-show="availableUsers.length > 0" class="mb-6">
                                    <h5 class="text-sm font-medium text-gray-300 mb-2">Select Team Members</h5>
                                    
                                    <div class="bg-gray-700 rounded-md border border-blue-800/20">
                                        <template x-for="user in availableUsers" :key="user.id">
                                            <div class="flex items-center justify-between p-3 border-b border-blue-800/10 last:border-b-0">
                                                <div class="flex items-center">
                                                    <div class="mr-3 bg-blue-900/50 w-8 h-8 rounded-full flex items-center justify-center text-blue-400 font-bold" x-text="user.name.charAt(0)">
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-white" x-text="user.username"></p>
                                                        <p class="text-sm text-gray-400" x-text="user.name + ' • ' + user.rank"></p>
                                                    </div>
                                                </div>
                                                
                                                <button 
                                                    type="button"
                                                    @click="addUser(user)"
                                                    :disabled="selectedUsers.length >= maxMembers"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-1 px-3 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    Add
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Selected Users Section -->
                                <div x-show="selectedUsers.length > 0" class="mb-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <h5 class="text-sm font-medium text-gray-300">Selected Team Members</h5>
                                        <button 
                                            type="button"
                                            @click="clearSelection()"
                                            class="text-red-400 hover:text-red-300 text-sm underline"
                                        >
                                            Clear All
                                        </button>
                                    </div>
                                    
                                    <div class="bg-gray-700 rounded-md border border-blue-800/20">
                                        <template x-for="user in selectedUsers" :key="user.id">
                                            <div class="flex items-center justify-between p-3 border-b border-blue-800/10 last:border-b-0">
                                                <div class="flex items-center">
                                                    <div class="mr-3 bg-blue-900/50 w-8 h-8 rounded-full flex items-center justify-center text-blue-400 font-bold" x-text="user.name.charAt(0)">
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-white" x-text="user.username"></p>
                                                        <p class="text-sm text-gray-400" x-text="user.name + ' • ' + user.rank"></p>
                                                    </div>
                                                </div>
                                                
                                                <button 
                                                    type="button"
                                                    @click="removeUser(user.id)"
                                                    class="text-red-400 hover:text-red-300"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div class="mt-2 text-blue-400 text-sm">
                                        <span x-text="selectedUsers.length"></span> of <span x-text="maxMembers"></span> users selected
                                    </div>
                                </div>
                                
                                <!-- Submit Form -->
                                <form action="{{ route('tournaments.teams.create', $tournament) }}" method="POST" x-show="selectedUsers.length > 0">
                                    @csrf
                                    
                                    <!-- Hidden inputs for selected users -->
                                    <template x-for="user in selectedUsers" :key="user.id">
                                        <input type="hidden" name="user_ids[]" :value="user.id">
                                    </template>
                                    
                                    <div class="flex justify-between items-center">
                                        <button 
                                            type="button" 
                                            onclick="toggleAddMembersForm()"
                                            class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded cursor-pointer"
                                        >
                                            Cancel
                                        </button>
                                        
                                        <button 
                                            type="submit" 
                                            :disabled="selectedUsers.length === 0"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            Add Selected Members
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    
                    <!-- For non-leaders when team is incomplete -->
                    @if(!$isLeader && !$isTeamComplete)
                        <div class="bg-amber-900/10 rounded-lg p-6 mb-8 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Team Status</h4>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white font-medium">Team is not complete</p>
                                    <p class="text-gray-400 text-sm">Your team needs {{ $tournament->team_size - $allTeamMembers->count() }} more members before you can participate in this tournament. Only the team leader can add new members.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Project Submission Form (for team leader only) -->
                    @if(!$tournament->hasEnded())
                        @if($isLeader)
                            <div class="bg-amber-900/10 rounded-lg p-6 mb-8 border border-amber-800/20">
                                <h4 class="font-semibold text-lg mb-4 text-amber-400">Project Submission</h4>
                                
                                @if(\Carbon\Carbon::parse($tournament->deadline)->isPast())
                                    <!-- Deadline has passed -->
                                    <div class="bg-red-900/20 p-4 rounded-lg text-red-400 mb-4">
                                        <p>The submission deadline has passed. No further submissions are allowed.</p>
                                    </div>
                                    
                                    @php
                                        $participant = $team->participants->where('user_id', auth()->id())->first();
                                        $submissionUrl = $participant ? $participant->submission_url : null;
                                    @endphp
                                    
                                    @if($submissionUrl)
                                        <div class="mt-4">
                                            <h5 class="font-medium text-white mb-2">Team Submission:</h5>
                                            <a href="{{ $submissionUrl }}" 
                                               target="_blank" 
                                               class="text-blue-400 hover:underline break-all">
                                                {{ $submissionUrl }}
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-gray-400">Your team did not submit a project before the deadline.</p>
                                    @endif
                                @else
                                    <!-- Check team completion status -->
                                    @if($isTeamComplete)
                                        <!-- Show submission form if team is complete and deadline not passed -->
                                        <p class="text-gray-300 mb-4">Submit your team's project URL below. You can update this at any time until the deadline.</p>
                                        
                                        @php
                                            $participant = $team->participants->where('user_id', auth()->id())->first();
                                            $submissionUrl = $participant ? $participant->submission_url : null;
                                        @endphp
                                        
                                        <form action="{{ route('tournaments.submit', $tournament) }}" method="POST" id="submit-project">
                                            @csrf
                                            <div class="mb-4">
                                                <label for="submission_url" class="block mb-2 text-sm font-medium text-gray-300">
                                                    Project URL (GitHub, Vercel, etc.)
                                                </label>
                                                <input 
                                                    type="url" 
                                                    name="submission_url" 
                                                    id="submission_url" 
                                                    value="{{ $submissionUrl }}"
                                                    class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                                    placeholder="https://github.com/yourusername/project"
                                                    required
                                                >
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded">
                                                    @if($submissionUrl) 
                                                        Update Submission
                                                    @else
                                                        Submit Project
                                                    @endif
                                                </button>
                                                
                                                <div class="text-sm text-gray-400">
                                                    Deadline: {{ \Carbon\Carbon::parse($tournament->deadline)->format('F j, Y, g:i a') }}
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        <!-- Message when team is not complete -->
                                        <div class="bg-gray-700/30 p-4 rounded-lg border border-amber-800/20">
                                            <p class="text-gray-300">
                                                You need to have all {{ $tournament->team_size }} members in your team before you can submit a project.
                                            </p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @else
                            <!-- For non-leader team members -->
                            <div class="bg-amber-900/10 rounded-lg p-6 mb-8 border border-amber-800/20">
                                <h4 class="font-semibold text-lg mb-4 text-amber-400">Project Submission</h4>
                                
                                <div class="bg-gray-700/30 p-4 rounded-lg border border-amber-800/20">
                                    <p class="text-gray-300">
                                        Only the team leader can submit the project for this tournament.
                                    </p>
                                    
                                    @php
                                        $leaderParticipant = $team->participants->where('user_id', $team->leader_id)->first();
                                        $submissionUrl = $leaderParticipant ? $leaderParticipant->submission_url : null;
                                    @endphp
                                    
                                    @if($submissionUrl)
                                        <div class="mt-4">
                                            <h5 class="font-medium text-white mb-2">Team Project Submission:</h5>
                                            <a href="{{ $submissionUrl }}" 
                                               target="_blank" 
                                               class="text-blue-400 hover:underline break-all">
                                                {{ $submissionUrl }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Tournament has ended -->
                        <div class="bg-gray-700/20 rounded-lg p-6 mb-8 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Tournament Results</h4>
                            
                            @php
                                $leaderParticipant = $team->participants->where('user_id', $team->leader_id)->first();
                                $submissionUrl = $leaderParticipant ? $leaderParticipant->submission_url : null;
                                $score = $leaderParticipant ? $leaderParticipant->score : null;
                                $feedback = $leaderParticipant ? $leaderParticipant->feedback : null;
                            @endphp
                            
                            @if($submissionUrl)
                                <div class="mb-4">
                                    <h5 class="font-medium text-white mb-2">Team Project Submission:</h5>
                                    <a href="{{ $submissionUrl }}" 
                                       target="_blank" 
                                       class="text-blue-400 hover:underline break-all">
                                        {{ $submissionUrl }}
                                    </a>
                                </div>
                                
                                @if($score !== null)
                                    <div class="mt-6 p-4 bg-gray-800 rounded-lg">
                                        <h5 class="font-medium text-amber-400 mb-2">Team Score:</h5>
                                        <p class="text-3xl font-bold {{ $score >= 7 ? 'text-green-400' : ($score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                            {{ $score }}/10
                                        </p>
                                        
                                        @if($feedback)
                                            <div class="mt-4">
                                                <h6 class="font-medium text-white mb-1">Feedback:</h6>
                                                <p class="text-gray-300 whitespace-pre-line">{{ $feedback }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-gray-400">Your team's submission has not been judged yet.</p>
                                @endif
                            @else
                                <p class="text-gray-400">Your team did not submit a project for this tournament.</p>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Tournament Results Section (Only show when tournament has ended) -->
                    @if($tournament->hasEnded())
                        <div class="bg-blue-900/10 rounded-lg p-6 mb-8 border border-blue-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-blue-400">Team Results</h4>
                            <p class="text-gray-300 mb-4">View individual results for all team members.</p>
                            <a href="{{ route('tournaments.team.results', $tournament) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                View Team Results
                            </a>
                        </div>
                    @endif
                    
                    <!-- Tournament Information -->
                    <div class="bg-gray-700/20 rounded-lg p-6 mb-6 border border-amber-800/20">
                        <h4 class="font-semibold text-lg mb-4 text-amber-400">Tournament Details</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="mb-2"><span class="font-medium text-gray-300">Date & Time:</span> 
                                    {{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}
                                </p>
                                <p class="mb-2"><span class="font-medium text-gray-300">Location:</span> 
                                    {{ $tournament->location }}
                                </p>
                                <p class="mb-2"><span class="font-medium text-gray-300">Team Size:</span> 
                                    {{ $tournament->team_size }} {{ $tournament->team_size > 1 ? 'members' : 'member' }}
                                </p>
                            </div>
                            <div>
                                <p class="mb-2"><span class="font-medium text-gray-300">Submission Deadline:</span> 
                                    {{ \Carbon\Carbon::parse($tournament->deadline)->format('F j, Y, g:i a') }}
                                </p>
                                <p class="mb-2"><span class="font-medium text-gray-300">Status:</span>
                                    @if($tournament->hasEnded())
                                        <span class="text-gray-400">Completed</span>
                                    @else
                                        <span class="text-green-400">Upcoming</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <a href="{{ route('tournaments.show', $tournament) }}" class="inline-block bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded border border-gray-600 transition-colors">
                            Back to Tournament
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleAddMembersForm() {
            const form = document.getElementById('add-members');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                form.scrollIntoView({ behavior: 'smooth' });
            } else {
                form.style.display = 'none';
            }
        }
        
        // Show the form if URL has #add-members hash
        if (window.location.hash === '#add-members') {
            document.addEventListener('DOMContentLoaded', function() {
                toggleAddMembersForm();
            });
        }
    </script>
</x-app-layout>