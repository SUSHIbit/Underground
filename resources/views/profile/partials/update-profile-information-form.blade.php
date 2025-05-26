<section>
    <header>
        <h2 class="text-lg font-medium text-amber-400">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        
        <div>
            <label for="name" class="block font-medium text-sm text-gray-300">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white focus:border-amber-500 focus:ring-amber-500" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @if($errors->get('name'))
                <ul class="mt-2 text-sm text-red-400 space-y-1">
                    @foreach ((array) $errors->get('name') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <label for="email" class="block font-medium text-sm text-gray-300">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white focus:border-amber-500 focus:ring-amber-500" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @if($errors->get('email'))
                <ul class="mt-2 text-sm text-red-400 space-y-1">
                    @foreach ((array) $errors->get('email') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-300">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-amber-400 hover:text-amber-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 active:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
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