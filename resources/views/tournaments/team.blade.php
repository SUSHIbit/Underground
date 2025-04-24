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
                                                {{ $memberData['user']->name }} (@{{ $memberData['user']->username }})
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
                                        
                                        @if($isLeader && !$memberData['is_leader'] && !$tournament->hasEnded())
                                            <form action="{{ route('tournaments.team.remove-member', ['tournament' => $tournament, 'participant' => $memberData['participant_id']]) }}" method="POST" class="ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded-full" onclick="return confirm('Are you sure you want to remove this member from the team?')">
                                                    Remove
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if(!$isLeader && $memberData['is_current_user'] && !$tournament->hasEnded())
                                            <form action="{{ route('tournaments.team.leave', $tournament) }}" method="POST" class="ml-2">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded-full" onclick="return confirm('Are you sure you want to leave this team?')">
                                                    Leave Team
                                                </button>
                                            </form>
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
                                        <p class="text-sm text-green-400">Team is complete!</p>
                                    @else
                                        <p class="text-sm text-amber-400">Team needs {{ $tournament->team_size - $allTeamMembers->count() }} more members</p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($isLeader && !$tournament->hasEnded())
                                <div class="mt-4">
                                    @if(!$isTeamComplete)
                                        <a href="{{ route('tournaments.create-team-form', $tournament) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold py-1 px-3 rounded">
                                            Add More Members
                                        </a>
                                    @endif
                                    
                                    <form action="{{ route('tournaments.team.disband', $tournament) }}" method="POST" class="inline-block ml-2">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-1 px-3 rounded" onclick="return confirm('Are you sure you want to disband this team? This action cannot be undone.')">
                                            Disband Team
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                    
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
                                        
                                        <form action="{{ route('tournaments.submit', $tournament) }}" method="POST">
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
</x-app-layout>