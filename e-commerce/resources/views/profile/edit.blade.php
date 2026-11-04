<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="eyebrow">Account centre</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Profile settings</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">Manage your personal details, sign-in security, and account preferences.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-secondary shrink-0">Back to dashboard</a>
        </div>
    </x-slot>

    <div class="w-full max-w-5xl">
            <div class="space-y-7">
                <section id="personal-details" class="panel scroll-mt-28">
                    @include('profile.partials.update-profile-information-form')
                </section>

                <section id="security" class="panel scroll-mt-28">
                    @include('profile.partials.update-password-form')
                </section>

                <section id="danger-zone" class="rounded-3xl border border-rose-200 bg-rose-50/50 p-6 sm:p-7 scroll-mt-28">
                    @include('profile.partials.delete-user-form')
                </section>
            </div>
    </div>
</x-app-layout>
