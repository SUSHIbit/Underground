<!-- resources/views/legions/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $legion->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success and Error Messages -->
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
        
            <!-- Legion Info Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row md:items-start">
                        <div class="flex-shrink-0 mr-6 mb-4 md:mb-0">
                            @if($legion->emblem)
                                <img src="{{ asset('storage/' . $legion->emblem) }}" alt="{{ $legion->name }} Emblem" class="h-24 w-24 object-cover rounded-lg">
                            @else
                                <div class="h-24 w-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-500 text-2xl font-bold">{{ strtoupper(substr($legion->name, 0, 2)) }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-blue-800 mb-2">{{ $legion->name }}</h3>
                            <p class="text-gray-600 mb-2">
                                <span class="font-medium">Leader:</span> {{ $legion->leader->name }}
                            </p>
                            <p class="text-gray-600 mb-4">
                                <span class="font-medium">Members:</span> {{ $legion->member_count }}/10
                            </p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <p class="text-sm text-gray-500">Legion Power</p>
                                    <p class="text-xl font-bold text-blue-600">{{ number_format($legion->power) }}</p>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <p class="text-sm text-gray-500">Total Points</p>
                                    <p class="text-xl font-bold text-green-600">{{ number_format($legion->total_points) }}</p>
                                </div>
                            </div>
                            
                            @if($legion->description)
                                <div class="mt-4">
                                    <h4 class="font-medium text-gray-700 mb-1">Description</h4>
                                    <p class="text-gray-600">{{ $legion->description }}</p>
                                </div>
                            @endif
                            
                            <!-- Action Buttons -->
                            <div class="mt-6 flex flex-wrap gap-2">
                                <a href="{{ route('legions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray transition ease-in-out duration-150">
                                    Back to Legions
                                </a>
                                
                                @if($isMember && $membership && $membership->hasManagementPrivileges())
                                    <a href="{{ route('legions.edit', $legion) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150">
                                        Edit Legion
                                    </a>
                                @endif
                                
                                @if(!$isMember && !$isPending && !$user->isInLegion() && !$legion->isLegionFull())
                                    <form action="{{ route('legions.apply', $legion) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                                            Apply to Join
                                        </button>
                                    </form>
                                @endif
                                
                                @if($isPending)
                                    <span class="inline-flex items-center px-4 py-2 bg-yellow-100 border border-transparent rounded-md font-semibold text-xs text-yellow-800 uppercase tracking-widest">
                                        Application Pending
                                    </span>
                                @endif
                                
                                @if($isMember && !$membership->isLeader())
                                    <form action="{{ route('legions.leave', $legion) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Are you sure you want to leave this legion?')" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                                            Leave Legion
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Management Section (only for officers and leaders) -->
            @if($isMember && $membership && $membership->hasManagementPrivileges())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Legion Management</h3>
                        
                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <h4 class="font-medium mb-3">Invite New Member</h4>
                            
                            @if(!$legion->isLegionFull())
                                <form action="{{ route('legions.invite', $legion) }}" method="POST" class="flex flex-col md:flex-row md:items-end gap-2">
                                    @csrf
                                    <div class="flex-1">
                                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                        <input type="text" name="username" id="username" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full" placeholder="Enter username to invite" required>
                                    </div>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150">
                                        Send Invitation
                                    </button>
                                </form>
                            @else
                                <p class="text-blue-700">Legion is full. Cannot invite more members.</p>
                            @endif
                        </div>
                        
                        <!-- Applications Section -->
                        @php
                            $pendingApplications = $legion->pendingMembers()->with('user')->get();
                        @endphp
                        
                        @if($pendingApplications->count() > 0)
                            <div class="bg-yellow-50 p-4 rounded-lg mb-4">
                                <h4 class="font-medium mb-3">Pending Applications ({{ $pendingApplications->count() }})</h4>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr>
                                                <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Username</th>
                                                <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Applied On</th>
                                                <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Points</th>
                                                <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Rank</th>
                                                <th class="py-2 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pendingApplications as $application)
                                                <tr>
                                                    <td class="py-2 px-4">{{ $application->user->username }}</td>
                                                    <td class="py-2 px-4">{{ $application->created_at->format('M d, Y') }}</td>
                                                    <td class="py-2 px-4">{{ number_format($application->user->points) }}</td>
                                                    <td class="py-2 px-4">{{ $application->user->getRank() }}</td>
                                                    <td class="py-2 px-4">
                                                        <div class="flex space-x-2">
                                                            <form action="{{ route('legions.accept-application', ['legion' => $legion, 'user' => $application->user]) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                                                                    Accept
                                                                </button>
                                                            </form>
                                                            
                                                            <form action="{{ route('legions.reject-application', ['legion' => $legion, 'user' => $application->user]) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                                                                    Reject
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Members Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Legion Members ({{ $legion->member_count }}/10)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Member</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Points</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Rank</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Joined</th>
                                    @if($isMember && $membership && $membership->hasManagementPrivileges())
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($legion->acceptedMembers()->with('user')->get()->sortByDesc(function($member) {
                                    // Sort by role priority (leader first, then officers, then members)
                                    if ($member->role == 'leader') return 3;
                                    if ($member->role == 'officer') return 2;
                                    return 1;
                                }) as $member)
                                    <tr class="{{ $member->user_id === auth()->id() ? 'bg-blue-50' : '' }}">
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 mr-3">
                                                    <img class="h-8 w-8 rounded-full object-cover" 
                                                        src="{{ $member->user->profile_picture ? asset('storage/' . $member->user->profile_picture) : asset('images/default-avatar.png') }}" 
                                                        alt="{{ $member->user->name }}">
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $member->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $member->user->username }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $member->role == 'leader' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $member->role == 'officer' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $member->role == 'member' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ ucfirst($member->role) }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200 font-medium">{{ number_format($member->user->points) }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $member->user->getRank() }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $member->joined_at ? $member->joined_at->format('M d, Y') : 'N/A' }}</td>
                                        
                                        @if($isMember && $membership && $membership->hasManagementPrivileges())
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <div class="flex items-center space-x-2">
                                                    @if($membership->isLeader() && $member->role == 'member')
                                                        <form action="{{ route('legions.promote', ['legion' => $legion, 'user' => $member->user]) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 py-1 px-2 rounded">
                                                                Promote to Officer
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($membership->isLeader() && $member->role == 'officer')
                                                        <form action="{{ route('legions.demote', ['legion' => $legion, 'user' => $member->user]) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 py-1 px-2 rounded">
                                                                Demote to Member
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($membership->isLeader() && $member->user_id !== auth()->id() && ($member->role == 'officer' || $member->role == 'member'))
                                                        <form action="{{ route('legions.transfer-leadership', ['legion' => $legion, 'user' => $member->user]) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" onclick="return confirm('Are you sure you want to transfer leadership? You will become an officer.')" class="text-xs bg-purple-100 hover:bg-purple-200 text-purple-800 py-1 px-2 rounded">
                                                                Make Leader
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if(($membership->isLeader() || ($membership->isOfficer() && $member->role == 'member')) && $member->user_id !== auth()->id() && $member->role !== 'leader')
                                                        <form action="{{ route('legions.remove-member', ['legion' => $legion, 'user' => $member->user]) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" onclick="return confirm('Are you sure you want to remove this member?')" class="text-xs bg-red-100 hover:bg-red-200 text-red-800 py-1 px-2 rounded">
                                                                Remove
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
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