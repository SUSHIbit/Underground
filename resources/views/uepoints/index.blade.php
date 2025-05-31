<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('UEPoints Information') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-4 sm:p-6 text-gray-200">
                    <!-- UEPoints Overview Section -->
                    <div class="mb-8 p-4 sm:p-6 bg-gray-900/50 rounded-lg border border-amber-800/20">
                        <h3 class="text-xl font-bold mb-4 text-amber-400">Your UEPoints</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Current UEPoints -->
                            <div class="text-center p-4 bg-gray-800 rounded-lg border border-amber-800/20">
                                <div class="text-sm text-gray-400 mb-1">Current UEPoints</div>
                                <div class="text-2xl sm:text-3xl font-bold text-amber-500">{{ $user->ue_points }}</div>
                            </div>
                            <!-- Total Earned -->
                            <div class="text-center p-4 bg-gray-800 rounded-lg border border-green-800/20">
                                <div class="text-sm text-gray-400 mb-1">Total Earned</div>
                                <div class="text-2xl sm:text-3xl font-bold text-green-400">+{{ $totalEarned ?? 0 }}</div>
                            </div>
                            <!-- Total Spent -->
                            <div class="text-center p-4 bg-gray-800 rounded-lg border border-red-800/20">
                                <div class="text-sm text-gray-400 mb-1">Total Spent</div>
                                <div class="text-2xl sm:text-3xl font-bold text-red-400">-{{ $totalSpent ?? 0 }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- About UEPoints Section -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4 text-amber-400">About UEPoints</h3>
                        <div class="bg-gray-900/50 p-4 sm:p-6 rounded-lg border border-amber-800/20">
                            <p class="mb-3 text-gray-300">UEPoints (UniKL E-Points) allow you to retake quizzes and challenges to improve your scores.</p>
                            <p class="mb-3 text-gray-300">When you retake a quiz or challenge, your new score will replace the previous one, potentially helping you to improve your overall ranking.</p>
                            
                            <!-- Mobile Layout - Stacked Cards -->
                            <div class="block lg:hidden space-y-3 mt-4">
                                <div class="bg-gray-800 p-3 rounded-lg border border-amber-800/20 flex justify-between items-center">
                                    <span class="text-sm">Retaking a quiz costs</span>
                                    <strong class="text-amber-400">5 UEPoints</strong>
                                </div>
                                <div class="bg-gray-800 p-3 rounded-lg border border-amber-800/20 flex justify-between items-center">
                                    <span class="text-sm">Retaking a challenge costs</span>
                                    <strong class="text-amber-400">10 UEPoints</strong>
                                </div>
                            </div>

                            <!-- Desktop Layout -->
                            <div class="hidden lg:block bg-gray-800/50 p-4 rounded-lg border border-amber-800/20 mt-4">
                                <ul class="space-y-2 text-gray-300">
                                    <li class="flex items-center gap-2">
                                        <span>Retaking a quiz costs</span>
                                        <strong class="text-amber-400">5 UEPoints</strong>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span>Retaking a challenge costs</span>
                                        <strong class="text-amber-400">10 UEPoints</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- How to Earn UEPoints Section -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4 text-amber-400">How to Earn UEPoints</h3>
                        <div class="bg-gray-900/50 p-4 sm:p-6 rounded-lg border border-amber-800/20">
                            <p class="mb-3 text-gray-300">You can earn UEPoints through various activities:</p>
                            
                            <!-- Mobile Layout - Stacked Cards -->
                            <div class="block lg:hidden space-y-3">
                                <div class="bg-gray-800 p-3 rounded-lg border border-amber-800/20 flex justify-between items-center">
                                    <span class="text-sm">Complete a quiz</span>
                                    <span class="text-green-400 text-sm font-bold">+2 UEPoints</span>
                                </div>
                                <div class="bg-gray-800 p-3 rounded-lg border border-amber-800/20 flex justify-between items-center">
                                    <span class="text-sm">Complete a challenge</span>
                                    <span class="text-green-400 text-sm font-bold">+2 UEPoints</span>
                                </div>
                                <div class="bg-gray-800 p-3 rounded-lg border border-amber-800/20 flex justify-between items-center">
                                    <span class="text-sm">Join a tournament</span>
                                    <span class="text-green-400 text-sm font-bold">+2 UEPoints</span>
                                </div>
                                <div class="bg-gray-800 p-3 rounded-lg border border-amber-800/20">
                                    <span class="text-sm">Tournament rankings (1st: +50, 2nd: +30, 3rd: +20, Others: +5)</span>
                                </div>
                                <div class="bg-gray-800 p-3 rounded-lg border border-amber-800/20">
                                    <span class="text-sm">Special events and administrative awards</span>
                                </div>
                            </div>

                            <!-- Desktop Layout -->
                            <div class="hidden lg:block bg-gray-800/50 p-4 rounded-lg border border-amber-800/20">
                                <ul class="space-y-2 text-gray-300">
                                    <li class="flex items-center gap-2">
                                        <span>Complete a quiz</span>
                                        <span class="text-green-400">(+2 UEPoints)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span>Complete a challenge</span>
                                        <span class="text-green-400">(+2 UEPoints)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span>Join a tournament</span>
                                        <span class="text-green-400">(+2 UEPoints)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span>Tournament rankings</span>
                                        <span class="text-green-400">(1st: +50, 2nd: +30, 3rd: +20, Others: +5 UEPoints)</span>
                                    </li>
                                    <li>Special events and administrative awards</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- UEPoints Activity History -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4 text-amber-400">Recent UEPoints Activity</h3>
                        @if(isset($allActivities) && $allActivities->count() > 0)
                            <!-- Mobile Card Layout -->
                            <div class="block lg:hidden space-y-3">
                                @foreach($allActivities as $activity)
                                    <div class="bg-gray-900/50 p-4 rounded-lg border border-amber-800/20">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                                <!-- Activity Icon -->
                                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm
                                                    {{ $activity['type'] === 'earning' ? 'bg-green-800/30 text-green-400' : 'bg-red-800/30 text-red-400' }}">
                                                    @if($activity['icon'] === 'quiz')
                                                        📝
                                                    @elseif($activity['icon'] === 'challenge')
                                                        🎯
                                                    @elseif($activity['icon'] === 'tournament')
                                                        🏆
                                                    @elseif($activity['icon'] === 'trophy')
                                                        🏅
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-300 truncate">
                                                        {{ $activity['activity'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-400 truncate">
                                                        {{ $activity['description'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-400">{{ $activity['created_at']->format('M d, Y g:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right ml-2">
                                                <p class="text-sm font-bold
                                                    {{ $activity['type'] === 'earning' ? 'text-green-400' : 'text-red-400' }}">
                                                    {{ $activity['points'] > 0 ? '+' : '' }}{{ $activity['points'] }}
                                                </p>
                                                @if($activity['score'] !== null && $activity['total_questions'] !== null)
                                                    <p class="text-xs {{ $activity['score']/$activity['total_questions']*100 >= 70 ? 'text-green-400' : ($activity['score']/$activity['total_questions']*100 >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                                        {{ $activity['score'] }}/{{ $activity['total_questions'] }}
                                                    </p>
                                                @elseif($activity['score'] !== null)
                                                    <p class="text-xs text-amber-400">
                                                        Score: {{ $activity['score'] }}/10
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Desktop Table Layout -->
                            <div class="hidden lg:block overflow-x-auto bg-gray-900/50 rounded-lg border border-amber-800/20">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-amber-800/20">
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Activity</th>
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">UEPoints</th>
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allActivities as $activity)
                                            <tr class="border-b border-gray-700">
                                                <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-400">
                                                    {{ $activity['created_at']->format('M d, Y g:i A') }}
                                                </td>
                                                <td class="py-3 px-4 text-sm font-medium text-gray-300">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-lg">
                                                            @if($activity['icon'] === 'quiz')
                                                                📝
                                                            @elseif($activity['icon'] === 'challenge')
                                                                🎯
                                                            @elseif($activity['icon'] === 'tournament')
                                                                🏆
                                                            @elseif($activity['icon'] === 'trophy')
                                                                🏅
                                                            @endif
                                                        </span>
                                                        <div>
                                                            <div>{{ $activity['activity'] }}</div>
                                                            <div class="text-xs text-gray-400">{{ $activity['description'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 whitespace-nowrap text-sm font-bold
                                                    {{ $activity['type'] === 'earning' ? 'text-green-400' : 'text-red-400' }}">
                                                    {{ $activity['points'] > 0 ? '+' : '' }}{{ $activity['points'] }}
                                                </td>
                                                <td class="py-3 px-4 whitespace-nowrap text-sm">
                                                    @if($activity['score'] !== null && $activity['total_questions'] !== null)
                                                        <span class="{{ $activity['score']/$activity['total_questions']*100 >= 70 ? 'text-green-400' : ($activity['score']/$activity['total_questions']*100 >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                                            {{ $activity['score'] }}/{{ $activity['total_questions'] }}
                                                            ({{ round($activity['score']/$activity['total_questions']*100) }}%)
                                                        </span>
                                                    @elseif($activity['score'] !== null)
                                                        <span class="text-amber-400">
                                                            Score: {{ $activity['score'] }}/10
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-900/50 p-4 sm:p-6 rounded-lg text-center border border-amber-800/20">
                                <p class="text-gray-400">No UEPoints activity yet. Start by completing quizzes, challenges, or joining tournaments!</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- UEPoints Management Tips -->
                    <div class="mb-4">
                        <h3 class="text-xl font-bold mb-4 text-amber-400">UEPoints Management Tips</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-900/50 p-4 rounded-lg border border-amber-800/20">
                                <h4 class="font-medium text-lg mb-2 text-amber-400">Strategic Planning</h4>
                                <p class="text-gray-300 text-sm sm:text-base">
                                    Save your UEPoints for challenges that offer maximum point potential. 
                                    Retaking a challenge with a potential 10-point reward is more efficient 
                                    than retaking multiple quizzes.
                                </p>
                            </div>
                            
                            <div class="bg-gray-900/50 p-4 rounded-lg border border-amber-800/20">
                                <h4 class="font-medium text-lg mb-2 text-amber-400">Be Prepared</h4>
                                <p class="text-gray-300 text-sm sm:text-base">
                                    Before spending UEPoints on retakes, review the study materials and 
                                    learn from your previous mistakes to maximize your improvement.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>