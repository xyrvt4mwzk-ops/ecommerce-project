<section class="space-y-7">
    <header>
        <div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-violet-50 font-bold text-violet-700">02</span><div><h2 class="text-lg font-semibold text-slate-900">{{ __('Sign-in security') }}</h2><p class="mt-0.5 text-sm text-slate-600">{{ __('Use a long, unique password to protect your account.') }}</p></div></div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="grid gap-5 sm:grid-cols-2">
        @csrf
        @method('put')

        <div class="sm:col-span-2">
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 sm:col-span-2 border-t border-slate-100 pt-5">
            <x-primary-button class="rounded-2xl px-6 py-3">{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-slate-500">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
