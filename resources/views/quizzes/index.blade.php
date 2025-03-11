<!-- resources/views/quizzes/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quizzes') }}
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
                    
                    <!-- Search and Filter Section -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form action="{{ route('quizzes.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input 
                                    type="text" 
                                    name="search" 
                                    id="search" 
                                    placeholder="Search by subject or topic name..." 
                                    class="w-full p-2 border border-gray-300 rounded-md"
                                    value="{{ $search ?? '' }}"
                                >
                            </div>
                            
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Filter by Subject</label>
                                <select name="subject" id="subject" class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="">All Subjects</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ isset($subjectId) && $subjectId == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                                    Apply
                                </button>
                                <a href="{{ route('quizzes.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Filter/Tab Navigation -->
                    <div class="border-b border-gray-200 mb-6">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                            <li class="mr-2">
                                <a href="#available-quizzes" class="inline-block p-4 border-b-2 border-blue-500 rounded-t-lg active text-blue-600" id="available-tab" onclick="showTab('available')">
                                    Available Quizzes
                                </a>
                            </li>
                            <li class="mr-2">
                                <a href="#completed-quizzes" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="completed-tab" onclick="showTab('completed')">
                                    Completed Quizzes
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Available Quizzes Section -->
                    <div id="available-quizzes" class="quiz-section">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Available Quizzes</h3>
                            <p class="text-sm text-gray-600">{{ $quizzes->where(function($quiz) use ($attemptedQuizIds) { return !in_array($quiz->id, $attemptedQuizIds); })->count() }} quizzes found</p>
                        </div>
                        
                        @php
                            $availableQuizzes = $quizzes->filter(function($quiz) use ($attemptedQuizIds) {
                                return !in_array($quiz->id, $attemptedQuizIds);
                            });
                        @endphp
                        
                        @if($availableQuizzes->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($availableQuizzes as $quiz)
                                    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">
                                        <div class="p-4 bg-blue-50 border-b">
                                            <h4 class="font-medium">Set #{{ $quiz->set_number }}: {{ $quiz->quizDetail->subject->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $quiz->quizDetail->topic->name }}</p>
                                        </div>
                                        <div class="p-4">
                                            <p class="mb-4">
                                                <span class="text-sm text-gray-600">Questions:</span> 
                                                {{ $quiz->questions->count() }}
                                            </p>
                                            
                                            <a href="{{ route('quizzes.show', $quiz) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Start Quiz
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                @if(isset($search) || isset($subjectId))
                                    <p class="text-gray-500">No quizzes found matching your search criteria.</p>
                                    <p class="text-gray-500 mt-2">Try different search terms or reset the filters.</p>
                                @else
                                    <p class="text-gray-500">You've completed all available quizzes!</p>
                                    <p class="text-gray-500 mt-2">Check back later for new content.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <!-- Completed Quizzes Section -->
                    <div id="completed-quizzes" class="quiz-section hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Completed Quizzes</h3>
                            <p class="text-sm text-gray-600">{{ $quizzes->whereIn('id', $attemptedQuizIds)->count() }} quizzes completed</p>
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
                                    <div class="border rounded-lg overflow-hidden shadow-sm">
                                        <div class="p-4 bg-green-50 border-b">
                                            <h4 class="font-medium">Set #{{ $quiz->set_number }}: {{ $quiz->quizDetail->subject->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $quiz->quizDetail->topic->name }}</p>
                                        </div>
                                        <div class="p-4">
                                            <div class="mb-4">
                                                <p class="text-sm text-gray-600">Questions: {{ $quiz->questions->count() }}</p>
                                                @if($attempt)
                                                    <p class="mt-1">
                                                        <span class="text-sm font-medium">Score:</span>
                                                        <span class="font-medium {{ $attempt->score_percentage >= 70 ? 'text-green-600' : ($attempt->score_percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                            {{ $attempt->score }}/{{ $attempt->total_questions }}
                                                            ({{ $attempt->score_percentage }}%)
                                                        </span>
                                                    </p>
                                                    <p class="mt-1 text-sm text-gray-500">
                                                        Completed on {{ $attempt->created_at->format('M d, Y') }}
                                                    </p>
                                                    <p class="mt-1 text-sm text-green-600">
                                                        <span class="font-medium">Points earned:</span> +5
                                                    </p>
                                                @endif
                                            </div>
                                            
                                            @if($attempt)
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
                                <p class="text-gray-500">You haven't completed any quizzes yet.</p>
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
                tab.classList.remove('text-blue-600', 'border-blue-500');
                tab.classList.add('border-transparent');
            });
            
            // Show the selected section and activate tab
            document.getElementById(tabName + '-quizzes').classList.remove('hidden');
            document.getElementById(tabName + '-tab').classList.add('text-blue-600', 'border-blue-500');
            document.getElementById(tabName + '-tab').classList.remove('border-transparent');
        }
    </script>
</x-app-layout>