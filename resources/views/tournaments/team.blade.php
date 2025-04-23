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
                                                
                                                @if($memberData['status'] === 'pending')
                                                    <span class="ml-2 px-2 py-1 bg-amber-900/20 text-amber-400 text-xs rounded-full">
                                                        Invitation Pending
                                                    </span>
                                                @elseif($memberData['status'] === 'accepted')
                                                    <span class="ml-2 px-2 py-1 bg-green-900/20 text-green-400 text-xs rounded-full">
                                                        Accepted
                                                    </span>
                                                @elseif($memberData['status'] === 'declined')
                                                    <span class="ml-2 px-2 py-1 bg-red-900/20 text-red-400 text-xs rounded-full">
                                                        Declined
                                                    </span>
                                                @elseif($memberData['status'] === 'expired')
                                                    <span class="ml-2 px-2 py-1 bg-gray-900/20 text-gray-400 text-xs rounded-full">
                                                        Expired
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        @if($memberData['is_current_user'])
                                            <span class="px-2 py-1 bg-gray-700/50 text-gray-300 text-xs rounded-full">You</span>
                                        @endif
                                        
                                        @if($isLeader && $memberData['status'] === 'declined' && isset($memberData['invitation']))
                                            <form action="{{ route('tournaments.create-team-form', $tournament) }}" method="GET" class="ml-2">
                                                <input type="hidden" name="add_user_id" value="{{ $memberData['user']->id }}">
                                                <button type="submit" class="px-2 py-1 bg-amber-600 hover:bg-amber-700 text-white text-xs rounded-full">
                                                    Re-invite
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
                                    <p class="text-sm text-gray-400">Required Members: <span class="text-amber-400">{{ $tournament->team_size }}</span></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Accepted Members: <span class="text-green-400">{{ $allTeamMembers->where('status', 'accepted')->count() }}</span></p>
                                    <p class="text-sm text-gray-400">Pending Invitations: <span class="text-amber-400">{{ $allTeamMembers->where('status', 'pending')->count() }}</span></p>
                                </div>
                            </div>
                            
                            <div class="mt-4 {{ $isTeamComplete ? 'text-green-400' : 'text-amber-400' }} text-sm">
                                @if($isTeamComplete)
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Team is complete! All members have accepted their invitations.
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Waiting for all team members to accept their invitations.
                                    </div>
                                @endif
                            </div>
                            
                            @if($isLeader && !$isTeamComplete && $allTeamMembers->where('status', 'pending')->count() === 0 && $allTeamMembers->count() < $tournament->team_size)
                                <!-- Show button to invite more members if needed -->
                                <div class="mt-4">
                                    <a href="{{ route('tournaments.create-team-form', $tournament) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold py-1 px-3 rounded">
                                        Invite More Members
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Project Submission Form -->
                    @if(!$tournament->hasEnded())
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
                                        <h5 class="font-medium text-white mb-2">Your Submitted Project:</h5>
                                        <a href="{{ $submissionUrl }}" 
                                           target="_blank" 
                                           class="text-blue-400 hover:underline break-all">
                                            {{ $submissionUrl }}
                                        </a>
                                    </div>
                                @else
                                    <p class="text-gray-400">You did not submit a project before the deadline.</p>
                                @endif
                            @else
                                <!-- Check team completion status -->
                                @if($isTeamComplete)
                                    <!-- Show submission form if team is complete and deadline not passed -->
                                    <p class="text-gray-300 mb-4">Submit your project URL below. You can update this at any time until the deadline.</p>
                                    
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
                                            You'll be able to submit your project once all team members have accepted their invitations.
                                        </p>
                                        <div class="mt-4 flex items-center text-amber-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            Waiting for pending invitations...
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @else
                        <!-- Tournament has ended -->
                        <div class="bg-gray-700/20 rounded-lg p-6 mb-8 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Tournament Results</h4>
                            
                            @php
                                $participant = $team->participants->where('user_id', auth()->id())->first();
                                $submissionUrl = $participant ? $participant->submission_url : null;
                                $score = $participant ? $participant->score : null;
                                $feedback = $participant ? $participant->feedback : null;
                            @endphp
                            
                            @if($submissionUrl)
                                <div class="mb-4">
                                    <h5 class="font-medium text-white mb-2">Your Submitted Project:</h5>
                                    <a href="{{ $submissionUrl }}" 
                                       target="_blank" 
                                       class="text-blue-400 hover:underline break-all">
                                        {{ $submissionUrl }}
                                    </a>
                                </div>
                                
                                @if($score !== null)
                                    <div class="mt-6 p-4 bg-gray-800 rounded-lg">
                                        <h5 class="font-medium text-amber-400 mb-2">Judge's Score:</h5>
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
                                    <p class="text-gray-400">Your submission has not been judged yet.</p>
                                @endif
                            @else
                                <p class="text-gray-400">You did not submit a project for this tournament.</p>
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
                                    @if(\Carbon\Carbon::parse($tournament->date_time)->isPast())
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