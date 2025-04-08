@props(['title' => 'Assessment in Progress'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'UniKL Underground') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-gray-900 text-white">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-gray-800 shadow-md border-b border-amber-800/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="text-amber-500 font-bold text-2xl">UG</div>
                
                <!-- Page Title -->
                <h2 class="font-semibold text-xl text-amber-400 leading-tight">
                    {{ $title }}
                </h2>
                
                <!-- Warning Text -->
                <div class="text-red-400 text-sm font-medium">
                    Do not leave this page until you finish
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-900">
            {{ $slot }}
        </main>
    </div>
    
    <!-- Include any scripts specific to attempts -->
    @stack('scripts')
</body>
</html>