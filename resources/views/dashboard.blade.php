<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome, {{ $user->username }}!</h3>


                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Your Progress</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="border rounded-lg p-4 bg-blue-50">
                                    <h4 class="font-medium text-gray-700 mb-2">Current Rank</h4>
                                    <p class="text-2xl font-bold 
                                        {{ $user->getRank() === 'Unranked' ? 'text-gray-600' : '' }}
                                        {{ $user->getRank() === 'Bronze' ? 'text-amber-600' : '' }}
                                        {{ $user->getRank() === 'Silver' ? 'text-gray-400' : '' }}
                                        {{ $user->getRank() === 'Gold' ? 'text-yellow-500' : '' }}
                                        {{ $user->getRank() === 'Master' ? 'text-purple-600' : '' }}
                                        {{ $user->getRank() === 'Grand Master' ? 'text-red-600' : '' }}
                                        {{ $user->getRank() === 'One Above All' ? 'text-indigo-600' : '' }}">
                                        {{ $user->getRank() }}
                                    </p>
                                </div>
                                
                                <div class="border rounded-lg p-4">
                                    <h4 class="font-medium text-gray-700 mb-2">Total Points</h4>
                                    <p class="text-2xl font-bold text-blue-600">{{ $user->points }}</p>
                                </div>
                                
                                <div class="border rounded-lg p-4">
                                    <h4 class="font-medium text-gray-700 mb-2">Next Rank</h4>
                                    @php
                                        $nextRankInfo = $user->getPointsToNextRank();
                                    @endphp
                                    <p class="text-lg font-medium">{{ $nextRankInfo['next_rank'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $nextRankInfo['points_needed'] }} points needed</p>
                                    
                                    @if($nextRankInfo['points_needed'] > 0)
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
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
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>


                    
                    <div class="mb-6">
                        <div class="flex gap-4">
                            <a href="{{ route('quizzes.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Take a Quiz
                            </a>
                            <a href="{{ route('challenges.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Take a Challenge
                            </a>
                        </div>
                    </div>
                    
                    <h4 class="text-lg font-medium mb-2">Your Recent Results:</h4>
                    
                    @if($quizAttempts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Score</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quizAttempts as $attempt)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ ucfirst($attempt->set->type) }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @if($attempt->set->type === 'quiz')
                                                    {{ $attempt->set->quizDetail->subject->name }} - 
                                                    {{ $attempt->set->quizDetail->topic->name }}
                                                @else
                                                    {{ $attempt->set->challengeDetail->name }}
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $attempt->score }}/{{ $attempt->total_questions }}
                                                ({{ $attempt->score_percentage }}%)
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $attempt->created_at->format('M d, Y') }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('results.show', $attempt) }}" class="text-blue-500 hover:text-blue-700">
                                                    View Results
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">You haven't completed any quizzes or challenges yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>