<!-- resources/views/challenges/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Challenge Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('challenges.index') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Challenges
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
                            
                            // Calculate points earned based on score percentage
                            $pointsEarned = 0;
                            if ($attempt) {
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
                            }
                        }
                    @endphp
                    
                    <h3 class="text-lg font-medium mb-2">Challenge: {{ $set->challengeDetail->name }}</h3>
                    <p class="text-gray-600 mb-6">Set #{{ $set->set_number }}</p>
                    
                    @if($isCompleted && $attempt)
                        <!-- Show completed challenge results -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                            <div class="flex flex-col md:flex-row justify-between items-center">
                                <div>
                                    <h4 class="text-lg font-medium text-green-800">Challenge Completed</h4>
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
                                        +{{ $pointsEarned }} points earned
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-center">
                                <a href="{{ route('results.show', $attempt) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                                    View Detailed Results
                                </a>
                            </div>
                        </div>
                        
                        <!-- Challenge Information -->
                        <div class="border rounded-lg p-6">
                            <h4 class="font-medium mb-4">Challenge Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="mb-2"><span class="font-medium">Name:</span> {{ $set->challengeDetail->name }}</p>
                                    <p><span class="font-medium">Questions:</span> {{ $set->questions->count() }}</p>
                                    
                                    <div class="mt-4">
                                        <p class="font-medium mb-2">Prerequisites:</p>
                                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                                            @foreach($set->challengeDetail->prerequisites as $prereq)
                                                <li class="{{ $prereq->isAttemptedBy($user) ? 'text-green-600' : '' }}">
                                                    Set #{{ $prereq->set_number }}:
                                                    {{ $prereq->quizDetail->subject->name }} - 
                                                    {{ $prereq->quizDetail->topic->name }}
                                                    @if($prereq->isAttemptedBy($user))
                                                        ✓
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-2"><span class="font-medium">Status:</span> <span class="text-green-600">Completed</span></p>
                                    <p class="mb-2"><span class="font-medium">Score:</span> {{ $attempt->score }}/{{ $attempt->total_questions }} ({{ $attempt->score_percentage }}%)</p>
                                    <p><span class="font-medium">Points:</span> <span class="text-green-600">+{{ $pointsEarned }}</span></p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-6">
                            <h4 class="font-medium mb-2">Prerequisites:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-600 mb-4">
                                @foreach($set->challengeDetail->prerequisites as $prereq)
                                    <li class="{{ $prereq->isAttemptedBy($user) ? 'text-green-600' : '' }}">
                                        Set #{{ $prereq->set_number }}:
                                        {{ $prereq->quizDetail->subject->name }} - 
                                        {{ $prereq->quizDetail->topic->name }}
                                        @if($prereq->isAttemptedBy($user))
                                            ✓
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            
                            <h4 class="font-medium mb-2">Instructions:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                <li>This challenge contains {{ $set->questions->count() }} multiple-choice questions.</li>
                                <li>You can only attempt this challenge once.</li>
                                <li>Each question has one correct answer.</li>
                                <li>Use the navigation on the right to move between questions.</li>
                                <li>You must answer all questions before submitting.</li>
                            </ul>
                        </div>
                        
                        <a href="{{ route('challenges.attempt', $set) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Start Challenge
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>