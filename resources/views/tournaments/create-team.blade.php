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
                        <p class="mb-2">You need to invite <span class="text-amber-400 font-medium">{{ $tournament->team_size - 1 }}</span> more team members.</p>
                    </div>
                    
                    @if(session('error'))
                        <div class="bg-red-900/20 border-l-4 border-red-500 text-red-400 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    <form id="teamForm" action="{{ route('tournaments.teams.create', $tournament) }}" method="POST">
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
                        
                        <div class="mb-6">
                            <label for="user_search" class="block mb-2 text-sm font-medium text-gray-300">
                                Team Members ({{ $tournament->team_size - 1 }} needed)
                            </label>
                            
                            <div class="relative mb-4">
                                <input 
                                    type="text" 
                                    id="user_search" 
                                    class="w-full p-2 border border-gray-600 rounded-md bg-gray-700 text-white"
                                    placeholder="Search for users by username (type at least 3 characters)..."
                                >
                                <div id="search_results" class="absolute z-10 mt-1 w-full bg-gray-800 border border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto hidden">
                                    <!-- Search results will appear here -->
                                </div>
                            </div>
                            
                            <div id="selected_users" class="space-y-2 mb-4">
                                <p class="text-gray-400 italic">No users selected yet.</p>
                            </div>
                            
                            <div id="user_ids_container">
                                <!-- Hidden inputs will be placed here -->
                            </div>
                        </div>
                        
                        <button 
                            type="submit" 
                            id="submit_button"
                            class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled
                        >
                            Create Team & Send Invitations
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('user_search');
            const searchResults = document.getElementById('search_results');
            const selectedUsersContainer = document.getElementById('selected_users');
            const userIdsContainer = document.getElementById('user_ids_container');
            const submitButton = document.getElementById('submit_button');
            const teamSizeNeeded = {{ $tournament->team_size - 1 }};
            let selectedUsers = [];
            
            // Function to update the submit button state
            function updateSubmitButton() {
                submitButton.disabled = selectedUsers.length !== teamSizeNeeded;
            }
            
            // Function to update hidden inputs for selected users
            function updateHiddenInputs() {
                userIdsContainer.innerHTML = '';
                selectedUsers.forEach(userId => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'invited_user_ids[]';
                    input.value = userId;
                    userIdsContainer.appendChild(input);
                });
            }
            
            // Function to select a user
            function selectUser(userId, username, name, role) {
                // Check if already selected
                if (selectedUsers.includes(userId)) {
                    return;
                }
                
                selectedUsers.push(userId);
                
                // Clear placeholder if this is the first user
                if (selectedUsers.length === 1) {
                    selectedUsersContainer.innerHTML = '';
                }
                
                // Create user element
                const userElement = document.createElement('div');
                userElement.className = 'flex items-center justify-between p-2 bg-gray-700 rounded-md';
                userElement.innerHTML = `
                    <div class="flex items-center">
                        <div class="mr-3 bg-amber-700 w-8 h-8 rounded-full flex items-center justify-center">
                            ${name.charAt(0)}
                        </div>
                        <div>
                            <p class="text-white font-medium">${username}</p>
                            <p class="text-sm text-gray-400">${name} • ${role}</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        class="p-1 text-red-400 hover:text-red-300"
                        data-user-id="${userId}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                `;
                
                // Add remove button handler
                userElement.querySelector('button').addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    selectedUsers = selectedUsers.filter(id => id != userId);
                    userElement.remove();
                    
                    // Show placeholder if no users left
                    if (selectedUsers.length === 0) {
                        selectedUsersContainer.innerHTML = '<p class="text-gray-400 italic">No users selected yet.</p>';
                    }
                    
                    updateHiddenInputs();
                    updateSubmitButton();
                });
                
                selectedUsersContainer.appendChild(userElement);
                updateHiddenInputs();
                updateSubmitButton();
                
                // Clear search and hide results
                searchInput.value = '';
                searchResults.classList.add('hidden');
            }
            
            // Handle user search input
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                
                const query = this.value.trim();
                
                // Only search if at least 3 characters
                if (query.length < 3) {
                    searchResults.classList.add('hidden');
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    // Make AJAX request to search users
                    fetch(`/tournaments/search-users?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            searchResults.innerHTML = '';
                            
                            if (!data.users || data.users.length === 0) {
                                searchResults.innerHTML = '<div class="p-3 text-gray-400">No users found</div>';
                            } else {
                                data.users.forEach(user => {
                                    // Skip if already selected
                                    if (selectedUsers.includes(user.id.toString())) {
                                        return;
                                    }
                                    
                                    const userItem = document.createElement('div');
                                    userItem.className = 'p-3 hover:bg-gray-700 cursor-pointer';
                                    userItem.innerHTML = `
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="font-medium text-white">${user.username}</p>
                                                <p class="text-sm text-gray-400">${user.name} • ${user.rank}</p>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                        </div>
                                    `;
                                    
                                    userItem.addEventListener('click', function() {
                                        selectUser(user.id, user.username, user.name, user.rank);
                                    });
                                    
                                    searchResults.appendChild(userItem);
                                });
                            }
                            
                            searchResults.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Error searching for users:', error);
                            searchResults.innerHTML = '<div class="p-3 text-red-400">Error searching for users</div>';
                            searchResults.classList.remove('hidden');
                        });
                }, 300);
            });
            
            // Hide search results when clicking outside
            document.addEventListener('click', function(event) {
                if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                    searchResults.classList.add('hidden');
                }
            });
            
            // Add form submission validation
            document.getElementById('teamForm').addEventListener('submit', function(event) {
                if (selectedUsers.length !== teamSizeNeeded) {
                    event.preventDefault();
                    alert(`Please select exactly ${teamSizeNeeded} team members.`);
                }
            });
        });
    </script>
</x-app-layout>