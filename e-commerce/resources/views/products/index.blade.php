<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="eyebrow">Marketplace</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Find the right digital product.</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">Browse clear, ready-to-buy tools, subscriptions, templates, and creator resources.</p>
        </div>
    </x-slot>

    <div class="page-wrap">
        <section
            class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"
            x-data="{
                loading: false,
                timer: null,
                requestId: 0,
                queueSearch() {
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => this.refreshResults(), 250);
                },
                async refreshResults() {
                    const currentRequest = ++this.requestId;
                    const query = new URLSearchParams(new FormData(this.$refs.searchForm));
                    const url = `${this.$refs.searchForm.action}?${query.toString()}`;
                    this.loading = true;

                    try {
                        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        if (!response.ok) throw new Error('Search request failed');
                        const html = await response.text();
                        if (currentRequest !== this.requestId) return;
                        document.getElementById('product-results').innerHTML = html;
                        window.history.replaceState({}, '', url);
                    } finally {
                        if (currentRequest === this.requestId) this.loading = false;
                    }
                }
            }"
        >
            <form x-ref="searchForm" method="GET" action="{{ route('products.index') }}" class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_11rem_11rem_auto]" @submit.prevent="refreshResults()">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="6" stroke-width="1.8"/><path d="m16 16 4 4" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <input id="search" name="search" value="{{ $search }}" placeholder="Type to search products" autocomplete="off" class="field-control mt-0 w-full border-slate-200 bg-slate-50 py-3 pl-11 pr-10" @input="queueSearch()" />
                    <svg x-cloak x-show="loading" class="absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin text-indigo-600" viewBox="0 0 24 24" fill="none" aria-label="Searching"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" class="opacity-20"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <select name="category" class="field-control mt-0 border-slate-200 bg-slate-50 py-3" @change="refreshResults()">
                    <option value="">All categories</option>
                    @foreach($categories as $key => $label)<option value="{{ $key }}" @selected($category === $key)>{{ $label }}</option>@endforeach
                </select>
                <select name="sort" class="field-control mt-0 border-slate-200 bg-slate-50 py-3" @change="refreshResults()">
                    <option value="" @selected($sort === '')>Newest</option>
                    <option value="price_low" @selected($sort === 'price_low')>Price: low first</option>
                    <option value="price_high" @selected($sort === 'price_high')>Price: high first</option>
                </select>
                <button type="submit" class="btn-primary min-h-11">Search</button>
            </form>
        </section>

        <div class="mt-7 flex flex-wrap items-center gap-2">
            <a href="{{ route('products.index') }}" @class(['rounded-full px-3.5 py-2 text-sm font-semibold transition', 'bg-slate-950 text-white' => $category === '', 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300' => $category !== ''])>All products</a>
            @foreach($categories as $key => $label)
                <a href="{{ route('products.index', ['category' => $key]) }}" @class(['rounded-full px-3.5 py-2 text-sm font-semibold transition', 'bg-indigo-600 text-white' => $category === $key, 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-indigo-50 hover:text-indigo-700' => $category !== $key])>{{ $label }}</a>
            @endforeach
        </div>

        <div id="product-results" aria-live="polite" aria-busy="false">
            @include('products.partials.results')
        </div>
    </div>
</x-app-layout>
