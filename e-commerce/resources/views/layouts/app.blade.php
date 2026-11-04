<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'DigitalMarket') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div
            x-data="{ open: false, collapsed: localStorage.getItem('digitalmarket-sidebar-collapsed') === '1' }"
            x-effect="document.body.classList.toggle('overflow-hidden', open)"
            @keydown.escape.window="open = false"
            class="flex min-h-screen w-full bg-slate-50 dark:bg-slate-950"
        >
            <div class="sticky top-0 hidden h-screen shrink-0 overflow-hidden transition-[width] duration-200 ease-out lg:block" :class="collapsed ? 'w-[4.75rem]' : 'w-[17rem]'">
                <x-ui.sidebar />
            </div>

            <div class="flex min-h-screen min-w-0 flex-1 flex-col">
                <x-ui.topbar />
                <x-ui.flash />

                @isset($header)
                    <header class="border-b border-slate-200/80 bg-white/60 px-4 py-5 backdrop-blur dark:border-slate-800 dark:bg-slate-900/60 sm:px-6 lg:px-8">
                        {{ $header }}
                    </header>
                @endisset

                <main class="min-w-0 flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>

            <div x-cloak x-show="open" class="fixed inset-0 z-[80] lg:hidden" role="dialog" aria-modal="true" aria-label="Workspace navigation">
                <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="open = false"></div>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="absolute inset-y-0 left-0" @click.outside="open = false">
                    <x-ui.sidebar mobile />
                </div>
            </div>
        </div>
    </body>
</html>
