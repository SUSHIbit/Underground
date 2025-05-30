<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Project Archive') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    <div class="mb-6">
                        <a href="{{ route('tournaments.index') }}" class="text-amber-400 hover:text-amber-300">
                            &larr; Back to Tournaments
                        </a>
                    </div>
                    
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-2 text-amber-400">Your Tournament Projects</h3>
                        <p class="text-gray-400">A collection of all your tournament submissions and projects.</p>
                    </div>
                    
                    @if($archivedProjects->count() > 0)
                        <!-- Statistics Overview -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                            <div class="bg-amber-900/10 p-4 rounded-lg border border-amber-800/20 text-center">
                                <div class="text-2xl font-bold text-amber-400">{{ $archivedProjects->count() }}</div>
                                <div class="text-sm text-gray-400">Total Projects</div>
                            </div>
                            <div class="bg-amber-900/10 p-4 rounded-lg border border-amber-800/20 text-center">
                                <div class="text-2xl font-bold text-amber-400">{{ $archivedProjects->where('tournament.team_size', 1)->count() }}</div>
                                <div class="text-sm text-gray-400">Solo Projects</div>
                            </div>
                            <div class="bg-amber-900/10 p-4 rounded-lg border border-amber-800/20 text-center">
                                <div class="text-2xl font-bold text-amber-400">{{ $archivedProjects->where('tournament.team_size', '>', 1)->count() }}</div>
                                <div class="text-sm text-gray-400">Team Projects</div>
                            </div>
                        </div>
                        
                        <!-- Projects grouped by tournament type -->
                        @if($groupedProjects->count() > 0)
                            <div class="space-y-8">
                                @foreach($groupedProjects as $type => $projects)
                                    <div class="bg-gray-900/30 rounded-lg p-6">
                                        <h4 class="text-lg font-medium mb-6 text-amber-400 border-b border-amber-800/20 pb-2">
                                            {{ $tournamentTypes[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}
                                            <span class="text-sm text-gray-400 font-normal">({{ $projects->count() }} projects)</span>
                                        </h4>
                                        
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            @foreach($projects as $participant)
                                                <div class="border border-amber-800/20 rounded-lg overflow-hidden shadow-sm bg-gray-800 hover:bg-gray-750 transition-colors">
                                                    <div class="p-6">
                                                        <!-- Tournament Header -->
                                                        <div class="mb-4">
                                                            <h5 class="font-medium text-amber-400 text-lg mb-1">{{ $participant->tournament->title }}</h5>
                                                            <p class="text-sm text-gray-400">
                                                                {{ \Carbon\Carbon::parse($participant->tournament->date_time)->format('F j, Y') }} • {{ $participant->tournament->location }}
                                                            </p>
                                                        </div>
                                                        
                                                        <!-- Project Type -->
                                                        <div class="mb-4">
                                                            @if($participant->tournament->team_size > 1)
                                                                <div class="flex items-center mb-2">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                    </svg>
                                                                    <span class="text-blue-400 font-medium">Team Project</span>
                                                                </div>
                                                                @if($participant->team)
                                                                    <p class="text-sm text-gray-400 mb-2">Team: <span class="text-white">{{ $participant->team->name }}</span></p>
                                                                @endif
                                                                <p class="text-xs text-gray-500">{{ $participant->tournament->team_size }} members</p>
                                                            @else
                                                                <div class="flex items-center mb-2">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                    </svg>
                                                                    <span class="text-green-400 font-medium">Solo Project</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <!-- Project Submission -->
                                                        <div class="mb-6">
                                                            <p class="text-sm font-medium text-gray-300 mb-2">Project Submission:</p>
                                                            <div class="bg-gray-700/30 p-3 rounded-lg border border-amber-800/10">
                                                                <a href="{{ $participant->submission_url }}" 
                                                                   target="_blank" 
                                                                   class="text-amber-400 hover:text-amber-300 break-all flex items-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                    </svg>
                                                                    {{ Str::limit($participant->submission_url, 60) }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Tournament Results (if available) -->
                                                        @if($participant->tournament_rank && $participant->tournament->isGradingComplete())
                                                            <div class="mb-4 p-3 bg-blue-900/10 rounded-lg border border-blue-800/20">
                                                                <div class="flex items-center justify-between">
                                                                    <div>
                                                                        <p class="text-sm font-medium text-blue-400">Tournament Result</p>
                                                                        <p class="text-lg font-bold {{ $participant->rank_color }}">{{ $participant->rank_display }}</p>
                                                                    </div>
                                                                    @if($participant->score)
                                                                        <div class="text-right">
                                                                            <p class="text-sm text-gray-400">Score</p>
                                                                            <p class="text-lg font-bold text-white">{{ $participant->score }}/10</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                @if($participant->ue_points_awarded)
                                                                    <div class="mt-2 pt-2 border-t border-blue-800/20">
                                                                        <p class="text-xs text-blue-400">
                                                                            Earned: {{ $participant->ue_points_awarded }} UEPoints
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        
                                                        <!-- Action Buttons -->
                                                        <div class="flex flex-wrap gap-2">
                                                            @if($participant->tournament->team_size > 1 && $participant->team_id)
                                                                <a href="{{ route('tournaments.team', $participant->tournament) }}" 
                                                                   class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                    </svg>
                                                                    View Team
                                                                </a>
                                                            @endif
                                                            
                                                            <a href="{{ route('tournaments.show', $participant->tournament) }}" 
                                                               class="inline-flex items-center px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-md transition duration-150">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                View Tournament
                                                            </a>
                                                            
                                                            @if($participant->tournament->hasEnded() && $participant->tournament->isGradingComplete())
                                                                <a href="{{ route('tournaments.participants', $participant->tournament) }}" 
                                                                   class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                                    </svg>
                                                                    Results
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Submission Date Footer -->
                                                    <div class="px-6 py-3 bg-gray-700/20 border-t border-amber-800/10">
                                                        <p class="text-xs text-gray-500">
                                                            Submitted {{ $participant->created_at->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-16">
                            <div class="mx-auto w-24 h-24 bg-amber-900/20 rounded-full flex items-center justify-center mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-medium text-gray-300 mb-2">No Projects Yet</h3>
                            <p class="text-gray-400 mb-6">You haven't submitted any tournament projects yet. Start by joining a tournament!</p>
                            <a href="{{ route('tournaments.index') }}" 
                               class="inline-flex items-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                Browse Tournaments
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>