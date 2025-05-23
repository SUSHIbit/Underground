<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('UEPoints Information') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    <!-- UEPoints Overview Section -->
                    <div class="mb-8 p-6 bg-gray-900/50 rounded-lg border border-amber-800/20">
                        <h3 class="text-xl font-bold mb-4 text-amber-400">Your UEPoints</h3>
                        <div class="flex flex-col md:flex-row md:items-center gap-6">
                            <div class="text-center p-4 bg-gray-800 rounded-lg border border-amber-800/20 flex-grow md:max-w-xs">
                                <div class="text-sm text-gray-400 mb-1">Current UEPoints</div>
                                <div class="text-3xl font-bold text-amber-500">{{ $user->ue_points }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- About UEPoints Section -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4 text-amber-400">About UEPoints</h3>
                        <div class="bg-gray-900/50 p-6 rounded-lg border border-amber-800/20">
                            <p class="mb-3 text-gray-300">UEPoints (UniKL E-Points) allow you to retake quizzes and challenges to improve your scores.</p>
                            <p class="mb-3 text-gray-300">When you retake a quiz or challenge, your new score will replace the previous one, potentially helping you to improve your overall ranking.</p>
                            <ul class="list-disc list-inside mb-3 space-y-2 text-gray-300">
                                <li>Retaking a quiz costs <strong class="text-amber-400">5 UEPoints</strong></li>
                                <li>Retaking a challenge costs <strong class="text-amber-400">10 UEPoints</strong></li>
                            </ul>
                        </div>
                    </div>

                    <!-- How to Earn UEPoints Section -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4 text-amber-400">How to Earn UEPoints</h3>
                        <div class="bg-gray-900/50 p-6 rounded-lg border border-amber-800/20">
                            <p class="mb-3 text-gray-300">You can earn UEPoints through various activities:</p>
                            <ul class="list-disc list-inside space-y-2 text-gray-300">
                                <li>Attempt a quiz (<span class="text-green-400">+2 UEPoints</span>)</li>
                                <li>Attempt a challenge (<span class="text-green-400">+2 UEPoints</span>)</li>
                                <li>Participate in a tournament (<span class="text-green-400">+2 UEPoints</span>)</li>
                                <li>Special events and administrative awards</li>
                            </ul>
                        </div>
                    </div>

                    <!-- UEPoints Spending History -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4 text-amber-400">Recent UEPoints Activity</h3>
                        @if($spendingHistory->count() > 0)
                            <div class="overflow-x-auto bg-gray-900/50 rounded-lg border border-amber-800/20">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-amber-800/20">
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Activity</th>
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Points</th>
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($spendingHistory as $history)
                                            <tr class="border-b border-gray-700">
                                                <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-400">
                                                    {{ $history->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="py-3 px-4 whitespace-nowrap text-sm font-medium text-gray-300">
                                                    Retake: 
                                                    @if($history->set->type === 'quiz')
                                                        Quiz - {{ $history->set->quizDetail->subject->name }}
                                                    @else
                                                        Challenge - {{ $history->set->challengeDetail->name }}
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 whitespace-nowrap text-sm text-red-400">
                                                    -{{ $history->ue_points_spent }}
                                                </td>
                                                <td class="py-3 px-4 whitespace-nowrap text-sm
                                                    {{ $history->score_percentage >= 70 ? 'text-green-400' : ($history->score_percentage >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                                    {{ $history->score }}/{{ $history->total_questions }}
                                                    ({{ $history->score_percentage }}%)
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-900/50 p-6 rounded-lg text-center border border-amber-800/20">
                                <p class="text-gray-400">You haven't spent any UEPoints yet.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- UEPoints Management Tips -->
                    <div class="mb-4">
                        <h3 class="text-xl font-bold mb-4 text-amber-400">UEPoints Management Tips</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-900/50 p-4 rounded-lg border border-amber-800/20">
                                <h4 class="font-medium text-lg mb-2 text-amber-400">Strategic Planning</h4>
                                <p class="text-gray-300">
                                    Save your UEPoints for challenges that offer maximum point potential. 
                                    Retaking a challenge with a potential 10-point reward is more efficient 
                                    than retaking multiple quizzes.
                                </p>
                            </div>
                            
                            <div class="bg-gray-900/50 p-4 rounded-lg border border-amber-800/20">
                                <h4 class="font-medium text-lg mb-2 text-amber-400">Be Prepared</h4>
                                <p class="text-gray-300">
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