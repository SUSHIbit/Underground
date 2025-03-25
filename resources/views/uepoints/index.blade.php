<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('UEPoints Information') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- UEPoints Overview Section -->
                    <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                        <h3 class="text-xl font-bold mb-4">Your UEPoints</h3>
                        <div class="flex flex-col md:flex-row md:items-center gap-6">
                            <div class="text-center p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex-grow md:max-w-xs">
                                <div class="text-sm text-gray-600 mb-1">Current UEPoints</div>
                                <div class="text-3xl font-bold text-blue-600">{{ $user->ue_points }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- About UEPoints Section -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4">About UEPoints</h3>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <p class="mb-3">UEPoints (UniKL E-Points) allow you to retake quizzes and challenges to improve your scores.</p>
                            <p class="mb-3">When you retake a quiz or challenge, your new score will replace the previous one, potentially helping you to improve your overall ranking.</p>
                            <ul class="list-disc list-inside mb-3 space-y-2">
                                <li>Retaking a quiz costs <strong>5 UEPoints</strong></li>
                                <li>Retaking a challenge costs <strong>10 UEPoints</strong></li>
                            </ul>
                        </div>
                    </div>

                    <!-- How to Earn UEPoints Section -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4">How to Earn UEPoints</h3>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <p class="mb-3">UEPoints are awarded by administration based on various activities and special events.</p>
                            <ul class="list-disc list-inside space-y-2">
                                <li>Participating in weekly challenges</li>
                                <li>Special events and tournaments</li>
                                <li>Classroom participation</li>
                                <li>Administrative awards</li>
                            </ul>
                        </div>
                    </div>

                    <!-- UEPoints Spending History -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4">Recent UEPoints Activity</h3>
                        @if($spendingHistory->count() > 0)
                            <div class="overflow-x-auto bg-white">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($spendingHistory as $history)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $history->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    Retake: 
                                                    @if($history->set->type === 'quiz')
                                                        Quiz - {{ $history->set->quizDetail->subject->name }}
                                                    @else
                                                        Challenge - {{ $history->set->challengeDetail->name }}
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                                    -{{ $history->ue_points_spent }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $history->score }}/{{ $history->total_questions }}
                                                    ({{ $history->score_percentage }}%)
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                <p class="text-gray-500">You haven't spent any UEPoints yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>