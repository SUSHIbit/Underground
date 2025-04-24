<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Create Tournament Team') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    <a href="{{ route('tournaments.show', $tournament) }}" class="text-amber-400 hover:text-amber-300">
                        ← Back to Tournament
                    </a>
                    
                    <h3 class="text-xl font-bold mt-4 mb-6">Create Team for {{ $tournament->title }}</h3>
                    
                    <div class="bg-gray-700/50 p-4 rounded-lg mb-6">
                        <p class="mb-2">You are creating a team for this tournament. You will be the team leader.</p>
                        <p class="mb-2">Team size: <span class="text-amber-400 font-medium">{{ $tournament->team_size }} members</span> (including you)</p>
                        <p class="mb-2">You need to select <span class="text-amber-400 font-medium">{{ $tournament->team_size - 1 }}</span> team members.</p>
                    </div>
                    
                    @if(session('error'))
                        <div class="bg-red-900/20 border-l-4 border-red-500 text-red-400 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Search form to filter eligible users -->
                    <div class="mb-4">
                        <form action="{{ route('tournaments.create-team-form', $tournament) }}" method="GET" class="flex">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search users by username or name" 
                                class="flex-1 p-2 border border-gray-600 rounded-l-md bg-gray-700 text-white"
                                value="{{ $searchQuery }}"
                            >
                            <button 
                                type="submit"
                                class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded-r-md"
                            >
                                Search
                            </button>
                        </form>
                    </div>
                    
                    <!-- Display eligible users -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-300 mb-2">Select Team Members ({{ $tournament->team_size - 1 }} required)</h4>
                        
                        @if($eligibleUsers->count() > 0)
                            <div class="bg-gray-700 rounded-md border border-amber-800/20 mb-4">
                                @foreach($eligibleUsers as $eligibleUser)
                                    <div class="flex items-center justify-between p-3 border-b border-amber-800/10 last:border-b-0">
                                        <div class="flex items-center">
                                            <div class="mr-3 bg-amber-900/50 w-8 h-8 rounded-full flex items-center justify-center text-amber-400 font-bold">
                                                {{ substr($eligibleUser->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-white">{{ $eligibleUser->username }}</p>
                                                <p class="text-sm text-gray-400">{{ $eligibleUser->name }} • {{ $eligibleUser->getRank() }}</p>
                                            </div>
                                        </div>
                                        
                                        <form action="{{ route('tournaments.create-team-form', $tournament) }}" method="GET">
                                            <input type="hidden" name="add_user_id" value="{{ $eligibleUser->id }}">
                                            <!-- Preserve search query if it exists -->
                                            @if($searchQuery)
                                                <input type="hidden" name="search" value="{{ $searchQuery }}">
                                            @endif
                                            <button 
                                                type="submit" 
                                                class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold py-1 px-3 rounded"
                                                {{ count($selectedUserIds) >= ($tournament->team_size - 1) ? 'disabled' : '' }}
                                            >
                                                Add
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-700 rounded-md border border-amber-800/20 p-4 text-center">
                                <p class="text-gray-400">
                                    @if(empty($searchQuery))
                                        No eligible users found. Try inviting different users.
                                    @else
                                        No users match your search "{{ $searchQuery }}". Try a different search term.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Display selected users -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-sm font-medium text-gray-300">Selected Team Members</h4>
                            
                            @if(count($selectedUserIds) > 0)
                                <form action="{{ route('tournaments.create-team-form', $tournament) }}" method="GET">
                                    <input type="hidden" name="clear_selection" value="1">
                                    <button 
                                        type="submit" 
                                        class="text-red-400 hover:text-red-300 text-sm underline"
                                    >
                                        Clear All
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        @if(count($selectedUsers) > 0)
                            <div class="bg-gray-700 rounded-md border border-amber-800/20">
                                @foreach($selectedUsers as $selectedUser)
                                    <div class="flex items-center justify-between p-3 border-b border-amber-800/10 last:border-b-0">
                                        <div class="flex items-center">
                                            <div class="mr-3 bg-amber-900/50 w-8 h-8 rounded-full flex items-center justify-center text-amber-400 font-bold">
                                                {{ substr($selectedUser->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-white">{{ $selectedUser->username }}</p>
                                                <p class="text-sm text-gray-400">{{ $selectedUser->name }} • {{ $selectedUser->getRank() }}</p>
                                            </div>
                                        </div>
                                        
                                        <form action="{{ route('tournaments.create-team-form', $tournament) }}" method="GET">
                                            <input type="hidden" name="remove_user_id" value="{{ $selectedUser->id }}">
                                            <!-- Preserve search query if it exists -->
                                            @if($searchQuery)
                                                <input type="hidden" name="search" value="{{ $searchQuery }}">
                                            @endif
                                            <button 
                                                type="submit" 
                                                class="text-red-400 hover:text-red-300"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-2 text-amber-400 text-sm">
                                {{ count($selectedUsers) }} of {{ $tournament->team_size - 1 }} users selected
                            </div>
                        @else
                            <div class="bg-gray-700 rounded-md border border-amber-800/20 p-4 text-center">
                                <p class="text-gray-400">No users selected yet.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Team Name and Submit Form -->
                    <form action="{{ route('tournaments.teams.create', $tournament) }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label for="team_name" class="block mb-2 text-sm font-medium text-gray-300">
                                Team Name
                            </label>
                            <input 
                                type="text" 
                                name="team_name" 
                                id="team_name" 
                                class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                required
                            >
                        </div>
                        
                        <button 
                            type="submit" 
                            class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ count($selectedUserIds) !== ($tournament->team_size - 1) ? 'disabled' : '' }}
                        >
                            Create Team
                        </button>
                        
                        @if(count($selectedUserIds) !== ($tournament->team_size - 1))
                            <p class="mt-2 text-sm text-amber-400">
                                Please select exactly {{ $tournament->team_size - 1 }} team members to create a team.
                            </p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>