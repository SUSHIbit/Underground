<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Taking Quiz') }}: {{ $set->quizDetail->subject->name }} - {{ $set->quizDetail->topic->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Question {{ $currentPage }} of {{ $totalPages }}</h3>
                        <div class="text-gray-600">
                            Set #{{ $set->set_number }}
                        </div>
                    </div>
                    
                    <form action="{{ route('quizzes.submit', $set) }}" method="POST" id="quiz-form">
                        @csrf
                        
                        <div class="mb-6">
                            <h4 class="text-xl mb-4">{{ $question->question_text }}</h4>
                            
                            <div class="space-y-3">
                                @foreach($question->options as $option => $text)
                                    <div class="flex items-center">
                                        <input type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               id="option-{{ $option }}" 
                                               value="{{ $option }}" 
                                               class="h-4 w-4 text-blue-600" 
                                               required>
                                        <label for="option-{{ $option }}" class="ml-2">
                                            {{ $option }}: {{ $text }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div>
                                @if($currentPage > 1)
                                    <a href="{{ route('quizzes.attempt', ['set' => $set, 'page' => $currentPage - 1]) }}" 
                                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Previous
                                    </a>
                                @endif
                            </div>
                            
                            <div class="flex space-x-1">
                                @for($i = 1; $i <= $totalPages; $i++)
                                    <a href="{{ route('quizzes.attempt', ['set' => $set, 'page' => $i]) }}" 
                                       class="inline-flex justify-center items-center w-8 h-8 {{ $i == $currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }} rounded">
                                        {{ $i }}
                                    </a>
                                @endfor
                            </div>
                            
                            <div>
                                @if($currentPage < $totalPages)
                                    <a href="{{ route('quizzes.attempt', ['set' => $set, 'page' => $currentPage + 1]) }}" 
                                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Next
                                    </a>
                                @else
                                    <button type="button" 
                                            onclick="submitForm()" 
                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Submit Quiz
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Store answers in localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('quiz-form');
            const radios = form.querySelectorAll('input[type="radio"]');
            
            // Load saved answer if exists
            const questionId = '{{ $question->id }}';
            const savedAnswer = localStorage.getItem(`quiz_{{ $set->id }}_answer_${questionId}`);
            
            if (savedAnswer) {
                const radio = document.getElementById(`option-${savedAnswer}`);
                if (radio) {
                    radio.checked = true;
                }
            }
            
            // Save answer when changed
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        localStorage.setItem(`quiz_{{ $set->id }}_answer_${questionId}`, this.value);
                    }
                });
            });
        });
        
        function submitForm() {
            // Check if all questions have been answered
            let allAnswered = true;
            const totalPages = {{ $totalPages }};
            const setId = {{ $set->id }};
            
            for (let i = 1; i <= {{ $totalPages }}; i++) {
                const questionId = document.querySelector(`input[name^="answers["]`).name.match(/\d+/)[0];
                const hasAnswer = localStorage.getItem(`quiz_${setId}_answer_${questionId}`);
                
                if (!hasAnswer) {
                    allAnswered = false;
                    break;
                }
            }
            
            if (!allAnswered) {
                if (!confirm('You have not answered all questions. Are you sure you want to submit?')) {
                    return;
                }
            }
            
            // Add all saved answers to the form
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key.startsWith(`quiz_${setId}_answer_`)) {
                    const questionId = key.split('_').pop();
                    const value = localStorage.getItem(key);
                    
                    if (!document.querySelector(`input[name="answers[${questionId}]"]`)) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `answers[${questionId}]`;
                        input.value = value;
                        document.getElementById('quiz-form').appendChild(input);
                    }
                }
            }
            
            // Submit the form
            document.getElementById('quiz-form').submit();
            
            // Clear localStorage for this quiz
            for (let i = localStorage.length - 1; i >= 0; i--) {
                const key = localStorage.key(i);
                if (key.startsWith(`quiz_${setId}_answer_`)) {
                    localStorage.removeItem(key);
                }
            }
        }
    </script>
</x-app-layout>