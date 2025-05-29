<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Grade Submission') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    <div class="mb-6">
                        <a href="{{ route('judge.tournament', $tournament) }}" class="text-amber-400 hover:text-amber-300">
                            &larr; Back to Tournament
                        </a>
                    </div>

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-amber-400">{{ $tournament->title }}</h3>
                            <p class="text-gray-400">Submission by: {{ $participant->user->name }}</p>
                        </div>
                        
                        <div class="mt-4 md:mt-0 flex flex-col items-end space-y-2">
                            @if($existingJudgeScore)
                                <div class="p-2 bg-green-900/20 rounded-lg border border-green-800/30">
                                    <p class="font-medium text-green-400">Your Score: {{ $existingJudgeScore->score }}/10</p>
                                </div>
                            @endif
                            
                            @if($participant->score && $allJudgeScores->count() > 0)
                                <div class="p-2 bg-blue-900/20 rounded-lg border border-blue-800/30">
                                    <p class="font-medium text-blue-400">Average Score: {{ $participant->score }}/10</p>
                                    <p class="text-xs text-gray-400">({{ $allJudgeScores->count() }} judges)</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Other Judges' Scores (if any) -->
                    @if($allJudgeScores->count() > 0)
                        <div class="mb-8 bg-gray-900/40 p-4 rounded-lg">
                            <h4 class="font-medium text-lg mb-4 text-amber-400">Judge Scores</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($allJudgeScores as $judgeScore)
                                    <div class="p-3 bg-gray-800/50 rounded-lg border {{ $judgeScore->judge_user_id === auth()->id() ? 'border-green-800/30 bg-green-900/10' : 'border-gray-700/30' }}">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="font-medium {{ $judgeScore->judge_user_id === auth()->id() ? 'text-green-400' : 'text-gray-300' }}">
                                                {{ $judgeScore->judge->name }}
                                                @if($judgeScore->judge_user_id === auth()->id())
                                                    <span class="text-xs">(You)</span>
                                                @endif
                                            </p>
                                            <span class="font-bold text-amber-400">{{ $judgeScore->score }}/10</span>
                                        </div>
                                        
                                        @if($judgeScore->feedback)
                                            <p class="text-sm text-gray-400 truncate">{{ Str::limit($judgeScore->feedback, 50) }}</p>
                                        @else
                                            <p class="text-sm text-gray-500 italic">No feedback provided</p>
                                        @endif
                                        
                                        <p class="text-xs text-gray-500 mt-1">
                                            Graded {{ $judgeScore->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Submission Details -->
                    <div class="mb-8">
                        <h4 class="font-medium text-lg mb-4 text-amber-400">Submission Details</h4>
                        
                        <div class="bg-gray-900/40 p-4 rounded-lg mb-6">
                            <div class="mb-4">
                                <span class="text-amber-400 font-medium">Submission URL:</span>
                                <a href="{{ $participant->submission_url }}" target="_blank" class="text-blue-400 hover:text-blue-300 ml-2">
                                    {{ $participant->submission_url }}
                                </a>
                            </div>
                            
                            @if($tournament->team_size > 1 && $participant->team)
                                <div class="mb-4">
                                    <span class="text-amber-400 font-medium">Team Name:</span>
                                    <span class="text-gray-300 ml-2">{{ $participant->team->name }}</span>
                                </div>
                                
                                @if($participant->team->participants()->count() > 0)
                                    <div>
                                        <span class="text-amber-400 font-medium">Team Members:</span>
                                        <ul class="list-disc list-inside mt-2 text-gray-300 ml-2">
                                            @foreach($participant->team->participants as $member)
                                                <li>{{ $member->user->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        <!-- Judging Criteria -->
                        <div class="bg-gray-900/40 p-4 rounded-lg mb-6">
                            <h5 class="font-medium text-amber-400 mb-2">Judging Criteria</h5>
                            <div class="text-gray-300 whitespace-pre-line">{{ $tournament->judging_criteria }}</div>
                        </div>
                        
                        <!-- Grading Form -->
                        <div class="bg-gray-900/20 p-4 rounded-lg">
                            <h4 class="font-medium text-lg mb-4 text-amber-400">
                                {{ $existingJudgeScore ? 'Update Your Score' : 'Submit Your Score' }}
                            </h4>
                            
                            <form action="{{ route('judge.submit-score', ['tournament' => $tournament, 'participant' => $participant]) }}" method="POST" id="grading-form">
                                @csrf
                                
                                <!-- Dynamic Rubric Section -->
                                <div class="mb-6">
                                    <h5 class="font-medium text-amber-400 mb-4">Evaluation Rubrics</h5>
                                    
                                    @if($tournament->rubrics->count() > 0)
                                        <div class="space-y-4 mb-6">
                                            @foreach($tournament->rubrics as $rubric)
                                                <div class="border border-amber-800/10 rounded-lg bg-gray-900/30 p-4">
                                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-2">
                                                        <label for="rubric_{{ $rubric->id }}" class="block text-sm font-medium text-gray-300 mb-2 md:mb-0">
                                                            {{ $rubric->title }} ({{ $rubric->score_weight }}%)
                                                        </label>
                                                        <input 
                                                            type="number" 
                                                            name="rubric_scores[{{ $rubric->id }}]" 
                                                            id="rubric_{{ $rubric->id }}" 
                                                            min="0" 
                                                            max="10" 
                                                            step="0.1"
                                                            class="rubric-score w-24 p-2 border border-gray-700 rounded-md bg-gray-800 text-white"
                                                            data-weight="{{ $rubric->score_weight }}"
                                                            value="{{ old('rubric_scores.'.$rubric->id, isset($rubricScores[$rubric->id]) ? $rubricScores[$rubric->id] : '') }}"
                                                            required
                                                        >
                                                    </div>
                                                    @error('rubric_scores.'.$rubric->id)
                                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Calculated Total Score -->
                                        <div class="bg-amber-900/10 rounded-lg p-4 mb-6 border border-amber-800/20">
                                            <div class="flex justify-between items-center">
                                                <span class="text-lg font-medium text-amber-400">Calculated Total Score:</span>
                                                <div class="flex items-center">
                                                    <span id="calculated-score" class="text-xl font-bold text-white">0.0</span>
                                                    <span class="text-white ml-1">/10</span>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-400 mt-2">This score is automatically calculated based on the weighted average of all rubric scores.</p>
                                            <input type="hidden" name="score" id="final-score-input" value="0">
                                        </div>
                                    @else
                                        <div class="bg-gray-900/30 p-4 rounded-lg border border-amber-800/20 mb-6">
                                            <p class="text-gray-400">No specific rubrics have been defined for this tournament. Please provide an overall score.</p>
                                            <div class="mt-4">
                                                <label for="score" class="block text-sm font-medium text-gray-300 mb-2">
                                                    Overall Score (0-10)
                                                </label>
                                                <input 
                                                    type="number" 
                                                    name="score" 
                                                    id="score" 
                                                    min="0" 
                                                    max="10" 
                                                    step="0.1"
                                                    required
                                                    value="{{ old('score', $existingJudgeScore ? $existingJudgeScore->score : '') }}" 
                                                    class="w-full md:w-1/4 p-2 border border-gray-700 rounded-md bg-gray-800 text-white"
                                                >
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mb-4">
                                    <label for="feedback" class="block text-sm font-medium text-gray-300 mb-2">
                                        Feedback <span class="text-gray-500">(Optional)</span>
                                    </label>
                                    <textarea 
                                        name="feedback" 
                                        id="feedback" 
                                        rows="6" 
                                        placeholder="Provide feedback for the participant (optional)..."
                                        class="w-full p-2 border border-gray-700 rounded-md bg-gray-800 text-white"
                                    >{{ old('feedback', $existingJudgeScore ? $existingJudgeScore->feedback : '') }}</textarea>
                                    @error('feedback')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-md">
                                        {{ $existingJudgeScore ? 'Update Your Grade' : 'Submit Your Grade' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for calculating score -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rubricScores = document.querySelectorAll('.rubric-score');
            const calculatedScore = document.getElementById('calculated-score');
            const finalScoreInput = document.getElementById('final-score-input');
            
            // Function to calculate the weighted score
            function calculateWeightedScore() {
                let totalWeight = 0;
                let weightedSum = 0;
                
                rubricScores.forEach(input => {
                    const score = parseFloat(input.value) || 0;
                    const weight = parseFloat(input.dataset.weight) || 0;
                    
                    if (!isNaN(score) && !isNaN(weight)) {
                        weightedSum += score * (weight / 100);
                        totalWeight += weight;
                    }
                });
                
                // Calculate the final score (0-10 scale)
                let finalScore = 0;
                if (totalWeight > 0) {
                    // Normalize to ensure it's based on 100% even if weights don't add up to 100
                    finalScore = (weightedSum / (totalWeight / 100)).toFixed(1);
                }
                
                // Update the displayed score and hidden input
                if (calculatedScore && finalScoreInput) {
                    calculatedScore.textContent = finalScore;
                    finalScoreInput.value = finalScore;
                }
            }
            
            // Add event listeners to all rubric score inputs
            rubricScores.forEach(input => {
                input.addEventListener('input', calculateWeightedScore);
            });
            
            // Calculate initial score
            calculateWeightedScore();
        });
    </script>
</x-app-layout>