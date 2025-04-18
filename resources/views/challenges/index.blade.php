<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Challenges') }}
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
                    
                    <!-- Search and Filter Section -->
                    <div class="mb-6 bg-gray-900/50 p-4 rounded-lg border border-amber-800/20">
                        <form action="{{ route('challenges.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-300 mb-1">Search</label>
                                <input 
                                    type="text" 
                                    name="search" 
                                    id="search" 
                                    placeholder="Search by challenge or subject name..." 
                                    class="w-full p-2 rounded-md border-amber-800/30 bg-gray-700 text-white focus:border-amber-500 focus:ring focus:ring-amber-600 focus:ring-opacity-50"
                                    value="{{ $search ?? '' }}"
                                >
                            </div>
                            
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-300 mb-1">Filter by Subject</label>
                                <select name="subject" id="subject" class="w-full p-2 rounded-md border-amber-800/30 bg-gray-700 text-white focus:border-amber-500 focus:ring focus:ring-amber-600 focus:ring-opacity-50">
                                    <option value="">All Subjects</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ isset($subjectId) && $subjectId == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded-md mr-2 transition duration-150">
                                    Apply
                                </button>
                                <a href="{{ route('challenges.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-4 rounded-md border border-amber-800/20 transition duration-150">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Filter/Tab Navigation -->
                    <div class="border-b border-amber-800/20 mb-6">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                            <li class="mr-2">
                                <a href="#available-challenges" class="inline-block p-4 border-b-2 border-amber-500 rounded-t-lg active text-amber-500" id="available-tab" onclick="showTab('available')">
                                    Available Challenges
                                </a>
                            </li>
                            <li class="mr-2">
                                <a href="#completed-challenges" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-400 hover:border-gray-500" id="completed-tab" onclick="showTab('completed')">
                                    Completed Challenges
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Available Challenges Section -->
                    <div id="available-challenges" class="challenge-section">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-amber-400">Available Challenges</h3>
                            <p class="text-sm text-gray-400">
                                {{ $challenges->filter(function($challenge) use ($attemptedChallengeIds) {
                                    return !in_array($challenge->id, $attemptedChallengeIds) && $challenge->canAttempt;
                                })->count() }} challenges available
                            </p>
                        </div>
                        
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
                                    <div class="border border-amber-800/20 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 bg-gray-800">
                                        <div class="p-4 bg-amber-900/20 border-b border-amber-800/20">
                                            <h4 class="font-medium text-amber-400">{{ $challenge->challengeDetail->name }}</h4>
                                            <p class="text-sm text-gray-400">Set #{{ $challenge->set_number }}</p>
                                        </div>
                                        <div class="p-4">
                                            <p class="mb-2 text-sm text-gray-300">
                                                <span class="font-medium">Prerequisites:</span>
                                            </p>
                                            <ul class="list-disc list-inside mb-4 text-sm text-gray-400">
                                                @foreach($challenge->challengeDetail->prerequisites as $prereq)
                                                    <li>
                                                        Set #{{ $prereq->set_number }}:
                                                        {{ $prereq->quizDetail->subject->name }} - 
                                                        {{ $prereq->quizDetail->topic->name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            <a href="{{ route('challenges.show', $challenge) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded-md transition duration-150">
                                                Start Challenge
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($lockedChallenges->count() > 0)
                                <h4 class="text-lg font-medium mt-8 mb-4 text-amber-400">Locked Challenges</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($lockedChallenges as $challenge)
                                        <div class="border border-amber-800/20 rounded-lg overflow-hidden shadow-sm opacity-75 bg-gray-800">
                                            <div class="p-4 bg-gray-900/50 border-b border-amber-800/20">
                                                <h4 class="font-medium text-gray-400">{{ $challenge->challengeDetail->name }}</h4>
                                                <p class="text-sm text-gray-500">Set #{{ $challenge->set_number }}</p>
                                            </div>
                                            <div class="p-4">
                                                <p class="mb-2 text-sm text-gray-400">
                                                    <span class="font-medium">Prerequisites:</span>
                                                </p>
                                                <ul class="list-disc list-inside mb-4 text-sm text-gray-500">
                                                    @foreach($challenge->challengeDetail->prerequisites as $prereq)
                                                        <li class="{{ in_array($prereq->id, $attemptedChallengeIds) ? 'text-green-500' : '' }}">
                                                            Set #{{ $prereq->set_number }}:
                                                            {{ $prereq->quizDetail->subject->name }} - 
                                                            {{ $prereq->quizDetail->topic->name }}
                                                            @if(in_array($prereq->id, $attemptedChallengeIds))
                                                                ✓
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                
                                                <span class="inline-block bg-amber-900/20 text-amber-400 text-xs px-2 py-1 rounded-md border border-amber-800/20">
                                                    Complete prerequisites first
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @elseif($lockedChallenges->count() > 0)
                            <div class="bg-gray-900/50 p-6 rounded-lg text-center mb-8 border border-amber-800/20">
                                @if(isset($search) || isset($subjectId))
                                    <p class="text-gray-400">No available challenges match your search criteria.</p>
                                    <p class="text-gray-500 mt-2">Try different search terms or reset the filters.</p>
                                @else
                                    <p class="text-gray-400">You don't have any available challenges yet.</p>
                                    <p class="text-gray-500 mt-2">Complete the prerequisites below to unlock challenges!</p>
                                @endif
                            </div>
                            
                            <h4 class="text-lg font-medium mt-8 mb-4 text-amber-400">Locked Challenges</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($lockedChallenges as $challenge)
                                    <div class="border border-amber-800/20 rounded-lg overflow-hidden shadow-sm opacity-75 bg-gray-800">
                                        <div class="p-4 bg-gray-900/50 border-b border-amber-800/20">
                                            <h4 class="font-medium text-gray-400">{{ $challenge->challengeDetail->name }}</h4>
                                            <p class="text-sm text-gray-500">Set #{{ $challenge->set_number }}</p>
                                        </div>
                                        <div class="p-4">
                                            <p class="mb-2 text-sm text-gray-400">
                                                <span class="font-medium">Prerequisites:</span>
                                            </p>
                                            <ul class="list-disc list-inside mb-4 text-sm text-gray-500">
                                                @foreach($challenge->challengeDetail->prerequisites as $prereq)
                                                    <li class="{{ in_array($prereq->id, $attemptedChallengeIds) ? 'text-green-500' : '' }}">
                                                        Set #{{ $prereq->set_number }}:
                                                        {{ $prereq->quizDetail->subject->name }} - 
                                                        {{ $prereq->quizDetail->topic->name }}
                                                        @if(in_array($prereq->id, $attemptedChallengeIds))
                                                            ✓
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            <span class="inline-block bg-amber-900/20 text-amber-400 text-xs px-2 py-1 rounded-md border border-amber-800/20">
                                                Complete prerequisites first
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-900/50 p-6 rounded-lg text-center border border-amber-800/20">
                                @if(isset($search) || isset($subjectId))
                                    <p class="text-gray-400">No challenges match your search criteria.</p>
                                    <p class="text-gray-500 mt-2">Try different search terms or reset the filters.</p>
                                @else
                                    <p class="text-gray-400">No challenges available at the moment.</p>
                                    <p class="text-gray-500 mt-2">Check back later for new content!</p>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <!-- Completed Challenges Section -->
                    <div id="completed-challenges" class="challenge-section hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-amber-400">Completed Challenges</h3>
                            <p class="text-sm text-gray-400">
                                {{ $challenges->filter(function($challenge) use ($attemptedChallengeIds) {
                                    return in_array($challenge->id, $attemptedChallengeIds);
                                })->count() }} challenges completed
                            </p>
                        </div>
                        
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
                                    <div class="border border-amber-800/20 rounded-lg overflow-hidden shadow-sm bg-gray-800">
                                        <a href="{{ route('challenges.show', $challenge) }}" class="block">
                                            <div class="p-4 bg-green-900/20 border-b border-amber-800/20">
                                                <h4 class="font-medium text-amber-400">{{ $challenge->challengeDetail->name }}</h4>
                                                <p class="text-sm text-gray-400">Set #{{ $challenge->set_number }}</p>
                                            </div>
                                        </a>
                                        <div class="p-4">
                                            @if($attempt)
                                                <div class="mb-4">
                                                    <p>
                                                        <span class="text-sm font-medium text-gray-400">Score:</span>
                                                        <span class="font-medium {{ $attempt->score_percentage >= 70 ? 'text-green-400' : ($attempt->score_percentage >= 50 ? 'text-amber-400' : 'text-red-400') }}">
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
                                                    
                                                    <p class="mt-1 text-sm text-green-500">
                                                        <span class="font-medium">Points earned:</span> +{{ $pointsEarned }}
                                                    </p>
                                                </div>
                                                
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('results.show', $attempt) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-150">
                                                        View Results
                                                    </a>
                                                    <a href="{{ route('challenges.show', $challenge) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded-md transition duration-150">
                                                        Challenge Details
                                                    </a>
                                                </div>
                                            @else
                                                <span class="inline-block bg-green-900/20 text-green-400 text-xs px-2 py-1 rounded-md border border-green-800/20">
                                                    Completed
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-900/50 p-6 rounded-lg text-center border border-amber-800/20">
                                <p class="text-gray-400">You haven't completed any challenges yet.</p>
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
                tab.classList.remove('text-amber-500', 'border-amber-500');
                tab.classList.add('border-transparent');
            });
            
            // Show the selected section and activate tab
            document.getElementById(tabName + '-challenges').classList.remove('hidden');
            document.getElementById(tabName + '-tab').classList.add('text-amber-500', 'border-amber-500');
            document.getElementById(tabName + '-tab').classList.remove('border-transparent');
        }
    </script>
</x-app-layout>