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
                            <p class="text-gray-600">Created: {{ $tournament->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-sm px-3 py-1 rounded-full 
                            {{ $tournament->status == 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $tournament->status == 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : '' }}
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
                                            value="{{ old('date_time', \Carbon\Carbon::parse($tournament->date_time)->format('Y-m-d\TH:i')) }}"
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
                                            value="{{ old('deadline', \Carbon\Carbon::parse($tournament->deadline)->format('Y-m-d\TH:i')) }}"
                                            required
                                        >
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
                            
                            <div class="mb-6">
                                <h4 class="font-medium mb-4">Judges</h4>
                                
                                <div id="judges-container">
                                    @foreach($tournament->judges as $index => $judge)
                                        <div class="grid grid-cols-3 gap-4 mb-2 judge-row">
                                            <div class="col-span-1">
                                                <input 
                                                    type="text" 
                                                    name="judges[{{ $index }}][name]" 
                                                    placeholder="Judge Name" 
                                                    class="w-full p-2 border border-gray-300 rounded-md"
                                                    value="{{ $judge->name }}"
                                                    required
                                                >
                                            </div>
                                            <div class="col-span-2">
                                                <input 
                                                    type="text" 
                                                    name="judges[{{ $index }}][role]" 
                                                    placeholder="Judge Role" 
                                                    class="w-full p-2 border border-gray-300 rounded-md"
                                                    value="{{ $judge->role }}"
                                                >
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @for($i = $tournament->judges->count(); $i < 4; $i++)
                                        <div class="grid grid-cols-3 gap-4 mb-2 judge-row">
                                            <div class="col-span-1">
                                                <input 
                                                    type="text" 
                                                    name="judges[{{ $i }}][name]" 
                                                    placeholder="Judge Name" 
                                                    class="w-full p-2 border border-gray-300 rounded-md"
                                                >
                                            </div>
                                            <div class="col-span-2">
                                                <input 
                                                    type="text" 
                                                    name="judges[{{ $i }}][role]" 
                                                    placeholder="Judge Role" 
                                                    class="w-full p-2 border border-gray-300 rounded-md"
                                                >
                                            </div>
                                        </div>
                                    @endfor
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
                                    <p>{{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}</p>
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
                                    <p>{{ \Carbon\Carbon::parse($tournament->deadline)->format('F j, Y, g:i a') }}</p>
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
                        
                        <div class="mb-6">
                            <h4 class="font-medium">Judges</h4>
                            <ul class="list-disc list-inside">
                                @foreach($tournament->judges as $judge)
                                    <li>{{ $judge->name }} - {{ $judge->role }}</li>
                                @endforeach
                            </ul>
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
</x-app-layout>