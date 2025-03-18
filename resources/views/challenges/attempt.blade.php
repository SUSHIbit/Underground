<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Taking Challenge') }}: {{ $set->challengeDetail->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Question {{ $currentPage }} of {{ $totalPages }}</h3>
                        
                        <!-- Timer Display -->
                        @if(isset($timer_minutes) && $timer_minutes > 0)
                            <div class="text-center">
                                <div id="timer" class="text-xl font-bold px-4 py-2 rounded-lg bg-blue-100">
                                    <span id="minutes">--</span>:<span id="seconds">--</span>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">Time Remaining</div>
                            </div>
                        @else
                            <div class="text-gray-600">
                                Set #{{ $set->set_number }}
                            </div>
                        @endif
                    </div>
                    
                    <form action="{{ route('challenges.submit', $set) }}" method="POST" id="challenge-form" onsubmit="return confirmSubmit()">
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
                                    <a href="{{ route('challenges.attempt', ['set' => $set, 'page' => $currentPage - 1]) }}" 
                                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Previous
                                    </a>
                                @endif
                            </div>
                            
                            <div class="flex space-x-1">
                                @for($i = 1; $i <= $totalPages; $i++)
                                    <a href="{{ route('challenges.attempt', ['set' => $set, 'page' => $i]) }}" 
                                       class="inline-flex justify-center items-center w-8 h-8 {{ $i == $currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }} rounded">
                                        {{ $i }}
                                    </a>
                                @endfor
                            </div>
                            
                            <div>
                                @if($currentPage < $totalPages)
                                    <a href="{{ route('challenges.attempt', ['set' => $set, 'page' => $currentPage + 1]) }}" 
                                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Next
                                    </a>
                                @else
                                    <button type="button" 
                                            onclick="submitForm()" 
                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Submit Challenge
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
            const form = document.getElementById('challenge-form');
            const radios = form.querySelectorAll('input[type="radio"]');
            
            // Load saved answer if exists
            const questionId = '{{ $question->id }}';
            const savedAnswer = localStorage.getItem(`challenge_{{ $set->id }}_answer_${questionId}`);
            
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
                        localStorage.setItem(`challenge_{{ $set->id }}_answer_${questionId}`, this.value);
                    }
                });
            });
            
            // Set up the timer if it exists
            @if(isset($timer_minutes) && $timer_minutes > 0 && isset($remaining_seconds))
                const totalSeconds = {{ $remaining_seconds }};
                let timeLeft = totalSeconds;
                
                function updateTimer() {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    
                    document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                    document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
                    
                    // Change color as time decreases
                    const timerElement = document.getElementById('timer');
                    if (timeLeft < 60) { // Less than 1 minute
                        timerElement.classList.remove('bg-blue-100', 'bg-yellow-100');
                        timerElement.classList.add('bg-red-100', 'text-red-700');
                    } else if (timeLeft < 300) { // Less than 5 minutes
                        timerElement.classList.remove('bg-blue-100');
                        timerElement.classList.add('bg-yellow-100', 'text-yellow-700');
                    }
                    
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        // Auto-submit the form
                        submitForm(true);
                    } else {
                        timeLeft--;
                    }
                }
                
                // Initial update
                updateTimer();
                
                // Update timer every second
                const timerInterval = setInterval(updateTimer, 1000);
            @endif
        });
        
        function confirmSubmit() {
            // Check if all questions have been answered
            let allAnswered = true;
            const totalPages = {{ $totalPages }};
            const setId = {{ $set->id }};
            
            for (let i = 1; i <= {{ $totalPages }}; i++) {
                const questionId = document.querySelector(`input[name^="answers["]`).name.match(/\d+/)[0];
                const hasAnswer = localStorage.getItem(`challenge_${setId}_answer_${questionId}`);
                
                if (!hasAnswer) {
                    allAnswered = false;
                    break;
                }
            }
            
            if (!allAnswered) {
                if (!confirm('You have not answered all questions. Are you sure you want to submit?')) {
                    return false;
                }
            }
            
            return true;
        }
        
        function submitForm(isTimeExpired = false) {
            // Add all saved answers to the form
            const setId = {{ $set->id }};
            
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key.startsWith(`challenge_${setId}_answer_`)) {
                    const questionId = key.split('_').pop();
                    const value = localStorage.getItem(key);
                    
                    if (!document.querySelector(`input[name="answers[${questionId}]"]`)) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `answers[${questionId}]`;
                        input.value = value;
                        document.getElementById('challenge-form').appendChild(input);
                    }
                }
            }
            
            if (isTimeExpired || confirmSubmit()) {
                // Submit the form
                document.getElementById('challenge-form').submit();
                
                // Clear localStorage for this challenge
                for (let i = localStorage.length - 1; i >= 0; i--) {
                    const key = localStorage.key(i);
                    if (key.startsWith(`challenge_${setId}_answer_`)) {
                        localStorage.removeItem(key);
                    }
                }
            }
        }
    </script>
</x-app-layout>