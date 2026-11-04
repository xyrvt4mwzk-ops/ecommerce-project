<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Dashboard"
            subtitle="A clean hub for your marketplace workspace."
        />
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-3">
            <x-ui.stat-card label="Status" value="You're logged in!" hint="Choose your role dashboard or continue browsing products." accent="indigo">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 13l4 4L19 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>

            <x-ui.stat-card label="Quick links" value="3" hint="Browse products, update your profile, and check your dashboard." accent="violet">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 12h16M12 4v16" stroke-width="1.8" stroke-linecap="round"/></svg>
            </x-ui.stat-card>

            <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 p-6 text-white shadow-sm">
                <p class="text-sm font-medium text-indigo-100">Presentation ready</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight">Clean modern dashboard UI</p>
                <p class="mt-2 text-sm leading-6 text-indigo-100">Refined spacing, softer surfaces, and stronger visual hierarchy.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900">Browse products</a>
                    <a href="{{ route('profile.edit') }}" class="rounded-2xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold text-white">Profile settings</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
