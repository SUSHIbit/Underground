<!-- resources/views/legions/leaderboard.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Legion Leaderboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('legions.index') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Legions
                        </a>
                    </div>
                    
                    <h3 class="text-lg font-medium mb-6">Legion Power Rankings</h3>
                    
                    <div class="overflow-x-auto">
                        @if($legions->count() > 0)
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Rank</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Emblem</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Legion Name</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Leader</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Members</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Power</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Total Points</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($legions as $index => $legion)
                                        <tr class="{{ auth()->user()->isInLegion() && auth()->user()->getCurrentLegion() && auth()->user()->getCurrentLegion()->id === $legion->id ? 'bg-blue-50' : '' }}">
                                            <td class="py-2 px-4 border-b border-gray-200 font-bold">{{ $index + 1 }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @if($legion->emblem)
                                                    <img src="{{ asset('storage/' . $legion->emblem) }}" alt="{{ $legion->name }} Emblem" class="h-10 w-10 object-cover rounded-full">
                                                @else
                                                    <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                        <span class="text-gray-500 text-xs font-bold">{{ strtoupper(substr($legion->name, 0, 2)) }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200 font-medium">{{ $legion->name }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $legion->leader->name }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $legion->member_count }}/10</td>
                                            <td class="py-2 px-4 border-b border-gray-200 font-bold text-blue-600">{{ number_format($legion->power) }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200 text-green-600">{{ number_format($legion->total_points) }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('legions.show', $legion) }}" class="text-blue-500 hover:text-blue-700">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                <p class="text-gray-500">No legions available yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>