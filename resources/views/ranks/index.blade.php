<!-- resources/views/ranks/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rank System') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- User's Current Rank Section -->
                    <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                        <h3 class="text-xl font-bold mb-4">Your Current Status</h3>
                        <div class="flex flex-col md:flex-row md:items-center gap-6">
                            <div class="text-center p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex-grow md:max-w-xs">
                                <div class="text-sm text-gray-600 mb-1">Current Rank</div>
                                <div class="text-2xl font-bold 
                                    {{ $user->getRank() === 'Unranked' ? 'text-gray-600' : '' }}
                                    {{ $user->getRank() === 'Bronze' ? 'text-amber-600' : '' }}
                                    {{ $user->getRank() === 'Silver' ? 'text-gray-400' : '' }}
                                    {{ $user->getRank() === 'Gold' ? 'text-yellow-500' : '' }}
                                    {{ $user->getRank() === 'Master' ? 'text-purple-600' : '' }}
                                    {{ $user->getRank() === 'Grand Master' ? 'text-red-600' : '' }}
                                    {{ $user->getRank() === 'One Above All' ? 'text-indigo-600' : '' }}">
                                    {{ $user->getRank() }}
                                </div>
                            </div>
                            
                            <div class="text-center p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex-grow md:max-w-xs">
                                <div class="text-sm text-gray-600 mb-1">Total Points</div>
                                <div class="text-2xl font-bold text-blue-600">{{ $user->points }} pts</div>
                            </div>
                            
                            <div class="text-center p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex-grow md:max-w-xs">
                                @php
                                    $nextRankInfo = $user->getPointsToNextRank();
                                @endphp
                                <div class="text-sm text-gray-600 mb-1">Next Rank</div>
                                <div class="text-xl font-bold {{ $nextRankInfo['points_needed'] > 0 ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ $nextRankInfo['next_rank'] }}
                                    @if($nextRankInfo['points_needed'] > 0)
                                        <div class="text-sm font-normal text-gray-600">
                                            {{ $nextRankInfo['points_needed'] }} points needed
                                        </div>
                                    @else
                                        <div class="text-sm font-normal text-gray-600">
                                            Maximum rank achieved!
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- All Ranks Section -->
                    <h3 class="text-xl font-bold mb-4">Rank System Overview</h3>
                    <p class="mb-6 text-gray-600">Earn points by completing quizzes and challenges to climb the ranks. Higher ranks unlock access to exclusive tournaments and features.</p>
                    
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($ranks as $rank)
                            @php
                                $isCurrentRank = $user->getRank() === $rank['name'];
                                $hasReached = $user->points >= $rank['min_points'];
                            @endphp
                            <div class="p-4 rounded-lg border {{ $isCurrentRank ? 'border-blue-300 bg-blue-50' : 'border-gray-200' }} {{ $hasReached ? '' : 'opacity-75' }}">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                    <div class="flex items-center gap-3 mb-2 md:mb-0">
                                        <!-- Rank Badge -->
                                        <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-{{ $rank['color'] }} bg-{{ $rank['bg_color'] }}">
                                            @if($isCurrentRank)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            @endif
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-lg font-bold text-{{ $rank['color'] }}">
                                                {{ $rank['name'] }}
                                                @if($isCurrentRank)
                                                    <span class="ml-2 text-xs px-2 py-1 bg-blue-200 text-blue-800 rounded-full">Current</span>
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-600">{{ $rank['description'] }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="md:text-right">
                                        <div class="text-sm text-gray-600">Points Required</div>
                                        <div class="font-bold">
                                            @if(isset($rank['max_points']))
                                                {{ $rank['min_points'] }} - {{ $rank['max_points'] }}
                                            @else
                                                {{ $rank['min_points'] }}+
                                            @endif
                                        </div>
                                        
                                        @if($hasReached && !$isCurrentRank)
                                            <span class="inline-block mt-1 text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">Achieved</span>
                                        @elseif(!$hasReached)
                                            <span class="inline-block mt-1 text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                                {{ $rank['min_points'] - $user->points }} more points needed
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($isCurrentRank && ($nextRankInfo['points_needed'] > 0))
                                    <div class="mt-3">
                                        <div class="text-xs text-gray-600 mb-1">Progress to next rank</div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            @php
                                                $currentPoints = $user->points - $rank['min_points'];
                                                $requiredPoints = isset($rank['max_points']) ? $rank['max_points'] - $rank['min_points'] + 1 : 0;
                                                $progressPercentage = $requiredPoints > 0 ? min(100, ($currentPoints / $requiredPoints) * 100) : 100;
                                            @endphp
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Benefits Section -->
                    <div class="mt-10">
                        <h3 class="text-xl font-bold mb-4">Rank Benefits</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 bg-white rounded-lg border border-gray-200">
                                <h4 class="font-bold text-lg mb-2">Tournament Eligibility</h4>
                                <p class="text-gray-600">Higher ranks grant access to more prestigious tournaments with better rewards.</p>
                            </div>
                            
                            <div class="p-4 bg-white rounded-lg border border-gray-200">
                                <h4 class="font-bold text-lg mb-2">Recognition</h4>
                                <p class="text-gray-600">Your rank is displayed on the leaderboard and in tournaments, showcasing your achievements.</p>
                            </div>
                            
                            <div class="p-4 bg-white rounded-lg border border-gray-200">
                                <h4 class="font-bold text-lg mb-2">Advanced Challenges</h4>
                                <p class="text-gray-600">Unlock more difficult challenges that offer greater point rewards as you climb the ranks.</p>
                            </div>
                            
                            <div class="p-4 bg-white rounded-lg border border-gray-200">
                                <h4 class="font-bold text-lg mb-2">Community Status</h4>
                                <p class="text-gray-600">Higher ranks earn you respect and recognition within the learning community.</p>
                            </div>
                        </div>
                    </div>

                    <!-- How to Earn Points Section -->
                    <div class="mt-10">
                        <h3 class="text-xl font-bold mb-4">How to Earn Points</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="border border-gray-200 px-4 py-2 text-left bg-gray-50">Activity</th>
                                        <th class="border border-gray-200 px-4 py-2 text-left bg-gray-50">Points Awarded</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-2">Completing a Quiz</td>
                                        <td class="border border-gray-200 px-4 py-2">5 points</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-2">Challenge (Score 0-19%)</td>
                                        <td class="border border-gray-200 px-4 py-2">0 points</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-2">Challenge (Score 20-39%)</td>
                                        <td class="border border-gray-200 px-4 py-2">2 points</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-2">Challenge (Score 40-59%)</td>
                                        <td class="border border-gray-200 px-4 py-2">4 points</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-2">Challenge (Score 60-79%)</td>
                                        <td class="border border-gray-200 px-4 py-2">6 points</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-2">Challenge (Score 80-99%)</td>
                                        <td class="border border-gray-200 px-4 py-2">8 points</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-2">Challenge (Score 100%)</td>
                                        <td class="border border-gray-200 px-4 py-2">10 points</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-2">Tournament Participation</td>
                                        <td class="border border-gray-200 px-4 py-2">Varies by tournament</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>