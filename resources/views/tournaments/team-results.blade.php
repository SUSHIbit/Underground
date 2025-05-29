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
                                        <div class="w-12 h-12 rounded-full bg-amber-600 flex items-center justify-center text-white font-bold mr-4">
                                            {{ substr($currentUserMember->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-amber-400">{{ $currentUserMember->user->name }} ({{ $currentUserMember->user->username }})</p>
                                            <p class="text-sm text-gray-400">
                                                {{ $currentUserMember->user_id === $team->leader_id ? 'Team Leader' : 'Team Member' }} • {{ $currentUserMember->user->getRank() }}
                                            </p>
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
                                            
                                            @if($currentUserMember->feedback)
                                                <div class="mt-3">
                                                    <p class="font-medium text-gray-300 mb-1">Feedback:</p>
                                                    <p class="text-gray-300 whitespace-pre-line bg-gray-700/50 p-3 rounded">{{ $currentUserMember->feedback }}</p>
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
                                                    <div class="w-10 h-10 rounded-full {{ $member->user_id === $team->leader_id ? 'bg-amber-600' : 'bg-gray-700' }} flex items-center justify-center text-white font-bold mr-3">
                                                        {{ substr($member->user->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-medium {{ $member->user_id === $team->leader_id ? 'text-amber-400' : 'text-white' }}">{{ $member->user->name }}</p>
                                                        <p class="text-sm text-gray-400">
                                                            {{ $member->user_id === $team->leader_id ? 'Team Leader' : 'Team Member' }} • {{ $member->user->getRank() }}
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                @if($member->score !== null)
                                                    <div class="mt-3 p-3 bg-gray-700/50 rounded-lg">
                                                        <p class="font-medium text-gray-300 mb-1">Score: 
                                                            <span class="text-lg font-bold {{ $member->score >= 7 ? 'text-green-400' : ($member->score >= 5 ? 'text-amber-400' : 'text-gray-400') }}">
                                                                {{ $member->score }}/10
                                                            </span>
                                                        </p>
                                                        
                                                        @if($member->feedback)
                                                            <div class="mt-2">
                                                                <p class="text-sm font-medium text-gray-300">Feedback:</p>
                                                                <p class="text-sm text-gray-400 mt-1">{{ Str::limit($member->feedback, 100) }}</p>
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
                    @endphp
                    
                    @if($averageScore !== null)
                        <div class="bg-gray-700/20 rounded-lg p-6 mb-6 border border-amber-800/20">
                            <h5 class="font-semibold text-lg mb-4 text-amber-400">Team Summary</h5>
                            
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