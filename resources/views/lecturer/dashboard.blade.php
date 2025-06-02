<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Lecturer Dashboard') }}
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
                    <h3 class="text-lg font-medium mb-4">Drafts</h3>
                    
                    @if($draftSets->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Set #</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($draftSets as $set)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->set_number }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ ucfirst($set->type) }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @if($set->type === 'quiz')
                                                    @if($set->quizDetail && $set->quizDetail->subject && $set->quizDetail->topic)
                                                        {{ $set->quizDetail->subject->name }} - 
                                                        {{ $set->quizDetail->topic->name }}
                                                    @else
                                                        No details available
                                                    @endif
                                                @else
                                                    @if($set->challengeDetail)
                                                        {{ $set->challengeDetail->name }}
                                                    @else
                                                        No details available
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->created_at ? $set->created_at->format('M d, Y') : 'Not available' }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('lecturer.sets.edit', $set) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                                    Edit
                                                </a>
                                                <form action="{{ route('lecturer.sets.submit', $set) }}" method="POST" class="inline">
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
                        <p class="text-gray-500">No draft sets available.</p>
                    @endif
                </div>
            </div>

            <!-- Pending Approval -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Pending Approval</h3>
                    
                    @if($pendingSets->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Set #</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Submitted</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingSets as $set)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->set_number }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ ucfirst($set->type) }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @if($set->type === 'quiz')
                                                    @if($set->quizDetail && $set->quizDetail->subject && $set->quizDetail->topic)
                                                        {{ $set->quizDetail->subject->name }} - 
                                                        {{ $set->quizDetail->topic->name }}
                                                    @else
                                                        No details available
                                                    @endif
                                                @else
                                                    @if($set->challengeDetail)
                                                        {{ $set->challengeDetail->name }}
                                                    @else
                                                        No details available
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->submitted_at ? $set->submitted_at->format('M d, Y') : 'Not submitted' }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('lecturer.sets.edit', $set) }}" class="text-blue-500 hover:text-blue-700">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No sets pending approval.</p>
                    @endif
                </div>
            </div>

            <!-- Ready to Publish -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Ready to Publish</h3>
                    
                    @php
                        $readyToPublishSets = $sets->filter(function ($set) {
                            return $set->isApprovedUnpublished();
                        });
                    @endphp
                    
                    @if($readyToPublishSets->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Set #</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Approved</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Reviewer</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($readyToPublishSets as $set)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->set_number }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ ucfirst($set->type) }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @if($set->type === 'quiz')
                                                    @if($set->quizDetail && $set->quizDetail->subject && $set->quizDetail->topic)
                                                        {{ $set->quizDetail->subject->name }} - 
                                                        {{ $set->quizDetail->topic->name }}
                                                    @else
                                                        No details available
                                                    @endif
                                                @else
                                                    @if($set->challengeDetail)
                                                        {{ $set->challengeDetail->name }}
                                                    @else
                                                        No details available
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->reviewed_at ? $set->reviewed_at->format('M d, Y') : 'Not reviewed' }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->reviewer ? $set->reviewer->name : 'Not reviewed' }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('lecturer.sets.edit', $set) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                                    View
                                                </a>
                                                <form action="{{ route('lecturer.sets.publish', $set) }}" method="POST" class="inline">
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
                        <p class="text-gray-500">No sets ready to publish.</p>
                    @endif
                </div>
            </div>

            <!-- Rejected -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Rejected Sets</h3>
                    
                    @if($rejectedSets->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Set #</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Reviewed</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Reviewer</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedSets as $set)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->set_number }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ ucfirst($set->type) }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @if($set->type === 'quiz')
                                                    @if($set->quizDetail && $set->quizDetail->subject && $set->quizDetail->topic)
                                                        {{ $set->quizDetail->subject->name }} - 
                                                        {{ $set->quizDetail->topic->name }}
                                                    @else
                                                        No details available
                                                    @endif
                                                @else
                                                    @if($set->challengeDetail)
                                                        {{ $set->challengeDetail->name }}
                                                    @else
                                                        No details available
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->reviewed_at ? $set->reviewed_at->format('M d, Y') : 'Not reviewed' }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->reviewer ? $set->reviewer->name : 'Not reviewed' }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('lecturer.sets.edit', $set) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                                    Edit
                                                </a>
                                                <form action="{{ route('lecturer.sets.submit', $set) }}" method="POST" class="inline">
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
                        <p class="text-gray-500">No rejected sets.</p>
                    @endif
                </div>
            </div>

            <!-- Approved -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Approved Sets</h3>
                    
                    @if($approvedSets->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Set #</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Approved</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Reviewer</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedSets as $set)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->set_number }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ ucfirst($set->type) }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @if($set->type === 'quiz')
                                                    @if($set->quizDetail && $set->quizDetail->subject && $set->quizDetail->topic)
                                                        {{ $set->quizDetail->subject->name }} - 
                                                        {{ $set->quizDetail->topic->name }}
                                                    @else
                                                        No details available
                                                    @endif
                                                @else
                                                    @if($set->challengeDetail)
                                                        {{ $set->challengeDetail->name }}
                                                    @else
                                                        No details available
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->reviewed_at ? $set->reviewed_at->format('M d, Y') : 'Not reviewed' }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $set->reviewer ? $set->reviewer->name : 'Not reviewed' }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('lecturer.sets.edit', $set) }}" class="text-blue-500 hover:text-blue-700">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No approved sets.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>