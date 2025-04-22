<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Tournament Participants') }}
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
                        <h3 class="text-xl font-bold mb-2">{{ $tournament->title }} - Participants</h3>
                        <p class="text-gray-400">
                            Total Participants: <span class="text-amber-400">{{ $participants->count() }}</span>
                        </p>
                    </div>
                    
                    @if($participants->count() > 0)
                        <div class="mb-6">
                            <!-- Your Participation Section -->
                            <div class="mb-8 bg-amber-900/10 p-6 rounded-lg border border-amber-800/20">
                                <h4 class="font-semibold text-lg mb-4 text-amber-400">Your Participation</h4>
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
                                    
                                    @if($tournament->team_size > 1 && $userParticipant->team_name)
                                        <div class="mb-4">
                                            <p class="font-medium text-gray-300">Team: <span class="text-amber-400">{{ $userParticipant->team_name }}</span></p>
                                            
                                            @if(isset($userParticipant->team_members) && is_array($userParticipant->team_members) && count($userParticipant->team_members) > 0)
                                                <p class="font-medium text-gray-300 mt-2">Members:</p>
                                                <ul class="list-disc list-inside ml-4 text-amber-400">
                                                    @foreach($userParticipant->team_members as $member)
                                                        <li>{{ $member }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if($hasEnded && $userParticipant->score !== null)
                                        <div class="mt-4">
                                            <p class="font-medium text-gray-300">Your Score: 
                                                <span class="font-medium {{ $userParticipant->score >= 7 ? 'text-green-400' : ($userParticipant->score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                                    {{ $userParticipant->score }}/10
                                                </span>
                                            </p>
                                        </div>
                                    @endif
                                    
                                    @if($userParticipant->submission_url)
                                        <div class="mt-4">
                                            <p class="font-medium text-gray-300">Your Submission:</p>
                                            <a href="{{ $userParticipant->submission_url }}" target="_blank" class="text-blue-400 hover:underline">
                                                {{ $userParticipant->submission_url }}
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-gray-400">You are not registered for this tournament.</p>
                                @endif
                            </div>
                            
                            <!-- Other Participants Section -->
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">All Participants</h4>
                            
                            @if($participants->where('user_id', '!=', auth()->id())->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($participants->where('user_id', '!=', auth()->id()) as $participant)
                                        <div class="p-4 border border-amber-800/20 rounded-md bg-gray-800 shadow-md">
                                            <div class="flex items-center mb-3">
                                                <div class="w-10 h-10 rounded-full bg-amber-900/50 flex items-center justify-center text-amber-400 font-bold mr-3">
                                                    {{ substr($participant->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-medium text-amber-400">{{ $participant->user->name }}</p>
                                                    <p class="text-sm text-gray-400">{{ $participant->user->getRank() }}</p>
                                                </div>
                                            </div>
                                            
                                            @if($tournament->team_size > 1 && $participant->team_name)
                                                <div class="mb-3 border-t border-amber-800/20 pt-3 mt-3">
                                                    <p class="text-sm font-medium text-gray-300">Team: <span class="text-white">{{ $participant->team_name }}</span></p>
                                                    
                                                    @if(isset($participant->team_members) && is_array($participant->team_members) && count($participant->team_members) > 0)
                                                        <p class="text-sm font-medium text-gray-300 mt-2">Members:</p>
                                                        <ul class="list-disc list-inside ml-2 text-sm text-gray-400">
                                                            @foreach($participant->team_members as $member)
                                                                <li>{{ $member }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <div class="border-t border-amber-800/20 pt-3 mt-3">
                                                @if($hasEnded && $participant->score !== null)
                                                    <div class="mb-2">
                                                        <span class="text-sm font-medium text-gray-300">Score: </span>
                                                        <span class="text-sm font-medium {{ $participant->score >= 7 ? 'text-green-400' : ($participant->score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                                            {{ $participant->score }}/10
                                                        </span>
                                                    </div>
                                                @endif
                                                
                                                @if($participant->submission_url && $hasEnded)
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-300">Project: </span>
                                                        <a href="{{ $participant->submission_url }}" target="_blank" class="text-sm text-blue-400 hover:underline truncate block">
                                                            {{ $participant->submission_url }}
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