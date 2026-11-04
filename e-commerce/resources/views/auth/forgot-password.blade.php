<x-guest-layout>
    <x-ui.auth-shell
        title="Reset your password"
        subtitle="Enter your email address and we’ll send you a password reset link."
    >
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <x-input-label for="email" :value="__('Email address')" />
                <x-text-input id="email" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center rounded-2xl py-3">{{ __('Email password reset link') }}</x-primary-button>
        </form>
    </x-ui.auth-shell>
</x-guest-layout>
