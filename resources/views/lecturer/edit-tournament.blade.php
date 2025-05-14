<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tournament') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('lecturer.tournaments') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Tournament Management
                        </a>
                    </div>
                    
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-medium mb-1">{{ $tournament->title }}</h3>
                            <p class="text-gray-600">Created: {{ $tournament->created_at ? $tournament->created_at->format('M d, Y') : 'Not set' }}</p>
                        </div>
                        <div class="text-sm px-3 py-1 rounded-full 
                            {{ $tournament->status == 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $tournament->status == 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $tournament->status == 'approved_unpublished' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $tournament->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $tournament->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $tournament->status)) }}
                        </div>
                    </div>
                    
                    @if($tournament->isRejected())
                        <div class="mb-6 p-4 bg-red-50 rounded-lg border border-red-200">
                            <h4 class="font-medium text-red-800 mb-2">Rejection Notes from {{ $tournament->reviewer ? $tournament->reviewer->name : 'Reviewer' }}</h4>
                            <p class="text-red-700">{{ $tournament->review_notes }}</p>
                        </div>
                    @endif
                    
                    @if($tournament->comments->count() > 0)
                        <div class="mb-6">
                            <h4 class="font-medium mb-2">Comments</h4>
                            <div class="space-y-3">
                                @foreach($tournament->comments as $comment)
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
                    
                    @if($tournament->isApprovedUnpublished())
                        <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <h4 class="font-medium text-yellow-800 mb-2">This tournament has been approved by an accessor</h4>
                            <p class="text-yellow-700">
                                It is ready to be published. Once published, it will be available to students.
                                You cannot make further edits to this content.
                            </p>
                            <div class="mt-4">
                                <form action="{{ route('lecturer.tournaments.publish', $tournament) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Publish Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                    
                    @if($tournament->isDraft() || $tournament->isRejected())
                        <form action="{{ route('lecturer.tournaments.update', $tournament) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <div class="mb-4">
                                        <label for="title" class="block mb-2 font-medium text-gray-700">Title</label>
                                        <input 
                                            type="text" 
                                            name="title" 
                                            id="title" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            value="{{ old('title', $tournament->title) }}"
                                            required
                                        >
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="date_time" class="block mb-2 font-medium text-gray-700">Date & Time</label>
                                        <input 
                                            type="datetime-local" 
                                            name="date_time" 
                                            id="date_time" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            value="{{ old('date_time', $tournament->date_time ? \Carbon\Carbon::parse($tournament->date_time)->format('Y-m-d\TH:i') : '') }}"
                                            required
                                        >
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="location" class="block mb-2 font-medium text-gray-700">Location</label>
                                        <input 
                                            type="text" 
                                            name="location" 
                                            id="location" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            value="{{ old('location', $tournament->location) }}"
                                            required
                                        >
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="team_size" class="block mb-2 font-medium text-gray-700">Team Size</label>
                                        <input 
                                            type="number" 
                                            name="team_size" 
                                            id="team_size" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            value="{{ old('team_size', $tournament->team_size) }}"
                                            min="1"
                                            max="5"
                                            required
                                        >
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="deadline" class="block mb-2 font-medium text-gray-700">Submission Deadline</label>
                                        <input 
                                            type="datetime-local" 
                                            name="deadline" 
                                            id="deadline" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            value="{{ old('deadline', $tournament->deadline ? \Carbon\Carbon::parse($tournament->deadline)->format('Y-m-d\TH:i') : '') }}"
                                            required
                                        >
                                    </div>
                                    
                                    <!-- Judging Date and Time Fields -->
                                    <div class="mb-4">
                                        <label for="judging_date" class="block mb-2 font-medium text-gray-700">Judging Date & Time</label>
                                        <input 
                                            type="datetime-local" 
                                            name="judging_date" 
                                            id="judging_date" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            value="{{ old('judging_date', $tournament->judging_date ? \Carbon\Carbon::parse($tournament->judging_date)->format('Y-m-d\TH:i') : '') }}"
                                            required
                                        >
                                        <p class="mt-1 text-sm text-gray-500">Please select a date and time after the submission deadline.</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="minimum_rank" class="block mb-2 font-medium text-gray-700">Minimum Rank</label>
                                        <select 
                                            name="minimum_rank" 
                                            id="minimum_rank" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            required
                                        >
                                            @foreach(['Unranked', 'Bronze', 'Silver', 'Gold', 'Master', 'Grand Master', 'One Above All'] as $rank)
                                                <option value="{{ $rank }}" {{ old('minimum_rank', $tournament->minimum_rank) == $rank ? 'selected' : '' }}>
                                                    {{ $rank }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="mb-4">
                                        <label for="description" class="block mb-2 font-medium text-gray-700">Description</label>
                                        <textarea 
                                            name="description" 
                                            id="description" 
                                            rows="5" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            required
                                        >{{ old('description', $tournament->description) }}</textarea>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="eligibility" class="block mb-2 font-medium text-gray-700">Eligibility</label>
                                        <textarea 
                                            name="eligibility" 
                                            id="eligibility" 
                                            rows="3" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            required
                                        >{{ old('eligibility', $tournament->eligibility) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="rules" class="block mb-2 font-medium text-gray-700">Rules</label>
                                <textarea 
                                    name="rules" 
                                    id="rules" 
                                    rows="5" 
                                    class="w-full p-2 border border-gray-300 rounded-md"
                                    required
                                >{{ old('rules', $tournament->rules) }}</textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="judging_criteria" class="block mb-2 font-medium text-gray-700">Judging Criteria</label>
                                <textarea 
                                    name="judging_criteria" 
                                    id="judging_criteria" 
                                    rows="5" 
                                    class="w-full p-2 border border-gray-300 rounded-md"
                                    required
                                >{{ old('judging_criteria', $tournament->judging_criteria) }}</textarea>
                            </div>
                            
                            <div class="mb-6">
                                <label for="project_submission" class="block mb-2 font-medium text-gray-700">Project Submission</label>
                                <textarea 
                                    name="project_submission" 
                                    id="project_submission" 
                                    rows="5" 
                                    class="w-full p-2 border border-gray-300 rounded-md"
                                    required
                                >{{ old('project_submission', $tournament->project_submission) }}</textarea>
                            </div>
                            
                            <!-- Judges Section -->
                            <div class="mb-6">
                                <h4 class="font-medium mb-4">Judges</h4>
                                
                                <div id="judges-container">
                                    @foreach($tournament->judges as $index => $judge)
                                        <div class="grid grid-cols-4 gap-4 mb-2 judge-row">
                                            <div class="col-span-3">
                                                <select 
                                                    name="judges[{{ $index }}]" 
                                                    class="w-full p-2 border border-gray-300 rounded-md"
                                                    required
                                                >
                                                    <option value="">Select a Judge</option>
                                                    @foreach(\App\Models\User::where('is_judge', true)->orderBy('name')->get() as $judgeUser)
                                                        <option value="{{ $judgeUser->id }}" {{ $judgeUser->id == $judge->id ? 'selected' : '' }}>
                                                            {{ $judgeUser->name }} ({{ $judgeUser->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-1">
                                                <input 
                                                    type="text" 
                                                    name="judge_roles[{{ $index }}]" 
                                                    placeholder="Judge Role" 
                                                    class="w-full p-2 border border-gray-300 rounded-md"
                                                    value="{{ $judge->pivot->role ?? '' }}"
                                                >
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <!-- Add empty rows for new judges if needed -->
                                    @for($i = $tournament->judges->count(); $i < 4; $i++)
                                        <div class="grid grid-cols-4 gap-4 mb-2 judge-row">
                                            <div class="col-span-3">
                                                <select 
                                                    name="judges[{{ $i }}]" 
                                                    class="w-full p-2 border border-gray-300 rounded-md"
                                                >
                                                    <option value="">Select a Judge</option>
                                                    @foreach(\App\Models\User::where('is_judge', true)->orderBy('name')->get() as $judgeUser)
                                                        <option value="{{ $judgeUser->id }}">
                                                            {{ $judgeUser->name }} ({{ $judgeUser->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-1">
                                                <input 
                                                    type="text" 
                                                    name="judge_roles[{{ $i }}]" 
                                                    placeholder="Judge Role" 
                                                    class="w-full p-2 border border-gray-300 rounded-md"
                                                >
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                
                                <button type="button" id="add-judge" class="text-blue-500 hover:text-blue-700 mt-2">
                                    + Add Judge
                                </button>
                            </div>
                            
                            <!-- Rubrics Section -->
                            <div class="mb-6">
                                <h4 class="font-medium mb-4">Judging Rubrics</h4>
                                
                                <!-- Rubric validation error message -->
                                @error('rubrics')
                                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                                        {{ $message }}
                                    </div>
                                @enderror
                                
                                <div id="rubrics-container">
                                    @foreach($tournament->rubrics as $index => $rubric)
                                        <div class="grid grid-cols-4 gap-4 mb-2 rubric-row">
                                            <input type="hidden" name="rubrics[{{ $index }}][id]" value="{{ $rubric->id }}">
                                            <div class="col-span-3">
                                                <input 
                                                    type="text" 
                                                    name="rubrics[{{ $index }}][title]" 
                                                    placeholder="Rubric Title" 
                                                    class="w-full p-2 border border-gray-300 rounded-md"
                                                    value="{{ old("rubrics.{$index}.title", $rubric->title) }}"
                                                    required
                                                >
                                            </div>
                                            <div class="col-span-1 flex items-center">
                                                <input 
                                                    type="number" 
                                                    name="rubrics[{{ $index }}][score_weight]" 
                                                    placeholder="Weight" 
                                                    class="w-full p-2 border border-gray-300 rounded-md rubric-weight"
                                                    value="{{ old("rubrics.{$index}.score_weight", $rubric->score_weight) }}"
                                                    min="1"
                                                    max="100"
                                                    required
                                                >
                                                <span class="ml-2">%</span>
                                                <button type="button" class="ml-2 text-red-500 remove-rubric">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <!-- For new rubrics -->
                                    <div id="new-rubrics-container"></div>
                                </div>
                                
                                <div class="flex justify-between mt-4">
                                    <button type="button" id="add-rubric" class="text-blue-500 hover:text-blue-700">
                                        + Add Rubric
                                    </button>
                                    <div>
                                        <span class="font-medium">Total Weight: </span>
                                        <span id="total-weight" class="font-medium {{ $tournament->getTotalRubricWeight() === 100 ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $tournament->getTotalRubricWeight() }}%
                                        </span>
                                        <span class="text-sm text-gray-500 ml-2">(Must equal 100%)</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <a href="{{ route('lecturer.tournaments') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                                
                                <div>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                                        Save Changes
                                    </button>
                                    
                                    @if($tournament->isDraft() || $tournament->isRejected())
                                        <button type="submit" form="submit-form" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Submit for Approval
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </form>
                        
                        <form id="submit-form" action="{{ route('lecturer.tournaments.submit', $tournament) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <div class="mb-4">
                                    <h4 class="font-medium">Title</h4>
                                    <p>{{ $tournament->title }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="font-medium">Date & Time</h4>
                                    <p>{{ $tournament->date_time ? \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') : 'Not set' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="font-medium">Location</h4>
                                    <p>{{ $tournament->location }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="font-medium">Team Size</h4>
                                    <p>{{ $tournament->team_size }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="font-medium">Submission Deadline</h4>
                                    <p>{{ $tournament->deadline ? \Carbon\Carbon::parse($tournament->deadline)->format('F j, Y, g:i a') : 'Not set' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="font-medium">Judging Date & Time</h4>
                                    <p>{{ $tournament->judging_date ? \Carbon\Carbon::parse($tournament->judging_date)->format('F j, Y, g:i a') : 'Not set' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="font-medium">Minimum Rank</h4>
                                    <p>{{ $tournament->minimum_rank }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <div class="mb-4">
                                    <h4 class="font-medium">Description</h4>
                                    <p class="whitespace-pre-line">{{ $tournament->description }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="font-medium">Eligibility</h4>
                                    <p class="whitespace-pre-line">{{ $tournament->eligibility }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h4 class="font-medium">Rules</h4>
                            <p class="whitespace-pre-line">{{ $tournament->rules }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h4 class="font-medium">Judging Criteria</h4>
                            <p class="whitespace-pre-line">{{ $tournament->judging_criteria }}</p>
                        </div>
                        
                        <div class="mb-6">
                            <h4 class="font-medium">Project Submission</h4>
                            <p class="whitespace-pre-line">{{ $tournament->project_submission }}</p>
                        </div>
                        
                        <!-- Read-only Judges Section -->
                        <div class="mb-6">
                            <h4 class="font-medium mb-4">Judges</h4>
                            
                            <div class="bg-gray-50 rounded-md p-4">
                                @if($tournament->judges->count() > 0)
                                    <ul class="list-disc list-inside">
                                        @foreach($tournament->judges as $judge)
                                            <li>
                                                {{ $judge->name }} 
                                                @if($judge->pivot->role)
                                                    <span class="text-gray-600">- {{ $judge->pivot->role }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500">No judges assigned to this tournament.</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Read-only Rubrics Section -->
                        <div class="mb-6">
                            <h4 class="font-medium mb-4">Judging Rubrics</h4>
                            <div class="bg-gray-50 rounded-md p-4">
                                @if($tournament->rubrics->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white">
                                            <thead>
                                                <tr>
                                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Rubric Title</th>
                                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($tournament->rubrics as $rubric)
                                                    <tr>
                                                        <td class="py-2 px-4 border-b border-gray-200">{{ $rubric->title }}</td>
                                                        <td class="py-2 px-4 border-b border-gray-200">{{ $rubric->score_weight }}%</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="bg-gray-50">
                                                    <td class="py-2 px-4 border-b border-gray-200 font-medium">Total</td>
                                                    <td class="py-2 px-4 border-b border-gray-200 font-medium">{{ $tournament->getTotalRubricWeight() }}%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500">No rubrics have been defined for this tournament.</p>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <a href="{{ route('lecturer.tournaments') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Back to Tournaments
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Dynamic Judges and Rubrics Management -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Judge management
        const addJudgeButton = document.getElementById('add-judge');
        const judgesContainer = document.getElementById('judges-container');
        
        if (addJudgeButton && judgesContainer) {
            let judgeIndex = document.querySelectorAll('.judge-row').length;
            
            addJudgeButton.addEventListener('click', function() {
                // Clone the last judge row and increment index
                const lastJudgeRow = document.querySelectorAll('.judge-row')[judgeIndex - 1];
                const newJudgeRow = lastJudgeRow.cloneNode(true);
                
                // Update input names with new index
                const selectInput = newJudgeRow.querySelector('select');
                const roleInput = newJudgeRow.querySelector('input');
                
                selectInput.name = `judges[${judgeIndex}]`;
                selectInput.value = '';
                roleInput.name = `judge_roles[${judgeIndex}]`;
                roleInput.value = '';
                
                judgesContainer.appendChild(newJudgeRow);
                judgeIndex++;
            });
        }
        
        // Rubric management
        const addRubricButton = document.getElementById('add-rubric');
        const newRubricsContainer = document.getElementById('new-rubrics-container');
        const totalWeightDisplay = document.getElementById('total-weight');
        
        if (addRubricButton && newRubricsContainer) {
            let rubricIndex = document.querySelectorAll('.rubric-row').length;
            
            // Function to update total weight
            function updateTotalWeight() {
                let total = 0;
                document.querySelectorAll('.rubric-weight').forEach(input => {
                    total += parseInt(input.value) || 0;
                });
                
                totalWeightDisplay.textContent = total + '%';
                
                if (total === 100) {
                    totalWeightDisplay.classList.remove('text-red-500');
                    totalWeightDisplay.classList.add('text-green-500');
                } else {
                    totalWeightDisplay.classList.remove('text-green-500');
                    totalWeightDisplay.classList.add('text-red-500');
                }
            }
            
            // Add event listener to all weight inputs
            document.querySelectorAll('.rubric-weight').forEach(input => {
                input.addEventListener('change', updateTotalWeight);
                input.addEventListener('keyup', updateTotalWeight);
            });
            
            // Add new rubric
            addRubricButton.addEventListener('click', function() {
                const newRubricRow = document.createElement('div');
                newRubricRow.className = 'grid grid-cols-4 gap-4 mb-2 rubric-row';
                
                newRubricRow.innerHTML = `
                    <div class="col-span-3">
                        <input 
                            type="text" 
                            name="rubrics[${rubricIndex}][title]" 
                            placeholder="Rubric Title" 
                            class="w-full p-2 border border-gray-300 rounded-md"
                            required
                        >
                    </div>
                    <div class="col-span-1 flex items-center">
                        <input 
                            type="number" 
                            name="rubrics[${rubricIndex}][score_weight]" 
                            placeholder="Weight" 
                            class="w-full p-2 border border-gray-300 rounded-md rubric-weight"
                            min="1"
                            max="100"
                            value="0"
                            required
                        >
                        <span class="ml-2">%</span>
                        <button type="button" class="ml-2 text-red-500 remove-rubric">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                `;

                newRubricsContainer.appendChild(newRubricRow);

                // Add event listener to new weight input
                const newWeightInput = newRubricRow.querySelector('.rubric-weight');
                newWeightInput.addEventListener('change', updateTotalWeight);
                newWeightInput.addEventListener('keyup', updateTotalWeight);
                
                // Add event listener to remove button
                const removeButton = newRubricRow.querySelector('.remove-rubric');
                removeButton.addEventListener('click', function() {
                    newRubricRow.remove();
                    updateTotalWeight();
                });
                
                rubricIndex++;
                updateTotalWeight();
            });
            
            // Handle existing remove rubric buttons
            document.querySelectorAll('.remove-rubric').forEach(button => {
                button.addEventListener('click', function() {
                    button.closest('.rubric-row').remove();
                    updateTotalWeight();
                });
            });
        }
    });
    </script>
</x-app-layout>