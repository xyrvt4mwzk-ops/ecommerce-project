<x-app-layout>
    <x-slot name="header"><x-ui.page-header title="Creators" subtitle="Discover public storefronts from creators with published products." /></x-slot>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($creators as $creator)
            <a href="{{ route('creators.show', $creator) }}" class="group rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl">
                <span class="grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 text-lg font-bold text-white">{{ str($creator->name)->substr(0, 1)->upper() }}</span>
                <h2 class="mt-5 text-lg font-bold tracking-tight text-slate-950 group-hover:text-indigo-700">{{ $creator->name }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $creator->published_products_count }} published {{ \Illuminate\Support\Str::plural('product', $creator->published_products_count) }}</p>
                <span class="mt-5 inline-flex text-sm font-semibold text-indigo-700">View storefront <span class="ml-1" aria-hidden="true">→</span></span>
            </a>
        @empty
            <div class="sm:col-span-2 xl:col-span-3"><x-ui.empty-state title="No public creators yet" message="Creator storefronts appear here once they publish their first product." /></div>
        @endforelse
    </div>
    <div class="mt-8">{{ $creators->links() }}</div>
</x-app-layout>
