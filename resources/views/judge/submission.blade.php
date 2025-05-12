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
                        
                        @if($participant->score !== null)
                            <div class="mt-4 md:mt-0 p-2 bg-green-900/20 rounded-lg border border-green-800/30">
                                <p class="font-medium text-green-400">Current Score: {{ $participant->score }}/10</p>
                            </div>
                        @endif
                    </div>

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
                            
                            @if($tournament->team_size > 1 && $participant->team_name)
                                <div class="mb-4">
                                    <span class="text-amber-400 font-medium">Team Name:</span>
                                    <span class="text-gray-300 ml-2">{{ $participant->team_name }}</span>
                                </div>
                                
                                @if(isset($participant->team_members) && count($participant->team_members) > 0)
                                    <div>
                                        <span class="text-amber-400 font-medium">Team Members:</span>
                                        <ul class="list-disc list-inside mt-2 text-gray-300 ml-2">
                                            @foreach($participant->team_members as $member)
                                                <li>{{ $member }}</li>
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
                        
                        <!-- Judging Rubrics -->
                        <div class="bg-gray-900/40 p-4 rounded-lg mb-6">
                            <h5 class="font-medium text-amber-400 mb-2">Judging Rubrics</h5>
                            
                            @if($tournament->rubrics->count() > 0)
                                <div class="space-y-2">
                                    @foreach($tournament->rubrics as $rubric)
                                        <div class="flex justify-between items-center border-b border-amber-800/10 py-2">
                                            <span class="text-gray-300">{{ $rubric->title }}</span>
                                            <span class="bg-amber-900/20 px-2 py-1 rounded-md text-amber-400">{{ $rubric->score_weight }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-400">No specific rubrics have been defined for this tournament.</p>
                            @endif
                        </div>
                        
                        <!-- Grading Form -->
                        <div class="bg-gray-900/20 p-4 rounded-lg">
                            <h4 class="font-medium text-lg mb-4 text-amber-400">
                                {{ $participant->score !== null ? 'Update Score' : 'Submit Score' }}
                            </h4>
                            
                            <form action="{{ route('judge.submit-score', ['tournament' => $tournament, 'participant' => $participant]) }}" method="POST">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="score" class="block text-sm font-medium text-gray-300 mb-2">
                                        Score (0-10)
                                    </label>
                                    <input 
                                        type="number" 
                                        name="score" 
                                        id="score" 
                                        min="0" 
                                        max="10" 
                                        required
                                        value="{{ old('score', $participant->score) }}" 
                                        class="w-full md:w-1/4 p-2 border border-gray-700 rounded-md bg-gray-800 text-white"
                                    >
                                    @error('score')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="feedback" class="block text-sm font-medium text-gray-300 mb-2">
                                        Feedback
                                    </label>
                                    <textarea 
                                        name="feedback" 
                                        id="feedback" 
                                        rows="6" 
                                        required
                                        class="w-full p-2 border border-gray-700 rounded-md bg-gray-800 text-white"
                                    >{{ old('feedback', $participant->feedback) }}</textarea>
                                    @error('feedback')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-md">
                                        {{ $participant->score !== null ? 'Update Grade' : 'Submit Grade' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>