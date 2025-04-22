{{-- resources/views/tournaments/invitations.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Tournament Invitations') }}
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

                    <div class="mb-6">
                        <a href="{{ route('tournaments.index') }}" class="text-amber-400 hover:text-amber-300">
                            &larr; Back to Tournaments
                        </a>
                    </div>

                    <h3 class="text-xl font-bold mb-6 text-amber-400">Pending Team Invitations</h3>
                    
                    @if($pendingInvitations->isEmpty())
                        <div class="bg-gray-700/50 p-6 rounded-lg text-center border border-amber-800/20">
                            <p class="text-gray-400">You don't have any pending team invitations.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($pendingInvitations as $invitation)
                                <div class="border border-amber-800/20 rounded-lg overflow-hidden">
                                    <div class="p-4 bg-amber-900/10">
                                        <h4 class="font-bold text-lg text-white">{{ $invitation->team->tournament->title }}</h4>
                                        <p class="text-sm text-gray-400">
                                            Invitation from {{ $invitation->team->leader->name }} 
                                            (@{{ $invitation->team->leader->username }})
                                        </p>
                                    </div>
                                    
                                    <div class="p-4 bg-gray-700/20">
                                        <p class="mb-2"><span class="font-medium text-gray-300">Team Name:</span> 
                                            <span class="text-white">{{ $invitation->team->name }}</span>
                                        </p>
                                        <p class="mb-2"><span class="font-medium text-gray-300">Tournament Date:</span> 
                                            {{ \Carbon\Carbon::parse($invitation->team->tournament->date_time)->format('F j, Y, g:i a') }}
                                        </p>
                                        <p class="mb-2"><span class="font-medium text-gray-300">Location:</span> 
                                            {{ $invitation->team->tournament->location }}
                                        </p>
                                        <p class="mb-4"><span class="font-medium text-gray-300">Expires:</span> 
                                            {{ \Carbon\Carbon::parse($invitation->expires_at)->format('F j, Y, g:i a') }}
                                        </p>
                                        
                                        <div class="flex space-x-3 mt-6">
                                            <form method="POST" action="{{ route('tournaments.invitations.decline', $invitation) }}">
                                                @csrf
                                                <button type="submit" class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                                                    Decline
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('tournaments.invitations.accept', $invitation) }}">
                                                @csrf
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                    Accept
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="p-2 bg-gray-700/30 text-xs text-gray-400">
                                        Invited {{ $invitation->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>