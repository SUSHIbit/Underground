<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Tournament Submissions') }}
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
                            <h3 class="text-lg font-medium mb-1">{{ $tournament->title }} - Submissions</h3>
                            <p class="text-gray-600">Tournament Date: {{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}</p>
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
                    
                    <!-- Submission deadline information -->
                    <div class="mb-6 bg-gray-100 p-4 rounded-lg">
                        <p class="text-gray-700">
                            <span class="font-medium">Submission Deadline:</span> 
                            {{ \Carbon\Carbon::parse($tournament->deadline)->format('F j, Y, g:i a') }}
                        </p>
                        
                        @php
                            $timeLeft = \Carbon\Carbon::parse($tournament->deadline)->diffForHumans(['parts' => 2]);
                            $isDeadlinePassed = \Carbon\Carbon::parse($tournament->deadline)->isPast();
                            
                            // Calculate submission counts based on tournament type
                            if ($tournament->team_size > 1) {
                                // For team tournaments, count teams (not individual participants)
                                $totalTeams = $tournament->participants()->whereNotNull('team_id')->distinct('team_id')->count('team_id');
                                $submittedTeams = $tournament->participants()->whereNotNull('team_id')->whereNotNull('submission_url')->distinct('team_id')->count('team_id');
                                $submissionCount = $totalTeams;
                                $submittedCount = $submittedTeams;
                            } else {
                                // For individual tournaments, count participants
                                $submissionCount = $tournament->participants()->count();
                                $submittedCount = $tournament->participants()->whereNotNull('submission_url')->count();
                            }
                        @endphp
                        
                        <p class="mt-2 {{ $isDeadlinePassed ? 'text-red-600' : 'text-blue-600' }} font-medium">
                            {{ $isDeadlinePassed 
                                ? 'Submission deadline has passed' 
                                : "Time remaining for submission: {$timeLeft}" }}
                        </p>
                        
                        <p class="mt-2">
                            <span class="font-medium">{{ $tournament->team_size > 1 ? 'Teams' : 'Participants' }}:</span> {{ $submissionCount }}
                        </p>
                        <p class="mt-1">
                            <span class="font-medium">Submissions received:</span> {{ $submittedCount }}/{{ $submissionCount }}
                            ({{ $submissionCount > 0 ? round(($submittedCount/$submissionCount)*100) : 0 }}%)
                        </p>
                    </div>
                    
                    @if($participants->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-200 bg-white">
                                <thead>
                                    <tr>
                                        @if($tournament->team_size > 1)
                                            <th class="px-4 py-2 border border-gray-200 bg-gray-50 text-left">Team Name</th>
                                            <th class="px-4 py-2 border border-gray-200 bg-gray-50 text-left">Team Leader</th>
                                            <th class="px-4 py-2 border border-gray-200 bg-gray-50 text-left">Team Members</th>
                                        @else
                                            <th class="px-4 py-2 border border-gray-200 bg-gray-50 text-left">Participant</th>
                                        @endif
                                        <th class="px-4 py-2 border border-gray-200 bg-gray-50 text-left">Submission</th>
                                        <th class="px-4 py-2 border border-gray-200 bg-gray-50 text-left">Submission Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($participants as $participant)
                                        <tr>
                                            @if($tournament->team_size > 1)
                                                <!-- Team Name -->
                                                <td class="px-4 py-2 border border-gray-200">
                                                    {{ $participant->team ? $participant->team->name : 'No Team' }}
                                                </td>
                                                
                                                <!-- Team Leader -->
                                                <td class="px-4 py-2 border border-gray-200">
                                                    @if($participant->team && $participant->team->leader)
                                                        {{ $participant->team->leader->name }}
                                                        <div class="text-xs text-gray-500">{{ $participant->team->leader->email }}</div>
                                                    @else
                                                        <span class="text-gray-500">No leader assigned</span>
                                                    @endif
                                                </td>
                                                
                                                <!-- Team Members -->
                                                <td class="px-4 py-2 border border-gray-200">
                                                    @if($participant->team && $participant->team->participants->count() > 0)
                                                        <ul class="list-disc list-inside">
                                                            @foreach($participant->team->participants as $member)
                                                                <li>
                                                                    {{ $member->user->name }}
                                                                    @if($member->role === 'leader')
                                                                        <span class="text-xs text-blue-600">(Leader)</span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-gray-500">No team members</span>
                                                    @endif
                                                </td>
                                            @else
                                                <!-- Individual Participant -->
                                                <td class="px-4 py-2 border border-gray-200">
                                                    {{ $participant->user->name }}
                                                    <div class="text-xs text-gray-500">{{ $participant->user->email }}</div>
                                                </td>
                                            @endif
                                            
                                            <!-- Submission URL -->
                                            <td class="px-4 py-2 border border-gray-200">
                                                @if($participant->submission_url)
                                                    <a href="{{ $participant->submission_url }}" target="_blank" class="text-blue-600 hover:underline">
                                                        {{ $participant->submission_url }}
                                                    </a>
                                                @else
                                                    <span class="text-red-500">Not submitted yet</span>
                                                @endif
                                            </td>
                                            
                                            <!-- Submission Date -->
                                            <td class="px-4 py-2 border border-gray-200">
                                                @if($participant->submission_url)
                                                    <span class="text-green-600">
                                                        {{ $participant->updated_at->format('M d, Y g:i a') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-gray-50 p-6 rounded-lg text-center">
                            <p class="text-gray-500">No {{ $tournament->team_size > 1 ? 'teams have' : 'participants have' }} registered for this tournament yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>