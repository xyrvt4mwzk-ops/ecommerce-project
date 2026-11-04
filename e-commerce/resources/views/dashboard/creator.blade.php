<x-app-layout>
    @php($user = Auth::user())
    @php($products = $user->products()->latest()->take(5)->get())
    @php($productCount = $user->products()->count())
    @php($salesCount = $user->orders()->where('status', 'paid')->count())
    @php($revenue = $user->products()->join('order_items', 'products.id', '=', 'order_items.product_id')->sum('order_items.price'))
    @php($downloadCount = $user->licenses()->count())
    @php($workspace = [
        ['label' => 'Overview', 'href' => route('creator.dashboard'), 'active' => true],
        ['label' => 'Products', 'href' => route('creator.products.index')],
        ['label' => 'Create product', 'href' => route('creator.products.create')],
        ['label' => 'Profile settings', 'href' => route('profile.edit')],
    ])

    <x-slot name="header">
        <x-ui.page-header
            title="Creator Dashboard"
            subtitle="Manage products, uploads, and publishing."
        >
            <x-slot name="actions">
                <a href="{{ route('creator.products.create') }}" class="inline-flex items-center rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Create Product</a>
                <a href="{{ route('creator.products.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700">My Products</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.dashboard-shell title="Creator studio" subtitle="Manage your store" :items="$workspace">
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.stat-card label="Products" :value="$productCount" hint="Your marketplace listings." accent="indigo">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16v12H4z" stroke-width="1.8"/><path d="M8 6v12" stroke-width="1.8"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Sales" :value="$salesCount" hint="Paid orders tied to your products." accent="violet">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 12h16" stroke-width="1.8"/><path d="M14 6l6 6-6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Revenue" :value="'$'.number_format($revenue, 2)" hint="Marketplace sales totals." accent="slate">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20" stroke-width="1.8"/><path d="M17 6.5c0-2-2.2-3.5-5-3.5S7 4.5 7 6.5 9.2 10 12 10s5 1.5 5 3.5S14.8 17 12 17s-5-1.5-5-3.5" stroke-width="1.8"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Downloads" :value="$downloadCount" hint="Licenses delivered to buyers." accent="indigo">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v10m0 0l-4-4m4 4l4-4M4 21h16" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-indigo-600">Recent products</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Your listings</h2>
                    </div>
                    <a href="{{ route('creator.products.index') }}" class="text-sm font-semibold text-indigo-600">Manage all</a>
                </div>
                <div class="mt-6 space-y-4">
                    @forelse($products as $product)
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-slate-500">{{ ucfirst($product->category) }}</p>
                                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $product->title }}</p>
                                    <p class="mt-2 text-sm text-slate-600">{{ $product->short_description }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-ui.status-badge :status="$product->status" />
                                    <a href="{{ route('creator.products.edit', $product) }}" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Edit</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-ui.empty-state title="No products yet" message="Create your first digital product to populate this dashboard." action-label="Create Product" :action-href="route('creator.products.create')">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </x-ui.empty-state>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] bg-gradient-to-br from-indigo-600 to-fuchsia-600 p-8 text-white shadow-lg">
                    <p class="text-sm uppercase tracking-[0.24em] text-indigo-100">Creator status</p>
                    <div class="mt-6 space-y-4">
                        <div>
                            <p class="text-sm text-indigo-100">Account</p>
                            <p class="text-xl font-semibold">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-indigo-100">Publishing</p>
                            <p class="text-xl font-semibold">Draft, publish, archive</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Recent orders</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Review product demand and order activity from a concise creator summary.</p>
                    <a href="{{ route('creator.products.index') }}" class="mt-6 inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Open product manager</a>
                </div>
            </div>
        </div>
    </x-ui.dashboard-shell>
</x-app-layout>
