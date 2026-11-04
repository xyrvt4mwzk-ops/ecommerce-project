<x-app-layout>
    <x-slot name="header"><x-ui.page-header title="Categories" subtitle="Explore published products by marketplace category." /></x-slot>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category['slug']) }}" class="group rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-indigo-50 text-indigo-700"><x-ui.sidebar-icon name="categories" /></span>
                <h2 class="mt-5 text-xl font-bold tracking-tight text-slate-950 group-hover:text-indigo-700">{{ $category['name'] }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $category['products_count'] }} {{ \Illuminate\Support\Str::plural('product', $category['products_count']) }}</p>
                <span class="mt-5 inline-flex text-sm font-semibold text-indigo-700">Browse category <span class="ml-1" aria-hidden="true">→</span></span>
            </a>
        @endforeach
    </div>
</x-app-layout>
