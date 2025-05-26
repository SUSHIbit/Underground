<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Leaderboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-4 sm:p-6 text-gray-200">
                    <h3 class="text-lg font-medium mb-4 text-amber-400">Top Students</h3>
                    
                    <!-- Mobile Card Layout -->
                    <div class="block lg:hidden space-y-3">
                        @foreach($users as $index => $user)
                            <div class="bg-gray-900/50 p-4 rounded-lg border border-amber-800/20 {{ $user->id === auth()->id() ? 'bg-amber-900/10 border-amber-600/30' : '' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                                        <div class="flex-shrink-0 w-8 h-8 bg-amber-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-white truncate">
                                                {{ $user->name }}
                                                @if($user->id === auth()->id())
                                                    <span class="text-xs text-amber-400 ml-1">(You)</span>
                                                @endif
                                            </p>
                                            <div class="mt-1">
                                                <span class="inline-block px-2 py-1 text-xs rounded-full
                                                    {{ $user->rankTitle === 'Unranked' ? 'bg-gray-700 text-gray-300' : '' }}
                                                    {{ $user->rankTitle === 'Bronze' ? 'bg-amber-900/30 text-amber-400' : '' }}
                                                    {{ $user->rankTitle === 'Silver' ? 'bg-gray-800 text-gray-300' : '' }}
                                                    {{ $user->rankTitle === 'Gold' ? 'bg-amber-800/30 text-amber-300' : '' }}
                                                    {{ $user->rankTitle === 'Master' ? 'bg-purple-900/30 text-purple-400' : '' }}
                                                    {{ $user->rankTitle === 'Grand Master' ? 'bg-red-900/30 text-red-400' : '' }}
                                                    {{ $user->rankTitle === 'One Above All' ? 'bg-indigo-900/30 text-indigo-400' : '' }}">
                                                    {{ $user->rankTitle }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right ml-3">
                                        <p class="text-lg font-bold text-amber-400">{{ $user->points }}</p>
                                        <p class="text-xs text-gray-400">points</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop Table Layout -->
                    <div class="hidden lg:block overflow-x-auto bg-gray-900/50 rounded-lg border border-amber-800/20">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-amber-800/20">
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Rank</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Student</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Points</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Rank Title</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                    <tr class="border-b border-gray-700 hover:bg-gray-800/50 {{ $user->id === auth()->id() ? 'bg-amber-900/10' : '' }}">
                                        <td class="py-3 px-4 whitespace-nowrap text-base font-medium">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="py-3 px-4 text-base">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="text-xs text-amber-400 ml-2">(You)</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-medium text-amber-400 text-base">
                                            {{ $user->points }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-block px-2 py-1 text-xs rounded-full
                                                {{ $user->rankTitle === 'Unranked' ? 'bg-gray-700 text-gray-300' : '' }}
                                                {{ $user->rankTitle === 'Bronze' ? 'bg-amber-900/30 text-amber-400' : '' }}
                                                {{ $user->rankTitle === 'Silver' ? 'bg-gray-800 text-gray-300' : '' }}
                                                {{ $user->rankTitle === 'Gold' ? 'bg-amber-800/30 text-amber-300' : '' }}
                                                {{ $user->rankTitle === 'Master' ? 'bg-purple-900/30 text-purple-400' : '' }}
                                                {{ $user->rankTitle === 'Grand Master' ? 'bg-red-900/30 text-red-400' : '' }}
                                                {{ $user->rankTitle === 'One Above All' ? 'bg-indigo-900/30 text-indigo-400' : '' }}">
                                                {{ $user->rankTitle }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>