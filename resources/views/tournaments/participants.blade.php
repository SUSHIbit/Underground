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
                            @if($tournament->team_size > 1)
                                @php
                                    $totalTeams = $participants->where('team_id', '!=', null)->groupBy('team_id')->count();
                                    $totalIndividualParticipants = $participants->count();
                                @endphp
                                <p class="text-gray-400">
                                    Total Teams: <span class="text-amber-400">{{ $totalTeams }}</span>
                                </p>
                                <p class="text-gray-400">
                                    Total Participants: <span class="text-amber-400">{{ $totalIndividualParticipants }}</span>
                                </p>
                            @else
                                <p class="text-gray-400">
                                    Total Participants: <span class="text-amber-400">{{ $participants->count() }}</span>
                                </p>
                            @endif
                            @if($hasEnded)
                                <span class="px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-sm">Tournament Completed</span>
                                @if($tournament->isGradingComplete())
                                    <span class="px-3 py-1 bg-green-700 text-green-300 rounded-full text-sm">Results Available</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    @if($participants->count() > 0)
                        <!-- Your Results Section -->
                        <div class="mb-8 bg-amber-900/10 p-6 rounded-lg border border-amber-800/20">
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">
                                @if($hasEnded) Your Results @else Your Participation @endif
                            </h4>
                            @if($userParticipant)
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 rounded-full {{ $userParticipant->isTopThree() ? $userParticipant->rank_bg_color : 'bg-amber-900/50' }} flex items-center justify-center text-white font-bold mr-4">
                                        @if($userParticipant->tournament_rank)
                                            {{ $userParticipant->tournament_rank }}
                                        @else
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-amber-400">{{ auth()->user()->name }}</p>
                                        <p class="text-sm text-gray-400">{{ auth()->user()->getRank() }}</p>
                                        @if($hasEnded && $tournament->isGradingComplete() && $userParticipant->tournament_rank)
                                            @if($tournament->team_size > 1)
                                                <p class="text-sm {{ $userParticipant->rank_color }} font-medium">
                                                    @if($userParticipant->tournament_rank <= 3)
                                                        🎉 Your team placed {{ $userParticipant->rank_display }}!
                                                    @else
                                                        Your team placed {{ $userParticipant->rank_display }}
                                                    @endif
                                                    @php
                                                        $totalTeamsRanked = $tournament->participants()->whereNotNull('tournament_rank')->groupBy('team_id')->count();
                                                    @endphp
                                                    <span class="text-gray-400 text-xs">(out of {{ $totalTeamsRanked }} teams)</span>
                                                </p>
                                            @else
                                                <p class="text-sm {{ $userParticipant->rank_color }} font-medium">
                                                    @if($userParticipant->tournament_rank <= 3)
                                                        🎉 Congratulations! You placed {{ $userParticipant->rank_display }}!
                                                    @else
                                                        You placed {{ $userParticipant->rank_display }}
                                                    @endif
                                                    @php
                                                        $totalParticipantsRanked = $tournament->participants()->whereNotNull('tournament_rank')->count();
                                                    @endphp
                                                    <span class="text-gray-400 text-xs">(out of {{ $totalParticipantsRanked }} participants)</span>
                                                </p>
                                            @endif
                                        @endif
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
                                        <p class="font-medium text-gray-300 mb-2">{{ $tournament->team_size > 1 ? 'Team' : 'Your' }} Score: 
                                            <span class="text-2xl font-bold {{ $userParticipant->score >= 7 ? 'text-green-400' : ($userParticipant->score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                                {{ $userParticipant->score }}/10
                                            </span>
                                        </p>
                                        
                                        @if($tournament->isGradingComplete() && $userParticipant->ue_points_awarded)
                                            <div class="mt-3 p-3 bg-blue-900/20 rounded-lg border border-blue-800/20">
                                                <p class="font-medium text-blue-400 mb-1">UEPoints Earned:</p>
                                                <div class="flex items-center space-x-4">
                                                    <span class="text-xl font-bold text-blue-400">{{ $userParticipant->ue_points_awarded }} UEPoints</span>
                                                    @if($userParticipant->tournament_rank)
                                                        <span class="text-sm text-gray-400">
                                                            ({{ $userParticipant->ue_points_awarded - 2 }} for {{ $userParticipant->rank_display }} place + 2 participation)
                                                        </span>
                                                    @else
                                                        <span class="text-sm text-gray-400">(2 participation points)</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
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
                                        <p class="font-medium text-gray-300">{{ $tournament->team_size > 1 ? 'Team' : 'Your' }} Submission:</p>
                                        <a href="{{ $userParticipant->submission_url }}" target="_blank" class="text-blue-400 hover:underline break-all">
                                            {{ $userParticipant->submission_url }}
                                        </a>
                                    </div>
                                @endif
                            @else
                                <p class="text-gray-400">You are not registered for this tournament.</p>
                            @endif
                        </div>
                        
                        <!-- Tournament Results Section -->
                        @if($hasEnded && $tournament->isGradingComplete())
                            @php
                                $topThree = $tournament->getTopThreeParticipants();
                                $allRanked = $tournament->getRankedParticipants();
                                $otherParticipants = $allRanked->where('user_id', '!=', auth()->id());
                            @endphp
                            
                            @if($topThree->count() > 0)
                                <!-- Top 3 Podium Display -->
                                <div class="mb-8">
                                    <h4 class="font-semibold text-lg mb-6 text-amber-400 text-center">
                                        🏆 Tournament Champions 🏆
                                        @if($tournament->team_size > 1)
                                            <span class="block text-sm text-gray-400 mt-1">(Team Rankings)</span>
                                        @endif
                                    </h4>
                                    
                                    <div class="flex justify-center items-end space-x-4 mb-8">
                                        @foreach([2, 1, 3] as $position)
                                            @php $winner = $topThree->where('tournament_rank', $position)->first(); @endphp
                                            @if($winner)
                                                <div class="text-center {{ $position === 1 ? 'order-2' : ($position === 2 ? 'order-1' : 'order-3') }}">
                                                    <!-- Podium -->
                                                    <div class="flex flex-col items-center">
                                                        <!-- Winner Info -->
                                                        <div class="mb-4 text-center">
                                                            <div class="w-16 h-16 rounded-full {{ $winner->rank_bg_color }} flex items-center justify-center text-white font-bold text-xl mb-2 mx-auto">
                                                                {{ $position }}
                                                            </div>
                                                            @if($tournament->team_size > 1 && $winner->team)
                                                                <p class="font-bold {{ $winner->rank_color }} text-lg">{{ $winner->team->name }}</p>
                                                                <p class="text-sm text-gray-400">{{ $winner->user->name }} (Leader)</p>
                                                            @else
                                                                <p class="font-bold {{ $winner->rank_color }} text-lg">{{ $winner->user->name }}</p>
                                                            @endif
                                                            <p class="text-sm text-gray-300">{{ $winner->score }}/10</p>
                                                            <p class="text-xs {{ $winner->rank_color }}">+{{ $winner->ue_points_awarded }} UEPoints</p>
                                                        </div>
                                                        
                                                        <!-- Podium Base -->
                                                        <div class="{{ $winner->rank_bg_color }} rounded-t-lg {{ $position === 1 ? 'h-24 w-20' : ($position === 2 ? 'h-20 w-18' : 'h-16 w-16') }} flex items-end justify-center pb-2">
                                                            <span class="text-white font-bold text-sm">{{ $position === 1 ? '1st' : ($position === 2 ? '2nd' : '3rd') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <!-- All Results Table -->
                            @if($otherParticipants->count() > 0)
                                <h4 class="font-semibold text-lg mb-4 text-amber-400">
                                    All Tournament Results
                                    @if($tournament->team_size > 1)
                                        <span class="text-sm text-gray-400 font-normal">(Showing one representative per team)</span>
                                    @endif
                                </h4>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-amber-800/20">
                                        <thead class="bg-gray-900">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Rank</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                                    {{ $tournament->team_size > 1 ? 'Team / Representative' : 'Participant' }}
                                                </th>
                                                @if($tournament->team_size > 1)
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Team Members</th>
                                                @endif
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Score</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">UEPoints Earned</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Submission</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-gray-800 divide-y divide-amber-800/20">
                                            @php
                                                // For team tournaments, group by team and show only one representative per team
                                                if ($tournament->team_size > 1) {
                                                    $displayParticipants = $otherParticipants->groupBy('team_id')->map(function($teamMembers) {
                                                        // Show team leader as representative, or first member if no leader
                                                        return $teamMembers->sortBy(function($participant) {
                                                            return $participant->role === 'leader' ? 0 : 1;
                                                        })->first();
                                                    })->sortBy('tournament_rank')->values();
                                                } else {
                                                    $displayParticipants = $otherParticipants;
                                                }
                                            @endphp
                                            
                                            @foreach($displayParticipants as $participant)
                                                <tr class="{{ $participant->isTopThree() ? 'bg-amber-900/5' : '' }}">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <div class="flex items-center">
                                                            <div class="w-8 h-8 rounded-full {{ $participant->isTopThree() ? $participant->rank_bg_color : 'bg-gray-600' }} flex items-center justify-center text-white font-bold text-sm mr-3">
                                                                {{ $participant->tournament_rank }}
                                                            </div>
                                                            <span class="{{ $participant->rank_color }} font-medium">{{ $participant->rank_display }}</span>
                                                            @if($tournament->team_size > 1)
                                                                @php
                                                                    $totalTeamsRanked = $tournament->participants()->whereNotNull('tournament_rank')->groupBy('team_id')->count();
                                                                @endphp
                                                                <span class="text-gray-400 text-xs ml-1">(of {{ $totalTeamsRanked }} teams)</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="w-10 h-10 rounded-full bg-amber-900/50 flex items-center justify-center text-amber-400 font-bold mr-3">
                                                                {{ substr($participant->user->name, 0, 1) }}
                                                            </div>
                                                            <div>
                                                                @if($tournament->team_size > 1 && $participant->team)
                                                                    <p class="font-medium text-amber-400">{{ $participant->team->name }}</p>
                                                                    <p class="text-sm text-gray-400">{{ $participant->user->name }} ({{ $participant->role === 'leader' ? 'Leader' : 'Member' }})</p>
                                                                @else
                                                                    <p class="font-medium text-amber-400">{{ $participant->user->name }}</p>
                                                                    <p class="text-sm text-gray-400">{{ $participant->user->getRank() }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    @if($tournament->team_size > 1)
                                                        <td class="px-6 py-4 text-sm text-gray-400">
                                                            @if($participant->team)
                                                                @php
                                                                    $teamMembers = $participant->team->participants()->with('user')->get();
                                                                @endphp
                                                                <div class="max-w-xs">
                                                                    @foreach($teamMembers as $index => $member)
                                                                        @if($index < 3)
                                                                            <div class="text-xs">{{ $member->user->name }}@if($member->user_id === $participant->team->leader_id) ⭐@endif</div>
                                                                        @elseif($index === 3)
                                                                            <div class="text-xs text-gray-500">+{{ $teamMembers->count() - 3 }} more...</div>
                                                                            @break
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <span class="text-gray-500">No team</span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <span class="font-bold {{ $participant->score >= 7 ? 'text-green-400' : ($participant->score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                                            {{ $participant->score }}/10
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <div class="flex items-center">
                                                            <span class="font-bold text-blue-400">{{ $participant->ue_points_awarded }}</span>
                                                            <span class="text-gray-400 ml-1">UEPoints</span>
                                                            @if($tournament->team_size > 1)
                                                                <span class="text-gray-500 text-xs ml-1">(each member)</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        @if($participant->submission_url)
                                                            <a href="{{ $participant->submission_url }}" target="_blank" class="text-blue-400 hover:underline">
                                                                View Project
                                                            </a>
                                                        @else
                                                            <span class="text-gray-500">No submission</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @elseif($hasEnded)
                            <!-- Tournament ended but results not available yet -->
                            <div class="mb-8 bg-amber-900/20 p-6 rounded-lg border border-amber-800/20">
                                <h4 class="font-semibold text-lg mb-2 text-amber-400">Tournament Results Pending</h4>
                                <p class="text-gray-300">The tournament has ended, but judges are still grading submissions. Results will be available once all judges complete their evaluations.</p>
                            </div>
                        @else
                            <!-- Show current participants for ongoing tournament -->
                            <h4 class="font-semibold text-lg mb-4 text-amber-400">Current {{ $tournament->team_size > 1 ? 'Teams' : 'Participants' }}</h4>
                            
                            @php
                                $otherParticipants = $participants->where('user_id', '!=', auth()->id());
                                
                                // For team tournaments, group by teams
                                if ($tournament->team_size > 1) {
                                    $teams = $otherParticipants->groupBy('team_id')->map(function($teamMembers, $teamId) {
                                        if ($teamId) {
                                            return [
                                                'team' => $teamMembers->first()->team,
                                                'members' => $teamMembers,
                                                'leader' => $teamMembers->where('role', 'leader')->first(),
                                                'submission_url' => $teamMembers->first()->submission_url
                                            ];
                                        }
                                        return null;
                                    })->filter();
                                }
                            @endphp
                            
                            @if($tournament->team_size > 1 && isset($teams) && $teams->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($teams as $teamData)
                                        <div class="p-4 border border-amber-800/20 rounded-md bg-gray-800 shadow-md">
                                            <div class="mb-3">
                                                <h5 class="font-medium text-amber-400 text-lg">{{ $teamData['team']->name }}</h5>
                                                <p class="text-sm text-gray-400">{{ $teamData['members']->count() }} member(s)</p>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <p class="text-sm font-medium text-gray-300 mb-2">Team Members:</p>
                                                @foreach($teamData['members'] as $member)
                                                    <div class="flex items-center mb-1">
                                                        <div class="w-6 h-6 rounded-full {{ $member->role === 'leader' ? 'bg-amber-600' : 'bg-gray-700' }} flex items-center justify-center text-white text-xs font-bold mr-2">
                                                            {{ substr($member->user->name, 0, 1) }}
                                                        </div>
                                                        <span class="text-sm {{ $member->role === 'leader' ? 'text-amber-400' : 'text-gray-300' }}">
                                                            {{ $member->user->name }}
                                                            @if($member->role === 'leader') (Leader) @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            <div class="border-t border-amber-800/20 pt-3 mt-3">
                                                @if($teamData['submission_url'])
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-300">Submitted: </span>
                                                        <span class="text-sm text-green-400">✓ Project submitted</span>
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-500">No submission yet</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($tournament->team_size === 1 && $otherParticipants->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($otherParticipants as $participant)
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
                                            
                                            <div class="border-t border-amber-800/20 pt-3 mt-3">
                                                @if($participant->submission_url)
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-300">Submitted: </span>
                                                        <span class="text-sm text-green-400">✓ Project submitted</span>
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-500">No submission yet</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-gray-700/30 p-4 rounded-lg border border-amber-800/20">
                                    <p class="text-gray-400">You are the only {{ $tournament->team_size > 1 ? 'team' : 'participant' }} registered for this tournament.</p>
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="bg-gray-700/30 p-6 rounded-lg border border-amber-800/20">
                            <p class="text-gray-400">No {{ $tournament->team_size > 1 ? 'teams' : 'participants' }} have registered for this tournament yet.</p>
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