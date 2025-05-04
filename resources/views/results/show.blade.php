<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Results') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('dashboard') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Dashboard
                        </a>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">
                            @if($attempt->set->type === 'quiz')
                                Quiz: {{ $attempt->set->quizDetail->subject->name }} - 
                                {{ $attempt->set->quizDetail->topic->name }}
                            @else
                                Challenge: {{ $attempt->set->challengeDetail->name }}
                            @endif
                        </h3>
                        <p class="text-gray-600">Set #{{ $attempt->set->set_number }}</p>
                    </div>
                    
                    @if($attempt->is_retake && session('showing_learning_mode'))
                    <!-- Learning Mode Results Banner -->
                    <div class="mb-8 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-6 rounded-md shadow-md">
                        <h3 class="text-lg font-bold mb-2">Learning Mode Results</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white p-4 rounded border border-blue-200">
                                <h4 class="font-semibold text-blue-800">Original Record (Unchanged):</h4>
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ $attempt->originalAttempt->score ?? session('original_score', '?') }}/{{ $attempt->originalAttempt->total_questions ?? session('original_total', '?') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    @if($attempt->originalAttempt)
                                        ({{ $attempt->originalAttempt->score_percentage }}%)
                                    @else
                                        Your official score remains unchanged
                                    @endif
                                </p>
                            </div>
                            <div class="bg-white p-4 rounded border border-blue-200">
                                <h4 class="font-semibold text-blue-800">Learning Attempt Result:</h4>
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ $attempt->score }}/{{ $attempt->total_questions }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    ({{ $attempt->score_percentage }}%) - For learning purposes only
                                </p>
                            </div>
                        </div>
                        <p class="mt-4 text-sm">
                            <strong>Note:</strong> This was a learning mode attempt. Your original score and rankings remain unchanged.
                        </p>
                    </div>
                    @else
                    <!-- Regular Results (Non-Learning Mode) -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <div>
                                <h4 class="text-xl font-bold text-gray-700">Your Score</h4>
                                <p class="text-gray-600">Completed on {{ $attempt->created_at->format('F j, Y') }}</p>
                            </div>
                            <div class="text-center mt-4 md:mt-0">
                                <div class="text-4xl font-bold {{ $attempt->score_percentage >= 70 ? 'text-green-600' : ($attempt->score_percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $attempt->score }}/{{ $attempt->total_questions }}
                                </div>
                                <div class="text-lg text-gray-700">
                                    {{ $attempt->score_percentage }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($attempt->is_retake && !session('showing_learning_mode'))
                    <div class="bg-blue-50 rounded-lg p-4 mb-6">
                        <h4 class="text-lg font-bold text-blue-700">Retake Information</h4>
                        <p class="text-gray-700">This was a retake attempt. You spent {{ $attempt->ue_points_spent }} UEPoints.</p>
                        
                        @php
                            $originalAttempt = $attempt->originalAttempt;
                            $scoreImprovement = null;
                            
                            if ($originalAttempt) {
                                $scoreImprovement = $attempt->score - $originalAttempt->score;
                            }
                        @endphp
                        
                        @if($originalAttempt && $scoreImprovement !== null)
                            <div class="mt-2">
                                <p class="text-gray-700">
                                    Original score: {{ $originalAttempt->score }}/{{ $originalAttempt->total_questions }} 
                                    ({{ $originalAttempt->score_percentage }}%)
                                </p>
                                <p class="text-gray-700">
                                    New score: {{ $attempt->score }}/{{ $attempt->total_questions }} 
                                    ({{ $attempt->score_percentage }}%)
                                </p>
                                
                                @if($scoreImprovement > 0)
                                    <p class="text-green-600 font-medium mt-2">
                                        You improved by {{ $scoreImprovement }} {{ Str::plural('point', $scoreImprovement) }}!
                                    </p>
                                @elseif($scoreImprovement < 0)
                                    <p class="text-red-600 font-medium mt-2">
                                        Your score decreased by {{ abs($scoreImprovement) }} {{ Str::plural('point', abs($scoreImprovement)) }}.
                                    </p>
                                @else
                                    <p class="text-yellow-600 font-medium mt-2">
                                        Your score remained the same.
                                    </p>
                                @endif
                            </div>
                        @endif
                        
                        <!-- No UEPoints rewards for retakes -->
                    </div>
                    @endif

                    @if(!$attempt->is_retake || !session('showing_learning_mode'))
                    <div class="mt-4 text-center">
                        <div class="inline-block px-3 py-1 rounded bg-green-100 text-green-800">
                            <p class="text-sm font-medium">
                                @if($attempt->set->type === 'quiz' && !$attempt->is_retake)
                                    <span class="font-bold">+5</span> points earned for completing this quiz
                                @elseif($attempt->set->type === 'challenge' && !$attempt->is_retake)
                                    @php
                                        $score = $attempt->score;
                                        $total = $attempt->total_questions;
                                        $percentage = ($score / $total) * 100;
                                        $pointsEarned = 0;
                                        
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
                                    <span class="font-bold">+{{ $pointsEarned }}</span> points earned for this challenge
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif
                    
                    <h3 class="text-lg font-medium mb-4">Question Review</h3>
                    
                    <div class="space-y-6">
                        @foreach($attempt->answers as $answer)
                            <div class="border rounded-lg overflow-hidden">
                                <div class="p-4 {{ $answer->is_correct ? 'bg-green-50' : 'bg-red-50' }}">
                                    <h4 class="text-lg font-medium mb-2">Question {{ $answer->question->question_number }}</h4>
                                    <p class="mb-4">{{ $answer->question->question_text }}</p>
                                    
                                    <div class="space-y-2 mb-4">
                                        @foreach($answer->question->options as $option => $text)
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 w-6">
                                                    {{ $option }}:
                                                </div>
                                                <div class="ml-1">
                                                    <span class="{{ $option === $answer->question->correct_answer ? 'font-bold text-green-600' : 
                                                                   ($option === $answer->selected_answer && !$answer->is_correct ? 'font-bold text-red-600' : '') }}">
                                                        {{ $text }}
                                                    </span>
                                                    
                                                    @if($option === $answer->question->correct_answer)
                                                        <span class="ml-2 text-xs text-green-600">(Correct answer)</span>
                                                    @elseif($option === $answer->selected_answer && !$answer->is_correct)
                                                        <span class="ml-2 text-xs text-red-600">(Your answer)</span>
                                                    @elseif($option === $answer->selected_answer)
                                                        <span class="ml-2 text-xs text-green-600">(Your answer)</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if(!$answer->is_correct)
                                        <div class="mt-2 p-3 bg-yellow-50 rounded-lg">
                                            <p class="text-sm font-medium text-yellow-800">Explanation:</p>
                                            <p class="text-sm text-yellow-700">{{ $answer->question->reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-8">
                        <a href="{{ route('dashboard') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Return to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>