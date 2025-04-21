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

                    <div class="mb-6">
                        <a href="{{ route('judge.dashboard') }}" class="text-amber-400 hover:text-amber-300">
                            &larr; Back to Judge Dashboard
                        </a>
                    </div>

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-amber-400">{{ $tournament->title }}</h3>
                            <p class="text-gray-400 mb-1">{{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}</p>
                            <p class="text-gray-400">Location: {{ $tournament->location }}</p>
                        </div>
                        
                        <div class="mt-4 md:mt-0 flex flex-col items-end">
                            <p class="text-sm text-gray-400">
                                <span class="font-medium text-amber-400">Your Role:</span> 
                                {{ $tournament->judges()->where('user_id', auth()->id())->first()->pivot->role ?? 'Judge' }}
                            </p>
                            <p class="text-sm text-gray-400 mt-1">
                                <span class="font-medium text-amber-400">Submissions:</span> 
                                {{ $submittedCount }}/{{ $totalParticipants }}
                            </p>
                            <p class="text-sm text-gray-400 mt-1">
                                <span class="font-medium text-amber-400">Graded:</span> 
                                {{ $gradedCount }}/{{ $submittedCount }}
                            </p>
                        </div>
                    </div>

                    <!-- Tournament Criteria -->
                    <div class="bg-gray-900/40 p-4 rounded-lg mb-8">
                        <h4 class="font-medium text-lg mb-2 text-amber-400">Judging Criteria</h4>
                        <div class="text-gray-300 whitespace-pre-line">{{ $tournament->judging_criteria }}</div>
                    </div>

                    <!-- Submissions -->
                    <h4 class="font-medium text-lg mb-4 text-amber-400">Participant Submissions</h4>
                    
                    @if($participants->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-amber-800/20">
                                <thead class="bg-gray-900">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Participant</th>
                                        @if($tournament->team_size > 1)
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Team</th>
                                        @endif
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Submission</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Score</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-amber-800/20">
                                    @foreach($participants as $participant)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-300">
                                                {{ $participant->user->name }}
                                                <div class="text-xs text-gray-500">{{ $participant->user->email }}</div>
                                            </td>
                                            
                                            @if($tournament->team_size > 1)
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                    <p>{{ $participant->team_name ?? 'No team name' }}</p>
                                                    @if(isset($participant->team_members) && count($participant->team_members) > 0)
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ count($participant->team_members) }} members
                                                        </div>
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
                                                @elseif($participant->score !== null)
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-900/30 text-green-400">
                                                        Graded
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-900/30 text-yellow-400">
                                                        Needs Grading
                                                    </span>
                                                @endif
                                            </td>
                                            
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                @if($participant->score !== null)
                                                    <span class="font-medium text-amber-400">{{ $participant->score }}/10</span>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if($participant->submission_url)
                                                    <a href="{{ route('judge.submission', ['tournament' => $tournament, 'participant' => $participant]) }}" class="text-amber-400 hover:text-amber-300">
                                                        {{ $participant->score !== null ? 'Review' : 'Grade' }}
                                                    </a>
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