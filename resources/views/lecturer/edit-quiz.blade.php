<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Quiz') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('lecturer.dashboard') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Dashboard
                        </a>
                    </div>
                    
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-medium mb-1">Quiz: {{ $set->quizDetail->subject->name }} - {{ $set->quizDetail->topic->name }}</h3>
                            <p class="text-gray-600">Set #{{ $set->set_number }}</p>
                        </div>
                        <div class="text-sm px-3 py-1 rounded-full 
                            {{ $set->status == 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $set->status == 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $set->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $set->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $set->status)) }}
                        </div>
                    </div>
                    
                    @if($set->isRejected())
                        <div class="mb-6 p-4 bg-red-50 rounded-lg border border-red-200">
                            <h4 class="font-medium text-red-800 mb-2">Rejection Notes from {{ $set->reviewer->name }}</h4>
                            <p class="text-red-700">{{ $set->review_notes }}</p>
                        </div>
                    @endif
                    
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
                    
                    @if($set->isDraft() || $set->isRejected())
                        <form action="{{ route('lecturer.sets.update', $set) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-6">
                                <h4 class="font-medium mb-4">Questions</h4>
                                
                                @foreach($set->questions as $question)
                                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                                        <div class="mb-4">
                                            <label for="questions[{{ $question->id }}][question_text]" class="block mb-2 font-medium text-gray-700">
                                                Question {{ $question->question_number }}
                                            </label>
                                            <textarea 
                                                name="questions[{{ $question->id }}][question_text]" 
                                                id="questions[{{ $question->id }}][question_text]" 
                                                class="w-full p-2 border border-gray-300 rounded-md"
                                                rows="3"
                                                required
                                            >{{ old("questions.{$question->id}.question_text", $question->question_text) }}</textarea>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            @foreach(['A', 'B', 'C', 'D'] as $option)
                                                <div>
                                                    <label for="questions[{{ $question->id }}][options][{{ $option }}]" class="block mb-2 font-medium text-gray-700">
                                                        Option {{ $option }}
                                                    </label>
                                                    <input 
                                                        type="text" 
                                                        name="questions[{{ $question->id }}][options][{{ $option }}]" 
                                                        id="questions[{{ $question->id }}][options][{{ $option }}]" 
                                                        class="w-full p-2 border border-gray-300 rounded-md"
                                                        value="{{ old("questions.{$question->id}.options.{$option}", $question->options[$option] ?? '') }}"
                                                        required
                                                    >
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label for="questions[{{ $question->id }}][correct_answer]" class="block mb-2 font-medium text-gray-700">
                                                Correct Answer
                                            </label>
                                            <select 
                                                name="questions[{{ $question->id }}][correct_answer]" 
                                                id="questions[{{ $question->id }}][correct_answer]" 
                                                class="w-full p-2 border border-gray-300 rounded-md"
                                                required
                                            >
                                                @foreach(['A', 'B', 'C', 'D'] as $option)
                                                    <option value="{{ $option }}" {{ (old("questions.{$question->id}.correct_answer", $question->correct_answer) == $option) ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <label for="questions[{{ $question->id }}][reason]" class="block mb-2 font-medium text-gray-700">
                                                Reason
                                            </label>
                                            <textarea 
                                                name="questions[{{ $question->id }}][reason]" 
                                                id="questions[{{ $question->id }}][reason]" 
                                                class="w-full p-2 border border-gray-300 rounded-md"
                                                rows="3"
                                                required
                                            >{{ old("questions.{$question->id}.reason", $question->reason) }}</textarea>
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
                            
                            <div class="flex justify-between items-center">
                                <a href="{{ route('lecturer.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                                
                                <div>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                                        Save Changes
                                    </button>
                                    
                                    @if($set->isDraft() || $set->isRejected())
                                        <button type="submit" form="submit-form" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Submit for Approval
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </form>
                        
                        <form id="submit-form" action="{{ route('lecturer.sets.submit', $set) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
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
                        
                        <div>
                            <a href="{{ route('lecturer.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Back to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>