<!-- resources/views/challenges/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Challenges') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    <!-- Filter/Tab Navigation -->
                    <div class="border-b border-gray-200 mb-6">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                            <li class="mr-2">
                                <a href="#available-challenges" class="inline-block p-4 border-b-2 border-blue-500 rounded-t-lg active text-blue-600" id="available-tab" onclick="showTab('available')">
                                    Available Challenges
                                </a>
                            </li>
                            <li class="mr-2">
                                <a href="#completed-challenges" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="completed-tab" onclick="showTab('completed')">
                                    Completed Challenges
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Available Challenges Section -->
                    <div id="available-challenges" class="challenge-section">
                        <h3 class="text-lg font-medium mb-4">Available Challenges</h3>
                        
                        @php
                            $availableChallenges = $challenges->filter(function($challenge) use ($attemptedChallengeIds) {
                                return !in_array($challenge->id, $attemptedChallengeIds) && $challenge->canAttempt;
                            });
                            
                            $lockedChallenges = $challenges->filter(function($challenge) use ($attemptedChallengeIds) {
                                return !in_array($challenge->id, $attemptedChallengeIds) && !$challenge->canAttempt;
                            });
                        @endphp
                        
                        @if($availableChallenges->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($availableChallenges as $challenge)
                                    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">
                                        <div class="p-4 bg-blue-50 border-b">
                                            <h4 class="font-medium">{{ $challenge->challengeDetail->name }}</h4>
                                            <p class="text-sm text-gray-600">Set #{{ $challenge->set_number }}</p>
                                        </div>
                                        <div class="p-4">
                                            <p class="mb-2 text-sm">
                                                <span class="font-medium">Prerequisites:</span>
                                            </p>
                                            <ul class="list-disc list-inside mb-4 text-sm text-gray-600">
                                                @foreach($challenge->challengeDetail->prerequisites as $prereq)
                                                    <li>
                                                        Set #{{ $prereq->set_number }}:
                                                        {{ $prereq->quizDetail->subject->name }} - 
                                                        {{ $prereq->quizDetail->topic->name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            <a href="{{ route('challenges.show', $challenge) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Start Challenge
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($lockedChallenges->count() > 0)
                                <h4 class="text-lg font-medium mt-8 mb-4">Locked Challenges</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($lockedChallenges as $challenge)
                                        <div class="border rounded-lg overflow-hidden shadow-sm opacity-75">
                                            <div class="p-4 bg-gray-100 border-b">
                                                <h4 class="font-medium">{{ $challenge->challengeDetail->name }}</h4>
                                                <p class="text-sm text-gray-600">Set #{{ $challenge->set_number }}</p>
                                            </div>
                                            <div class="p-4">
                                                <p class="mb-2 text-sm">
                                                    <span class="font-medium">Prerequisites:</span>
                                                </p>
                                                <ul class="list-disc list-inside mb-4 text-sm text-gray-600">
                                                    @foreach($challenge->challengeDetail->prerequisites as $prereq)
                                                        <li class="{{ in_array($prereq->id, $attemptedChallengeIds) ? 'text-green-600' : '' }}">
                                                            Set #{{ $prereq->set_number }}:
                                                            {{ $prereq->quizDetail->subject->name }} - 
                                                            {{ $prereq->quizDetail->topic->name }}
                                                            @if(in_array($prereq->id, $attemptedChallengeIds))
                                                                ✓
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                
                                                <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                                                    Complete prerequisites first
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @elseif($lockedChallenges->count() > 0)
                            <div class="bg-gray-50 p-6 rounded-lg text-center mb-8">
                                <p class="text-gray-500">You don't have any available challenges yet.</p>
                                <p class="text-gray-500 mt-2">Complete the prerequisites below to unlock challenges!</p>
                            </div>
                            
                            <h4 class="text-lg font-medium mt-8 mb-4">Locked Challenges</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($lockedChallenges as $challenge)
                                    <div class="border rounded-lg overflow-hidden shadow-sm opacity-75">
                                        <div class="p-4 bg-gray-100 border-b">
                                            <h4 class="font-medium">{{ $challenge->challengeDetail->name }}</h4>
                                            <p class="text-sm text-gray-600">Set #{{ $challenge->set_number }}</p>
                                        </div>
                                        <div class="p-4">
                                            <p class="mb-2 text-sm">
                                                <span class="font-medium">Prerequisites:</span>
                                            </p>
                                            <ul class="list-disc list-inside mb-4 text-sm text-gray-600">
                                                @foreach($challenge->challengeDetail->prerequisites as $prereq)
                                                    <li class="{{ in_array($prereq->id, $attemptedChallengeIds) ? 'text-green-600' : '' }}">
                                                        Set #{{ $prereq->set_number }}:
                                                        {{ $prereq->quizDetail->subject->name }} - 
                                                        {{ $prereq->quizDetail->topic->name }}
                                                        @if(in_array($prereq->id, $attemptedChallengeIds))
                                                            ✓
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                                                Complete prerequisites first
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                <p class="text-gray-500">No challenges available at the moment.</p>
                                <p class="text-gray-500 mt-2">Check back later for new content!</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Completed Challenges Section -->
                    <div id="completed-challenges" class="challenge-section hidden">
                        <h3 class="text-lg font-medium mb-4">Completed Challenges</h3>
                        
                        @php
                            $completedChallenges = $challenges->filter(function($challenge) use ($attemptedChallengeIds) {
                                return in_array($challenge->id, $attemptedChallengeIds);
                            });
                        @endphp
                        
                        @if($completedChallenges->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($completedChallenges as $challenge)
                                    @php
                                        $attempt = \App\Models\QuizAttempt::where('user_id', auth()->id())
                                                  ->where('set_id', $challenge->id)
                                                  ->where('completed', true)
                                                  ->first();
                                    @endphp
                                    <div class="border rounded-lg overflow-hidden shadow-sm">
                                        <div class="p-4 bg-green-50 border-b">
                                            <h4 class="font-medium">{{ $challenge->challengeDetail->name }}</h4>
                                            <p class="text-sm text-gray-600">Set #{{ $challenge->set_number }}</p>
                                        </div>
                                        <div class="p-4">
                                            @if($attempt)
                                                <div class="mb-4">
                                                    <p>
                                                        <span class="text-sm font-medium">Score:</span>
                                                        <span class="font-medium {{ $attempt->score_percentage >= 70 ? 'text-green-600' : ($attempt->score_percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                            {{ $attempt->score }}/{{ $attempt->total_questions }}
                                                            ({{ $attempt->score_percentage }}%)
                                                        </span>
                                                    </p>
                                                    <p class="mt-1 text-sm text-gray-500">
                                                        Completed on {{ $attempt->created_at->format('M d, Y') }}
                                                    </p>
                                                    
                                                    @php
                                                        $pointsEarned = 0;
                                                        $percentage = ($attempt->score / $attempt->total_questions) * 100;
                                                        
                                                        if ($percentage >= 20 && $percentage < 40) {
                                                            $pointsEarned = 2;
                                                        } elseif ($percentage >= 40 && $percentage < 60) {
                                                            $pointsEarned = 4;
                                                        } elseif ($percentage >= 60 && $percentage < 80) {
                                                            $pointsEarned = 6;
                                                        } elseif ($percentage >= 80 && $percentage < 100) {
                                                            $pointsEarned = 8;
                                                        } elseif ($percentage == 100) {
                                                            $pointsEarned = 10;
                                                        }
                                                    @endphp
                                                    
                                                    <p class="mt-1 text-sm text-green-600">
                                                        <span class="font-medium">Points earned:</span> +{{ $pointsEarned }}
                                                    </p>
                                                </div>
                                                
                                                <a href="{{ route('results.show', $attempt) }}" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                    View Results
                                                </a>
                                            @else
                                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                                    Completed
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                <p class="text-gray-500">You haven't completed any challenges yet.</p>
                                <p class="text-gray-500 mt-2">Start taking challenges to see your progress here!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            // Hide all sections
            document.querySelectorAll('.challenge-section').forEach(section => {
                section.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('[id$="-tab"]').forEach(tab => {
                tab.classList.remove('text-blue-600', 'border-blue-500');
                tab.classList.add('border-transparent');
            });
            
            // Show the selected section and activate tab
            document.getElementById(tabName + '-challenges').classList.remove('hidden');
            document.getElementById(tabName + '-tab').classList.add('text-blue-600', 'border-blue-500');
            document.getElementById(tabName + '-tab').classList.remove('border-transparent');
        }
    </script>
</x-app-layout>