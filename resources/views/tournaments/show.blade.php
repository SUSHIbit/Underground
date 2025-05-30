<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Tournament Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    <div class="mb-6">
                        <a href="{{ route('tournaments.index') }}" class="text-amber-400 hover:text-amber-300">
                            &larr; Back to Tournaments
                        </a>
                    </div>
                    
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold mb-2">{{ $tournament->title }}</h3>
                            <p class="text-gray-400 mb-2">Created by: {{ $tournament->creator->name }}</p>
                        </div>
                        
                        @if($hasEnded)
                            <span class="px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-sm">Completed</span>
                        @endif
                    </div>
                    
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
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <div class="bg-gray-700/50 p-4 rounded-lg mb-6">
                                <h4 class="font-semibold text-lg mb-2 text-amber-400">Tournament Information</h4>
                                <p class="mb-2"><span class="font-medium text-gray-300">Date & Time:</span> {{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}</p>
                                <p class="mb-2"><span class="font-medium text-gray-300">Location:</span> {{ $tournament->location }}</p>
                                <p class="mb-2"><span class="font-medium text-gray-300">Team Size:</span> {{ $tournament->team_size }} {{ $tournament->team_size > 1 ? 'members' : 'member' }}</p>
                                <p class="mb-2"><span class="font-medium text-gray-300">Submission Deadline:</span> {{ \Carbon\Carbon::parse($tournament->deadline)->format('F j, Y, g:i a') }}</p>
                                <p class="mb-2"><span class="font-medium text-gray-300">Minimum Rank Required:</span> {{ $tournament->minimum_rank }}</p>
                            </div>
                            
                            <div class="bg-gray-700/50 p-4 rounded-lg">
                                <h4 class="font-semibold text-lg mb-2 text-amber-400">Judges</h4>
                                <ul class="list-disc list-inside text-gray-300">
                                    @foreach($tournament->judges as $judge)
                                        <li>{{ $judge->name }}{{ $judge->pivot->role ? ' - '.$judge->pivot->role : '' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-700/50 p-4 rounded-lg mb-6">
                                <h4 class="font-semibold text-lg mb-2 text-amber-400">Description</h4>
                                <p class="whitespace-pre-line text-gray-300">{{ $tournament->description }}</p>
                            </div>
                            
                            <div class="bg-gray-700/50 p-4 rounded-lg mb-6">
                                <h4 class="font-semibold text-lg mb-2 text-amber-400">Eligibility</h4>
                                <p class="whitespace-pre-line text-gray-300">{{ $tournament->eligibility }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-2 text-amber-400">Rules</h4>
                        <div class="bg-gray-700/50 p-4 rounded-lg">
                            <p class="whitespace-pre-line text-gray-300">{{ $tournament->rules }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-2 text-amber-400">Judging Criteria</h4>
                        <div class="bg-gray-700/50 p-4 rounded-lg">
                            <p class="whitespace-pre-line text-gray-300">{{ $tournament->judging_criteria }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-2 text-amber-400">Judging Rubrics</h4>
                        <div class="bg-gray-700/50 p-4 rounded-lg">
                            @if($tournament->rubrics->count() > 0)
                                <div class="space-y-2">
                                    @foreach($tournament->rubrics as $rubric)
                                        <div class="flex justify-between items-center border-b border-amber-800/10 py-2">
                                            <span class="text-gray-300">{{ $rubric->title }}</span>
                                            <span class="bg-amber-900/20 px-2 py-1 rounded-md text-amber-400">{{ $rubric->score_weight }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-400">No specific rubrics have been defined for this tournament.</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <h4 class="font-semibold text-lg mb-2 text-amber-400">Project Submission</h4>
                        <div class="bg-gray-700/50 p-4 rounded-lg">
                            <p class="whitespace-pre-line text-gray-300">{{ $tournament->project_submission }}</p>
                        </div>
                    </div>
                    
                    <!-- Your Participation Section -->
                    @if($isParticipating)
                        <div class="bg-amber-900/10 p-6 rounded-lg mb-6 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Your Participation</h4>
                            
                            @if($tournament->team_size > 1)
                                <!-- For team tournaments - Enhanced team management -->
                                <p class="text-white mb-4">You are registered for this tournament as part of a team.</p>
                                
                                @php
                                    // Get user's team information
                                    $userParticipant = $tournament->participants()->where('user_id', auth()->id())->first();
                                    $team = $userParticipant ? $userParticipant->team : null;
                                    $isTeamLeader = $team && $team->leader_id === auth()->id();
                                    $teamMemberCount = $team ? $team->participants()->count() : 0;
                                    $isTeamComplete = $teamMemberCount >= $tournament->team_size;
                                @endphp
                                
                                @if($team)
                                    <div class="bg-gray-800/50 p-4 rounded-lg mb-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h5 class="font-medium text-lg text-white">{{ $team->name }}</h5>
                                            @if($isTeamLeader)
                                                <span class="px-2 py-1 bg-amber-900/30 text-amber-400 text-xs rounded-full">Team Leader</span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-700/50 text-gray-300 text-xs rounded-full">Team Member</span>
                                            @endif
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm text-gray-400">Team Size: <span class="text-amber-400">{{ $tournament->team_size }} required</span></p>
                                                <p class="text-sm text-gray-400">Current Members: <span class="text-white">{{ $teamMemberCount }}</span></p>
                                            </div>
                                            <div>
                                                @if($isTeamComplete)
                                                    <p class="text-sm text-green-400">✓ Team is complete!</p>
                                                @else
                                                    <p class="text-sm text-amber-400">⚠ Need {{ $tournament->team_size - $teamMemberCount }} more members</p>
                                                @endif
                                                
                                                @if($userParticipant && $userParticipant->submission_url)
                                                    <p class="text-sm text-green-400">✓ Project submitted</p>
                                                @elseif(!$deadlinePassed && $isTeamComplete)
                                                    <p class="text-sm text-blue-400">Ready to submit project</p>
                                                @elseif(!$deadlinePassed)
                                                    <p class="text-sm text-gray-400">Complete team to submit</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Team Management Actions -->
                                        <div class="flex flex-wrap gap-3 mt-4">
                                            @if($hasEnded)
                                                <a href="{{ route('tournaments.team', $tournament) }}" 
                                                   class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                                    View Team
                                                </a>
                                            @else
                                                <a href="{{ route('tournaments.team', $tournament) }}" 
                                                   class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                                    Manage Team
                                                </a>
                                            @endif
                                            
                                            @if(!$hasEnded)
                                                @if($isTeamLeader && !$isTeamComplete)
                                                    <a href="{{ route('tournaments.team', $tournament) }}#add-members" 
                                                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                                        Add Members
                                                    </a>
                                                @endif
                                                
                                                @if($isTeamLeader && $isTeamComplete && !$deadlinePassed)
                                                    <a href="{{ route('tournaments.team', $tournament) }}#submit-project" 
                                                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                                        Submit Project
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('tournaments.team.results', $tournament) }}" 
                                                   class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                                    View Results
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <!-- Fallback if team data is missing -->
                                    <div class="bg-red-900/20 p-4 rounded-lg border border-red-800/20 text-red-400 mb-4">
                                        <p>⚠ There seems to be an issue with your team registration. Please contact support.</p>
                                    </div>
                                @endif
                            @else
                                <!-- For solo tournaments - include submission form here -->
                                <p class="text-white mb-4">You are registered for this tournament as an individual participant.</p>
                                
                                @if(!$deadlinePassed)
                                    <div class="mt-6 border-t border-amber-800/20 pt-6">
                                        <h5 class="font-medium text-lg mb-3 text-amber-400">
                                            @if(isset($participant) && $participant->submission_url)
                                                Update Your Submission
                                            @else
                                                Submit Your Project
                                            @endif
                                        </h5>
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
                                                    value="{{ isset($participant) ? $participant->submission_url : '' }}"
                                                    class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                                    placeholder="https://github.com/yourusername/project"
                                                    required
                                                >
                                            </div>
                                            
                                            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded">
                                                @if(isset($participant) && $participant->submission_url)
                                                    Update Submission
                                                @else
                                                    Submit Project
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                @elseif(isset($participant) && $participant->submission_url)
                                    <!-- Show submission details if deadline passed -->
                                    <div class="mt-6 border-t border-amber-800/20 pt-6">
                                        <h5 class="font-medium text-lg mb-3 text-amber-400">Your Submission</h5>
                                        <p class="text-gray-300 mb-2">You have submitted your project for this tournament:</p>
                                        <a href="{{ $participant->submission_url }}" target="_blank" class="text-blue-400 hover:underline break-all">
                                            {{ $participant->submission_url }}
                                        </a>
                                        <p class="mt-4 text-orange-400">
                                            <span class="font-medium">Note:</span> The submission deadline has passed. No further changes can be made.
                                        </p>
                                    </div>
                                @else
                                    <!-- Show missed deadline message -->
                                    <div class="mt-6 border-t border-amber-800/20 pt-6">
                                        <div class="bg-red-900/20 p-4 rounded-lg border border-red-800/20 text-red-400">
                                            <p>The submission deadline has passed. You did not submit a project for this tournament.</p>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Add View Results button for ended tournaments -->
                                @if($hasEnded)
                                    <div class="mt-6 border-t border-amber-800/20 pt-6">
                                        <h5 class="font-medium text-lg mb-3 text-amber-400">Your Tournament Results</h5>
                                        @if($tournament->isGradingComplete())
                                            <p class="text-gray-300 mb-4">View your score and see how you ranked against other participants:</p>
                                            <a href="{{ route('tournaments.participants', $tournament) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                                View Results
                                            </a>
                                        @else
                                            <div class="bg-amber-900/20 p-4 rounded-lg border border-amber-800/20">
                                                <div class="flex items-start space-x-3">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-5 w-5 text-amber-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h6 class="font-medium text-amber-400 mb-1">Results Not Yet Available</h6>
                                                        <p class="text-gray-300 text-sm">
                                                            The judges are still grading submissions for this tournament. 
                                                            Your results will be available once all judges have completed their evaluations.
                                                        </p>
                                                        <div class="mt-2">
                                                            @php
                                                                $completedJudges = $tournament->getCompletedJudgesCount();
                                                                $totalJudges = $tournament->judges()->count();
                                                            @endphp
                                                            <p class="text-xs text-gray-400">
                                                                Judges completed: {{ $completedJudges }}/{{ $totalJudges }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                    
                    <!-- Registration/Team Creation Section -->
                    @if(!$isParticipating && $canParticipate && !$hasEnded)
                        @if($tournament->team_size > 1)
                            <!-- Button to go to team creation page -->
                            <div class="bg-amber-900/10 p-6 rounded-lg mb-6 border border-amber-800/20">
                                <h4 class="font-semibold text-lg mb-4 text-amber-400">Join This Tournament as a Team</h4>
                                <p class="text-gray-300 mb-4">This tournament requires a team of {{ $tournament->team_size }} members. Create a team and add other participants.</p>
                                <a href="{{ route('tournaments.create-team-form', $tournament) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Create a Team
                                </a>
                            </div>
                        @else
                            <!-- Simple registration button for solo tournaments -->
                            <div class="bg-amber-900/10 p-6 rounded-lg mb-6 border border-amber-800/20">
                                <h4 class="font-semibold text-lg mb-4 text-amber-400">Join This Tournament</h4>
                                <p class="text-gray-300 mb-4">This is an individual tournament. Register below to participate.</p>
                                <form action="{{ route('tournaments.join', $tournament) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Register for Tournament
                                    </button>
                                </form>
                            </div>
                        @endif
                    @elseif($hasEnded)
                        <!-- Show tournament ended message -->
                        <div class="bg-gray-700/50 p-6 rounded-lg mb-6">
                            <h4 class="font-semibold text-lg mb-2 text-amber-400">Tournament Has Ended</h4>
                            <p class="text-gray-300">This tournament was held on {{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y') }} and is now complete.</p>
                        </div>
                    @elseif(!$canParticipate)
                        <!-- Show eligibility requirements -->
                        <div class="bg-amber-900/20 p-6 rounded-lg mb-6 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-2 text-amber-400">Eligibility Requirements</h4>
                            <p class="mb-4 text-gray-300">You do not currently meet the eligibility requirements for this tournament.</p>
                            <p class="text-gray-300">Minimum rank required: <strong class="text-amber-400">{{ $tournament->minimum_rank }}</strong></p>
                            <p class="text-gray-300">Your current rank: <strong class="text-gray-400">{{ auth()->user()->getRank() }}</strong></p>
                        </div>
                    @endif
                    
                    <!-- Bottom Back button -->
                    <div class="mt-6">
                        <a href="{{ route('tournaments.index') }}" class="inline-block bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded border border-gray-600 transition-colors">
                            Back to Tournaments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>