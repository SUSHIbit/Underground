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
            
            <!-- App Theme Settings -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-amber-800/20">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-amber-400">
                        {{ __('Appearance') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-400">
                        {{ __('Customize how the application looks.') }}
                    </p>

                    <div class="mt-6">
                        <!-- Theme Settings -->
                        <div class="mb-4">
                            <label for="theme" class="block text-sm font-medium text-gray-300">Theme</label>
                            <select id="theme" name="theme" class="mt-1 block w-full bg-gray-700 border-amber-800/20 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm text-white">
                                <option value="dark" selected>Dark (Default)</option>
                                <option value="darker" disabled>Darker (Coming Soon)</option>
                                <option value="custom" disabled>Custom (Coming Soon)</option>
                            </select>
                        </div>
                        
                        <!-- Accent Color -->
                        <div class="mb-4">
                            <label for="accent_color" class="block text-sm font-medium text-gray-300">Accent Color</label>
                            <select id="accent_color" name="accent_color" class="mt-1 block w-full bg-gray-700 border-amber-800/20 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm text-white">
                                <option value="amber" selected>Amber (Default)</option>
                                <option value="blue" disabled>Blue (Coming Soon)</option>
                                <option value="green" disabled>Green (Coming Soon)</option>
                                <option value="purple" disabled>Purple (Coming Soon)</option>
                                <option value="red" disabled>Red (Coming Soon)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notifications Settings -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-amber-800/20">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-amber-400">
                        {{ __('Notifications') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-400">
                        {{ __('Manage your notification preferences.') }}
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="email_notifications" name="email_notifications" type="checkbox" checked class="focus:ring-amber-500 h-4 w-4 text-amber-600 border-gray-600 rounded bg-gray-700">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="email_notifications" class="font-medium text-gray-300">Email Notifications</label>
                                <p class="text-gray-500">Receive email notifications about important events.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="tournament_reminders" name="tournament_reminders" type="checkbox" checked class="focus:ring-amber-500 h-4 w-4 text-amber-600 border-gray-600 rounded bg-gray-700">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="tournament_reminders" class="font-medium text-gray-300">Tournament Reminders</label>
                                <p class="text-gray-500">Receive reminders about upcoming tournaments.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="system_notifications" name="system_notifications" type="checkbox" checked class="focus:ring-amber-500 h-4 w-4 text-amber-600 border-gray-600 rounded bg-gray-700">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="system_notifications" class="font-medium text-gray-300">System Notifications</label>
                                <p class="text-gray-500">Receive notifications about system updates and maintenance.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition ease-in-out duration-150">
                            {{ __('Save Preferences') }}
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Privacy Settings -->
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-amber-800/20">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-amber-400">
                        {{ __('Privacy') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-400">
                        {{ __('Manage your privacy settings.') }}
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="show_profile" name="show_profile" type="checkbox" checked class="focus:ring-amber-500 h-4 w-4 text-amber-600 border-gray-600 rounded bg-gray-700">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="show_profile" class="font-medium text-gray-300">Show Profile</label>
                                <p class="text-gray-500">Allow others to view your profile.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="show_on_leaderboard" name="show_on_leaderboard" type="checkbox" checked class="focus:ring-amber-500 h-4 w-4 text-amber-600 border-gray-600 rounded bg-gray-700">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="show_on_leaderboard" class="font-medium text-gray-300">Show on Leaderboard</label>
                                <p class="text-gray-500">Allow your name to appear on the leaderboard.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition ease-in-out duration-150">
                            {{ __('Save Privacy Settings') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>