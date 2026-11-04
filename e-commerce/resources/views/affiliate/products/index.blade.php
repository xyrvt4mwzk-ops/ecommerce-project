<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Products to promote"
            subtitle="Choose a published product, create your referral link, and share it with your audience."
        >
            <x-slot name="actions">
                <a href="{{ route('affiliate.dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700">
                    Affiliate dashboard
                </a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 px-5 py-4 text-sm leading-6 text-indigo-950">
            Referral visits are tracked automatically. When a referred customer completes an eligible paid order, the marketplace records the conversion and commission.
        </div>

        @if(session('success'))
            <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if($products->count())
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($products as $product)
                    @php($referral = $referrals->get($product->id))
                    <article class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
                        <a href="{{ route('products.show', $product) }}" class="block border-b border-slate-100 bg-slate-950 p-3">
                            <x-ui.product-cover :product="$product" class="aspect-video rounded-2xl" />
                        </a>

                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">{{ \App\Models\Product::categories()[$product->category] ?? ucwords($product->category) }}</p>
                                    <h2 class="mt-2 truncate text-base font-bold text-slate-950">{{ $product->title }}</h2>
                                    <p class="mt-1 truncate text-sm text-slate-500">by {{ $product->creator->name }}</p>
                                </div>
                                <span class="shrink-0 text-base font-bold text-slate-950">${{ number_format($product->price, 2) }}</span>
                            </div>

                            <p class="mt-4 line-clamp-2 min-h-12 text-sm leading-6 text-slate-600">{{ $product->short_description }}</p>
                            <p class="mt-3 text-sm font-semibold text-emerald-700">{{ number_format($product->affiliate_commission_rate, 2) }}% commission per confirmed sale</p>

                            @if($referral)
                                <div x-data="{ copied: false }" class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Referral link ready</span>
                                        <span class="text-xs font-semibold text-slate-500">{{ number_format($referral->clicks) }} clicks</span>
                                    </div>
                                    <label for="referral-{{ $referral->id }}" class="sr-only">Referral link for {{ $product->title }}</label>
                                    <div class="mt-3 flex gap-2">
                                        <input
                                            id="referral-{{ $referral->id }}"
                                            x-ref="link"
                                            type="text"
                                            readonly
                                            value="{{ route('affiliate.capture', $referral->code) }}"
                                            class="min-w-0 flex-1 rounded-xl border-emerald-200 bg-white px-3 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                        <button
                                            type="button"
                                            x-on:click="navigator.clipboard.writeText($refs.link.value).then(() => { copied = true; setTimeout(() => copied = false, 1600) })"
                                            class="shrink-0 rounded-xl bg-slate-950 px-3 py-2 text-xs font-bold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                        >
                                            <span x-show="!copied">Copy</span>
                                            <span x-cloak x-show="copied">Copied</span>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <form method="POST" action="{{ route('affiliate.products.referral.store', $product) }}" class="mt-5">
                                    @csrf
                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Create referral link
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div>{{ $products->links() }}</div>
        @else
            <x-ui.empty-state
                title="No products available to promote"
                message="Published marketplace products will appear here when creators make them available."
                action-label="Return to dashboard"
                :action-href="route('affiliate.dashboard')"
            />
        @endif
    </div>
</x-app-layout>
