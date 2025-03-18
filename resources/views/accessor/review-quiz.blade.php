<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Quiz') }}
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
                    
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-medium mb-1">Quiz: {{ $set->quizDetail->subject->name }} - {{ $set->quizDetail->topic->name }}</h3>
                            <p class="text-gray-600">Set #{{ $set->set_number }}</p>
                            <p class="text-gray-600">Created by: {{ $set->creator->name }}</p>
                            <p class="text-gray-600">Submitted: {{ $set->submitted_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="text-sm px-3 py-1 rounded-full 
                            {{ $set->status == 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $set->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $set->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $set->status)) }}
                        </div>
                    </div>
                    
                    @if($set->isRejected() || $set->isApproved())
                        <div class="mb-6 p-4 {{ $set->isApproved() ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} rounded-lg">
                            <h4 class="font-medium {{ $set->isApproved() ? 'text-green-800' : 'text-red-800' }} mb-2">Review Notes</h4>
                            <p class="{{ $set->isApproved() ? 'text-green-700' : 'text-red-700' }}">{{ $set->review_notes }}</p>
                        </div>
                    @endif

                    <!-- Add this section to resources/views/accessor/review-quiz.blade.php -->
                    <!-- Insert after the title section and before the comments section -->

                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium mb-2">Quiz Settings</h4>
                        
                        <div class="mb-1">
                            <span class="font-medium">Time Limit:</span> 
                            @if(isset($set->quizDetail->timer_minutes) && $set->quizDetail->timer_minutes > 0)
                                {{ $set->quizDetail->timer_minutes }} minutes
                            @else
                                No time limit
                            @endif
                        </div>
                        
                        <div>
                            <span class="font-medium">Questions:</span> {{ $set->questions->count() }}
                        </div>
                    </div>
                    
                    <!-- Overall comments for the set -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-2">Set Comments</h4>
                        
                        <div class="space-y-3 mb-4">
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
                        
                        @if($set->isPendingApproval())
                            <form action="{{ route('accessor.sets.comment', $set) }}" method="POST" class="space-y-2">
                                @csrf
                                <div>
                                    <label for="comment" class="block mb-1 text-sm font-medium text-gray-700">Add a comment</label>
                                    <textarea 
                                        name="comment" 
                                        id="comment" 
                                        rows="3" 
                                        class="w-full p-2 border border-gray-300 rounded-md"
                                        placeholder="Enter your comment about the entire set"
                                        required
                                    ></textarea>
                                </div>
                                <div>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Add Comment
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                    
                    <!-- Questions review -->
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
                                    
                                    <div class="mb-4">
                                        <h6 class="font-medium text-gray-700 mb-2">Reason:</h6>
                                        <p class="text-gray-600">{{ $question->reason }}</p>
                                    </div>
                                    
                                    <!-- Question-specific comments -->
                                    <div>
                                        <h6 class="font-medium text-gray-700 mb-2">Comments on this Question:</h6>
                                        
                                        <div class="space-y-3 mb-3">
                                            @foreach($set->comments->where('question_id', $question->id) as $comment)
                                                <div class="p-2 bg-yellow-50 rounded-lg">
                                                    <div class="flex justify-between items-center mb-1">
                                                        <span class="font-medium">{{ $comment->user->name }}</span>
                                                        <span class="text-sm text-gray-500">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                                                    </div>
                                                    <p class="text-gray-700">{{ $comment->comment }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        @if($set->isPendingApproval())
                                            <form action="{{ route('accessor.sets.comment', $set) }}" method="POST" class="space-y-2">
                                                @csrf
                                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                                                <div>
                                                    <textarea 
                                                        name="comment" 
                                                        rows="2" 
                                                        class="w-full p-2 border border-gray-300 rounded-md"
                                                        placeholder="Add a comment about this specific question"
                                                        required
                                                    ></textarea>
                                                </div>
                                                <div>
                                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                        Add Comment
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($set->isPendingApproval())
                        <div class="flex justify-between items-center">
                            <a href="{{ route('accessor.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Back to Dashboard
                            </a>
                            <div class="flex space-x-4">
                                <button type="button" onclick="document.getElementById('rejection-modal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Reject
                                </button>
                                
                                <button type="button" onclick="document.getElementById('approval-modal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Approve
                                </button>
                            </div>
                        </div>
                        
                        <!-- Approval Modal -->
                        <div id="approval-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center">
                            <div class="bg-white rounded-lg p-8 max-w-md w-full">
                                <h3 class="text-lg font-medium mb-4">Approve this Set</h3>
                                
                                <form action="{{ route('accessor.sets.approve', $set) }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-4">
                                        <label for="review_notes" class="block mb-2 text-sm font-medium text-gray-700">
                                            Approval Notes (optional)
                                        </label>
                                        <textarea 
                                            name="review_notes" 
                                            id="review_notes" 
                                            rows="3" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            placeholder="Add any notes about your approval"
                                        ></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="document.getElementById('approval-modal').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                            Cancel
                                        </button>
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Confirm Approval
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Rejection Modal -->
                        <div id="rejection-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center">
                            <div class="bg-white rounded-lg p-8 max-w-md w-full">
                                <h3 class="text-lg font-medium mb-4">Reject this Set</h3>
                                
                                <form action="{{ route('accessor.sets.reject', $set) }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-4">
                                        <label for="review_notes" class="block mb-2 text-sm font-medium text-gray-700">
                                            Rejection Reason (required)
                                        </label>
                                        <textarea 
                                            name="review_notes" 
                                            id="review_notes" 
                                            rows="3" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            placeholder="Please explain why you are rejecting this set"
                                            required
                                        ></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="document.getElementById('rejection-modal').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                            Cancel
                                        </button>
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Confirm Rejection
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div>
                            <a href="{{ route('accessor.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Back to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>