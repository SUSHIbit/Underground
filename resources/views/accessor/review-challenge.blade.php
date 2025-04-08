<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Challenge') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('accessor.dashboard') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Dashboard
                        </a>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-1">Challenge: {{ $set->challengeDetail->name }}</h3>
                        <p class="text-gray-600">Set #{{ $set->set_number }}</p>
                        <p class="text-gray-600">Created by: {{ $set->creator->name }}</p>
                    </div>
                    
                    <!-- Prerequisites -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-2">Prerequisites:</h4>
                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                            @foreach($set->challengeDetail->prerequisites as $prereq)
                                <li>
                                    Set #{{ $prereq->set_number }}: 
                                    @if($prereq->quizDetail && $prereq->quizDetail->subject && $prereq->quizDetail->topic)
                                        {{ $prereq->quizDetail->subject->name }} - 
                                        {{ $prereq->quizDetail->topic->name }}
                                    @else
                                        No details available
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    @if($set->comments->count() > 0)
                        <div class="mb-6">
                            <h4 class="font-medium mb-2">Comments</h4>
                            <div class="space-y-3">
                                @foreach($set->comments->where('question_id', null) as $comment)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-medium">{{ $comment->user->name }}</span>
                                            <span class="text-sm text-gray-500">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                                        </div>
                                        <p>{{ $comment->comment }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-6">
                        <h4 class="font-medium mb-4">Questions</h4>
                        
                        @foreach($set->questions as $question)
                            <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                                <div class="mb-4">
                                    <h5 class="font-medium text-gray-700 mb-2">Question {{ $question->question_number }}</h5>
                                    <p class="mb-4">{{ $question->question_text }}</p>
                                    
                                    <div class="mb-4">
                                        <h6 class="font-medium text-gray-700 mb-2">Options:</h6>
                                        <ul class="list-disc list-inside space-y-1 ml-2">
                                            @foreach($question->options as $option => $text)
                                                <li>
                                                    <span class="{{ $option == $question->correct_answer ? 'font-medium text-green-600' : '' }}">
                                                        {{ $option }}: {{ $text }}
                                                    </span>
                                                    @if($option == $question->correct_answer)
                                                        <span class="ml-2 text-xs text-green-600">(Correct answer)</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    
                                    <div>
                                        <h6 class="font-medium text-gray-700 mb-2">Reason:</h6>
                                        <p class="text-gray-600">{{ $question->reason }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4 border-t pt-4">
                                    <h5 class="font-medium text-gray-700 mb-2">Add Comment</h5>
                                    <form action="{{ route('accessor.sets.comment', $set) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                                        <div class="mb-2">
                                            <textarea 
                                                name="comment" 
                                                rows="2" 
                                                class="w-full p-2 border border-gray-300 rounded-md"
                                                placeholder="Add a comment about this question..."
                                            ></textarea>
                                        </div>
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-1 px-3 rounded">
                                            Add Comment
                                        </button>
                                    </form>
                                </div>
                                
                                @if($set->comments->where('question_id', $question->id)->count() > 0)
                                    <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                                        <h5 class="font-medium text-yellow-800 mb-2">Comments on this question</h5>
                                        <div class="space-y-2">
                                            @foreach($set->comments->where('question_id', $question->id) as $comment)
                                                <div class="p-2 bg-yellow-100 rounded">
                                                    <div class="flex justify-between items-center mb-1">
                                                        <span class="font-medium">{{ $comment->user->name }}</span>
                                                        <span class="text-sm text-gray-500">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                                                    </div>
                                                    <p class="text-yellow-700">{{ $comment->comment }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-8 border-t pt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium mb-2">Approve Challenge</h4>
                            <p class="mb-4 text-gray-600">Approving this challenge means you've reviewed it and found it suitable for students.</p>
                            
                            <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded-r-md">
                                <p class="text-sm text-blue-700">
                                    <strong>Note:</strong> Approving this set will make it available for the lecturer to publish. The lecturer will need to explicitly publish it to make it visible to students.
                                </p>
                            </div>
                            
                            <form action="{{ route('accessor.sets.approve', $set) }}" method="POST" class="mt-4">
                                @csrf
                                <div class="mb-4">
                                    <label for="review_notes_approve" class="block mb-2 text-sm font-medium text-gray-700">
                                        Review Notes (Optional)
                                    </label>
                                    <textarea 
                                        name="review_notes" 
                                        id="review_notes_approve" 
                                        rows="3" 
                                        class="w-full p-2 border border-gray-300 rounded-md"
                                        placeholder="Add any notes or feedback about this challenge..."
                                    ></textarea>
                                </div>
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Approve (Ready for Publishing)
                                </button>
                            </form>
                        </div>
                        
                        <div>
                            <h4 class="font-medium mb-2">Reject Challenge</h4>
                            <p class="mb-4 text-gray-600">Rejecting this challenge will send it back to the lecturer for revisions.</p>
                            
                            <form action="{{ route('accessor.sets.reject', $set) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="review_notes_reject" class="block mb-2 text-sm font-medium text-gray-700">
                                        Review Notes (Required)
                                    </label>
                                    <textarea 
                                        name="review_notes" 
                                        id="review_notes_reject" 
                                        rows="3" 
                                        class="w-full p-2 border border-gray-300 rounded-md"
                                        placeholder="Explain why this challenge is being rejected and what changes are needed..."
                                        required
                                    ></textarea>
                                </div>
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h4 class="font-medium mb-2">Add General Comment</h4>
                        
                        <form action="{{ route('accessor.sets.comment', $set) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <textarea 
                                    name="comment" 
                                    rows="3" 
                                    class="w-full p-2 border border-gray-300 rounded-md"
                                    placeholder="Add a general comment about this challenge..."
                                ></textarea>
                            </div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Comment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>