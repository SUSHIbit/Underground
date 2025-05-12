<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Tournament') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('accessor.tournaments') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Tournament Review
                        </a>
                    </div>
                    
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-medium mb-1">{{ $tournament->title }}</h3>
                            <p class="text-gray-600">Created by: {{ $tournament->creator->name }}</p>
                            <p class="text-gray-600">Submitted: {{ $tournament->submitted_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="text-sm px-3 py-1 rounded-full 
                            {{ $tournament->status == 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $tournament->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $tournament->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $tournament->status)) }}
                        </div>
                    </div>
                    
                    @if($tournament->isRejected() || $tournament->isApproved())
                        <div class="mb-6 p-4 {{ $tournament->isApproved() ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} rounded-lg">
                            <h4 class="font-medium {{ $tournament->isApproved() ? 'text-green-800' : 'text-red-800' }} mb-2">Review Notes</h4>
                            <p class="{{ $tournament->isApproved() ? 'text-green-700' : 'text-red-700' }}">{{ $tournament->review_notes }}</p>
                        </div>
                    @endif
                    
                    <!-- Tournament details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
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
                    
                    <!-- Rubrics Section (Added) -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-2">Judging Rubrics</h4>
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
                    
                    <!-- Judges Section -->
                    <div class="mb-6">
                        <h4 class="font-medium">Judges</h4>
                        <ul class="list-disc list-inside">
                            @foreach($tournament->judges as $judge)
                                <li>{{ $judge->name }} - {{ $judge->role }}</li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <!-- Overall comments for the tournament -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-2">Comments</h4>
                        
                        <div class="space-y-3 mb-4">
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
                        
                        @if($tournament->isPendingApproval())
                            <form action="{{ route('accessor.tournaments.comment', $tournament) }}" method="POST" class="space-y-2">
                                @csrf
                                <div>
                                    <label for="comment" class="block mb-1 text-sm font-medium text-gray-700">Add a comment</label>
                                    <textarea 
                                        name="comment" 
                                        id="comment" 
                                        rows="3" 
                                        class="w-full p-2 border border-gray-300 rounded-md"
                                        placeholder="Enter your comment about the tournament"
                                        required
                                    ></textarea>
                                </div>
                                <div>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Add Comment
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                    
                    @if($tournament->isPendingApproval())
                        <div class="flex justify-between items-center">
                            <a href="{{ route('accessor.tournaments') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Back to Dashboard
                            </a>
                            
                            <div class="flex space-x-4">
                                <button type="button" onclick="document.getElementById('rejection-modal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Reject
                                </button>
                                
                                <button type="button" onclick="document.getElementById('approval-modal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Approve
                                </button>
                            </div>
                        </div>
                        
                        <!-- Approval Modal -->
                        <div id="approval-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center">
                            <div class="bg-white rounded-lg p-8 max-w-md w-full">
                                <h3 class="text-lg font-medium mb-4">Approve this Tournament</h3>
                                
                                <form action="{{ route('accessor.tournaments.approve', $tournament) }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-4">
                                        <label for="review_notes" class="block mb-2 text-sm font-medium text-gray-700">
                                            Approval Notes (optional)
                                        </label>
                                        <textarea 
                                            name="review_notes" 
                                            id="review_notes" 
                                            rows="3" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            placeholder="Add any notes about your approval"
                                        ></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="document.getElementById('approval-modal').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                            Cancel
                                        </button>
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Confirm Approval
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Rejection Modal -->
                        <div id="rejection-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center">
                            <div class="bg-white rounded-lg p-8 max-w-md w-full">
                                <h3 class="text-lg font-medium mb-4">Reject this Tournament</h3>
                                
                                <form action="{{ route('accessor.tournaments.reject', $tournament) }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-4">
                                        <label for="review_notes" class="block mb-2 text-sm font-medium text-gray-700">
                                            Rejection Reason (required)
                                        </label>
                                        <textarea 
                                            name="review_notes" 
                                            id="review_notes" 
                                            rows="3" 
                                            class="w-full p-2 border border-gray-300 rounded-md"
                                            placeholder="Please explain why you are rejecting this tournament"
                                            required
                                        ></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="document.getElementById('rejection-modal').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                            Cancel
                                        </button>
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Confirm Rejection
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div>
                            <a href="{{ route('accessor.tournaments') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Back to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>