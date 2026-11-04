<x-guest-layout>
    <x-ui.auth-shell
        title="Set a new password"
        subtitle="Choose a strong new password to continue using your account."
    >
        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('Email address')" />
                <x-text-input id="email" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="password" :value="__('New password')" />
                    <x-text-input id="password" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm password')" />
                    <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <x-primary-button class="w-full justify-center rounded-2xl py-3">{{ __('Reset password') }}</x-primary-button>
        </form>
    </x-ui.auth-shell>
</x-guest-layout>
