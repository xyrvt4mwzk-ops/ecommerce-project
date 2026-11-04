<x-guest-layout>
    <x-ui.auth-shell
        title="Confirm your password"
        subtitle="This is a secure area. Please confirm your password to continue."
    >
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center rounded-2xl py-3">{{ __('Confirm password') }}</x-primary-button>
        </form>
    </x-ui.auth-shell>
</x-guest-layout>
