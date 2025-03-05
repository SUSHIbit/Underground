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
                    <h3 class="text-lg font-medium mb-4">Available Quizzes</h3>
                    
                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    @if($quizzes->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($quizzes as $quiz)
                                <div class="border rounded-lg overflow-hidden shadow-sm">
                                    <div class="p-4 bg-gray-50 border-b">
                                        <h4 class="font-medium">Set #{{ $quiz->set_number }}: {{ $quiz->quizDetail->subject->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $quiz->quizDetail->topic->name }}</p>
                                    </div>
                                    <div class="p-4">
                                        <p class="mb-4">
                                            <span class="text-sm text-gray-600">Questions:</span> 
                                            {{ $quiz->questions->count() }}
                                        </p>
                                        
                                        @if(in_array($quiz->id, $attemptedQuizIds))
                                            <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                                Completed
                                            </span>
                                        @else
                                            <a href="{{ route('quizzes.show', $quiz) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Start Quiz
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No quizzes available at the moment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>