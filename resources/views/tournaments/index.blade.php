<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tournaments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Available Tournaments</h3>
                    
                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    @if($tournaments->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($tournaments as $tournament)
                                <div class="border rounded-lg overflow-hidden shadow-sm">
                                    <!-- Make the entire header section a link -->
                                    <a href="{{ route('tournaments.show', $tournament) }}" class="block">
                                        <div class="p-4 bg-gray-50 border-b">
                                            <h4 class="font-medium">{{ $tournament->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($tournament->date_time)->format('F j, Y, g:i a') }}</p>
                                        </div>
                                    </a>
                                    <div class="p-4">
                                        <p class="mb-4 text-sm text-gray-600">
                                            <span class="font-medium">Location:</span> {{ $tournament->location }}
                                        </p>
                                        <p class="mb-4 text-sm text-gray-600">
                                            <span class="font-medium">Team Size:</span> {{ $tournament->team_size }} {{ $tournament->team_size > 1 ? 'members' : 'member' }}
                                        </p>
                                        <p class="mb-4 text-sm text-gray-600">
                                            <span class="font-medium">Minimum Rank:</span> {{ $tournament->minimum_rank }}
                                        </p>
                                        
                                        @if($tournament->isParticipating)
                                            <div class="flex space-x-2">
                                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                                    Registered
                                                </span>
                                                <a href="{{ route('tournaments.show', $tournament) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 text-xs rounded">
                                                    View Details
                                                </a>
                                            </div>
                                        @elseif(!$tournament->canParticipate)
                                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                                                Not Eligible
                                            </span>
                                        @else
                                            <a href="{{ route('tournaments.show', $tournament) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                View Details
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No tournaments available at the moment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>