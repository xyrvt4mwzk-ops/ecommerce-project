<x-app-layout>
    <x-slot name="header"><x-ui.page-header title="About DigitalMarket" subtitle="A secure marketplace for discovering, selling, and licensing digital products." /></x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        @foreach([
            ['Creators publish', 'Creators present polished product pages, previews, pricing, and clear delivery details.'],
            ['Buyers discover', 'Guests can browse all public marketplace content before signing in with Google to purchase.'],
            ['Licenses stay private', 'Orders, license keys, downloads, revenue, and account information remain protected.'],
        ] as [$title, $copy])
            <section class="panel"><h2 class="text-lg font-bold text-slate-950">{{ $title }}</h2><p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p></section>
        @endforeach
    </div>
</x-app-layout>
