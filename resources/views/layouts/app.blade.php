<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="theme-{{ auth()->check() ? auth()->user()->theme_preference : 'dark' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UniKL Underground') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-background text-foreground">
    <div class="flex min-h-screen" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <div class="relative flex-shrink-0" :class="sidebarOpen ? 'w-64' : 'w-20'" 
            x-transition:enter="transition-all ease-in-out duration-300"
            x-transition:enter-start="opacity-25 -translate-x-full" 
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition-all ease-in-out duration-300" 
            x-transition:leave-start="opacity-100 translate-x-0" 
            x-transition:leave-end="opacity-0 -translate-x-full">
            
            <div class="fixed inset-y-0 flex flex-col h-full bg-gray-800 border-r border-amber-800/20 text-white"
                :class="sidebarOpen ? 'w-64' : 'w-20'">
                
                <!-- Logo (Toggle Sidebar) -->
                <div class="flex items-center p-4 cursor-pointer" @click="sidebarOpen = !sidebarOpen">
                    <div class="text-amber-500 font-bold text-2xl">UG</div>
                    <span x-show="sidebarOpen" class="ml-2 text-amber-500 font-bold">Underground</span>
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 px-2 py-4 space-y-1">
                    @if(auth()->user()->role === 'student')
                        <!-- Student Navigation -->
                        <a href="{{ route('dashboard') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                        </a>

                        <a href="{{ route('quizzes.index') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('quizzes.*') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Quizzes</span>
                        </a>

                        <a href="{{ route('challenges.index') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('challenges.*') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Challenges</span>
                        </a>

                        <a href="{{ route('tournaments.index') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('tournaments.*') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Tournaments</span>
                        </a>
                    @elseif(auth()->user()->role === 'lecturer')
                        <!-- Lecturer Navigation -->
                        <a href="{{ route('lecturer.dashboard') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('lecturer.dashboard') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                        </a>
                        
                        <!-- Add Tournament Navigation for Lecturers -->
                        <a href="{{ route('lecturer.tournaments') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('lecturer.tournaments*') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Tournaments</span>
                        </a>
                    @elseif(auth()->user()->role === 'accessor')
                        <!-- Accessor Navigation -->
                        <a href="{{ route('accessor.dashboard') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('accessor.dashboard') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                        </a>
                        
                        <!-- Add Tournament Navigation for Accessors -->
                        <a href="{{ route('accessor.tournaments') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('accessor.tournaments*') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Tournaments</span>
                        </a>
                    @elseif(auth()->user()->role === 'admin')
                        <!-- Admin Navigation -->
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                        </a>
                    @elseif(auth()->user()->role === 'judge')
                        <!-- Judge Navigation -->
                        <a href="{{ route('judge.dashboard') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('judge.dashboard') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                        </a>
                    @endif

                    <!-- Replace Settings with Extras here -->
                    <a href="{{ route('extras') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('extras') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ml-3">Extras</span>
                    </a>
                </nav>

                <!-- Bottom Section with User Profile and Logout -->
                <div class="mt-auto">
                    <!-- Add Settings here before the User Profile -->
                    <div class="border-t border-amber-800/20 pt-2">
                        <a href="{{ route('settings') }}" class="flex items-center justify-center md:justify-start p-3 rounded-md {{ request()->routeIs('settings') ? 'bg-gray-700 text-amber-500' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Settings</span>
                        </a>
                    </div>

                    <!-- User Profile -->
                    <div class="p-4 border-t border-amber-800/20">
                        <div class="flex items-center">
                            <div class="h-12 w-12 rounded-full bg-amber-600 flex items-center justify-center text-white">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div x-show="sidebarOpen" class="ml-3">
                                <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate max-w-[140px]">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}" class="p-4 border-t border-amber-800/20">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center md:justify-start p-2 rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-gray-800 shadow-md border-b border-amber-800/20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-center items-center h-16">
                    <!-- Page Title -->
                    <h2 class="font-semibold text-xl text-amber-400 leading-tight">
                        @isset($header)
                            {{ $header }}
                        @else
                            {{ __('Dashboard') }}
                        @endisset
                    </h2>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-900">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="py-4 border-t border-amber-800/20 bg-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-center items-center">
                        <div class="text-sm text-gray-400">
                            &copy; {{ date('Y') }} UniKL Underground
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>