@php
    $user = auth()->user();
    $navigation = app(\App\Services\DashboardNavigation::class)->for($user);
    $activeItem = collect($navigation['groups'])->flatMap(fn ($group) => $group['items'])->firstWhere('active', true);
    $pageTitle = $activeItem['label'] ?? str(request()->route()?->getName() ?? 'workspace')->afterLast('.')->replace('-', ' ')->title();
@endphp

<header class="sticky top-0 z-40 flex h-[4.5rem] shrink-0 items-center gap-3 border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/90 sm:px-6 lg:px-8">
    <button type="button" @click="open = true" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white lg:hidden" aria-label="Open workspace menu">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 7h16M4 12h16M4 17h16" stroke-width="1.8" stroke-linecap="round"/></svg>
    </button>

    <div class="min-w-0">
        <p class="hidden text-xs font-semibold text-slate-400 sm:block">{{ $navigation['role'] }} / {{ $navigation['context'] }}</p>
        <h1 class="truncate text-base font-bold tracking-tight text-slate-950 dark:text-white sm:text-lg">{{ $pageTitle }}</h1>
    </div>

    <button type="button" @click="$store.theme.toggle()" class="ml-auto grid h-10 w-10 shrink-0 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white" :aria-label="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'">
        <svg x-show="!$store.theme.dark" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="4" stroke-width="1.8"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32 1.41-1.41" stroke-width="1.8" stroke-linecap="round"/></svg>
        <svg x-cloak x-show="$store.theme.dark" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.5 14.2A8.5 8.5 0 0 1 9.8 3.5 8.5 8.5 0 1 0 20.5 14.2Z" stroke-width="1.8" stroke-linejoin="round"/></svg>
    </button>

    @auth
        <x-dropdown align="right" width="64">
            <x-slot name="trigger">
                <button class="flex min-w-0 items-center gap-2 rounded-xl p-1.5 text-left transition hover:bg-slate-100 dark:hover:bg-slate-800">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 text-sm font-bold text-white">{{ str($user->name)->substr(0, 1)->upper() }}</span>
                    <span class="hidden min-w-0 xl:block"><span class="block max-w-40 truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</span><span class="block text-xs text-slate-500 dark:text-slate-400">{{ $navigation['role'] }}</span></span>
                    <svg class="hidden h-4 w-4 text-slate-400 sm:block" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path d="m5 7 5 5 5-5" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">Profile settings</x-dropdown-link>
                <x-dropdown-link :href="route('products.index')">View marketplace</x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">@csrf<x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log out</x-dropdown-link></form>
            </x-slot>
        </x-dropdown>
    @else
        <div class="flex shrink-0 items-center gap-2">
            <a href="{{ route('register') }}" class="hidden items-center justify-center rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 sm:inline-flex">Register</a>
            <a href="{{ route('login') }}" aria-label="Guest profile — sign in" title="Sign in" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-slate-950 text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-slate-950 dark:hover:bg-indigo-100 dark:focus:ring-offset-slate-900">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <circle cx="12" cy="8" r="3.25" stroke-width="1.8" />
                    <path d="M5.5 20c.7-3.2 3.1-5 6.5-5s5.8 1.8 6.5 5" stroke-width="1.8" stroke-linecap="round" />
                </svg>
            </a>
        </div>
    @endauth
</header>
