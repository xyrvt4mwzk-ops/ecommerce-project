<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Creator Products"
            subtitle="Manage your digital products and publishing status."
        >
            <x-slot name="actions">
                <a href="{{ route('creator.products.create') }}" class="inline-flex items-center rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Create Product</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="w-full">
        <div class="grid gap-6 md:grid-cols-3">
            <x-ui.stat-card label="Products" :value="$products->total()" hint="Listings in your catalog." accent="indigo">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16v12H4z" stroke-width="1.8"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Published" :value="$products->where('status', 'published')->count()" hint="Visible on public pages." accent="violet">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 6L9 17l-5-5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Drafts" :value="$products->where('status', 'draft')->count()" hint="Ready for future publishing." accent="slate">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 4h16v16H4z" stroke-width="1.8"/><path d="M8 8h8M8 12h8M8 16h5" stroke-width="1.8" stroke-linecap="round"/></svg>
            </x-ui.stat-card>
        </div>

        <div class="mt-8 space-y-6">
            @forelse($products as $product)
                <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    <div class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr] lg:items-center">
                        <div class="flex items-start gap-4">
                            <div class="h-24 w-24 overflow-hidden rounded-3xl ring-1 ring-slate-200/70">
                                <x-ui.product-cover :product="$product" class="h-24 w-24" />
                            </div>
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-3">
                                <x-ui.status-badge :status="$product->status" />
                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ ucfirst($product->category) }}</span>
                                </div>
                                <h3 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $product->title }}</h3>
                                <p class="text-sm leading-6 text-slate-600">{{ $product->short_description }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <span class="inline-flex items-center justify-center rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">${{ number_format($product->price, 2) }}</span>
                            <a href="{{ route('creator.products.edit', $product) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700">Edit</a>
                            <form action="{{ route('creator.products.destroy', $product) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <x-ui.empty-state title="No products yet" message="Create your first digital product to populate this dashboard." action-label="Create Product" :action-href="route('creator.products.create')">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="1.8" stroke-linecap="round"/></svg>
                </x-ui.empty-state>
            @endforelse
        </div>

        <div class="mt-8">{{ $products->links() }}</div>
    </div>
</x-app-layout>
