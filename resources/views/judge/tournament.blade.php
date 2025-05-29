<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Tournament Submissions') }}
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

                    @if(session('info'))
                        <div class="bg-blue-900/20 border-l-4 border-blue-500 text-blue-400 p-4 mb-4" role="alert">
                            <p>{{ session('info') }}</p>
                        </div>
                    @endif

                    @php
                        $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');
                        $judgingDate = \Carbon\Carbon::parse($tournament->judging_date)->setTimezone('Asia/Kuala_Lumpur');
                        $canJudgeNow = $now->greaterThanOrEqualTo($judgingDate);
                    @endphp

                    <div class="mb-6">
                        <a href="{{ route('judge.dashboard') }}" class="text-amber-400 hover:text-amber-300">
                            &larr; Back to Judge Dashboard
                        </a>
                    </div>

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-amber-400">{{ $tournament->title }}</h3>
                            <p class="text-gray-400 mb-1">Judging Date: {{ $judgingDate->format('F j, Y, g:i a') }}</p>
                            <p class="text-gray-400">Event Date: {{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}</p>
                            <p class="text-gray-400">Location: {{ $tournament->location }}</p>
                            @if($tournament->team_size > 1)
                                <p class="text-amber-400 text-sm mt-1">
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                        </svg>
                                        Team Tournament ({{ $tournament->team_size }} members per team)
                                    </span>
                                </p>
                            @endif
                        </div>
                        
                        <div class="mt-4 md:mt-0 flex flex-col items-end">
                            <p class="text-sm text-gray-400">
                                <span class="font-medium text-amber-400">Your Role:</span> 
                                {{ $tournament->judges()->where('user_id', auth()->id())->first()->pivot->role ?? 'Judge' }}
                            </p>
                            <p class="text-sm text-gray-400 mt-1">
                                <span class="font-medium text-amber-400">Total Participants:</span> 
                                {{ $totalParticipants }}
                            </p>
                            <p class="text-sm text-gray-400 mt-1">
                                <span class="font-medium text-amber-400">Submissions:</span> 
                                {{ $submittedCount }}/{{ $totalParticipants }}
                            </p>
                            <p class="text-sm text-gray-400 mt-1">
                                <span class="font-medium text-amber-400">Graded by You:</span> 
                                {{ $gradedByCurrentJudgeCount }}/{{ $submittedCount }}
                            </p>
                            <p class="text-sm text-green-400 mt-1">
                                <span class="font-medium">Fully Graded:</span> 
                                {{ $fullyGradedCount }}/{{ $submittedCount }}
                            </p>
                        </div>
                    </div>

                    <!-- Team Scoring Notice -->
                    @if($tournament->team_size > 1)
                        <div class="mb-8 bg-blue-900/20 p-4 rounded-lg border border-blue-800/20">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-blue-400 mb-1">Team Tournament Scoring</h4>
                                    <p class="text-gray-300 text-sm">
                                        When you grade any team member, all members of that team will receive the same score from you. 
                                        You only need to grade one member per team.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!$canJudgeNow)
                        <div class="mb-8 bg-amber-900/20 p-4 rounded-lg border border-amber-800/20">
                            <h4 class="font-medium text-lg mb-2 text-amber-400">Judging in Waiting Period</h4>
                            <p class="text-gray-300">
                                To ensure fair judging, please wait until the judging date before grading submissions.
                                This allows all participants to finalize their submissions.
                            </p>
                            <p class="mt-4 text-amber-400 font-medium">
                                Judging will be available on {{ $judgingDate->format('F j, Y, g:i a') }} 
                                ({{ $judgingDate->diffForHumans() }})
                            </p>
                        </div>
                    @else
                        <div class="mb-8 bg-green-900/20 p-4 rounded-lg border border-green-800/20">
                            <h4 class="font-medium text-lg mb-2 text-green-400">Judging Available Now</h4>
                            <p class="text-gray-300">
                                The judging period has begun. You can now grade participant submissions.
                            </p>
                        </div>
                    @endif

                    <!-- Grading Completion Section -->
                    <div class="bg-gray-900/40 p-4 rounded-lg mb-8">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h4 class="font-medium text-lg mb-2 text-amber-400">Grading Status</h4>
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-300">
                                        Judges Completed: <span class="text-amber-400">{{ $completedJudgesCount }}/{{ $totalJudgesCount }}</span>
                                    </p>
                                    @if($isCurrentJudgeComplete)
                                        <p class="text-sm text-green-400">✓ You have completed grading for this tournament</p>
                                    @else
                                        <p class="text-sm text-gray-400">You have not completed grading yet</p>
                                    @endif
                                    @if($isAllGradingComplete)
                                        <p class="text-sm text-green-400">✓ All judges have completed grading - Results are now visible to participants</p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($canCompleteGrading && $canJudgeNow)
                                <div class="mt-4 md:mt-0">
                                    <form action="{{ route('judge.complete-grading', $tournament) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark your grading as complete? You will not be able to change any scores after this.')">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                            Mark Grading Complete
                                        </button>
                                    </form>
                                </div>
                            @elseif($isCurrentJudgeComplete)
                                <div class="mt-4 md:mt-0">
                                    <span class="inline-flex items-center px-3 py-2 bg-green-900/20 text-green-400 rounded-lg border border-green-800/30">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Grading Complete
                                    </span>
                                </div>
                            @elseif($canJudgeNow)
                                <div class="mt-4 md:mt-0">
                                    <p class="text-sm text-amber-400 bg-amber-900/20 px-3 py-2 rounded-lg border border-amber-800/30">
                                        Grade all submissions to complete grading
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tournament Criteria -->
                    <div class="bg-gray-900/40 p-4 rounded-lg mb-8">
                        <h4 class="font-medium text-lg mb-2 text-amber-400">Judging Criteria</h4>
                        <div class="text-gray-300 whitespace-pre-line">{{ $tournament->judging_criteria }}</div>
                    </div>

                    <!-- Judging Rubrics -->
                    <div class="bg-gray-900/40 p-4 rounded-lg mb-8">
                        <h4 class="font-medium text-lg mb-2 text-amber-400">Judging Rubrics</h4>
                        
                        @if($tournament->rubrics->count() > 0)
                            <div class="space-y-2">
                                @foreach($tournament->rubrics as $rubric)
                                    <div class="flex justify-between items-center border-b border-amber-800/10 py-2">
                                        <span class="text-gray-300">{{ $rubric->title }}</span>
                                        <span class="bg-amber-900/20 px-2 py-1 rounded-md text-amber-400">{{ $rubric->score_weight }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400">No specific rubrics have been defined for this tournament.</p>
                        @endif
                    </div>

                    <!-- Submissions -->
                    <h4 class="font-medium text-lg mb-4 text-amber-400">
                        {{ $tournament->team_size > 1 ? 'Team Submissions' : 'Participant Submissions' }}
                    </h4>
                    
                    @if($participants->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-amber-800/20">
                                <thead class="bg-gray-900">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                            {{ $tournament->team_size > 1 ? 'Team Member' : 'Participant' }}
                                        </th>
                                        @if($tournament->team_size > 1)
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Team</th>
                                        @endif
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Submission</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Your Score</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Average Score</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-amber-800/20">
                                    @foreach($participants as $participant)
                                        <tr class="{{ $tournament->team_size > 1 && $participant->currentJudgeScore ? 'bg-green-900/10' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-300">
                                                <div class="flex items-center space-x-2">
                                                    <span>{{ $participant->user->name }}</span>
                                                    @if($tournament->team_size > 1 && $participant->role === 'leader')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-900/30 text-amber-400">
                                                            Leader
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">{{ $participant->user->email }}</div>
                                            </td>
                                            
                                            @if($tournament->team_size > 1)
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                    @if($participant->team)
                                                        <div>
                                                            <p class="font-medium">{{ $participant->team->name }}</p>
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                {{ $participant->team->participants()->count() }} member(s)
                                                            </div>
                                                            @if($participant->currentJudgeScore)
                                                                <div class="text-xs text-green-400 mt-1">
                                                                    <span class="inline-flex items-center">
                                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                        Team Scored
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-gray-500">No team</span>
                                                    @endif
                                                </td>
                                            @endif
                                            
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                @if($participant->submission_url)
                                                    <a href="{{ $participant->submission_url }}" target="_blank" class="text-blue-400 hover:text-blue-300">
                                                        View Submission
                                                    </a>
                                                @else
                                                    <span class="text-red-400">Not submitted</span>
                                                @endif
                                            </td>
                                            
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if(!$participant->submission_url)
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-900/30 text-red-400">
                                                        No Submission
                                                    </span>
                                                @elseif($participant->currentJudgeScore)
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-900/30 text-green-400">
                                                        {{ $tournament->team_size > 1 ? 'Team Graded' : 'Graded by You' }}
                                                    </span>
                                                @elseif($participant->judgeCount > 0)
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-900/30 text-blue-400">
                                                        Partially Graded ({{ $participant->judgeCount }}/{{ $participant->totalJudges }})
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-900/30 text-yellow-400">
                                                        {{ $tournament->team_size > 1 ? 'Team Needs Grading' : 'Needs Your Grading' }}
                                                    </span>
                                                @endif
                                            </td>
                                            
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                @if($participant->currentJudgeScore)
                                                    <span class="font-medium text-amber-400">{{ $participant->currentJudgeScore->score }}/10</span>
                                                    @if($tournament->team_size > 1)
                                                        <div class="text-xs text-green-400">
                                                            (All team members)
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-500">Not graded</span>
                                                @endif
                                            </td>
                                            
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                @if($participant->judgeCount > 0)
                                                    <div class="flex flex-col">
                                                        <span class="font-medium text-green-400">{{ $participant->score }}/10</span>
                                                        <span class="text-xs text-gray-500">({{ $participant->judgeCount }}/{{ $participant->totalJudges }} judges)</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-500">No scores yet</span>
                                                @endif
                                            </td>
                                            
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if($participant->submission_url)
                                                    @if($canJudgeNow && !$isCurrentJudgeComplete)
                                                        <a href="{{ route('judge.submission', ['tournament' => $tournament, 'participant' => $participant]) }}" class="text-amber-400 hover:text-amber-300">
                                                            @if($participant->currentJudgeScore)
                                                                {{ $tournament->team_size > 1 ? 'Update Team Grade' : 'Update Grade' }}
                                                            @else
                                                                {{ $tournament->team_size > 1 ? 'Grade Team' : 'Grade' }}
                                                            @endif
                                                        </a>
                                                    @elseif($isCurrentJudgeComplete)
                                                        <span class="text-green-400">Grading Complete</span>
                                                    @else
                                                        <span class="text-gray-500">Waiting for judging date</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-500">Cannot grade</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-gray-900/50 rounded-lg border border-amber-800/20 p-6 text-center">
                            <p class="text-gray-400">There are no participants in this tournament yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>