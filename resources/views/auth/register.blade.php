<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __('Register') }} - {{ config('app.name', 'UniKL Underground') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-100 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-800 border border-amber-900/20 shadow-md overflow-hidden rounded-lg">
                <div class="flex justify-center mb-6">
                    <div class="text-amber-500 font-bold text-4xl">UG</div>
                </div>
                
                <h2 class="text-xl font-bold text-center text-amber-500 mb-6">{{ __('Create an account') }}</h2>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300">{{ __('Name') }}</label>
                        <input id="name" class="block w-full mt-1 rounded-md border-amber-800/30 bg-gray-700 text-white focus:border-amber-500 focus:ring focus:ring-amber-600 focus:ring-opacity-50" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                        @error('name')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="mt-4">
                        <label for="username" class="block text-sm font-medium text-gray-300">{{ __('Username') }}</label>
                        <input id="username" class="block w-full mt-1 rounded-md border-amber-800/30 bg-gray-700 text-white focus:border-amber-500 focus:ring focus:ring-amber-600 focus:ring-opacity-50" type="text" name="username" value="{{ old('username') }}" required autocomplete="username" />
                        @error('username')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="mt-4">
                        <label for="email" class="block text-sm font-medium text-gray-300">{{ __('Email') }}</label>
                        <input id="email" class="block w-full mt-1 rounded-md border-amber-800/30 bg-gray-700 text-white focus:border-amber-500 focus:ring focus:ring-amber-600 focus:ring-opacity-50" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                        @error('email')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <label for="password" class="block text-sm font-medium text-gray-300">{{ __('Password') }}</label>
                        <input id="password" class="block w-full mt-1 rounded-md border-amber-800/30 bg-gray-700 text-white focus:border-amber-500 focus:ring focus:ring-amber-600 focus:ring-opacity-50" type="password" name="password" required autocomplete="new-password" />
                        @error('password')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300">{{ __('Confirm Password') }}</label>
                        <input id="password_confirmation" class="block w-full mt-1 rounded-md border-amber-800/30 bg-gray-700 text-white focus:border-amber-500 focus:ring focus:ring-amber-600 focus:ring-opacity-50" type="password" name="password_confirmation" required autocomplete="new-password" />
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <a class="text-sm text-amber-500 hover:text-amber-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" href="{{ route('login') }}">
                            {{ __('Already registered?') }}
                        </a>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Register') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 flex justify-center">
                <a href="/" class="text-sm text-gray-400 hover:text-gray-300">
                    &larr; {{ __('Back to home') }}
                </a>
            </div>
        </div>
    </body>
</html>