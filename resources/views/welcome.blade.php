<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>UniKL Underground</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-900 text-white">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <header class="border-b border-amber-800/20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <!-- Logo -->
                                <div class="text-amber-500 font-bold text-2xl">UG</div>
                            </div>
                        </div>
                        <!-- Auth Links -->
                        <div class="flex items-center space-x-4">
                            @if (Route::has('login'))
                                <div>
                                    @auth
                                        <a href="{{ url('/dashboard') }}" class="text-amber-500 hover:text-amber-400 font-medium transition-colors">
                                            Dashboard
                                        </a>
                                    @else
                                        <div class="flex space-x-4">
                                            <a href="{{ route('login') }}" class="text-amber-500 hover:text-amber-400 font-medium transition-colors">
                                                Log in
                                            </a>
                                            @if (Route::has('register'))
                                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-amber-600 text-white hover:bg-amber-700 h-9 px-4 py-2">
                                                    Register
                                                </a>
                                            @endif
                                        </div>
                                    @endauth
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex flex-col items-center justify-center">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <!-- Crown Icon -->
                    <div class="mb-8">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-24 h-24 mx-auto text-amber-500">
                            <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" />
                            <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z" />
                            <path d="M4.462 19.462c.42-.419.753-.89 1-1.394.453.213.902.434 1.347.661a6.743 6.743 0 01-1.286 1.794.75.75 0 11-1.06-1.06z" />
                        </svg>
                    </div>

                    <!-- Heading -->
                    <h1 class="text-4xl font-extrabold tracking-tight lg:text-5xl mb-6 bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-amber-600">
                        Welcome to UniKL Underground
                    </h1>

                    <!-- Description -->
                    <p class="text-lg text-gray-300 max-w-2xl mx-auto mb-8">
                        Test your knowledge, take challenges, and compete with other students to earn ranks and climb the leaderboards.
                    </p>

                    <!-- CTA Button -->
                    <div>
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-amber-600 px-8 text-sm font-medium text-white shadow transition-colors hover:bg-amber-700 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-amber-500">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-amber-600 px-8 text-sm font-medium text-white shadow transition-colors hover:bg-amber-700 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-amber-500">
                                Get Started
                            </a>
                        @endauth
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="py-6 border-t border-amber-800/20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-400">
                            &copy; {{ date('Y') }} UniKL Underground. All rights reserved.
                        </div>
                        <div class="text-sm text-gray-400">
                            Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>