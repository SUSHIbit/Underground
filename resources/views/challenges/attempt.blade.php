<x-attempt-layout>
    <x-slot name="title">
        {{ __('Taking Challenge') }}: {{ $set->challengeDetail->name }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($attempt->is_retake)
            <!-- Learning Mode Banner -->
            <div class="mb-4 bg-blue-900/20 border-l-4 border-blue-500 text-blue-400 p-4 rounded-md shadow-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm leading-5 font-medium">
                            Learning Mode - No result update
                        </p>
                        <p class="text-sm leading-5">
                            Your original score ({{ session('original_score', 0) }}/{{ session('original_total', 0) }}) will remain unchanged
                        </p>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-medium text-amber-400">Question {{ $currentPage }} of {{ $totalPages }}</h3>
                        
                        <!-- Timer Display -->
                        @if(isset($timer_minutes) && $timer_minutes > 0)
                            <div class="text-center">
                                <div id="timer" class="text-xl font-bold px-4 py-2 rounded-lg bg-purple-900/20 border border-purple-800/30">
                                    <span id="minutes">--</span>:<span id="seconds">--</span>
                                </div>
                                <div class="text-sm text-gray-400 mt-1">Time Remaining</div>
                            </div>
                        @else
                            <div class="text-gray-400">
                                Set #{{ $set->set_number }}
                            </div>
                        @endif
                    </div>
                    
                    <form action="{{ route('challenges.submit', $set) }}" method="POST" id="quiz-form" onsubmit="return confirmSubmit()">
                        @csrf
                        
                        <div class="mb-6 bg-gray-900/50 p-4 rounded-lg border border-amber-800/20">
                            <h4 class="text-xl mb-4 text-purple-300">{{ $question->question_text }}</h4>
                            
                            <div class="space-y-3">
                                @foreach($question->options as $option => $text)
                                    <div class="flex items-center p-2 rounded-md hover:bg-gray-700/50 cursor-pointer">
                                        <input type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               id="option-{{ $option }}" 
                                               value="{{ $option }}" 
                                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 bg-gray-700"
                                               required>
                                        <label for="option-{{ $option }}" class="ml-2 block text-gray-300 cursor-pointer w-full">
                                            <span class="font-medium text-purple-400">{{ $option }}:</span> {{ $text }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Add a hidden input to ensure this question ID is always included in submission -->
                            <input type="hidden" name="question_ids[]" value="{{ $question->id }}">
                        </div>
                        
                        <!-- Add a debug section that only shows in development -->
                        @if(config('app.env') === 'local')
                        <div class="mt-4 p-3 bg-gray-900/80 rounded-lg border border-gray-700">
                            <details>
                                <summary class="cursor-pointer text-sm text-gray-400">Debug Info</summary>
                                <div class="mt-2 text-xs text-gray-500">
                                    <p>Current Question ID: {{ $question->id }}</p>
                                    <p>Current Question Number: {{ $currentPage }}</p>
                                    <p>Total Questions: {{ $totalPages }}</p>
                                    <p>Current Set ID: {{ $set->id }}</p>
                                    <div id="answered-status" class="mt-1"></div>
                                </div>
                            </details>
                        </div>
                        @endif
                        
                        <!-- IMPROVED NAVIGATION SECTION START -->
                        <div class="flex flex-col space-y-4 mt-6">
                            <!-- Question progress bar -->
                            <div class="w-full bg-gray-700 rounded-full h-2.5 mb-2">
                                <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ ($currentPage / $totalPages) * 100 }}%"></div>
                            </div>

                            <!-- Question counter -->
                            <div class="text-center mb-2">
                                <span class="text-sm text-gray-400">Question {{ $currentPage }} of {{ $totalPages }}</span>
                            </div>

                            <!-- Main pagination options -->
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 navigation-container">
                                <!-- Previous button -->
                                <div class="nav-buttons w-full sm:w-auto">
                                    @if($currentPage > 1)
                                        <a href="{{ route('challenges.attempt', ['set' => $set, 'page' => $currentPage - 1]) }}" 
                                           class="w-full sm:w-auto inline-block text-center bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-4 rounded-md transition duration-150 border border-amber-800/20"
                                           onclick="savePage({{ $currentPage - 1 }})">
                                            Previous
                                        </a>
                                    @else
                                        <button disabled class="w-full sm:w-auto bg-gray-800 text-gray-500 font-bold py-2 px-4 rounded-md cursor-not-allowed border border-gray-700">
                                            Previous
                                        </button>
                                    @endif
                                </div>

                                <!-- Pagination batches with dynamic calculation -->
                                <div x-data="{ showAllQuestions: false, currentBatch: {{ ceil($currentPage / 10) }} }" class="pagination-batch order-3 sm:order-2">
                                    <!-- Current batch pagination (shows 10 at a time) -->
                                    <div class="flex flex-wrap justify-center space-x-1 items-center">
                                        @php
                                            $batchStart = (ceil($currentPage / 10) - 1) * 10 + 1;
                                            $batchEnd = min($batchStart + 9, $totalPages);
                                        @endphp

                                        <!-- Batch navigation -->
                                        @if($batchStart > 1)
                                            <button @click="currentBatch--" 
                                                    onclick="navigateToBatch({{ ceil($currentPage / 10) - 1 }})"
                                                    class="w-8 h-8 flex items-center justify-center bg-gray-700 hover:bg-gray-600 rounded-md batch-nav-btn text-gray-200 border border-amber-800/20"
                                                    title="Previous batch">
                                                &laquo;
                                            </button>
                                        @endif

                                        <!-- Question numbers in current batch -->
                                        <div class="flex flex-wrap gap-1 justify-center">
                                            <template x-for="pageNum in {{ $batchEnd - $batchStart + 1 }}" :key="pageNum">
                                                <div x-data="{ page: {{ $batchStart }} - 1 + pageNum }">
                                                    <a :href="'{{ route('challenges.attempt', ['set' => $set, 'page' => '']) }}' + page"
                                                       :class="page == {{ $currentPage }} ? 
                                                               'bg-purple-600 text-white border-purple-700' : 
                                                               'bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-700'"
                                                       class="question-nav-link inline-flex justify-center items-center w-8 h-8 rounded-md relative border"
                                                       @click="savePage(page)">
                                                        <span class="question-number" x-text="page"></span>
                                                    </a>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Next batch button -->
                                        @if($batchEnd < $totalPages)
                                            <button @click="currentBatch++" 
                                                    onclick="navigateToBatch({{ ceil($currentPage / 10) + 1 }})"
                                                    class="w-8 h-8 flex items-center justify-center bg-gray-700 hover:bg-gray-600 rounded-md batch-nav-btn text-gray-200 border border-amber-800/20"
                                                    title="Next batch">
                                                &raquo;
                                            </button>
                                        @endif

                                        <!-- Toggle button for all questions -->
                                        <button @click="showAllQuestions = !showAllQuestions" 
                                                class="ml-2 px-2 py-1 text-xs bg-gray-700 hover:bg-gray-600 rounded-md flex items-center text-gray-200 border border-amber-800/20"
                                                title="Show all questions">
                                            <span>All</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path x-show="!showAllQuestions" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                <path x-show="showAllQuestions" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- All questions panel (hidden by default) -->
                                    <div x-show="showAllQuestions" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute z-10 mt-2 p-3 bg-gray-800 rounded-md shadow-lg border border-amber-800/20 all-questions-panel"
                                         style="display: none;">
                                        
                                        <h4 class="text-sm font-medium text-purple-400 mb-2">All Questions</h4>
                                        <div class="grid grid-cols-5 gap-2 max-h-60 overflow-y-auto p-1">
                                            @for($i = 1; $i <= $totalPages; $i++)
                                                <a href="{{ route('challenges.attempt', ['set' => $set, 'page' => $i]) }}" 
                                                   class="question-nav-link inline-flex justify-center items-center w-8 h-8 {{ $i == $currentPage ? 'bg-purple-600 text-white border-purple-700' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-700' }} rounded-md relative border"
                                                   onclick="savePage({{ $i }})">
                                                    <span class="question-number">{{ $i }}</span>
                                                </a>
                                            @endfor
                                        </div>
                                        
                                        <div class="mt-2 text-center">
                                            <button @click="showAllQuestions = false" class="text-xs text-purple-400 hover:text-purple-300">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Next button and submit -->
                                <div class="nav-buttons w-full sm:w-auto order-2 sm:order-3">
                                    @if($currentPage < $totalPages)
                                        <a href="{{ route('challenges.attempt', ['set' => $set, 'page' => $currentPage + 1]) }}" 
                                           class="w-full sm:w-auto inline-block text-center bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-4 rounded-md transition duration-150 border border-amber-800/20"
                                           onclick="savePage({{ $currentPage + 1 }})">
                                            Next
                                        </a>
                                    @else
                                        <button type="button" 
                                                onclick="submitForm()" 
                                                class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-150">
                                            Submit Challenge
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Visual answered status -->
                            <div class="mt-1 text-center">
                                <span class="text-xs text-gray-400">
                                    <span id="answered-count">0</span> of {{ $totalPages }} questions answered
                                </span>
                            </div>
                        </div>
                        <!-- IMPROVED NAVIGATION SECTION END -->
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <style>
        /* Style for question navigation links */
        .question-nav-link {
            position: relative;
            transition: all 0.2s;
        }

        .question-nav-link:hover {
            transform: translateY(-2px);
        }

        /* Style for answered questions */
        .answered {
            background-color: rgba(147, 51, 234, 0.2) !important; /* Light purple background with opacity */
            border-color: rgba(126, 34, 206, 0.5) !important;
        }

        .answer-indicator {
            position: absolute;
            top: -4px;
            right: -4px;
            font-size: 10px;
            background-color: rgb(147, 51, 234);
            color: white;
            border-radius: 50%;
            width: 14px;
            height: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Mobile responsiveness */
        @media (max-width: 640px) {
            .all-questions-panel {
                width: 90vw;
                left: 5vw;
                right: 5vw;
            }
        }
    </style>
    
    <script>
        // Store answers in localStorage
        document.addEventListener('DOMContentLoaded', function() {
            // REMOVED: Flag to track intentional navigation and beforeunload event
            // No longer tracking or interrupting page navigation
            
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
            
            // Update navigation UI based on saved answers
            updateNavigationUI();
            
            // Set up the timer if it exists
            @if(isset($timer_minutes) && $timer_minutes > 0 && isset($remaining_seconds))
                const totalSeconds = {{ $remaining_seconds }};
                let timeLeft = totalSeconds;
                
                function updateTimer() {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    
                    document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                    document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
                    
                    // Update the nav timer if it exists
                    const navMinutes = document.getElementById('minutes-nav');
                    const navSeconds = document.getElementById('seconds-nav');
                    if (navMinutes && navSeconds) {
                        navMinutes.textContent = String(minutes).padStart(2, '0');
                        navSeconds.textContent = String(seconds).padStart(2, '0');
                    }
                    
                    // Change color as time decreases
                    const timerElement = document.getElementById('timer');
                    if (timeLeft < 60) { // Less than 1 minute
                        timerElement.classList.remove('bg-purple-900/20', 'bg-yellow-900/20', 'border-purple-800/30', 'border-yellow-800/30');
                        timerElement.classList.add('bg-red-900/20', 'text-red-400', 'border-red-800/30');
                    } else if (timeLeft < 300) { // Less than 5 minutes
                        timerElement.classList.remove('bg-purple-900/20', 'border-purple-800/30');
                        timerElement.classList.add('bg-yellow-900/20', 'text-yellow-400', 'border-yellow-800/30');
                    }
                    
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        // Auto-submit the form only when timer expires
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
            
            // Initialize pagination
            initializePagination();
        });

        // Save current page to restore if user comes back
        function savePage(pageNum) {
            localStorage.setItem(`quiz_{{ $set->id }}_current_page`, pageNum);
        }

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
                        
                        // Update UI
                        updateNavigationUI();
                    }
                });
            });
            
            // Check if this question was previously answered
            const answeredQuestions = JSON.parse(localStorage.getItem(`quiz_${setId}_answered_all`));
            if (answeredQuestions[questionNumber]) {
                isAnswered = true;
            }
            
            // Update navigation to show which questions are answered
            updateNavigationUI();
            
            return isAnswered;
        }

        // Update the navigation to show which questions are answered
        function updateNavigationUI() {
            const setId = {{ $set->id }};
            const totalQuestions = {{ $totalPages }};
            
            if (localStorage.getItem(`quiz_${setId}_answered_all`)) {
                const answeredQuestions = JSON.parse(localStorage.getItem(`quiz_${setId}_answered_all`));
                const answeredCount = Object.values(answeredQuestions).filter(val => val === true).length;
                
                // Update counter
                const counterEl = document.getElementById('answered-count');
                if (counterEl) {
                    counterEl.textContent = answeredCount;
                }
                
                // Update navigation links
                document.querySelectorAll('.question-nav-link').forEach(link => {
                    const questionNum = parseInt(link.querySelector('.question-number').textContent.trim());
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
        }

        /**
         * Initialize the pagination system
         */
        function initializePagination() {
            const totalQuestions = {{ $totalPages }};
            const currentPage = {{ $currentPage }};
            const setId = {{ $set->id }};
            
            // Get current batch (groups of 10)
            const currentBatch = Math.ceil(currentPage / 10);
            const batchStart = (currentBatch - 1) * 10 + 1;
            const batchEnd = Math.min(batchStart + 9, totalQuestions);
            
            // Track the current batch
            localStorage.setItem(`quiz_${setId}_current_batch`, currentBatch);
        }

        /**
         * Navigate to a specific batch
         */
        function navigateToBatch(batchNumber) {
            const setId = {{ $set->id }};
            const totalQuestions = {{ $totalPages }};
            
            // Calculate the page number for the first question in the batch
            const firstQuestionInBatch = (batchNumber - 1) * 10 + 1;
            
            // Make sure the batch is valid
            if (firstQuestionInBatch > 0 && firstQuestionInBatch <= totalQuestions) {
                // Save the current batch
                localStorage.setItem(`quiz_${setId}_current_batch`, batchNumber);
                
                // Navigate to the first question in the batch
                window.location.href = `{{ route('challenges.attempt', ['set' => $set, 'page' => '']) }}${firstQuestionInBatch}`;
            }
        }

        function confirmSubmit() {
            const setId = {{ $set->id }};
            const totalQuestions = {{ $totalPages }};
            
            // Get tracking data
            const answeredQuestions = JSON.parse(localStorage.getItem(`quiz_${setId}_answered_all`));
            
            // Check if all questions have been answered
            let allAnswered = true;
            let unansweredCount = 0;
            let unansweredList = [];
            
            for (let i = 1; i <= totalQuestions; i++) {
                if (!answeredQuestions[i]) {
                    allAnswered = false;
                    unansweredCount++;
                    unansweredList.push(i);
                }
            }
            
            if (!allAnswered) {
                const confirmMsg = `You have not answered ${unansweredCount} question(s).\n\nQuestions: ${unansweredList.join(', ')}\n\nAre you sure you want to submit?`;
                return confirm(confirmMsg);
            }
            
            return true;
        }

        function submitForm(isAutoSubmit = false) {
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
            
            if (isAutoSubmit || confirmSubmit()) {
                // Show a loading state
                const submitBtn = document.querySelector('button[onclick="submitForm()"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Submitting...
                    `;
                }
                
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
    @endpush
</x-attempt-layout>