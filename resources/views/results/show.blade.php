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

                    

                    <div class="mt-4 text-center">
                        <div class="inline-block px-3 py-1 rounded bg-green-100 text-green-800">
                            <p class="text-sm font-medium">
                                @if($attempt->set->type === 'quiz')
                                    <span class="font-bold">+5</span> points earned for completing this quiz
                                @else
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