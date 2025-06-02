<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Edit Challenge') }}
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
                            <h3 class="text-lg font-medium mb-1">Challenge: {{ $set->challengeDetail->name }}</h3>
                            <p class="text-gray-600">Set #{{ $set->set_number }}</p>
                        </div>
                        <div class="flex items-center">
                            <div class="text-sm px-3 py-1 rounded-full 
                                {{ $set->status == 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $set->status == 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $set->status == 'approved_unpublished' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $set->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $set->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $set->status)) }}
                            </div>
                            @if($set->isApprovedUnpublished())
                                <div class="ml-4 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                                    Approved - Ready to Publish
                                </div>
                            @endif
                        </div>
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
                    
                    @if($set->isRejected())
                        <div class="mb-6 p-4 bg-red-50 rounded-lg border border-red-200">
                            <h4 class="font-medium text-red-800 mb-2">Rejection Notes from {{ $set->reviewer ? $set->reviewer->name : 'Reviewer' }}</h4>
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

                            <!-- FIXED: Challenge Timer Settings Section -->
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-medium mb-2">Challenge Timer Settings</h4>
                                
                                <div class="flex items-center mb-2">
                                    <!-- FIXED: Added hidden input for unchecked state -->
                                    <input type="hidden" name="enable_timer" value="0">
                                    <input type="checkbox" id="enable_timer" name="enable_timer" value="1" class="h-4 w-4 text-blue-600" 
                                        {{ isset($set->challengeDetail->timer_minutes) && $set->challengeDetail->timer_minutes > 0 ? 'checked' : '' }}>
                                    <label for="enable_timer" class="ml-2 text-gray-700">Enable Timer</label>
                                </div>
                                
                                <div id="timer_settings" class="{{ isset($set->challengeDetail->timer_minutes) && $set->challengeDetail->timer_minutes > 0 ? '' : 'hidden' }}">
                                    <div class="flex items-center">
                                        <input type="number" id="timer_minutes" name="timer_minutes" 
                                            class="w-20 p-2 border border-gray-300 rounded-md"
                                            min="1" max="180" value="{{ $set->challengeDetail->timer_minutes ?? 45 }}">
                                        <label for="timer_minutes" class="ml-2 text-gray-700">minutes</label>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Students will have this amount of time to complete the challenge. After the time expires, the challenge will be automatically submitted with the answers provided so far.</p>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="font-medium mb-4">Questions</h4>
                                
                                @foreach($set->questions as $question)
                                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                                        <div class="mb-4">
                                            <input type="hidden" name="questions[{{ $question->id }}][id]" value="{{ $question->id }}">
                                            <label for="questions_{{ $question->id }}_question_text" class="block mb-2 font-medium text-gray-700">
                                                Question {{ $question->question_number }}
                                            </label>
                                            <textarea 
                                                name="questions[{{ $question->id }}][question_text]" 
                                                id="questions_{{ $question->id }}_question_text" 
                                                class="w-full p-2 border border-gray-300 rounded-md"
                                                rows="3"
                                                required
                                            >{{ old("questions.{$question->id}.question_text", $question->question_text) }}</textarea>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            @foreach(['A', 'B', 'C', 'D'] as $option)
                                                <div>
                                                    <label for="questions_{{ $question->id }}_options_{{ $option }}" class="block mb-2 font-medium text-gray-700">
                                                        Option {{ $option }}
                                                    </label>
                                                    <input 
                                                        type="text" 
                                                        name="questions[{{ $question->id }}][options][{{ $option }}]" 
                                                        id="questions_{{ $question->id }}_options_{{ $option }}" 
                                                        class="w-full p-2 border border-gray-300 rounded-md"
                                                        value="{{ old("questions.{$question->id}.options.{$option}", $question->options[$option] ?? '') }}"
                                                        required
                                                    >
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label for="questions_{{ $question->id }}_correct_answer" class="block mb-2 font-medium text-gray-700">
                                                Correct Answer
                                            </label>
                                            <select 
                                                name="questions[{{ $question->id }}][correct_answer]" 
                                                id="questions_{{ $question->id }}_correct_answer" 
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
                                            <label for="questions_{{ $question->id }}_reason" class="block mb-2 font-medium text-gray-700">
                                                Reason
                                            </label>
                                            <textarea 
                                                name="questions[{{ $question->id }}][reason]" 
                                                id="questions_{{ $question->id }}_reason" 
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
                        @if($set->isApprovedUnpublished())
                            <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <h4 class="font-medium text-yellow-800 mb-2">This set has been approved by an accessor</h4>
                                <p class="text-yellow-700">
                                    It is ready to be published. Once published, it will be available to students.
                                    You cannot make further edits to this content.
                                </p>
                                <div class="mt-4">
                                    <form action="{{ route('lecturer.sets.publish', $set) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Publish Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                        
                        <!-- FIXED: Added Challenge Configuration Display for Read-only View -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium mb-4">Challenge Configuration</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h5 class="font-medium text-gray-700 mb-2">Challenge Name</h5>
                                    <p class="text-gray-600">{{ $set->challengeDetail->name }}</p>
                                </div>
                                <div>
                                    <h5 class="font-medium text-gray-700 mb-2">Timer Settings</h5>
                                    @if(isset($set->challengeDetail->timer_minutes) && $set->challengeDetail->timer_minutes > 0)
                                        <p class="text-green-600">
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Enabled ({{ $set->challengeDetail->timer_minutes }} minutes)
                                            </span>
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">Students have {{ $set->challengeDetail->timer_minutes }} minutes to complete this challenge.</p>
                                    @else
                                        <p class="text-gray-500">
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 10.414l2.707-2.707a1 1 0 00-1.414-1.414L10 7.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                                Disabled
                                            </span>
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">No time limit - students can take as long as needed.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
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

    <!-- JavaScript for Timer Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const enableTimerCheckbox = document.getElementById('enable_timer');
            const timerSettings = document.getElementById('timer_settings');
            
            if (enableTimerCheckbox && timerSettings) {
                enableTimerCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        timerSettings.classList.remove('hidden');
                    } else {
                        timerSettings.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>