<!-- resources/views/legions/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Legions') }}
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

            <!-- User's Current Legion Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Your Legion Status</h3>
                    
                    @if($userLegion)
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 mr-4">
                                    @if($userLegion->emblem)
                                        <img src="{{ asset('storage/' . $userLegion->emblem) }}" alt="{{ $userLegion->name }} Emblem" class="h-16 w-16 object-cover rounded-lg">
                                    @else
                                        <div class="h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-500 text-xl font-bold">{{ strtoupper(substr($userLegion->name, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold text-blue-800">{{ $userLegion->name }}</h4>
                                    <p class="text-sm text-gray-600">
                                        Member since {{ $user->legionMembership->joined_at ? $user->legionMembership->joined_at->format('M d, Y') : 'N/A' }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Role: {{ ucfirst($user->legionMembership->role) }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="bg-white p-3 rounded-lg shadow-sm">
                                    <p class="text-sm text-gray-500">Legion Power</p>
                                    <p class="text-xl font-bold text-blue-600">{{ number_format($userLegion->power) }}</p>
                                </div>
                                <div class="bg-white p-3 rounded-lg shadow-sm">
                                    <p class="text-sm text-gray-500">Total Points</p>
                                    <p class="text-xl font-bold text-green-600">{{ number_format($userLegion->total_points) }}</p>
                                </div>
                                <div class="bg-white p-3 rounded-lg shadow-sm">
                                    <p class="text-sm text-gray-500">Members</p>
                                    <p class="text-xl font-bold">{{ $userLegion->member_count }}/10</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-between">
                                <a href="{{ route('legions.show', $userLegion) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150">
                                    View Legion
                                </a>
                                
                                <form action="{{ route('legions.leave', $userLegion) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Are you sure you want to leave this legion?')" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                                        Leave Legion
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseif($user->hasPendingLegionInvitation())
                        @php
                            $invitation = $user->legionMembership;
                            $invitingLegion = $invitation->legion;
                        @endphp
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h4 class="text-lg font-medium mb-2">Pending Invitation</h4>
                            <p class="mb-4">You have been invited to join <strong>{{ $invitingLegion->name }}</strong>.</p>
                            
                            <div class="flex space-x-4">
                                <form action="{{ route('legions.accept-invitation', $invitingLegion) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                                        Accept Invitation
                                    </button>
                                </form>
                                
                                <form action="{{ route('legions.reject-invitation', $invitingLegion) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                                        Reject Invitation
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <p class="mb-4">You are not currently a member of any legion.</p>
                            <div class="flex space-x-4">
                                <a href="{{ route('legions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150">
                                    Create a Legion
                                </a>
                                <a href="{{ route('legions.leaderboard') }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                                    View Leaderboard
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Top Legions Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Top Legions</h3>
                        <a href="{{ route('legions.leaderboard') }}" class="text-blue-500 hover:text-blue-700">
                            View Full Leaderboard
                        </a>
                    </div>
                    
                    @if($topLegions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Rank</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Emblem</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Legion</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Leader</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Members</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Power</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topLegions as $index => $legion)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $index + 1 }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @if($legion->emblem)
                                                    <img src="{{ asset('storage/' . $legion->emblem) }}" alt="{{ $legion->name }} Emblem" class="h-8 w-8 object-cover rounded-full">
                                                @else
                                                    <div class="h-8 w-8 bg-gray-200 rounded-full flex items-center justify-center">
                                                        <span class="text-gray-500 text-xs font-bold">{{ strtoupper(substr($legion->name, 0, 2)) }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200 font-medium">{{ $legion->name }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $legion->leader->name }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $legion->member_count }}/10</td>
                                            <td class="py-2 px-4 border-b border-gray-200 font-bold text-blue-600">{{ number_format($legion->power) }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <a href="{{ route('legions.show', $legion) }}" class="text-blue-500 hover:text-blue-700">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No legions available yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>