<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Challenges') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Available Challenges</h3>
                    
                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    @if($challenges->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($challenges as $challenge)
                                <div class="border rounded-lg overflow-hidden shadow-sm">
                                    <div class="p-4 bg-gray-50 border-b">
                                        <h4 class="font-medium">{{ $challenge->challengeDetail->name }}</h4>
                                        <p class="text-sm text-gray-600">Set #{{ $challenge->set_number }}</p>
                                    </div>
                                    <div class="p-4">
                                        <p class="mb-2 text-sm">
                                            <span class="font-medium">Prerequisites:</span>
                                        </p>
                                        <ul class="list-disc list-inside mb-4 text-sm text-gray-600">
                                            @foreach($challenge->challengeDetail->prerequisites as $prereq)
                                                <li>
                                                    Set #{{ $prereq->set_number }}:
                                                    {{ $prereq->quizDetail->subject->name }} - 
                                                    {{ $prereq->quizDetail->topic->name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                        
                                        @if(in_array($challenge->id, $attemptedChallengeIds))
                                            <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                                Completed
                                            </span>
                                        @elseif($challenge->canAttempt)
                                            <a href="{{ route('challenges.show', $challenge) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Start Challenge
                                            </a>
                                        @else
                                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                                                Complete prerequisites first
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No challenges available at the moment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>