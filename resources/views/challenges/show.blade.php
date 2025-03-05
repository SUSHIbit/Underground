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
                    
                    <h3 class="text-lg font-medium mb-2">Challenge: {{ $set->challengeDetail->name }}</h3>
                    <p class="text-gray-600 mb-6">Set #{{ $set->set_number }}</p>
                    
                    <div class="mb-6">
                        <h4 class="font-medium mb-2">Prerequisites:</h4>
                        <ul class="list-disc list-inside space-y-1 text-gray-600 mb-4">
                            @foreach($set->challengeDetail->prerequisites as $prereq)
                                <li>
                                    Set #{{ $prereq->set_number }}:
                                    {{ $prereq->quizDetail->subject->name }} - 
                                    {{ $prereq->quizDetail->topic->name }}
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>