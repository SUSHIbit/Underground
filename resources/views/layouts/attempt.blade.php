<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Quiz Attempt' }} - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body {
            background-color: rgb(17, 24, 39); /* bg-gray-900 */
            color: rgb(229, 231, 235); /* text-gray-200 */
        }
        
        /* Default dark scrollbar for modern browsers */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgb(31, 41, 55); /* bg-gray-800 */
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgb(75, 85, 99); /* bg-gray-600 */
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgb(107, 114, 128); /* bg-gray-500 */
        }
    </style>
</head>
<body class="font-sans antialiased theme-dark">
    <div class="min-h-screen bg-gray-900">
        <nav class="bg-gray-800 border-b border-amber-800/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-amber-400 hover:text-amber-300 font-semibold text-xl">
                            {{ config('app.name', 'UE Scholar') }}
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- Timer Status (if any) -->
                        @if(isset($timer_minutes) && $timer_minutes > 0 && isset($remaining_seconds))
                        <div class="hidden sm:flex flex-col items-center justify-center">
                            <span class="text-sm text-gray-400">Time Remaining</span>
                            <div id="timer-nav" class="text-md font-bold text-amber-400">
                                <span id="minutes-nav">--</span>:<span id="seconds-nav">--</span>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Current User Info -->
                        <div class="flex items-center">
                            <div class="text-right">
                                <div class="text-sm text-gray-400">
                                    {{ Auth::user()->name }}
                                </div>
                                @if(isset($attempt) && $attempt->is_retake)
                                <div class="text-xs text-blue-400">
                                    Learning Mode
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    
    <!-- Scripts Section -->
    @stack('scripts')
</body>
</html>