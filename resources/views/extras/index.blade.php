<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Extras') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- User Stats and Info Section -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-amber-800/20">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-amber-400">
                        {{ __('User Information') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-400">
                        {{ __('View and manage your account information.') }}
                    </p>
                    
                    <div class="mt-6 bg-gray-900/50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="border border-amber-800/20 rounded-lg p-4 bg-gray-800">
                                <h4 class="font-medium text-gray-300 mb-2">Current Rank</h4>
                                <p class="text-2xl font-bold 
                                    {{ $user->getRank() === 'Unranked' ? 'text-gray-400' : '' }}
                                    {{ $user->getRank() === 'Bronze' ? 'text-amber-600' : '' }}
                                    {{ $user->getRank() === 'Silver' ? 'text-gray-400' : '' }}
                                    {{ $user->getRank() === 'Gold' ? 'text-amber-500' : '' }}
                                    {{ $user->getRank() === 'Master' ? 'text-purple-400' : '' }}
                                    {{ $user->getRank() === 'Grand Master' ? 'text-red-400' : '' }}
                                    {{ $user->getRank() === 'One Above All' ? 'text-indigo-400' : '' }}">
                                    {{ $user->getRank() }}
                                </p>
                            </div>
                            
                            <div class="border border-amber-800/20 rounded-lg p-4 bg-gray-800">
                                <h4 class="font-medium text-gray-300 mb-2">Total Points</h4>
                                <p class="text-2xl font-bold text-amber-500">{{ $user->points }}</p>
                            </div>
                            
                            <div class="border border-amber-800/20 rounded-lg p-4 bg-gray-800">
                                <h4 class="font-medium text-gray-300 mb-2">UEPoints</h4>
                                <p class="text-2xl font-bold text-amber-500">{{ $user->ue_points }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Options Panel -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-amber-800/20">
                <h2 class="text-lg font-medium text-amber-400">
                    {{ __('Options and Features') }}
                </h2>

                <p class="mt-1 text-sm text-gray-400">
                    {{ __('Access various features and system options.') }}
                </p>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- View Ranks Card -->
                    <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-amber-800/20 hover:shadow-md transition-shadow duration-300">
                        <div class="p-5">
                            <h3 class="font-semibold text-lg text-amber-400 mb-2">View Ranks</h3>
                            <p class="text-gray-400 text-sm mb-4">Explore the ranking system and track your progress</p>
                            <a href="{{ route('ranks.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                View Ranks
                            </a>
                        </div>
                    </div>

                    <!-- Tournament Skills Card -->
                    <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-amber-800/20 hover:shadow-md transition-shadow duration-300">
                        <div class="p-5">
                            <h3 class="font-semibold text-lg text-amber-400 mb-2">Tournament Skills</h3>
                            <p class="text-gray-400 text-sm mb-4">View your performance across different tournament types</p>
                            <a href="{{ route('skills.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                View Skills
                            </a>
                        </div>
                    </div>

                    <!-- Leaderboard Card -->
                    <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-amber-800/20 hover:shadow-md transition-shadow duration-300">
                        <div class="p-5">
                            <h3 class="font-semibold text-lg text-amber-400 mb-2">Leaderboard</h3>
                            <p class="text-gray-400 text-sm mb-4">Check the top performers and your position</p>
                            <a href="{{ route('leaderboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                </svg>
                                View Leaderboard
                            </a>
                        </div>
                    </div>

                    <!-- UEPoints Card -->
                    <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-amber-800/20 hover:shadow-md transition-shadow duration-300">
                        <div class="p-5">
                            <h3 class="font-semibold text-lg text-amber-400 mb-2">UEPoints</h3>
                            <p class="text-gray-400 text-sm mb-4">Manage and learn about your UEPoints</p>
                            <a href="{{ route('uepoints.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                                Manage UEPoints
                            </a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>