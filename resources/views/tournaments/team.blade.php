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
                            <!-- Team Leader -->
                            <div class="flex items-center p-3 bg-amber-900/10 rounded-lg">
                                <div class="w-12 h-12 rounded-full bg-amber-600 flex items-center justify-center text-white font-bold mr-4">
                                    {{ substr($team->leader->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-amber-400">{{ $team->leader->name }} (@{{ $team->leader->username }})</p>
                                    <p class="text-sm text-gray-400">Team Leader • {{ $team->leader->getRank() }}</p>
                                </div>
                                @if($isLeader)
                                    <span class="ml-4 px-2 py-1 bg-amber-900/20 text-amber-400 text-xs rounded-full">You</span>
                                @endif
                            </div>
                            
                            <!-- Team Members -->
                            @foreach($team->participants as $participant)
                                @if($participant->user_id !== $team->leader_id)
                                    <div class="flex items-center p-3 bg-gray-800 rounded-lg">
                                        <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center text-white font-bold mr-4">
                                            {{ substr($participant->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-white">{{ $participant->user->name }} (@{{ $participant->user->username }})</p>
                                            <p class="text-sm text-gray-400">Team Member • {{ $participant->user->getRank() }}</p>
                                        </div>
                                        @if($participant->user_id === auth()->id())
                                            <span class="ml-4 px-2 py-1 bg-gray-700/50 text-gray-300 text-xs rounded-full">You</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    @if($isLeader && $pendingInvitations && $pendingInvitations->count() > 0)
                        <!-- Pending Invitations (Only shown to team leader) -->
                        <div class="bg-gray-700/20 rounded-lg p-6 mb-8 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Pending Invitations</h4>
                            
                            <div class="space-y-3">
                                @foreach($pendingInvitations as $invitation)
                                    <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-white font-bold mr-3">
                                                {{ substr($invitation->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-white">{{ $invitation->user->name }} (@{{ $invitation->user->username }})</p>
                                                <p class="text-xs text-gray-400">Invited {{ $invitation->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="px-2 py-1 bg-amber-900/20 text-amber-400 text-xs rounded-full">
                                                Expires {{ $invitation->expires_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Project Submission Form -->
                    @if(!$tournament->hasEnded())
                        <div class="bg-amber-900/10 rounded-lg p-6 mb-8 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Project Submission</h4>
                            
                            @if(\Carbon\Carbon::parse($tournament->deadline)->isPast())
                                <!-- Deadline has passed -->
                                <div class="bg-red-900/20 p-4 rounded-lg text-red-400 mb-4">
                                    <p>The submission deadline has passed. No further submissions are allowed.</p>
                                </div>
                                
                                @if($team->participants->where('user_id', auth()->id())->first()->submission_url)
                                    <div class="mt-4">
                                        <h5 class="font-medium text-white mb-2">Your Submitted Project:</h5>
                                        <a href="{{ $team->participants->where('user_id', auth()->id())->first()->submission_url }}" 
                                           target="_blank" 
                                           class="text-blue-400 hover:underline break-all">
                                            {{ $team->participants->where('user_id', auth()->id())->first()->submission_url }}
                                        </a>
                                    </div>
                                @else
                                    <p class="text-gray-400">You did not submit a project before the deadline.</p>
                                @endif
                            @else
                                <!-- Show submission form if deadline not passed -->
                                <p class="text-gray-300 mb-4">Submit your project URL below. You can update this at any time until the deadline.</p>
                                
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
                                            value="{{ $team->participants->where('user_id', auth()->id())->first()->submission_url ?? '' }}"
                                            class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                            placeholder="https://github.com/yourusername/project"
                                            required
                                        >
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded">
                                            @if($team->participants->where('user_id', auth()->id())->first()->submission_url) 
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
                            @endif
                        </div>
                    @else
                        <!-- Tournament has ended -->
                        <div class="bg-gray-700/20 rounded-lg p-6 mb-8 border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Tournament Results</h4>
                            
                            @if($team->participants->where('user_id', auth()->id())->first()->submission_url)
                                <div class="mb-4">
                                    <h5 class="font-medium text-white mb-2">Your Submitted Project:</h5>
                                    <a href="{{ $team->participants->where('user_id', auth()->id())->first()->submission_url }}" 
                                       target="_blank" 
                                       class="text-blue-400 hover:underline break-all">
                                        {{ $team->participants->where('user_id', auth()->id())->first()->submission_url }}
                                    </a>
                                </div>
                                
                                @if($team->participants->where('user_id', auth()->id())->first()->score !== null)
                                    <div class="mt-6 p-4 bg-gray-800 rounded-lg">
                                        <h5 class="font-medium text-amber-400 mb-2">Judge's Score:</h5>
                                        <p class="text-3xl font-bold {{ $team->participants->where('user_id', auth()->id())->first()->score >= 7 ? 'text-green-400' : ($team->participants->where('user_id', auth()->id())->first()->score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                            {{ $team->participants->where('user_id', auth()->id())->first()->score }}/10
                                        </p>
                                        
                                        @if($team->participants->where('user_id', auth()->id())->first()->feedback)
                                            <div class="mt-4">
                                                <h6 class="font-medium text-white mb-1">Feedback:</h6>
                                                <p class="text-gray-300 whitespace-pre-line">{{ $team->participants->where('user_id', auth()->id())->first()->feedback }}</p>
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