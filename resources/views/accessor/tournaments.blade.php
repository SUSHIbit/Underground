<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tournament Review') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Pending Approval -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Tournaments Pending Approval</h3>
                    
                    @if($pendingTournaments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Created By</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Location</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Submitted</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingTournaments as $tournament)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->title }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->creator->name }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ \Carbon\Carbon::parse($tournament->date_time)->format('M d, Y g:i a') }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->location }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->submitted_at->format('M d, Y') }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('accessor.tournaments.review', $tournament) }}" class="text-blue-500 hover:text-blue-700">
                                                    Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No tournaments pending approval.</p>
                    @endif
                </div>
            </div>

            <!-- Recently Reviewed -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Recently Reviewed Tournaments</h3>
                    
                    @if($reviewedTournaments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Created By</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Reviewed</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reviewedTournaments as $tournament)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->title }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->creator->name }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ \Carbon\Carbon::parse($tournament->date_time)->format('M d, Y g:i a') }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <span class="px-2 py-1 rounded-full text-xs 
                                                    {{ $tournament->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($tournament->status) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->reviewed_at->format('M d, Y') }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('accessor.tournaments.review', $tournament) }}" class="text-blue-500 hover:text-blue-700">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No recently reviewed tournaments.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>