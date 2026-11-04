<section class="space-y-7">
    <header>
        <div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-50 font-bold text-indigo-700">01</span><div><h2 class="text-lg font-semibold text-slate-900">{{ __('Personal details') }}</h2><p class="mt-0.5 text-sm text-slate-600">{{ __("Keep your name and email address current.") }}</p></div></div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="grid gap-5 sm:grid-cols-2">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    <p>{{ __('Your email address is unverified.') }}</p>
                    <button form="send-verification" class="mt-2 font-semibold underline">{{ __('Click here to re-send the verification email.') }}</button>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-emerald-700">{{ __('A new verification link has been sent to your email address.') }}</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 sm:col-span-2 border-t border-slate-100 pt-5">
            <x-primary-button class="rounded-2xl px-6 py-3">{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-slate-500">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
