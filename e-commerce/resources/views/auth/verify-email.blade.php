<x-guest-layout>
    <x-ui.auth-shell
        title="Verify your email"
        subtitle="We sent a verification link to your email address. Please verify to continue."
    >
        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                A new verification link has been sent to your inbox.
            </div>
        @endif

        <div class="space-y-4">
            <p class="text-sm leading-6 text-slate-600">If you did not receive the email, you can resend it below or log out and try again with a different address.</p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-primary-button class="w-full justify-center rounded-2xl py-3">{{ __('Resend verification email') }}</x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700">Log out</button>
            </form>
        </div>
    </x-ui.auth-shell>
</x-guest-layout>
