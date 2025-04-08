<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Quizzes') }}
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
                        <form action="{{ route('quizzes.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-300 mb-1">Search</label>
                                <input 
                                    type="text" 
                                    name="search" 
                                    id="search" 
                                    placeholder="Search by subject or topic name..." 
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
                                <a href="{{ route('quizzes.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-4 rounded-md border border-amber-800/20 transition duration-150">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Filter/Tab Navigation -->
                    <div class="border-b border-amber-800/20 mb-6">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                            <li class="mr-2">
                                <a href="#available-quizzes" class="inline-block p-4 border-b-2 border-amber-500 rounded-t-lg active text-amber-500" id="available-tab" onclick="showTab('available')">
                                    Available Quizzes
                                </a>
                            </li>
                            <li class="mr-2">
                                <a href="#completed-quizzes" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-400 hover:border-gray-500" id="completed-tab" onclick="showTab('completed')">
                                    Completed Quizzes
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Available Quizzes Section -->
                    <div id="available-quizzes" class="quiz-section">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-amber-400">Available Quizzes</h3>
                            <p class="text-sm text-gray-400">{{ $quizzes->where(function($quiz) use ($attemptedQuizIds) { return !in_array($quiz->id, $attemptedQuizIds); })->count() }} quizzes found</p>
                        </div>
                        
                        @php
                            $availableQuizzes = $quizzes->filter(function($quiz) use ($attemptedQuizIds) {
                                return !in_array($quiz->id, $attemptedQuizIds);
                            });
                        @endphp
                        
                        @if($availableQuizzes->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($availableQuizzes as $quiz)
                                    <div class="border border-amber-800/20 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 bg-gray-800">
                                        <div class="p-4 bg-amber-900/20 border-b border-amber-800/20">
                                            <h4 class="font-medium text-amber-400">Set #{{ $quiz->set_number }}: {{ $quiz->quizDetail->subject->name }}</h4>
                                            <p class="text-sm text-gray-400">{{ $quiz->quizDetail->topic->name }}</p>
                                        </div>
                                        <div class="p-4">
                                            <p class="mb-4">
                                                <span class="text-sm text-gray-400">Questions:</span> 
                                                <span class="text-white">{{ $quiz->questions->count() }}</span>
                                            </p>
                                            
                                            <a href="{{ route('quizzes.show', $quiz) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded-md transition duration-150">
                                                Start Quiz
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-900/50 p-6 rounded-lg text-center border border-amber-800/20">
                                @if(isset($search) || isset($subjectId))
                                    <p class="text-gray-400">No quizzes found matching your search criteria.</p>
                                    <p class="text-gray-500 mt-2">Try different search terms or reset the filters.</p>
                                @else
                                    <p class="text-gray-400">You've completed all available quizzes!</p>
                                    <p class="text-gray-500 mt-2">Check back later for new content.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <!-- Completed Quizzes Section -->
                    <div id="completed-quizzes" class="quiz-section hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-amber-400">Completed Quizzes</h3>
                            <p class="text-sm text-gray-400">{{ $quizzes->whereIn('id', $attemptedQuizIds)->count() }} quizzes completed</p>
                        </div>
                        
                        @php
                            $completedQuizzes = $quizzes->filter(function($quiz) use ($attemptedQuizIds) {
                                return in_array($quiz->id, $attemptedQuizIds);
                            });
                        @endphp
                        
                        @if($completedQuizzes->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($completedQuizzes as $quiz)
                                    @php
                                        $attempt = \App\Models\QuizAttempt::where('user_id', auth()->id())
                                                 ->where('set_id', $quiz->id)
                                                 ->where('completed', true)
                                                 ->first();
                                    @endphp
                                    <div class="border border-amber-800/20 rounded-lg overflow-hidden shadow-sm bg-gray-800">
                                        <a href="{{ route('quizzes.show', $quiz) }}" class="block">
                                            <div class="p-4 bg-green-900/20 border-b border-amber-800/20">
                                                <h4 class="font-medium text-amber-400">Set #{{ $quiz->set_number }}: {{ $quiz->quizDetail->subject->name }}</h4>
                                                <p class="text-sm text-gray-400">{{ $quiz->quizDetail->topic->name }}</p>
                                            </div>
                                        </a>
                                        <div class="p-4">
                                            <div class="mb-4">
                                                <p class="text-sm text-gray-400">Questions: <span class="text-white">{{ $quiz->questions->count() }}</span></p>
                                                @if($attempt)
                                                    <p class="mt-1">
                                                        <span class="text-sm font-medium text-gray-400">Score:</span>
                                                        <span class="font-medium {{ $attempt->score_percentage >= 70 ? 'text-green-400' : ($attempt->score_percentage >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                                            {{ $attempt->score }}/{{ $attempt->total_questions }}
                                                            ({{ $attempt->score_percentage }}%)
                                                        </span>
                                                    </p>
                                                    <p class="mt-1 text-sm text-gray-500">
                                                        Completed on {{ $attempt->created_at->format('M d, Y') }}
                                                    </p>
                                                    <p class="mt-1 text-sm text-green-500">
                                                        <span class="font-medium">Points earned:</span> +5
                                                    </p>
                                                @endif
                                            </div>
                                            
                                            <div class="flex space-x-2">
                                                @if($attempt)
                                                    <a href="{{ route('results.show', $attempt) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-150">
                                                        View Results
                                                    </a>
                                                    <a href="{{ route('quizzes.show', $quiz) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded-md transition duration-150">
                                                        Quiz Details
                                                    </a>
                                                @else
                                                    <span class="inline-block bg-green-900/20 text-green-400 text-xs px-2 py-1 rounded-md border border-green-800/20">
                                                        Completed
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-900/50 p-6 rounded-lg text-center border border-amber-800/20">
                                <p class="text-gray-400">You haven't completed any quizzes yet.</p>
                                <p class="text-gray-500 mt-2">Start taking quizzes to see your progress here!</p>
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
            document.querySelectorAll('.quiz-section').forEach(section => {
                section.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('[id$="-tab"]').forEach(tab => {
                tab.classList.remove('text-amber-500', 'border-amber-500');
                tab.classList.add('border-transparent');
            });
            
            // Show the selected section and activate tab
            document.getElementById(tabName + '-quizzes').classList.remove('hidden');
            document.getElementById(tabName + '-tab').classList.add('text-amber-500', 'border-amber-500');
            document.getElementById(tabName + '-tab').classList.remove('border-transparent');
        }
    </script>
</x-app-layout>