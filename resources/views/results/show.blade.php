<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Quiz Results') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-200">
                    <div class="mb-6">
                        <a href="{{ $attempt->set->type === 'quiz' ? route('quizzes.index') : route('challenges.index') }}" class="text-amber-400 hover:text-amber-300">
                            &larr; Back to {{ $attempt->set->type === 'quiz' ? 'Quizzes' : 'Challenges' }}
                        </a>
                    </div>
                    
                    @php
                        $isQuiz = $attempt->set->type === 'quiz';
                        
                        // For challenges, calculate points earned
                        $pointsEarned = 0;
                        if (!$isQuiz) {
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
                        } else {
                            $pointsEarned = 5; // Standard for quizzes
                        }
                    @endphp
                    
                    <!-- Results Header -->
                    <div class="mb-8">
                        <h3 class="text-xl font-medium mb-2 text-amber-400">
                            {{ $isQuiz ? 'Quiz' : 'Challenge' }}: 
                            @if($isQuiz)
                                {{ $attempt->set->quizDetail->subject->name }} - {{ $attempt->set->quizDetail->topic->name }}
                            @else
                                {{ $attempt->set->challengeDetail->name }}
                            @endif
                        </h3>
                        <p class="text-gray-400">Set #{{ $attempt->set->set_number }}</p>
                    </div>
                    
                    <!-- Results Summary -->
                    <div class="bg-gray-900/50 border border-amber-800/20 rounded-lg p-6 mb-8">
                        <div class="flex flex-col md:flex-row justify-between gap-6">
                            <div>
                                <h4 class="text-lg font-medium text-amber-400 mb-2">Results Summary</h4>
                                <p class="mb-1 text-gray-300">
                                    <span class="font-medium text-gray-400">{{ $isQuiz ? 'Quiz' : 'Challenge' }} Completed:</span> 
                                    {{ $attempt->created_at->format('F j, Y, g:i a') }}
                                </p>
                                <p class="mb-1 text-gray-300">
                                    <span class="font-medium text-gray-400">Total Questions:</span> 
                                    {{ $attempt->total_questions }}
                                </p>
                                <p class="mb-1 text-gray-300">
                                    <span class="font-medium text-gray-400">Questions Answered:</span> 
                                    {{ $attempt->answers->count() }}
                                </p>
                                <p class="mb-1 text-gray-300">
                                    <span class="font-medium text-gray-400">Correct Answers:</span> 
                                    {{ $attempt->score }}
                                </p>
                                @if($attempt->is_retake)
                                <div class="mt-3 text-blue-400 bg-blue-900/20 rounded-md p-2 border border-blue-800/20 inline-block">
                                    <span class="font-medium">Learning Mode:</span> Result not counted
                                </div>
                                @else
                                <p class="mt-3 text-green-400">
                                    <span class="font-medium">Points Earned:</span> 
                                    +{{ $pointsEarned }}
                                </p>
                                @endif
                            </div>
                            
                            <div class="text-center">
                                <div class="inline-block bg-gray-900/80 rounded-lg p-4 border border-amber-800/20">
                                    <div class="text-4xl font-bold mb-2 {{ $attempt->score_percentage >= 70 ? 'text-green-400' : ($attempt->score_percentage >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                        {{ $attempt->score }}/{{ $attempt->total_questions }}
                                    </div>
                                    <div class="text-2xl mb-1 {{ $attempt->score_percentage >= 70 ? 'text-green-400' : ($attempt->score_percentage >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                        {{ $attempt->score_percentage }}%
                                    </div>
                                    <div class="text-gray-400 text-sm">
                                        @if($attempt->score_percentage >= 90)
                                            Excellent!
                                        @elseif($attempt->score_percentage >= 70)
                                            Good job!
                                        @elseif($attempt->score_percentage >= 50)
                                            Nice try!
                                        @else
                                            Keep practicing!
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Results -->
                    <div class="mb-8">
                        <h4 class="text-lg font-medium text-amber-400 mb-4">Detailed Results</h4>
                        
                        <div class="space-y-6">
                            @foreach($attempt->answers as $answer)
                                <div class="bg-gray-900/30 rounded-lg p-4 border {{ $answer->is_correct ? 'border-green-800/30' : 'border-red-800/30' }}">
                                    <div class="flex flex-col sm:flex-row justify-between items-start gap-3 mb-3">
                                        <h5 class="text-lg font-medium flex-1">{{ $answer->question->question_text }}</h5>
                                        <div class="px-3 py-1 rounded-full text-sm {{ $answer->is_correct ? 'bg-green-900/20 text-green-400' : 'bg-red-900/20 text-red-400' }}">
                                            {{ $answer->is_correct ? 'Correct' : 'Incorrect' }}
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                        @foreach($answer->question->options as $option => $text)
                                            <div class="flex items-start p-2 rounded-md {{ $option === $answer->question->correct_answer ? 'bg-green-900/20 border border-green-800/30' : ($option === $answer->selected_answer && !$answer->is_correct ? 'bg-red-900/20 border border-red-800/30' : 'bg-gray-800/50 border border-gray-700/30') }}">
                                                <div class="h-5 w-5 mr-2 flex-shrink-0 {{ $option === $answer->question->correct_answer ? 'text-green-500' : ($option === $answer->selected_answer && !$answer->is_correct ? 'text-red-500' : 'text-gray-500') }}">
                                                    @if($option === $answer->question->correct_answer)
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @elseif($option === $answer->selected_answer && !$answer->is_correct)
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                        </svg>
                                                    @else
                                                        <span class="block h-5 w-5 rounded-full border border-gray-600"></span>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <p class="{{ $option === $answer->question->correct_answer ? 'text-green-400' : ($option === $answer->selected_answer && !$answer->is_correct ? 'text-red-400' : 'text-gray-400') }}">
                                                        <span class="font-medium">{{ $option }}</span>: {{ $text }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if(!$answer->is_correct || $attempt->is_retake)
                                        <div class="p-3 bg-gray-800/70 rounded-md border border-amber-800/20">
                                            <p class="text-sm font-medium text-amber-400 mb-1">Explanation:</p>
                                            <p class="text-gray-300">{{ $answer->question->reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-between items-center">
                        <a href="{{ $isQuiz ? route('quizzes.show', $attempt->set) : route('challenges.show', $attempt->set) }}" class="w-full sm:w-auto text-center inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-6 rounded-md transition duration-150">
                            {{ $isQuiz ? 'Quiz' : 'Challenge' }} Details
                        </a>
                        
                        <a href="{{ $isQuiz ? route('quizzes.index') : route('challenges.index') }}" class="w-full sm:w-auto text-center inline-block bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-6 rounded-md border border-amber-800/20 transition duration-150">
                            View All {{ $isQuiz ? 'Quizzes' : 'Challenges' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>