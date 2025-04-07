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
                            <h4 class="font-semibold text-lg mb-4">Your Registration</h4>
                            
                            @if(isset($participant) && $participant)
                                <div class="mb-4">
                                    @if($tournament->team_size > 1)
                                        <div class="mb-3">
                                            <p class="font-medium text-gray-700">Team Name: 
                                                <span class="text-gray-900">{{ $participant->team_name ?? 'Not specified' }}</span>
                                            </p>
                                            
                                            @if(isset($participant->team_members) && is_array($participant->team_members) && count($participant->team_members) > 0)
                                                <p class="font-medium text-gray-700 mt-2">Team Members:</p>
                                                <ul class="list-disc list-inside ml-4 text-gray-900">
                                                    @foreach($participant->team_members as $member)
                                                        <li>{{ $member }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if($participant->submission_url)
                                        <div class="mb-4">
                                            <p class="font-medium text-green-700">Your project has been submitted:</p>
                                            <a href="{{ $participant->submission_url }}" target="_blank" class="text-blue-600 hover:underline">
                                                {{ $participant->submission_url }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Project submission form -->
                                <form action="{{ route('tournaments.submit', $tournament) }}" method="POST" class="border-t pt-4 mt-4">
                                    @csrf
                                    <h5 class="font-medium text-lg mb-3">
                                        {{ $participant->submission_url ? 'Update Your Submission' : 'Submit Your Project' }}
                                    </h5>
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
                                        {{ $participant->submission_url ? 'Update Submission' : 'Submit Project' }}
                                    </button>
                                </form>
                                
                                @if($tournament->team_size > 1 && now() < \Carbon\Carbon::parse($tournament->date_time))
                                    <!-- Team members update form -->
                                    <form action="{{ route('tournaments.update-team', $tournament) }}" method="POST" class="border-t pt-4 mt-4">
                                        @csrf
                                        @method('PUT')
                                        <h5 class="font-medium text-lg mb-3">Update Team Information</h5>
                                        
                                        <div class="mb-4">
                                            <label for="team_name" class="block mb-2 text-sm font-medium text-gray-700">
                                                Team Name
                                            </label>
                                            <input 
                                                type="text" 
                                                name="team_name" 
                                                id="team_name" 
                                                class="w-full p-2 border border-gray-300 rounded-md"
                                                value="{{ $participant->team_name ?? '' }}"
                                                required
                                            >
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                                Team Members ({{ $tournament->team_size - 1 }} additional members)
                                            </label>
                                            
                                            @for($i = 0; $i < $tournament->team_size - 1; $i++)
                                                <div class="mb-2">
                                                    <input 
                                                        type="text" 
                                                        name="team_members[]" 
                                                        class="w-full p-2 border border-gray-300 rounded-md"
                                                        placeholder="Full name of team member {{ $i + 1 }}"
                                                        value="{{ isset($participant->team_members[$i]) ? $participant->team_members[$i] : '' }}"
                                                        required
                                                    >
                                                </div>
                                            @endfor
                                        </div>
                                        
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Update Team Information
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Tournament deadline information -->
                                <div class="mt-6 bg-gray-100 p-4 rounded-lg">
                                    <p class="text-gray-700">
                                        <span class="font-medium">Submission Deadline:</span> 
                                        {{ \Carbon\Carbon::parse($tournament->deadline)->format('F j, Y, g:i a') }}
                                    </p>
                                    <p class="text-gray-700">
                                        <span class="font-medium">Tournament Date:</span> 
                                        {{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}
                                    </p>
                                    
                                    @php
                                        $timeLeft = \Carbon\Carbon::parse($tournament->deadline)->diffForHumans(['parts' => 2]);
                                    @endphp
                                    <p class="mt-2 {{ \Carbon\Carbon::parse($tournament->deadline)->isPast() ? 'text-red-600' : 'text-blue-600' }} font-medium">
                                        {{ \Carbon\Carbon::parse($tournament->deadline)->isPast() 
                                            ? 'Submission deadline has passed' 
                                            : "Time remaining for submission: {$timeLeft}" }}
                                    </p>
                                </div>
                            @endif
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