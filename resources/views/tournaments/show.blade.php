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
                    
                    @if($isParticipating)
                        <!-- Show submission form if already registered -->
                        <div class="bg-amber-900/10 p-6 rounded-lg mb-6 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Your Registration</h4>
                            
                            @if(isset($participant) && $participant)
                                <div class="mb-4">
                                    @if($tournament->team_size > 1)
                                        <div class="mb-3">
                                            <p class="font-medium text-gray-300">Team Name: 
                                                <span class="text-amber-400">{{ $participant->team_name ?? 'Not specified' }}</span>
                                            </p>
                                            
                                            @if(isset($participant->team_members) && is_array($participant->team_members) && count($participant->team_members) > 0)
                                                <p class="font-medium text-gray-300 mt-2">Team Members:</p>
                                                <ul class="list-disc list-inside ml-4 text-amber-400">
                                                    @foreach($participant->team_members as $member)
                                                        <li>{{ $member }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if($participant->submission_url)
                                        <div class="mb-4">
                                            <p class="font-medium text-green-400">Your project has been submitted:</p>
                                            <a href="{{ $participant->submission_url }}" target="_blank" class="text-blue-400 hover:underline">
                                                {{ $participant->submission_url }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                
                                @if(!$hasEnded && !$deadlinePassed)
                                    <!-- Project submission form (only show if tournament hasn't ended and deadline hasn't passed) -->
                                    <form action="{{ route('tournaments.submit', $tournament) }}" method="POST" class="border-t border-amber-800/20 pt-4 mt-4">
                                        @csrf
                                        <h5 class="font-medium text-lg mb-3 text-amber-400">
                                            {{ $participant->submission_url ? 'Update Your Submission' : 'Submit Your Project' }}
                                        </h5>
                                        <div class="mb-4">
                                            <label for="submission_url" class="block mb-2 text-sm font-medium text-gray-300">
                                                Project URL (GitHub, Vercel, etc.)
                                            </label>
                                            <input 
                                                type="url" 
                                                name="submission_url" 
                                                id="submission_url" 
                                                value="{{ $participant->submission_url ?? '' }}"
                                                class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                                placeholder="https://github.com/yourusername/project"
                                                required
                                            >
                                        </div>
                                        
                                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded">
                                            {{ $participant->submission_url ? 'Update Submission' : 'Submit Project' }}
                                        </button>
                                    </form>
                                @elseif($hasEnded)
                                    <div class="border-t border-amber-800/20 pt-4 mt-4">
                                        <div class="bg-gray-800 p-4 rounded-lg">
                                            <p class="text-gray-300">This tournament has ended. No further submissions or modifications are allowed.</p>
                                            
                                            @if(!$participant->submission_url)
                                                <p class="mt-2 text-red-400">You did not submit a project for this tournament.</p>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($deadlinePassed)
                                    <div class="border-t border-amber-800/20 pt-4 mt-4">
                                        <div class="bg-gray-800 p-4 rounded-lg">
                                            <p class="text-gray-300">The submission deadline for this tournament has passed. No further submissions or modifications are allowed.</p>
                                            
                                            @if(!$participant->submission_url)
                                                <p class="mt-2 text-red-400">You did not submit a project for this tournament.</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                @if(!$hasEnded && $tournament->team_size > 1)
                                    <!-- Team members update form (only if tournament hasn't ended) -->
                                    <form action="{{ route('tournaments.update-team', $tournament) }}" method="POST" class="border-t border-amber-800/20 pt-4 mt-4">
                                        @csrf
                                        @method('PUT')
                                        <h5 class="font-medium text-lg mb-3 text-amber-400">Update Team Information</h5>
                                        
                                        <div class="mb-4">
                                            <label for="team_name" class="block mb-2 text-sm font-medium text-gray-300">
                                                Team Name
                                            </label>
                                            <input 
                                                type="text" 
                                                name="team_name" 
                                                id="team_name" 
                                                class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                                value="{{ $participant->team_name ?? '' }}"
                                                required
                                            >
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="block mb-2 text-sm font-medium text-gray-300">
                                                Team Members ({{ $tournament->team_size - 1 }} additional members)
                                            </label>
                                            
                                            @for($i = 0; $i < $tournament->team_size - 1; $i++)
                                                <div class="mb-2">
                                                    <input 
                                                        type="text" 
                                                        name="team_members[]" 
                                                        class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                                        placeholder="Full name of team member {{ $i + 1 }}"
                                                        value="{{ isset($participant->team_members[$i]) ? $participant->team_members[$i] : '' }}"
                                                        required
                                                    >
                                                </div>
                                            @endfor
                                        </div>
                                        
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Update Team Information
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Tournament deadline information -->
                                <div class="mt-6 bg-gray-800 p-4 rounded-lg border border-amber-800/20">
                                    <p class="text-gray-300">
                                        <span class="font-medium text-amber-400">Submission Deadline:</span> 
                                        {{ \Carbon\Carbon::parse($tournament->deadline)->format('F j, Y, g:i a') }}
                                    </p>
                                    <p class="text-gray-300">
                                        <span class="font-medium text-amber-400">Tournament Date:</span> 
                                        {{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}
                                    </p>
                                    
                                    @if(!$deadlinePassed)
                                        @php
                                            $timeLeft = \Carbon\Carbon::parse($tournament->deadline)->diffForHumans(['parts' => 2]);
                                        @endphp
                                        <p class="mt-2 text-blue-400 font-medium">
                                            Time remaining for submission: {{ $timeLeft }}
                                        </p>
                                    @else
                                        <p class="mt-2 text-red-400 font-medium">
                                            Submission deadline has passed
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- View Participants Button - replaces the previous participants section -->
                        <div class="mt-8 border-t border-amber-800/20 pt-6">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">View Participants</h4>
                            <div class="bg-gray-700/30 p-4 rounded-lg border border-amber-800/20">
                                <p class="text-gray-300 mb-4">See who else is participating in this tournament.</p>
                                <a href="{{ route('tournaments.participants', $tournament) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                    View All Participants
                                </a>
                            </div>
                        </div>
                    @elseif($canParticipate && !$hasEnded)
                        <!-- Show registration form, only if tournament hasn't ended -->
                        <div class="bg-amber-900/10 p-6 rounded-lg mb-6 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Join This Tournament</h4>
                            
                            <form action="{{ route('tournaments.join', $tournament) }}" method="POST">
                                @csrf
                                
                                @if($tournament->team_size > 1)
                                    <div class="mb-4">
                                        <label for="team_name" class="block mb-2 text-sm font-medium text-gray-300">
                                            Team Name
                                        </label>
                                        <input 
                                            type="text" 
                                            name="team_name" 
                                            id="team_name" 
                                            class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                            required
                                        >
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block mb-2 text-sm font-medium text-gray-300">
                                            Team Members ({{ $tournament->team_size - 1 }} additional members)
                                        </label>
                                        
                                        @for($i = 1; $i < $tournament->team_size; $i++)
                                            <div class="mb-2">
                                                <input 
                                                    type="text" 
                                                    name="team_members[]" 
                                                    class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                                    placeholder="Full name of team member {{ $i }}"
                                                    required
                                                >
                                            </div>
                                        @endfor
                                    </div>
                                @endif
                                
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Register for Tournament
                                </button>
                            </form>
                        </div>
                    @elseif($hasEnded)
                        <!-- Show tournament ended message -->
                        <div class="bg-gray-700/50 p-6 rounded-lg mb-6">
                            <h4 class="font-semibold text-lg mb-2 text-amber-400">Tournament Has Ended</h4>
                            <p class="text-gray-300">This tournament was held on {{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y') }} and is now complete.</p>
                        </div>
                    @else
                        <!-- Show eligibility requirements -->
                        <div class="bg-amber-900/20 p-6 rounded-lg mb-6 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-2 text-amber-400">Eligibility Requirements</h4>
                            <p class="mb-4 text-gray-300">You do not currently meet the eligibility requirements for this tournament.</p>
                            <p class="text-gray-300">Minimum rank required: <strong class="text-amber-400">{{ $tournament->minimum_rank }}</strong></p>
                            <p class="text-gray-300">Your current rank: <strong class="text-gray-400">{{ auth()->user()->getRank() }}</strong></p>
                        </div>
                    @endif
                    
                    <!-- Replace the bottom Back button with this to match your screenshot -->
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