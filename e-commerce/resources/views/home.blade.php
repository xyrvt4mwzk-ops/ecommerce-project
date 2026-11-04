<x-app-layout>
    @php($categoryMap = \App\Models\Product::categories())

    <section class="overflow-hidden rounded-[2rem] bg-slate-950 text-white shadow-2xl shadow-slate-900/20">
        <div class="grid gap-10 px-6 py-16 sm:px-10 lg:grid-cols-[1.1fr,0.9fr] lg:px-14 lg:py-20">
            <div class="space-y-8">
                <span class="inline-flex rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.28em] text-indigo-200">Digital marketplace</span>
                <div class="space-y-5">
                    <h1 class="max-w-2xl text-4xl font-semibold tracking-tight sm:text-5xl lg:text-6xl">Sell your knowledge. Download your next idea.</h1>
                    <p class="max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">DigitalMarket is a polished storefront for creators, buyers, and affiliates — built to showcase premium digital products, secure downloads, and clean role-based flows.</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-400">Browse Products</a>
                    <a href="{{ auth()->check() ? route('role.select') : route('register') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Start Selling</a>
                </div>
                <div class="grid gap-4 sm:grid-cols-4">
                    @foreach([
                        ['label' => 'Products', 'value' => $featuredProducts->count()],
                        ['label' => 'Categories', 'value' => count($categoryMap)],
                        ['label' => 'Secure Downloads', 'value' => '100%'],

                    ] as $stat)
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-sm text-slate-300">{{ $stat['label'] }}</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $stat['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach($featuredProducts->take(4) as $product)
                    <x-ui.product-card :product="$product" :categories="$categoryMap" class="border border-white/10 bg-white/95" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-5">
        @foreach($categoryMap as $slug => $label)
            <a href="{{ route('products.index', ['category' => $slug]) }}" class="group rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 transition hover:-translate-y-0.5 hover:shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Category</p>
                        <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $label }}</h3>
                    </div>
                    <span class="rounded-2xl bg-indigo-50 p-3 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16M4 12h10M4 18h16" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </a>
        @endforeach
    </section>

    <section class="mt-10 grid gap-6 lg:grid-cols-3">
        <div class="rounded-[1.75rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70 lg:col-span-2">
            <x-ui.page-header title="Featured products" subtitle="A clean preview of what the marketplace can sell." />
            <div class="mt-8 grid gap-6 md:grid-cols-2">
                @foreach($featuredProducts->take(4) as $product)
                    <x-ui.product-card :product="$product" :categories="$categoryMap" />
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[1.75rem] bg-gradient-to-br from-indigo-600 to-fuchsia-600 p-8 text-white shadow-lg">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-indigo-100">Creator CTA</p>
                <h2 class="mt-4 text-3xl font-semibold tracking-tight">Launch your own digital storefront.</h2>
                <p class="mt-4 text-sm leading-6 text-indigo-100">Upload products, publish instantly, and manage your library of digital goods from a modern creator dashboard.</p>
                <a href="{{ auth()->check() ? route('role.select') : route('register') }}" class="mt-6 inline-flex items-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-900">Become a Creator</a>
            </div>

            <div class="rounded-[1.75rem] bg-slate-950 p-8 text-white shadow-lg">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-300">Affiliate promotion</p>
                <h2 class="mt-4 text-2xl font-semibold tracking-tight">Turn product clicks into affiliate commissions.</h2>
                <p class="mt-4 text-sm leading-6 text-slate-300">Affiliates can share referral links, track conversions, and review commission history from a dedicated dashboard.</p>
                <a href="{{ auth()->check() ? route('role.select') : route('register') }}" class="mt-6 inline-flex items-center rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-semibold text-white">Join as Affiliate</a>
            </div>
        </div>
    </section>

    <section class="mt-10 rounded-[1.75rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
        <x-ui.page-header title="How it works" subtitle="A quick path for buyers, creators, and affiliates." />
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            @foreach([
                ['title' => 'Buyers', 'text' => 'Browse products, pay with KHQR, and download completed licenses from My Library.'],
                ['title' => 'Creators', 'text' => 'Create products, upload files, manage status, and present your work with a premium storefront.'],
                ['title' => 'Affiliates', 'text' => 'Share referral links, monitor clicks, and review commission records.'],
            ] as $item)
                <div class="rounded-3xl bg-slate-50 p-6">
                    <p class="text-sm font-medium text-indigo-600">{{ $item['title'] }}</p>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <footer class="mt-10 rounded-[1.75rem] bg-slate-950 px-8 py-10 text-white shadow-2xl">
        <div class="grid gap-8 lg:grid-cols-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 font-bold text-white">DM+</span>
                    <div>
                        <p class="text-lg font-semibold">DigitalMarket</p>
                        <p class="text-sm text-slate-400">Modern digital downloads marketplace</p>
                    </div>
                </div>
                <p class="mt-4 text-sm leading-6 text-slate-400">A premium storefront designed for browsing, selling, and presenting digital products with confidence.</p>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-400">Explore</p>
                <ul class="mt-4 space-y-3 text-sm text-slate-300">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-white">Browse Products</a></li>
                    <li><a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="hover:text-white">Join DigitalMarket</a></li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-400">Marketplace</p>
                <ul class="mt-4 space-y-3 text-sm text-slate-300">
                    @foreach($categoryMap as $slug => $label)
                        <li><a href="{{ route('products.index', ['category' => $slug]) }}" class="hover:text-white">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

        </div>
    </footer>
</x-app-layout>
