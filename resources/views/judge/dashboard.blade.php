<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Judge Dashboard') }}
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

                    <!-- Ready to Judge Tournaments Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-amber-400">Ready to Judge</h3>
                        
                        @if($readyToJudgeTournaments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-amber-800/20">
                                    <thead class="bg-gray-900">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider whitespace-nowrap">Date & Time</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider hidden md:table-cell">Location</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider hidden sm:table-cell">Your Role</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-gray-800 divide-y divide-amber-800/20">
                                        @foreach($readyToJudgeTournaments as $tournament)
                                            <tr>
                                                <td class="px-4 py-4 text-sm font-medium text-gray-300 truncate max-w-[150px]">
                                                    <div class="truncate">{{ $tournament->title }}</div>
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-400 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($tournament->date_time)->format('M d, Y g:i a') }}
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-400 hidden md:table-cell truncate max-w-[150px]">
                                                    <div class="truncate">{{ $tournament->location }}</div>
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-400 hidden sm:table-cell">
                                                    {{ $tournament->pivot->role ?? 'Judge' }}
                                                </td>
                                                <td class="px-4 py-4 text-sm font-medium">
                                                    <a href="{{ route('judge.tournament', $tournament) }}" class="text-amber-400 hover:text-amber-300 whitespace-nowrap">
                                                        Start Judging
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-900/50 rounded-lg border border-amber-800/20 p-6 text-center">
                                <p class="text-gray-400">You have no tournaments that are ready for judging.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Waiting Period Tournaments Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-amber-400">In Waiting Period</h3>
                        
                        @if($waitingPeriodTournaments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-amber-800/20">
                                    <thead class="bg-gray-900">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider whitespace-nowrap">Date & Time</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider hidden md:table-cell">Location</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider hidden sm:table-cell">Your Role</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Available</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-gray-800 divide-y divide-amber-800/20">
                                        @foreach($waitingPeriodTournaments as $tournament)
                                            <tr>
                                                <td class="px-4 py-4 text-sm font-medium text-gray-300 truncate max-w-[150px]">
                                                    <div class="truncate">{{ $tournament->title }}</div>
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-400 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($tournament->date_time)->format('M d, Y g:i a') }}
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-400 hidden md:table-cell truncate max-w-[150px]">
                                                    <div class="truncate">{{ $tournament->location }}</div>
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-400 hidden sm:table-cell">
                                                    {{ $tournament->pivot->role ?? 'Judge' }}
                                                </td>
                                                <td class="px-4 py-4 text-sm text-yellow-400 whitespace-nowrap">
                                                    @php
                                                        $waitingPeriodDays = 2;
                                                        $waitingPeriodEnd = \Carbon\Carbon::parse($tournament->date_time)->addDays($waitingPeriodDays);
                                                    @endphp
                                                    Available {{ $waitingPeriodEnd->diffForHumans() }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-900/50 rounded-lg border border-amber-800/20 p-6 text-center">
                                <p class="text-gray-400">You have no tournaments in waiting period.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Upcoming Tournaments Section -->
                    <div>
                        <h3 class="text-lg font-medium mb-4 text-amber-400">Upcoming Tournaments</h3>
                        
                        @if($upcomingTournaments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-amber-800/20">
                                    <thead class="bg-gray-900">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider whitespace-nowrap">Date & Time</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider hidden md:table-cell">Location</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider hidden sm:table-cell">Your Role</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-gray-800 divide-y divide-amber-800/20">
                                        @foreach($upcomingTournaments as $tournament)
                                            <tr>
                                                <td class="px-4 py-4 text-sm font-medium text-gray-300 truncate max-w-[150px]">
                                                    <div class="truncate">{{ $tournament->title }}</div>
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-400 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($tournament->date_time)->format('M d, Y g:i a') }}
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-400 hidden md:table-cell truncate max-w-[150px]">
                                                    <div class="truncate">{{ $tournament->location }}</div>
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-400 hidden sm:table-cell">
                                                    {{ $tournament->pivot->role ?? 'Judge' }}
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-400 whitespace-nowrap">
                                                    Starts {{ \Carbon\Carbon::parse($tournament->date_time)->diffForHumans() }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-900/50 rounded-lg border border-amber-800/20 p-6 text-center">
                                <p class="text-gray-400">You have no upcoming tournaments to judge.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>