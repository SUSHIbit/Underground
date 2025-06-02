<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Tournament Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Drafts -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Draft Tournaments</h3>
                    
                    @if($draftTournaments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Location</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($draftTournaments as $tournament)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->title }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ \Carbon\Carbon::parse($tournament->date_time)->format('M d, Y g:i a') }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->location }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $tournament->created_at ? $tournament->created_at->format('M d, Y') : 'Not set' }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('lecturer.tournaments.edit', $tournament) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                                    Edit
                                                </a>
                                                <form action="{{ route('lecturer.tournaments.submit', $tournament) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-500 hover:text-green-700">
                                                        Submit for Approval
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No draft tournaments available.</p>
                    @endif
                </div>
            </div>

            <!-- Ready to Publish -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Ready to Publish</h3>
                    
                    @if(isset($approvedUnpublishedTournaments) && $approvedUnpublishedTournaments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Approved</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Reviewer</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedUnpublishedTournaments as $tournament)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->title }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ \Carbon\Carbon::parse($tournament->date_time)->format('M d, Y g:i a') }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $tournament->reviewed_at ? $tournament->reviewed_at->format('M d, Y') : 'Not reviewed' }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $tournament->reviewer ? $tournament->reviewer->name : 'Not reviewed' }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('lecturer.tournaments.edit', $tournament) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                                    View
                                                </a>
                                                <form action="{{ route('lecturer.tournaments.publish', $tournament) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-500 hover:text-green-700">
                                                        Publish
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No tournaments ready to publish.</p>
                    @endif
                </div>
            </div>

            <!-- Pending Approval -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Pending Approval</h3>
                    
                    @if($pendingTournaments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
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
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ \Carbon\Carbon::parse($tournament->date_time)->format('M d, Y g:i a') }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->location }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $tournament->submitted_at ? $tournament->submitted_at->format('M d, Y') : 'Not submitted' }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('lecturer.tournaments.edit', $tournament) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                                    View
                                                </a>
                                                <a href="{{ route('lecturer.tournaments.submissions', $tournament) }}" class="text-green-500 hover:text-green-700">
                                                    View Submissions
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

            <!-- Rejected -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Rejected Tournaments</h3>
                    
                    @if($rejectedTournaments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Reviewed</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Reviewer</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedTournaments as $tournament)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->title }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ \Carbon\Carbon::parse($tournament->date_time)->format('M d, Y g:i a') }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $tournament->reviewed_at ? $tournament->reviewed_at->format('M d, Y') : 'Not reviewed' }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $tournament->reviewer ? $tournament->reviewer->name : 'Not reviewed' }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('lecturer.tournaments.edit', $tournament) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                                    Edit
                                                </a>
                                                <form action="{{ route('lecturer.tournaments.submit', $tournament) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-500 hover:text-green-700">
                                                        Resubmit
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No rejected tournaments.</p>
                    @endif
                </div>
            </div>

            <!-- Approved -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Approved Tournaments</h3>
                    
                    @if($approvedTournaments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Approved</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Reviewer</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedTournaments as $tournament)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $tournament->title }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ \Carbon\Carbon::parse($tournament->date_time)->format('M d, Y g:i a') }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $tournament->reviewed_at ? $tournament->reviewed_at->format('M d, Y') : 'Not reviewed' }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $tournament->reviewer ? $tournament->reviewer->name : 'Not reviewed' }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('lecturer.tournaments.edit', $tournament) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                                    View
                                                </a>
                                                <a href="{{ route('lecturer.tournaments.submissions', $tournament) }}" class="text-green-500 hover:text-green-700">
                                                    View Submissions
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No approved tournaments.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>