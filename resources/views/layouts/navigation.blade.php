<!-- resources/views/layouts/navigation.blade.php -->
<nav x-data="{ open: false }" class="bg-gray-800 border-b border-amber-800/20">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <div class="text-amber-500 font-bold text-2xl">UG</div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @if(Auth::user()->role === 'student')
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Dashboard') }}
                        </a>
                        
                        <a href="{{ route('quizzes.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('quizzes.*') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Quizzes') }}
                        </a>
                        
                        <a href="{{ route('challenges.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('challenges.*') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Challenges') }}
                        </a>
                        
                        <a href="{{ route('tournaments.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('tournaments.*') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Tournaments') }}
                        </a>
                    @elseif(Auth::user()->role === 'lecturer')
                        <a href="{{ route('lecturer.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('lecturer.dashboard') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Dashboard') }}
                        </a>
                        
                        <a href="{{ route('lecturer.tournaments') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('lecturer.tournaments*') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Tournaments') }}
                        </a>
                    @elseif(Auth::user()->role === 'accessor')
                        <a href="{{ route('accessor.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('accessor.dashboard') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Dashboard') }}
                        </a>
                        
                        <a href="{{ route('accessor.tournaments') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('accessor.tournaments*') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Tournaments') }}
                        </a>
                    @elseif(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.dashboard') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Dashboard') }}
                        </a>
                    @elseif(Auth::user()->role === 'judge')
                        <a href="{{ route('judge.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('judge.dashboard') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Dashboard') }}
                        </a>
                    @endif

                    @if(Auth::user()->is_judge)
                        <a href="{{ route('judge.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('judge.*') ? 'border-amber-400 text-amber-500' : 'border-transparent text-gray-300 hover:text-gray-200 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out">
                            {{ __('Judge') }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                    <div @click="open = ! open">
                        <div class="flex items-center">
                            <!-- Profile Picture - FIXED SIZE -->
                            <img class="h-8 w-8 rounded-full object-cover mr-2" 
                                style="max-width: 32px !important; max-height: 32px !important;"
                                src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default-avatar.png') }}" 
                                alt="{{ Auth::user()->name }}">

                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 mt-2 w-48 rounded-md shadow-lg ltr:origin-top-right rtl:origin-top-left end-0"
                            style="display: none;"
                            @click="open = false">
                        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-gray-700">
                            <a href="{{ route('profile.edit') }}" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-300 hover:bg-gray-600 focus:outline-none focus:bg-gray-600 transition duration-150 ease-in-out">
                                {{ __('Profile') }}
                            </a>

                            <!-- Settings Section - Available for ALL roles -->
                            <a href="{{ route('settings') }}" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-300 hover:bg-gray-600 focus:outline-none focus:bg-gray-600 transition duration-150 ease-in-out">
                                {{ __('Settings') }}
                            </a>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-300 hover:bg-gray-600 focus:outline-none focus:bg-gray-600 transition duration-150 ease-in-out">
                                    {{ __('Log Out') }}
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-300 hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-gray-300 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->role === 'student')
                <a href="{{ route('dashboard') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Dashboard') }}
                </a>
                
                <a href="{{ route('quizzes.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('quizzes.*') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Quizzes') }}
                </a>
                
                <a href="{{ route('challenges.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('challenges.*') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Challenges') }}
                </a>
                
                <a href="{{ route('tournaments.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('tournaments.*') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Tournaments') }}
                </a>
            @elseif(Auth::user()->role === 'lecturer')
                <a href="{{ route('lecturer.dashboard') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('lecturer.dashboard') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Dashboard') }}
                </a>
                
                <a href="{{ route('lecturer.tournaments') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('lecturer.tournaments*') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Tournaments') }}
                </a>
            @elseif(Auth::user()->role === 'accessor')
                <a href="{{ route('accessor.dashboard') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('accessor.dashboard') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Dashboard') }}
                </a>
                
                <a href="{{ route('accessor.tournaments') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('accessor.tournaments*') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Tournaments') }}
                </a>
            @elseif(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('admin.dashboard') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Dashboard') }}
                </a>
            @elseif(Auth::user()->role === 'judge')
                <a href="{{ route('judge.dashboard') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('judge.dashboard') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Dashboard') }}
                </a>
            @endif

            @if(Auth::user()->is_judge)
                <a href="{{ route('judge.dashboard') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('judge.*') ? 'border-amber-400 text-amber-300 bg-amber-50/10' : 'border-transparent text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300' }} text-start text-base font-medium focus:outline-none focus:text-amber-300 focus:bg-amber-50/10 focus:border-amber-700 transition duration-150 ease-in-out">
                    {{ __('Judge') }}
                </a>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-600">
            <div class="flex items-center px-4">
                <!-- Add profile picture to mobile view with proper size constraints -->
                <div class="flex-shrink-0 mr-3">
                    <img class="h-10 w-10 rounded-full object-cover" 
                        src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default-avatar.png') }}" 
                        alt="{{ Auth::user()->name }}">
                </div>
                <div>
                    <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-200 focus:bg-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                    {{ __('Profile') }}
                </a>

                <!-- Responsive Settings Link - Available for ALL roles -->
                <a href="{{ route('settings') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-200 focus:bg-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                    {{ __('Settings') }}
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-200 focus:bg-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>