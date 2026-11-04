<section class="space-y-6">
    <header>
        <div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-rose-100 font-bold text-rose-700">03</span><div><h2 class="text-lg font-semibold text-rose-950">{{ __('Delete account') }}</h2><p class="mt-0.5 text-sm text-rose-800">{{ __('This permanently removes your data and cannot be undone.') }}</p></div></div>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-slate-900">{{ __('Are you sure you want to delete your account?') }}</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('Please enter your password to confirm you would like to permanently delete your account.') }}</p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" placeholder="{{ __('Password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
