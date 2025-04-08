<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    <h3 class="text-lg font-medium mb-4 text-amber-400">Welcome, {{ $user->username }}!</h3>

                    <div class="bg-gray-900 overflow-hidden shadow-sm rounded-lg mt-6">
                        <div class="p-6 text-gray-200">
                            <h3 class="text-lg font-medium mb-4 text-amber-400">Your Progress</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="border border-amber-800/20 rounded-lg p-4 bg-gray-800">
                                    <h4 class="font-medium text-gray-300 mb-2">Current Rank</h4>
                                    <p class="text-2xl font-bold 
                                        {{ $user->getRank() === 'Unranked' ? 'text-gray-400' : '' }}
                                        {{ $user->getRank() === 'Bronze' ? 'text-amber-600' : '' }}
                                        {{ $user->getRank() === 'Silver' ? 'text-gray-400' : '' }}
                                        {{ $user->getRank() === 'Gold' ? 'text-amber-500' : '' }}
                                        {{ $user->getRank() === 'Master' ? 'text-purple-400' : '' }}
                                        {{ $user->getRank() === 'Grand Master' ? 'text-red-400' : '' }}
                                        {{ $user->getRank() === 'One Above All' ? 'text-indigo-400' : '' }}">
                                        {{ $user->getRank() }}
                                    </p>
                                </div>
                                
                                <div class="border border-amber-800/20 rounded-lg p-4 bg-gray-800">
                                    <h4 class="font-medium text-gray-300 mb-2">Total Points</h4>
                                    <p class="text-2xl font-bold text-amber-500">{{ $user->points }}</p>
                                </div>
                                
                                <div class="border border-amber-800/20 rounded-lg p-4 bg-gray-800">
                                    <h4 class="font-medium text-gray-300 mb-2">UEPoints</h4>
                                    <p class="text-2xl font-bold text-amber-500">{{ $user->ue_points }}</p>
                                    <p class="text-sm text-gray-400 mt-1">
                                        Use UEPoints to retake quizzes and challenges
                                    </p>
                                    <a href="{{ route('uepoints.index') }}" class="mt-2 inline-block text-sm text-amber-400 hover:text-amber-300">
                                        Learn more about UEPoints
                                    </a>
                                </div>
                                
                                <div class="border border-amber-800/20 rounded-lg p-4 bg-gray-800">
                                    <h4 class="font-medium text-gray-300 mb-2">Next Rank</h4>
                                    @php
                                        $nextRankInfo = $user->getPointsToNextRank();
                                    @endphp
                                    <p class="text-lg font-medium text-amber-500">{{ $nextRankInfo['next_rank'] }}</p>
                                    <p class="text-sm text-gray-400">{{ $nextRankInfo['points_needed'] }} points needed</p>
                                    
                                    @if($nextRankInfo['points_needed'] > 0)
                                        <div class="w-full bg-gray-600 rounded-full h-2.5 mt-2">
                                            @php
                                                $progressPercentage = 0;
                                                if ($user->getRank() === 'Unranked') {
                                                    $progressPercentage = ($user->points / 50) * 100;
                                                } elseif ($user->getRank() === 'Bronze') {
                                                    $progressPercentage = (($user->points - 50) / 50) * 100;
                                                } elseif ($user->getRank() === 'Silver') {
                                                    $progressPercentage = (($user->points - 100) / 150) * 100;
                                                } elseif ($user->getRank() === 'Gold') {
                                                    $progressPercentage = (($user->points - 250) / 250) * 100;
                                                } elseif ($user->getRank() === 'Master') {
                                                    $progressPercentage = (($user->points - 500) / 250) * 100;
                                                } elseif ($user->getRank() === 'Grand Master') {
                                                    $progressPercentage = (($user->points - 750) / 250) * 100;
                                                }
                                            @endphp
                                            <div class="bg-amber-600 h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 mb-6">
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('quizzes.index') }}" class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded-md shadow transition-colors">
                                Take a Quiz
                            </a>
                            <a href="{{ route('challenges.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md shadow border border-amber-800/20 transition-colors">
                                Take a Challenge
                            </a>
                            <a href="{{ route('tournaments.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md shadow border border-amber-800/20 transition-colors">
                                Join a Tournament
                            </a>
                        </div>
                    </div>
                    
                    <h4 class="text-lg font-medium mb-4 text-amber-400">Your Recent Results:</h4>
                    
                    @if($quizAttempts->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-amber-800/20">
                            <table class="min-w-full divide-y divide-amber-800/20">
                                <thead class="bg-gray-900">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Score</th>
                                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-amber-800/10">
                                    @foreach($quizAttempts as $attempt)
                                        <tr>
                                            <td class="py-3 px-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $attempt->set->type === 'quiz' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ ucfirst($attempt->set->type) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-300">
                                                @if($attempt->set->type === 'quiz')
                                                    {{ $attempt->set->quizDetail->subject->name }} - 
                                                    {{ $attempt->set->quizDetail->topic->name }}
                                                @else
                                                    {{ $attempt->set->challengeDetail->name }}
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap text-sm">
                                                <span class="{{ $attempt->score_percentage >= 70 ? 'text-green-400' : ($attempt->score_percentage >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                                    {{ $attempt->score }}/{{ $attempt->total_questions }}
                                                    ({{ $attempt->score_percentage }}%)
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-300">{{ $attempt->created_at->format('M d, Y') }}</td>
                                            <td class="py-3 px-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('results.show', $attempt) }}" class="text-amber-400 hover:text-amber-300">
                                                    View Results
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-gray-900 rounded-lg border border-amber-800/20 p-6 text-center">
                            <p class="text-gray-400">You haven't completed any quizzes or challenges yet.</p>
                            <p class="mt-2 text-gray-500">Start your journey by taking a quiz or challenge above!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>