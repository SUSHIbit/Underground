<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Team Results') }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    <div class="mb-6">
                        <a href="{{ route('tournaments.team', $tournament) }}" class="text-amber-400 hover:text-amber-300">
                            &larr; Back to Team
                        </a>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-xl font-bold mb-2">{{ $team->name }} - Results</h3>
                        <div class="flex items-center gap-4">
                            <p class="text-gray-400">
                                Team Members: <span class="text-amber-400">{{ $teamMembers->count() }}</span>
                            </p>
                            <span class="px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-sm">Tournament Completed</span>
                        </div>
                        <p class="text-gray-400 mt-2">Tournament: {{ $tournament->title }}</p>
                    </div>
                    
                    <!-- Team Results Section -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-4 text-amber-400">Team Member Results</h4>
                        
                        @php
                            // Sort team members by score (highest first), but keep current user at top if they have results
                            $currentUserMember = $teamMembers->where('user_id', auth()->id())->first();
                            $otherMembers = $teamMembers->where('user_id', '!=', auth()->id())->sortByDesc('score');
                        @endphp
                        
                        <div class="space-y-4">
                            <!-- Current User's Results (if participating) -->
                            @if($currentUserMember)
                                <div class="bg-amber-900/10 p-6 rounded-lg border border-amber-800/20">
                                    <h5 class="font-semibold text-lg mb-4 text-amber-400">Your Results</h5>
                                    
                                    <div class="flex items-center mb-4">
                                        <div class="w-12 h-12 rounded-full {{ $currentUserMember->isTopThree() ? $currentUserMember->rank_bg_color : 'bg-amber-600' }} flex items-center justify-center text-white font-bold mr-4">
                                            @if($currentUserMember->tournament_rank)
                                                {{ $currentUserMember->tournament_rank }}
                                            @else
                                                {{ substr($currentUserMember->user->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-amber-400">{{ $currentUserMember->user->name }} ({{ $currentUserMember->user->username }})</p>
                                            <p class="text-sm text-gray-400">
                                                {{ $currentUserMember->user_id === $team->leader_id ? 'Team Leader' : 'Team Member' }} • {{ $currentUserMember->user->getRank() }}
                                            </p>
                                            @if($currentUserMember->tournament_rank && $tournament->isGradingComplete())
                                                <p class="text-sm {{ $currentUserMember->rank_color }} font-medium">
                                                    @if($currentUserMember->tournament_rank <= 3)
                                                        🎉 You placed {{ $currentUserMember->rank_display }}!
                                                    @else
                                                        You placed {{ $currentUserMember->rank_display }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                        <span class="ml-auto px-2 py-1 bg-gray-700/50 text-gray-300 text-xs rounded-full">You</span>
                                    </div>
                                    
                                    @if($currentUserMember->score !== null)
                                        <div class="mt-4 p-4 bg-gray-800 rounded-lg">
                                            <p class="font-medium text-gray-300 mb-2">Your Score: 
                                                <span class="text-2xl font-bold {{ $currentUserMember->score >= 7 ? 'text-green-400' : ($currentUserMember->score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                                    {{ $currentUserMember->score }}/10
                                                </span>
                                            </p>
                                            
                                            @if($tournament->isGradingComplete() && $currentUserMember->ue_points_awarded)
                                                <div class="mt-3 p-3 bg-blue-900/20 rounded-lg border border-blue-800/20">
                                                    <p class="font-medium text-blue-400 mb-1">UEPoints Earned:</p>
                                                    <div class="flex items-center space-x-4">
                                                        <span class="text-xl font-bold text-blue-400">{{ $currentUserMember->ue_points_awarded }} UEPoints</span>
                                                        @if($currentUserMember->tournament_rank)
                                                            <span class="text-sm text-gray-400">
                                                                ({{ $currentUserMember->ue_points_awarded - 2 }} for {{ $currentUserMember->rank_display }} place + 2 participation)
                                                            </span>
                                                        @else
                                                            <span class="text-sm text-gray-400">(2 participation points)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="mt-4 p-4 bg-gray-700/30 rounded-lg">
                                            <p class="text-gray-400">Your submission has not been judged yet.</p>
                                        </div>
                                    @endif
                                    
                                    @if($currentUserMember->submission_url)
                                        <div class="mt-4">
                                            <p class="font-medium text-gray-300">Your Submission:</p>
                                            <a href="{{ $currentUserMember->submission_url }}" target="_blank" class="text-blue-400 hover:underline break-all">
                                                {{ $currentUserMember->submission_url }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Other Team Members' Results -->
                            @if($otherMembers->count() > 0)
                                <div class="bg-gray-700/20 rounded-lg p-6 border border-amber-800/20">
                                    <h5 class="font-semibold text-lg mb-4 text-amber-400">Team Members Results</h5>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($otherMembers as $member)
                                            <div class="p-4 border border-amber-800/20 rounded-md bg-gray-800 shadow-md">
                                                <div class="flex items-center mb-3">
                                                    <div class="w-10 h-10 rounded-full {{ $member->isTopThree() ? $member->rank_bg_color : ($member->user_id === $team->leader_id ? 'bg-amber-600' : 'bg-gray-700') }} flex items-center justify-center text-white font-bold mr-3">
                                                        @if($member->tournament_rank)
                                                            {{ $member->tournament_rank }}
                                                        @else
                                                            {{ substr($member->user->name, 0, 1) }}
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="font-medium {{ $member->user_id === $team->leader_id ? 'text-amber-400' : 'text-white' }}">{{ $member->user->name }}</p>
                                                        <p class="text-sm text-gray-400">
                                                            {{ $member->user_id === $team->leader_id ? 'Team Leader' : 'Team Member' }} • {{ $member->user->getRank() }}
                                                        </p>
                                                        @if($member->tournament_rank && $tournament->isGradingComplete())
                                                            <p class="text-xs {{ $member->rank_color }}">
                                                                Ranked {{ $member->rank_display }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                @if($member->score !== null)
                                                    <div class="mt-3 p-3 bg-gray-700/50 rounded-lg">
                                                        <p class="font-medium text-gray-300 mb-1">Score: 
                                                            <span class="text-lg font-bold {{ $member->score >= 7 ? 'text-green-400' : ($member->score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                                                {{ $member->score }}/10
                                                            </span>
                                                        </p>
                                                        
                                                        @if($tournament->isGradingComplete() && $member->ue_points_awarded)
                                                            <div class="mt-2">
                                                                <p class="text-sm font-medium text-blue-400">UEPoints: {{ $member->ue_points_awarded }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="mt-3 p-3 bg-gray-700/30 rounded-lg">
                                                        <p class="text-sm text-gray-400">Not judged yet</p>
                                                    </div>
                                                @endif
                                                
                                                @if($member->submission_url)
                                                    <div class="mt-3">
                                                        <p class="text-sm font-medium text-gray-300">Submission:</p>
                                                        <a href="{{ $member->submission_url }}" target="_blank" class="text-sm text-blue-400 hover:underline truncate block">
                                                            {{ Str::limit($member->submission_url, 40) }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Team Summary -->
                    @php
                        $judgedMembers = $teamMembers->where('score', '!=', null);
                        $averageScore = $judgedMembers->count() > 0 ? $judgedMembers->avg('score') : null;
                        $totalMembers = $teamMembers->count();
                        $teamRank = $currentUserMember && $currentUserMember->tournament_rank ? $currentUserMember->tournament_rank : null;
                        $teamUEPoints = $currentUserMember && $currentUserMember->ue_points_awarded ? $currentUserMember->ue_points_awarded : null;
                    @endphp
                    
                    @if($averageScore !== null)
                        <div class="bg-gray-700/20 rounded-lg p-6 mb-6 border border-amber-800/20">
                            <h5 class="font-semibold text-lg mb-4 text-amber-400">Team Summary</h5>
                            
                            @if($teamRank && $tournament->isGradingComplete())
                                <div class="mb-6 p-4 bg-blue-900/20 rounded-lg border border-blue-800/20">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-16 h-16 rounded-full {{ $teamRank <= 3 ? ($teamRank === 1 ? 'bg-yellow-600' : ($teamRank === 2 ? 'bg-gray-400' : 'bg-amber-600')) : 'bg-blue-600' }} flex items-center justify-center text-white font-bold text-xl">
                                            {{ $teamRank }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-lg {{ $teamRank <= 3 ? ($teamRank === 1 ? 'text-yellow-400' : ($teamRank === 2 ? 'text-gray-300' : 'text-amber-600')) : 'text-blue-400' }}">
                                                @if($teamRank <= 3)
                                                    🎉 Team placed {{ $teamRank === 1 ? '1st' : ($teamRank === 2 ? '2nd' : '3rd') }}!
                                                @else
                                                    Team placed {{ $teamRank }}{{ $teamRank % 10 === 1 && $teamRank % 100 !== 11 ? 'st' : ($teamRank % 10 === 2 && $teamRank % 100 !== 12 ? 'nd' : ($teamRank % 10 === 3 && $teamRank % 100 !== 13 ? 'rd' : 'th')) }}
                                                @endif
                                            </p>
                                            @if($teamUEPoints)
                                                <p class="text-blue-400">Each member earned: <span class="font-bold">{{ $teamUEPoints }} UEPoints</span></p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <p class="text-sm text-gray-400">Team Average</p>
                                    <p class="text-2xl font-bold {{ $averageScore >= 7 ? 'text-green-400' : ($averageScore >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                        {{ number_format($averageScore, 1) }}/10
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-400">Members Judged</p>
                                    <p class="text-2xl font-bold text-blue-400">{{ $judgedMembers->count() }}/{{ $totalMembers }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-400">Highest Score</p>
                                    <p class="text-2xl font-bold text-green-400">{{ $judgedMembers->max('score') ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Judge Comments Section -->
                    @if($tournament->isGradingComplete() && $judgedMembers->count() > 0)
                        <div class="bg-purple-900/10 rounded-lg p-6 mb-6 border border-purple-800/20">
                            <h5 class="font-semibold text-lg mb-4 text-purple-400">Judge Comments & Feedback</h5>
                            
                            @php
                                // Get all judge feedback for the team (using any team member since they all have the same feedback)
                                $representativeMember = $judgedMembers->first();
                                $allJudgeFeedback = $representativeMember ? $representativeMember->getAllJudgeFeedback() : collect();
                            @endphp
                            
                            @if($allJudgeFeedback->count() > 0)
                                <div class="mb-4">
                                    <h6 class="font-medium text-purple-300 mb-4">
                                        @if($tournament->team_size > 1)
                                            All Judge Feedback for {{ $team->name }}
                                        @else
                                            Judge Feedback
                                        @endif
                                    </h6>
                                    
                                    <div class="space-y-4">
                                        @foreach($allJudgeFeedback as $feedback)
                                            <div class="bg-gray-800/60 p-4 rounded-lg border-l-4 border-purple-500">
                                                <div class="flex items-center space-x-3 mb-3">
                                                    <div class="w-10 h-10 rounded-full bg-purple-900/50 flex items-center justify-center text-purple-400 font-bold">
                                                        {{ substr($feedback['judge_name'], 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-purple-400">{{ $feedback['judge_name'] }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            Graded on {{ $feedback['created_at']->format('M j, Y \a\t g:i a') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                @if($feedback['feedback'])
                                                    <div>
                                                        <p class="text-gray-300 whitespace-pre-line bg-gray-700/30 p-3 rounded-lg border border-purple-800/20">{{ $feedback['feedback'] }}</p>
                                                    </div>
                                                @else
                                                    <div>
                                                        <p class="text-gray-500 italic">No written feedback provided by this judge.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-700/50 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.418 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.418-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-400 mb-2">No judge feedback available yet</p>
                                    <p class="text-gray-500 text-sm">
                                        @if($tournament->team_size > 1)
                                            Judges haven't provided written feedback for your team submission.
                                        @else
                                            Judges haven't provided written feedback for your submission.
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('tournaments.team', $tournament) }}" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            Back to Team
                        </a>
                        <a href="{{ route('tournaments.show', $tournament) }}" class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded border border-gray-600 transition-colors">
                            Back to Tournament
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>