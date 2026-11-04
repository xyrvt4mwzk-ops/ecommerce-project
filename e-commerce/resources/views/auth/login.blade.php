<x-guest-layout>
    <x-ui.auth-shell
        title="Welcome back"
        subtitle="Log in to browse products, manage your creator storefront, or continue your affiliate dashboard."
    >
        <div class="space-y-6">
            @if($purchaseIntent)
                <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4 text-sm text-indigo-950">
                    <p class="font-semibold">Continue purchasing {{ $purchaseIntent->title }}</p>
                    <p class="mt-1 leading-5 text-indigo-700">After sign-in, Buyer accounts continue directly to secure KHQR payment.</p>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200/80 bg-slate-50/80 p-4">
                <a href="{{ route('google.redirect', ['intent' => 'login']) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50">
                    <svg class="mr-2 h-5 w-5" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.9 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6 29.4 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.5-.4-3.5z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15 19 12 24 12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6 29.4 4 24 4c-7.9 0-14.8 4.5-17.7 10.7z"/><path fill="#4CAF50" d="M24 44c5.3 0 10-2 13.5-5.3l-6.2-5.1c-2 1.4-4.5 2.2-7.3 2.2-5.3 0-9.7-3.1-11.3-7.5l-6.5 5C10.3 39.6 16.6 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1 3-3 5.2-5.9 6.7l.1-.1 6.2 5.1C35.2 41 40 44 24 44c7.1 0 12.9-2.3 17.1-6.2l-6.2-5.1C37.2 33.6 40 29.5 40 24c0-1.3-.1-2.5-.4-3.5z"/></svg>
                    Continue with Google
                </a>
                <p class="mt-3 text-center text-xs leading-5 text-slate-500">Sign in securely with your Google account.</p>
            </div>

            <div class="flex items-center gap-3 text-xs font-medium uppercase tracking-[0.14em] text-slate-400"><span class="h-px flex-1 bg-slate-200"></span>or use email<span class="h-px flex-1 bg-slate-200"></span></div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email address')" />
                    <x-text-input id="email" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between gap-3">
                    <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                        <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500" href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <x-primary-button class="w-full justify-center rounded-2xl py-3">
                    {{ __('Log in') }}
                </x-primary-button>
            </form>

            <p class="text-center text-sm text-slate-600">
                New here? <a href="{{ route('register') }}" class="font-semibold text-indigo-600">Create an account</a>
            </p>
        </div>
    </x-ui.auth-shell>
</x-guest-layout>
