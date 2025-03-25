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
                        
                        <div class="mb-6">
                            <h4 class="font-medium mb-2">Challenge Information:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                <li>This challenge contains {{ $set->questions->count() }} multiple-choice questions.</li>
                                <li>You can only attempt this challenge once.</li>
                                <li>Each question has one correct answer.</li>
                                @if(isset($set->challengeDetail->timer_minutes) && $set->challengeDetail->timer_minutes > 0)
                                    <li class="font-medium text-blue-600">
                                        Time Limit: {{ $set->challengeDetail->timer_minutes }} minutes
                                        <span class="block ml-5 mt-1 text-sm">The challenge will be automatically submitted when the time expires.</span>
                                    </li>
                                @else
                                    <li>There is no time limit for this challenge.</li>
                                @endif
                                <li>Use the navigation on the right to move between questions.</li>
                                <li>You must answer all questions before submitting.</li>
                            </ul>
                        </div>

                        @if($isCompleted && $canRetake)
                            <div class="mt-6 border-t pt-6">
                                <h4 class="text-lg font-medium mb-2">Retake Challenge</h4>
                                <p class="mb-4">
                                    Want to improve your score? You can retake this challenge for <strong>10 UEPoints</strong>.
                                    <br>
                                    <span class="text-sm text-gray-600">Your new score will overwrite the previous one.</span>
                                </p>
                                
                                <form action="{{ route('challenges.retake', $set) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                        Retake Challenge (10 UEPoints)
                                    </button>
                                </form>
                                
                                <p class="mt-2 text-sm text-gray-600">
                                    Your current UEPoints: <strong>{{ auth()->user()->ue_points }}</strong>
                                </p>
                            </div>
                        @elseif($isCompleted && !$canRetake)
                            <div class="mt-6 border-t pt-6">
                                @if(!auth()->user()->hasEnoughUEPoints(10))
                                    <p class="text-gray-600">
                                        You don't have enough UEPoints to retake this challenge. 
                                        <a href="{{ route('uepoints.index') }}" class="text-blue-500 hover:underline">Learn how to earn more</a>.
                                    </p>
                                @elseif(!$hasCompletedPrerequisites)
                                    <p class="text-gray-600">
                                        You need to complete all prerequisites again before you can retake this challenge.
                                    </p>
                                @endif
                                
                                <p class="mt-2 text-sm text-gray-600">
                                    Your current UEPoints: <strong>{{ auth()->user()->ue_points }}</strong>
                                </p>
                            </div>
                        @endif
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