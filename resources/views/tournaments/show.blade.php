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
                    
                    <div class="mb-8">
                        <h4 class="font-semibold text-lg mb-2 text-amber-400">Project Submission</h4>
                        <div class="bg-gray-700/50 p-4 rounded-lg">
                            <p class="whitespace-pre-line text-gray-300">{{ $tournament->project_submission }}</p>
                        </div>
                    </div>
                    
                    <!-- Your Team Section -->
                    @if($isParticipating)
                        <div class="bg-amber-900/10 p-6 rounded-lg mb-6 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Your Participation</h4>
                            
                            @if($tournament->team_size > 1)
                                <!-- For team tournaments -->
                                <p class="text-white mb-4">You are part of a team for this tournament.</p>
                                <a href="{{ route('tournaments.team', $tournament) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded mb-4">
                                    View Team
                                </a>
                            @else
                                <!-- For solo tournaments -->
                                <p class="text-white mb-4">You are registered for this tournament.</p>
                            @endif
                            
                            <!-- Submission form (always show for registered participants if deadline hasn't passed) -->
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
                        </div>
                    @endif
                    
                    <!-- Registration/Team Creation Section -->
                    @if(!$isParticipating && $canParticipate && !$hasEnded)
                        @if($tournament->team_size > 1)
                            <!-- Button to go to team creation page -->
                            <div class="bg-amber-900/10 p-6 rounded-lg mb-6 border border-amber-800/20">
                                <h4 class="font-semibold text-lg mb-4 text-amber-400">Join This Tournament as a Team</h4>
                                <p class="text-gray-300 mb-4">This tournament requires a team of {{ $tournament->team_size }} members. Create a team and invite other participants.</p>
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
                    
                    <!-- Participants and Invitations Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 border-t border-amber-800/20 pt-6">
                        <!-- View Participants Button -->
                        <div class="bg-gray-700/30 p-4 rounded-lg border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">View Participants</h4>
                            <p class="text-gray-300 mb-4">See who else is participating in this tournament.</p>
                            <a href="{{ route('tournaments.participants', $tournament) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                View All Participants
                            </a>
                        </div>
                        
                        <!-- View Invitations Button -->
                        <div class="bg-gray-700/30 p-4 rounded-lg border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Your Invitations</h4>
                            <p class="text-gray-300 mb-4">Check if you have any pending team invitations.</p>
                            <a href="{{ route('tournaments.invitations') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white py-2 px-4 rounded transition-colors">
                                View Your Invitations
                            </a>
                        </div>
                    </div>
                    
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