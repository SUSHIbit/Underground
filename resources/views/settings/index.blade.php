<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Account Management -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-amber-800/20">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-amber-400">
                        {{ __('Account Management') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-400">
                        {{ __('Manage your account settings and profile information.') }}
                    </p>

                    <div class="mt-6">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                            {{ __('Edit Profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>