<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome, {{ $user->username }}!</h3>
                    
                    <div class="mb-6">
                        <div class="flex gap-4">
                            <a href="{{ route('quizzes.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Take a Quiz
                            </a>
                            <a href="{{ route('challenges.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Take a Challenge
                            </a>
                        </div>
                    </div>
                    
                    <h4 class="text-lg font-medium mb-2">Your Recent Results:</h4>
                    
                    @if($quizAttempts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Score</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quizAttempts as $attempt)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ ucfirst($attempt->set->type) }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @if($attempt->set->type === 'quiz')
                                                    {{ $attempt->set->quizDetail->subject->name }} - 
                                                    {{ $attempt->set->quizDetail->topic->name }}
                                                @else
                                                    {{ $attempt->set->challengeDetail->name }}
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $attempt->score }}/{{ $attempt->total_questions }}
                                                ({{ $attempt->score_percentage }}%)
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $attempt->created_at->format('M d, Y') }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('results.show', $attempt) }}" class="text-blue-500 hover:text-blue-700">
                                                    View Results
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">You haven't completed any quizzes or challenges yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>