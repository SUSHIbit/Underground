<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Quiz Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-200">
                    <div class="mb-6">
                        <a href="{{ route('quizzes.index') }}" class="text-amber-400 hover:text-amber-300">
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
                    
                    <h3 class="text-lg font-medium mb-2 text-amber-400">Quiz: {{ $set->quizDetail->subject->name }} - {{ $set->quizDetail->topic->name }}</h3>
                    <p class="text-gray-400 mb-6">Set #{{ $set->set_number }}</p>
                    
                    @if($isCompleted && $attempt)
                        <!-- Show completed quiz results -->
                        <div class="bg-green-900/20 border border-green-800/20 rounded-lg p-6 mb-6">
                            <div class="flex flex-col md:flex-row justify-between items-center">
                                <div>
                                    <h4 class="text-lg font-medium text-green-400">Quiz Completed</h4>
                                    <p class="text-gray-400">Completed on {{ $attempt->created_at->format('F j, Y') }}</p>
                                </div>
                                <div class="mt-4 md:mt-0 text-center">
                                    <div class="text-3xl font-bold {{ $attempt->score_percentage >= 70 ? 'text-green-400' : ($attempt->score_percentage >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                        {{ $attempt->score }}/{{ $attempt->total_questions }}
                                    </div>
                                    <div class="text-lg text-gray-300">
                                        {{ $attempt->score_percentage }}%
                                    </div>
                                    <div class="mt-1 text-sm text-green-400 font-medium">
                                        +5 points earned
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-center">
                                <a href="{{ route('results.show', $attempt) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-md">
                                    View Detailed Results
                                </a>
                            </div>
                        </div>
                        
                        <!-- Quiz Information -->
                        <div class="border border-amber-800/20 rounded-lg p-6 bg-gray-900/50">
                            <h4 class="font-medium mb-4 text-amber-400">Quiz Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="mb-2"><span class="font-medium text-gray-300">Subject:</span> <span class="text-gray-400">{{ $set->quizDetail->subject->name }}</span></p>
                                    <p class="mb-2"><span class="font-medium text-gray-300">Topic:</span> <span class="text-gray-400">{{ $set->quizDetail->topic->name }}</span></p>
                                    <p><span class="font-medium text-gray-300">Questions:</span> <span class="text-gray-400">{{ $set->questions->count() }}</span></p>
                                </div>
                                <div>
                                    <p class="mb-2"><span class="font-medium text-gray-300">Status:</span> <span class="text-green-400">Completed</span></p>
                                    <p class="mb-2"><span class="font-medium text-gray-300">Score:</span> <span class="text-gray-400">{{ $attempt->score }}/{{ $attempt->total_questions }} ({{ $attempt->score_percentage }}%)</span></p>
                                    <p><span class="font-medium text-gray-300">Points:</span> <span class="text-green-400">+5</span></p>
                                </div>
                            </div>
                        </div>

                        @if($isCompleted && $canRetake)
                            <div class="mt-6 border-t border-amber-800/20 pt-6">
                                <h4 class="text-lg font-medium mb-2 text-amber-400">Retake Quiz</h4>
                                <p class="mb-4 text-gray-300">
                                    Want to improve your score? You can retake this quiz for <strong class="text-amber-400">5 UEPoints</strong>.
                                    <br>
                                    <span class="text-sm text-gray-400">Your new score will overwrite the previous one.</span>
                                </p>
                                
                                <form action="{{ route('quizzes.retake', $set) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-6 rounded-md transition duration-150">
                                        Retake Quiz (5 UEPoints)
                                    </button>
                                </form>
                                
                                <p class="mt-2 text-sm text-gray-400">
                                    Your current UEPoints: <strong class="text-amber-400">{{ auth()->user()->ue_points }}</strong>
                                </p>
                            </div>
                        @elseif($isCompleted && !$canRetake)
                            <div class="mt-6 border-t border-amber-800/20 pt-6">
                                <p class="text-gray-400">
                                    You don't have enough UEPoints to retake this quiz. 
                                    <a href="{{ route('uepoints.index') }}" class="text-amber-400 hover:text-amber-300 hover:underline">Learn how to earn more</a>.
                                </p>
                                
                                <p class="mt-2 text-sm text-gray-400">
                                    Your current UEPoints: <strong class="text-red-400">{{ auth()->user()->ue_points }}</strong>
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="mb-6 bg-gray-900/50 p-4 rounded-lg border border-amber-800/20">
                            <h4 class="font-medium mb-2 text-amber-400">Quiz Information:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-400">
                                <li>This quiz contains {{ $set->questions->count() }} multiple-choice questions.</li>
                                <li>You can only attempt this quiz once.</li>
                                <li>Each question has one correct answer.</li>
                                @if(isset($set->quizDetail->timer_minutes) && $set->quizDetail->timer_minutes > 0)
                                    <li class="font-medium text-amber-400">
                                        Time Limit: {{ $set->quizDetail->timer_minutes }} minutes
                                        <span class="block ml-5 mt-1 text-sm text-gray-400">The quiz will be automatically submitted when the time expires.</span>
                                    </li>
                                @else
                                    <li>There is no time limit for this quiz.</li>
                                @endif
                                <li>Use the navigation on the right to move between questions.</li>
                                <li>You must answer all questions before submitting.</li>
                            </ul>
                        </div>
                        
                        <a href="{{ route('quizzes.attempt', $set) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded-md transition duration-150">
                            Start Quiz
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>