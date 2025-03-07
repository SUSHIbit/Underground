<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tournament Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('tournaments.index') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Tournaments
                        </a>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-2">{{ $tournament->title }}</h3>
                    <p class="text-gray-600 mb-6">Created by: {{ $tournament->creator->name }}</p>
                    
                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                <h4 class="font-semibold text-lg mb-2">Tournament Information</h4>
                                <p class="mb-2"><span class="font-medium">Date & Time:</span> {{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}</p>
                                <p class="mb-2"><span class="font-medium">Location:</span> {{ $tournament->location }}</p>
                                <p class="mb-2"><span class="font-medium">Team Size:</span> {{ $tournament->team_size }} {{ $tournament->team_size > 1 ? 'members' : 'member' }}</p>
                                <p class="mb-2"><span class="font-medium">Submission Deadline:</span> {{ \Carbon\Carbon::parse($tournament->deadline)->format('F j, Y, g:i a') }}</p>
                                <p class="mb-2"><span class="font-medium">Minimum Rank Required:</span> {{ $tournament->minimum_rank }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-lg mb-2">Judges</h4>
                                <ul class="list-disc list-inside">
                                    @foreach($tournament->judges as $judge)
                                        <li>{{ $judge->name }} - {{ $judge->role }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                <h4 class="font-semibold text-lg mb-2">Description</h4>
                                <p class="whitespace-pre-line">{{ $tournament->description }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                <h4 class="font-semibold text-lg mb-2">Eligibility</h4>
                                <p class="whitespace-pre-line">{{ $tournament->eligibility }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-2">Rules</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="whitespace-pre-line">{{ $tournament->rules }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-2">Judging Criteria</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="whitespace-pre-line">{{ $tournament->judging_criteria }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <h4 class="font-semibold text-lg mb-2">Project Submission</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="whitespace-pre-line">{{ $tournament->project_submission }}</p>
                        </div>
                    </div>
                    
                    @if($isParticipating)
                        <!-- Show submission form if already registered -->
                        <div class="bg-blue-50 p-6 rounded-lg mb-6">
                            <h4 class="font-semibold text-lg mb-4">Submit Your Project</h4>
                            
                            @php 
                                $participant = \App\Models\TournamentParticipant::where('tournament_id', $tournament->id)
                                              ->where('user_id', auth()->id())
                                              ->first();
                            @endphp
                            
                            @if($participant && $participant->submission_url)
                                <div class="mb-4">
                                    <p class="font-medium text-green-700">Your project has been submitted:</p>
                                    <a href="{{ $participant->submission_url }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $participant->submission_url }}
                                    </a>
                                </div>
                            @endif
                            
                            <form action="{{ route('tournaments.submit', $tournament) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="submission_url" class="block mb-2 text-sm font-medium text-gray-700">
                                        Project URL (GitHub, Vercel, etc.)
                                    </label>
                                    <input 
                                        type="url" 
                                        name="submission_url" 
                                        id="submission_url" 
                                        value="{{ $participant->submission_url ?? '' }}"
                                        class="w-full p-2 border border-gray-300 rounded-md"
                                        placeholder="https://github.com/yourusername/project"
                                        required
                                    >
                                </div>
                                
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Submit Project
                                </button>
                            </form>
                        </div>
                    @elseif($canParticipate)
                        <!-- Show registration form -->
                        <div class="bg-blue-50 p-6 rounded-lg mb-6">
                            <h4 class="font-semibold text-lg mb-4">Join This Tournament</h4>
                            
                            <form action="{{ route('tournaments.join', $tournament) }}" method="POST">
                                @csrf
                                
                                @if($tournament->team_size > 1)
                                    <div class="mb-4">
                                        <label for="team_name" class="block mb-2 text-sm font-medium text-gray-700">
                                            Team Name
                                        </label>
                                        <input 
                                            type="text" 
                                            name="team_name" 
                                            id="team_name" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            required
                                        >
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block mb-2 text-sm font-medium text-gray-700">
                                            Team Members ({{ $tournament->team_size - 1 }} additional members)
                                        </label>
                                        
                                        @for($i = 1; $i < $tournament->team_size; $i++)
                                            <div class="mb-2">
                                                <input 
                                                    type="text" 
                                                    name="team_members[]" 
                                                    class="w-full p-2 border border-gray-300 rounded-md"
                                                    placeholder="Full name of team member {{ $i }}"
                                                    required
                                                >
                                            </div>
                                        @endfor
                                    </div>
                                @endif
                                
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Register for Tournament
                                </button>
                            </form>
                        </div>
                        @else
                        <!-- Show eligibility requirements -->
                        <div class="bg-yellow-50 p-6 rounded-lg mb-6">
                            <h4 class="font-semibold text-lg mb-2 text-yellow-800">Eligibility Requirements</h4>
                            <p class="mb-4">You do not currently meet the eligibility requirements for this tournament.</p>
                            <p>Minimum rank required: <strong>{{ $tournament->minimum_rank }}</strong></p>
                            <p>Your current rank: <strong>{{ auth()->user()->getRank() }}</strong></p>
                        </div>
                    @endif
                    
                    <div>
                        <a href="{{ route('tournaments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Back to Tournaments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>