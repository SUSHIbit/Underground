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
                    <h3 class="text-lg font-medium mb-4 text-amber-400">Available Tournaments</h3>
                    
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
                    
                    @if($tournaments->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($tournaments as $tournament)
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
                    @else
                        <div class="bg-gray-900/50 p-6 rounded-lg text-center border border-amber-800/20">
                            <p class="text-gray-400">No tournaments available at the moment.</p>
                            <p class="text-gray-500 mt-2">Check back later for upcoming tournaments!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>