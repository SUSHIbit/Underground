<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leaderboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Top Students</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Rank</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Student</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Points</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Rank Title</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                    <tr class="{{ $user->id === auth()->id() ? 'bg-blue-50' : '' }}">
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $index + 1 }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="text-xs text-blue-600 ml-2">(You)</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200 font-medium">{{ $user->points }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <span class="inline-block px-2 py-1 text-xs rounded-full
                                                {{ $user->rankTitle === 'Unranked' ? 'bg-gray-200 text-gray-800' : '' }}
                                                {{ $user->rankTitle === 'Bronze' ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $user->rankTitle === 'Silver' ? 'bg-gray-300 text-gray-800' : '' }}
                                                {{ $user->rankTitle === 'Gold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $user->rankTitle === 'Master' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $user->rankTitle === 'Grand Master' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $user->rankTitle === 'One Above All' ? 'bg-indigo-100 text-indigo-800' : '' }}">
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