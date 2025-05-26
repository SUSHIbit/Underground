<section>
    <header>
        <h2 class="text-lg font-medium text-amber-400">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block font-medium text-sm text-gray-300">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white focus:border-amber-500 focus:ring-amber-500" autocomplete="current-password" />
            @if($errors->updatePassword->get('current_password'))
                <ul class="mt-2 text-sm text-red-400 space-y-1">
                    @foreach ((array) $errors->updatePassword->get('current_password') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="block font-medium text-sm text-gray-300">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white focus:border-amber-500 focus:ring-amber-500" autocomplete="new-password" />
            @if($errors->updatePassword->get('password'))
                <ul class="mt-2 text-sm text-red-400 space-y-1">
                    @foreach ((array) $errors->updatePassword->get('password') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block font-medium text-sm text-gray-300">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white focus:border-amber-500 focus:ring-amber-500" autocomplete="new-password" />
            @if($errors->updatePassword->get('password_confirmation'))
                <ul class="mt-2 text-sm text-red-400 space-y-1">
                    @foreach ((array) $errors->updatePassword->get('password_confirmation') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 active:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>