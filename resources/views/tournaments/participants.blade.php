<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            @if($hasEnded)
                {{ __('Tournament Results') }}
            @else
                {{ __('Tournament Participants') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    <div class="mb-6">
                        <a href="{{ route('tournaments.show', $tournament) }}" class="text-amber-400 hover:text-amber-300">
                            &larr; Back to Tournament Details
                        </a>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-xl font-bold mb-2">{{ $tournament->title }} - @if($hasEnded) Results @else Participants @endif</h3>
                        <div class="flex items-center gap-4">
                            <p class="text-gray-400">
                                Total Participants: <span class="text-amber-400">{{ $participants->count() }}</span>
                            </p>
                            @if($hasEnded)
                                <span class="px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-sm">Tournament Completed</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($participants->count() > 0)
                        <div class="mb-6">
                            <!-- Your Participation Section -->
                            <div class="mb-8 bg-amber-900/10 p-6 rounded-lg border border-amber-800/20">
                                <h4 class="font-semibold text-lg mb-4 text-amber-400">
                                    @if($hasEnded) Your Results @else Your Participation @endif
                                </h4>
                                @if($userParticipant)
                                    <div class="flex items-center mb-4">
                                        <div class="w-10 h-10 rounded-full bg-amber-900/50 flex items-center justify-center text-amber-400 font-bold mr-3">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-amber-400">{{ auth()->user()->name }}</p>
                                            <p class="text-sm text-gray-400">{{ auth()->user()->getRank() }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($tournament->team_size > 1 && $userParticipant->team)
                                        <div class="mb-4">
                                            <p class="font-medium text-gray-300">Team: <span class="text-amber-400">{{ $userParticipant->team->name }}</span></p>
                                            
                                            @php
                                                $teamMembers = $userParticipant->team->participants()->with('user')->get();
                                            @endphp
                                            
                                            @if($teamMembers->count() > 0)
                                                <p class="font-medium text-gray-300 mt-2">Team Members:</p>
                                                <ul class="list-disc list-inside ml-4 text-amber-400">
                                                    @foreach($teamMembers as $member)
                                                        <li>{{ $member->user->name }} @if($member->user_id === $userParticipant->team->leader_id) (Leader) @endif</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if($hasEnded && $userParticipant->score !== null)
                                        <div class="mt-4 p-4 bg-gray-800 rounded-lg">
                                            <p class="font-medium text-gray-300 mb-2">Your Score: 
                                                <span class="text-2xl font-bold {{ $userParticipant->score >= 7 ? 'text-green-400' : ($userParticipant->score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                                    {{ $userParticipant->score }}/10
                                                </span>
                                            </p>
                                            
                                            @if($userParticipant->feedback)
                                                <div class="mt-3">
                                                    <p class="font-medium text-gray-300 mb-1">Feedback:</p>
                                                    <p class="text-gray-300 whitespace-pre-line bg-gray-700/50 p-3 rounded">{{ $userParticipant->feedback }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if($userParticipant->submission_url)
                                        <div class="mt-4">
                                            <p class="font-medium text-gray-300">Your Submission:</p>
                                            <a href="{{ $userParticipant->submission_url }}" target="_blank" class="text-blue-400 hover:underline break-all">
                                                {{ $userParticipant->submission_url }}
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-gray-400">You are not registered for this tournament.</p>
                                @endif
                            </div>
                            
                            <!-- Other Participants/Results Section - Only show for team tournaments or ongoing solo tournaments -->
                            @if($tournament->team_size > 1 || !$hasEnded)
                                <h4 class="font-semibold text-lg mb-4 text-amber-400">
                                    @if($hasEnded) 
                                        All Results 
                                        @if($participants->where('score', '!=', null)->count() > 0)
                                            (Ranked by Score)
                                        @endif
                                    @else 
                                        All Participants 
                                    @endif
                                </h4>
                                
                                @php
                                    $otherParticipants = $participants->where('user_id', '!=', auth()->id());
                                    
                                    // If tournament has ended, sort by score (descending)
                                    if($hasEnded) {
                                        $otherParticipants = $otherParticipants->sortByDesc('score');
                                    }
                                @endphp
                                
                                @if($otherParticipants->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($otherParticipants as $index => $participant)
                                            <div class="p-4 border border-amber-800/20 rounded-md bg-gray-800 shadow-md {{ $hasEnded && $participant->score !== null && $participant->score >= 8 ? 'border-green-400/30 bg-green-900/5' : '' }}">
                                                <!-- Add ranking number if tournament ended and has scores -->
                                                @if($hasEnded && $participant->score !== null)
                                                    <div class="flex justify-between items-start mb-3">
                                                        <span class="text-xs px-2 py-1 rounded-full {{ $index === 0 ? 'bg-yellow-600 text-yellow-100' : ($index === 1 ? 'bg-gray-400 text-gray-900' : ($index === 2 ? 'bg-amber-600 text-amber-100' : 'bg-gray-600 text-gray-300')) }}">
                                                            #{{ $index + 1 + ($userParticipant && $userParticipant->score !== null && $userParticipant->score > $participant->score ? 0 : 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex items-center mb-3">
                                                    <div class="w-10 h-10 rounded-full bg-amber-900/50 flex items-center justify-center text-amber-400 font-bold mr-3">
                                                        {{ substr($participant->user->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-amber-400">{{ $participant->user->name }}</p>
                                                        <p class="text-sm text-gray-400">{{ $participant->user->getRank() }}</p>
                                                    </div>
                                                </div>
                                                
                                                @if($tournament->team_size > 1 && $participant->team)
                                                    <div class="mb-3 border-t border-amber-800/20 pt-3 mt-3">
                                                        <p class="text-sm font-medium text-gray-300">Team: <span class="text-white">{{ $participant->team->name }}</span></p>
                                                        
                                                        @php
                                                            $teamMembers = $participant->team->participants()->with('user')->get();
                                                        @endphp
                                                        
                                                        @if($teamMembers->count() > 1)
                                                            <p class="text-sm font-medium text-gray-300 mt-1">Members:</p>
                                                            <ul class="list-disc list-inside ml-2 text-sm text-gray-400">
                                                                @foreach($teamMembers->take(3) as $member)
                                                                    <li>{{ $member->user->name }}@if($member->user_id === $participant->team->leader_id) (L) @endif</li>
                                                                @endforeach
                                                                @if($teamMembers->count() > 3)
                                                                    <li class="text-gray-500">+{{ $teamMembers->count() - 3 }} more</li>
                                                                @endif
                                                            </ul>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                <div class="border-t border-amber-800/20 pt-3 mt-3">
                                                    @if($hasEnded && $participant->score !== null)
                                                        <div class="mb-2">
                                                            <span class="text-sm font-medium text-gray-300">Score: </span>
                                                            <span class="text-lg font-bold {{ $participant->score >= 7 ? 'text-green-400' : ($participant->score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                                                {{ $participant->score }}/10
                                                            </span>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($participant->submission_url && ($hasEnded || auth()->user()->role === 'judge'))
                                                        <div>
                                                            <span class="text-sm font-medium text-gray-300">Project: </span>
                                                            <a href="{{ $participant->submission_url }}" target="_blank" class="text-sm text-blue-400 hover:underline truncate block">
                                                                {{ Str::limit($participant->submission_url, 40) }}
                                                            </a>
                                                        </div>
                                                    @elseif($hasEnded)
                                                        <div class="text-sm text-gray-500">No submission</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="bg-gray-700/30 p-4 rounded-lg border border-amber-800/20">
                                        <p class="text-gray-400">You are the only participant registered for this tournament.</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @else
                        <div class="bg-gray-700/30 p-6 rounded-lg border border-amber-800/20">
                            <p class="text-gray-400">No participants have registered for this tournament yet.</p>
                        </div>
                    @endif
                    
                    <div class="mt-8 mb-4 border-t border-amber-800/20 pt-6 flex">
                        <a href="{{ route('tournaments.show', $tournament) }}" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            Back to Tournament
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>