<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Tournaments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    @if(session('error'))
                        <div class="bg-red-900/20 border-l-4 border-red-500 text-red-400 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-900/20 border-l-4 border-green-500 text-green-400 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <!-- Project Archive Section -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-amber-400">Project Archive</h3>
                            <a href="{{ route('tournaments.archive') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-md transition duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                View My Projects
                            </a>
                        </div>
                        <div class="bg-amber-900/10 p-4 rounded-lg border border-amber-800/20">
                            <p class="text-gray-300 text-sm">
                                View all your tournament submissions and projects in one place. Access your portfolio of work from past competitions.
                            </p>
                        </div>
                    </div>

                    <!-- Available Tournaments Section -->
                    <h3 class="text-lg font-medium mb-4 text-amber-400">Available Tournaments</h3>
                    
                    @php
                        $hasUpcomingTournaments = false;
                        foreach ($upcomingGrouped as $type => $group) {
                            if ($group['tournaments']->count() > 0) {
                                $hasUpcomingTournaments = true;
                                break;
                            }
                        }
                    @endphp
                    
                    @if($hasUpcomingTournaments)
                        <div class="space-y-8">
                            @foreach($upcomingGrouped as $type => $group)
                                @if($group['tournaments']->count() > 0)
                                    <div class="bg-gray-900/30 rounded-lg p-4">
                                        <h4 class="text-lg font-medium mb-4 text-amber-400 border-b border-amber-800/20 pb-2">
                                            {{ $group['display_name'] }}
                                        </h4>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($group['tournaments'] as $tournament)
                                                <div class="border border-amber-800/20 rounded-lg overflow-hidden shadow-sm bg-gray-800">
                                                    <!-- Make the entire header section a link -->
                                                    <a href="{{ route('tournaments.show', $tournament) }}" class="block">
                                                        <div class="p-4 bg-amber-900/20 border-b border-amber-800/20">
                                                            <h4 class="font-medium text-amber-400">{{ $tournament->title }}</h4>
                                                            <p class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}</p>
                                                        </div>
                                                    </a>
                                                    <div class="p-4">
                                                        <p class="mb-4 text-sm text-gray-400">
                                                            <span class="font-medium text-gray-300">Location:</span> {{ $tournament->location }}
                                                        </p>
                                                        <p class="mb-4 text-sm text-gray-400">
                                                            <span class="font-medium text-gray-300">Team Size:</span> {{ $tournament->team_size }} {{ $tournament->team_size > 1 ? 'members' : 'member' }}
                                                        </p>
                                                        <p class="mb-4 text-sm text-gray-400">
                                                            <span class="font-medium text-gray-300">Minimum Rank:</span> {{ $tournament->minimum_rank }}
                                                        </p>
                                                        
                                                        @if($tournament->isParticipating)
                                                            <div class="flex space-x-2">
                                                                <span class="inline-block bg-green-900/20 text-green-400 text-xs px-2 py-1 rounded-md border border-green-800/20">
                                                                    Registered
                                                                </span>
                                                                <a href="{{ route('tournaments.show', $tournament) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold py-1 px-3 rounded-md transition duration-150">
                                                                    View Details
                                                                </a>
                                                            </div>
                                                        @elseif(!$tournament->canParticipate)
                                                            <span class="inline-block bg-amber-900/20 text-amber-400 text-xs px-2 py-1 rounded-md border border-amber-800/20">
                                                                Not Eligible
                                                            </span>
                                                        @else
                                                            <a href="{{ route('tournaments.show', $tournament) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded-md transition duration-150">
                                                                View Details
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-900/50 p-6 rounded-lg text-center border border-amber-800/20 mb-8">
                            <p class="text-gray-400">No upcoming tournaments available at the moment.</p>
                            <p class="text-gray-500 mt-2">Check back later for new tournaments!</p>
                        </div>
                    @endif

                    <!-- Completed Tournaments Section -->
                    @php
                        $hasCompletedTournaments = false;
                        foreach ($completedGrouped as $type => $group) {
                            if ($group['tournaments']->count() > 0) {
                                $hasCompletedTournaments = true;
                                break;
                            }
                        }
                    @endphp
                    
                    @if($hasCompletedTournaments)
                        <div class="mt-12">
                            <h3 class="text-lg font-medium mb-4 text-gray-400">Completed Tournaments</h3>
                            
                            <div class="space-y-8">
                                @foreach($completedGrouped as $type => $group)
                                    @if($group['tournaments']->count() > 0)
                                        <div class="bg-gray-900/50 rounded-lg p-4 opacity-80">
                                            <h4 class="text-lg font-medium mb-4 text-gray-400 border-b border-gray-700/30 pb-2">
                                                {{ $group['display_name'] }}
                                            </h4>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                @foreach($group['tournaments'] as $tournament)
                                                    <div class="border border-gray-700/30 rounded-lg overflow-hidden shadow-sm bg-gray-800/60">
                                                        <!-- Make the entire header section a link -->
                                                        <a href="{{ route('tournaments.show', $tournament) }}" class="block">
                                                            <div class="p-4 bg-gray-700/20 border-b border-gray-700/20">
                                                                <h4 class="font-medium text-gray-300">{{ $tournament->title }}</h4>
                                                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}</p>
                                                                <span class="inline-block mt-1 bg-gray-700/30 text-gray-400 text-xs px-2 py-1 rounded-md">
                                                                    Completed
                                                                </span>
                                                            </div>
                                                        </a>
                                                        <div class="p-4">
                                                            <p class="mb-4 text-sm text-gray-500">
                                                                <span class="font-medium text-gray-400">Location:</span> {{ $tournament->location }}
                                                            </p>
                                                            <p class="mb-4 text-sm text-gray-500">
                                                                <span class="font-medium text-gray-400">Team Size:</span> {{ $tournament->team_size }} {{ $tournament->team_size > 1 ? 'members' : 'member' }}
                                                            </p>
                                                            
                                                            @if($tournament->isParticipating)
                                                                <a href="{{ route('tournaments.show', $tournament) }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white text-xs font-bold py-1 px-3 rounded-md transition duration-150">
                                                                    View Results
                                                                </a>
                                                            @else
                                                                <a href="{{ route('tournaments.show', $tournament) }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white text-xs font-bold py-1 px-3 rounded-md transition duration-150">
                                                                    View Details
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>