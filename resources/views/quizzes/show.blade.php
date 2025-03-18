<!-- resources/views/quizzes/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quiz Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('quizzes.index') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Quizzes
                        </a>
                    </div>
                    
                    @php
                        $user = auth()->user();
                        $isCompleted = $set->isAttemptedBy($user);
                        $attempt = null;
                        
                        if ($isCompleted) {
                            $attempt = \App\Models\QuizAttempt::where('user_id', $user->id)
                                     ->where('set_id', $set->id)
                                     ->where('completed', true)
                                     ->first();
                        }
                    @endphp
                    
                    <h3 class="text-lg font-medium mb-2">Quiz: {{ $set->quizDetail->subject->name }} - {{ $set->quizDetail->topic->name }}</h3>
                    <p class="text-gray-600 mb-6">Set #{{ $set->set_number }}</p>
                    
                    @if($isCompleted && $attempt)
                        <!-- Show completed quiz results -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                            <div class="flex flex-col md:flex-row justify-between items-center">
                                <div>
                                    <h4 class="text-lg font-medium text-green-800">Quiz Completed</h4>
                                    <p class="text-gray-600">Completed on {{ $attempt->created_at->format('F j, Y') }}</p>
                                </div>
                                <div class="mt-4 md:mt-0 text-center">
                                    <div class="text-3xl font-bold {{ $attempt->score_percentage >= 70 ? 'text-green-600' : ($attempt->score_percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $attempt->score }}/{{ $attempt->total_questions }}
                                    </div>
                                    <div class="text-lg text-gray-700">
                                        {{ $attempt->score_percentage }}%
                                    </div>
                                    <div class="mt-1 text-sm text-green-600 font-medium">
                                        +5 points earned
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-center">
                                <a href="{{ route('results.show', $attempt) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                                    View Detailed Results
                                </a>
                            </div>
                        </div>
                        
                        <!-- Quiz Information -->
                        <div class="border rounded-lg p-6">
                            <h4 class="font-medium mb-4">Quiz Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="mb-2"><span class="font-medium">Subject:</span> {{ $set->quizDetail->subject->name }}</p>
                                    <p class="mb-2"><span class="font-medium">Topic:</span> {{ $set->quizDetail->topic->name }}</p>
                                    <p><span class="font-medium">Questions:</span> {{ $set->questions->count() }}</p>
                                </div>
                                <div>
                                    <p class="mb-2"><span class="font-medium">Status:</span> <span class="text-green-600">Completed</span></p>
                                    <p class="mb-2"><span class="font-medium">Score:</span> {{ $attempt->score }}/{{ $attempt->total_questions }} ({{ $attempt->score_percentage }}%)</p>
                                    <p><span class="font-medium">Points:</span> <span class="text-green-600">+5</span></p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-6">
                            <h4 class="font-medium mb-2">Quiz Information:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                <li>This quiz contains {{ $set->questions->count() }} multiple-choice questions.</li>
                                <li>You can only attempt this quiz once.</li>
                                <li>Each question has one correct answer.</li>
                                @if(isset($set->quizDetail->timer_minutes) && $set->quizDetail->timer_minutes > 0)
                                    <li class="font-medium text-blue-600">
                                        Time Limit: {{ $set->quizDetail->timer_minutes }} minutes
                                        <span class="block ml-5 mt-1 text-sm">The quiz will be automatically submitted when the time expires.</span>
                                    </li>
                                @else
                                    <li>There is no time limit for this quiz.</li>
                                @endif
                                <li>Use the navigation on the right to move between questions.</li>
                                <li>You must answer all questions before submitting.</li>
                            </ul>
                        </div>
                        
                        <a href="{{ route('quizzes.attempt', $set) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Start Quiz
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>