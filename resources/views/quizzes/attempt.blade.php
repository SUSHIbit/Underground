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
                    
                    <form action="{{ route('quizzes.submit', $set) }}" method="POST" id="quiz-form" onsubmit="return confirmSubmit()">
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
                            
                            <!-- Add a hidden input to ensure this question ID is always included in submission -->
                            <input type="hidden" name="question_ids[]" value="{{ $question->id }}">
                        </div>
                        
                        <!-- Add a debug section that only shows in development -->
                        @if(config('app.env') === 'local')
                        <div class="mt-4 p-3 bg-gray-100 rounded-lg">
                            <details>
                                <summary class="cursor-pointer text-sm text-gray-600">Debug Info</summary>
                                <div class="mt-2 text-xs">
                                    <p>Current Question ID: {{ $question->id }}</p>
                                    <p>Current Question Number: {{ $currentPage }}</p>
                                    <p>Total Questions: {{ $totalPages }}</p>
                                    <p>Current Set ID: {{ $set->id }}</p>
                                    <div id="answered-status" class="mt-1"></div>
                                </div>
                            </details>
                        </div>
                        @endif
                        
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
                                       class="inline-flex justify-center items-center w-8 h-8 {{ $i == $currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }} rounded question-nav-link">
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
            
            // Track all answered questions
            trackAnsweredQuestions();
            
            // Add class to style the navigation links
            const styleElement = document.createElement('style');
            styleElement.innerHTML = `
                .answered {
                    background-color: #d1fae5 !important; /* Light green */
                }
                .answer-indicator {
                    display: inline-block;
                    margin-left: 4px;
                    color: #10b981; /* Green color */
                }
            `;
            document.head.appendChild(styleElement);
            
            // Add classes to the navigation links
            const navLinks = document.querySelectorAll('.inline-flex.justify-center.items-center');
            navLinks.forEach(link => {
                link.classList.add('question-nav-link');
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
            
            // Debug info for local environment
            @if(config('app.env') === 'local')
            if (localStorage.getItem(`quiz_{{ $set->id }}_answered_all`)) {
                const status = JSON.parse(localStorage.getItem(`quiz_{{ $set->id }}_answered_all`));
                const statusEl = document.getElementById('answered-status');
                if (statusEl) {
                    statusEl.innerHTML = '<p>Questions answered status:</p>';
                    for (const [num, answered] of Object.entries(status)) {
                        statusEl.innerHTML += `<p>Question ${num}: ${answered ? '✓' : '❌'}</p>`;
                    }
                }
            }
            @endif
        });
        
        // Track all answered questions
        function trackAnsweredQuestions() {
            const setId = {{ $set->id }};
            const totalQuestions = {{ $totalPages }};
            
            // Initialize the tracking object if not exists
            if (!localStorage.getItem(`quiz_${setId}_answered_all`)) {
                let answeredQuestions = {};
                for (let i = 1; i <= totalQuestions; i++) {
                    answeredQuestions[i] = false;
                }
                localStorage.setItem(`quiz_${setId}_answered_all`, JSON.stringify(answeredQuestions));
            }
            
            // Update the current question's status
            const currentQuestion = {{ $question->id }};
            const radios = document.querySelectorAll('input[type="radio"]');
            const questionNumber = {{ $currentPage }};
            
            // Check if any radio button is selected
            let isAnswered = false;
            radios.forEach(radio => {
                if (radio.checked) {
                    isAnswered = true;
                    
                    // Update tracking
                    let answeredQuestions = JSON.parse(localStorage.getItem(`quiz_${setId}_answered_all`));
                    answeredQuestions[questionNumber] = true;
                    localStorage.setItem(`quiz_${setId}_answered_all`, JSON.stringify(answeredQuestions));
                    
                    // Also make sure the answer is saved
                    localStorage.setItem(`quiz_${setId}_answer_${currentQuestion}`, radio.value);
                }
                
                // Add event listener to track when an answer is selected
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        // Update tracking
                        let answeredQuestions = JSON.parse(localStorage.getItem(`quiz_${setId}_answered_all`));
                        answeredQuestions[questionNumber] = true;
                        localStorage.setItem(`quiz_${setId}_answered_all`, JSON.stringify(answeredQuestions));
                        
                        // Save the answer
                        localStorage.setItem(`quiz_${setId}_answer_${currentQuestion}`, this.value);
                    }
                });
            });
            
            // Check if this question was previously answered
            const answeredQuestions = JSON.parse(localStorage.getItem(`quiz_${setId}_answered_all`));
            if (answeredQuestions[questionNumber]) {
                isAnswered = true;
            }
            
            // Update navigation to show which questions are answered
            updateNavigationStatus(answeredQuestions);
            
            return isAnswered;
        }

        // Update the navigation to show which questions are answered
        function updateNavigationStatus(answeredQuestions) {
            const navLinks = document.querySelectorAll('.question-nav-link');
            navLinks.forEach(link => {
                const questionNum = parseInt(link.textContent.trim());
                if (answeredQuestions[questionNum]) {
                    link.classList.add('answered');
                    // Add a visual indicator
                    if (!link.querySelector('.answer-indicator')) {
                        const indicator = document.createElement('span');
                        indicator.className = 'answer-indicator';
                        indicator.innerHTML = '✓';
                        link.appendChild(indicator);
                    }
                }
            });
        }
        
        function confirmSubmit() {
            const setId = {{ $set->id }};
            const totalQuestions = {{ $totalPages }};
            
            // Get tracking data
            const answeredQuestions = JSON.parse(localStorage.getItem(`quiz_${setId}_answered_all`));
            
            // Check if all questions have been answered
            let allAnswered = true;
            let unansweredCount = 0;
            
            for (let i = 1; i <= totalQuestions; i++) {
                if (!answeredQuestions[i]) {
                    allAnswered = false;
                    unansweredCount++;
                }
            }
            
            if (!allAnswered) {
                return confirm(`You have not answered ${unansweredCount} question(s). Are you sure you want to submit?`);
            }
            
            return true;
        }
        
        function submitForm(isTimeExpired = false) {
            const setId = {{ $set->id }};
            const totalQuestions = {{ $totalPages }};
            const questionIds = {!! json_encode($set->questions->pluck('id', 'question_number')->toArray()) !!};
            
            // Add all saved answers to the form
            for (let i = 1; i <= totalQuestions; i++) {
                const questionId = questionIds[i];
                if (questionId) {
                    const answerKey = `quiz_${setId}_answer_${questionId}`;
                    const value = localStorage.getItem(answerKey);
                    
                    if (value) {
                        // Check if input already exists
                        if (!document.querySelector(`input[name="answers[${questionId}]"]`)) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = `answers[${questionId}]`;
                            input.value = value;
                            document.getElementById('quiz-form').appendChild(input);
                        }
                    }
                }
            }
            
            if (isTimeExpired || confirmSubmit()) {
                // Log what we're submitting
                console.log("Submitting quiz with answers:", 
                    Array.from(document.querySelectorAll('input[name^="answers["]'))
                        .map(el => `${el.name}: ${el.value}`)
                );
                
                // Submit the form
                document.getElementById('quiz-form').submit();
                
                // Clear localStorage for this quiz
                for (let i = localStorage.length - 1; i >= 0; i--) {
                    const key = localStorage.key(i);
                    if (key.startsWith(`quiz_${setId}_`)) {
                        localStorage.removeItem(key);
                    }
                }
            }
        }
    </script>
</x-app-layout>