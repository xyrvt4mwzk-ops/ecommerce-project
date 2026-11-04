<x-app-layout>
    @php($user = Auth::user())
    @php($recentOrders = $user->orders()->with('items.product', 'payment', 'licenses')->latest()->take(4)->get())
    @php($purchaseCount = $user->orders()->where('status', 'paid')->count())
    @php($downloadCount = $user->licenses()->count())
    @php($licenseCount = $user->licenses()->count())
    @php($workspace = [
        ['label' => 'Overview', 'href' => route('buyer.dashboard'), 'active' => true],
        ['label' => 'My library', 'href' => route('buyer.library.index')],
        ['label' => 'Wishlist', 'href' => route('buyer.wishlist.index')],
        ['label' => 'Profile settings', 'href' => route('profile.edit')],
        ['label' => 'Marketplace', 'href' => route('products.index')],
    ])

    <x-slot name="header">
        <x-ui.page-header
            title="Buyer Dashboard"
            subtitle="Access your library, orders, and download history."
        >
            <x-slot name="actions">
                <a href="{{ route('buyer.library.index') }}" class="inline-flex items-center rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Open My Library</a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Browse Products</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.dashboard-shell title="Buyer workspace" subtitle="Your purchases" :items="$workspace">
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            <x-ui.stat-card label="Purchases" :value="$purchaseCount" hint="Completed purchases in your account." accent="indigo">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 4h2l2.5 12h10l2-8H7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Downloads" :value="$downloadCount" hint="Secure downloads available in your library." accent="violet">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v10m0 0l-4-4m4 4l4-4M4 21h16" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Active licenses" :value="$licenseCount" hint="Unique license keys tied to completed orders." accent="slate">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 7h10v10H7z" stroke-width="1.8"/><path d="M10 10h4v4h-4z" stroke-width="1.8"/></svg>
            </x-ui.stat-card>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-indigo-600">Recent purchases</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Orders & receipts</h2>
                    </div>
                    <a href="{{ route('buyer.library.index') }}" class="text-sm font-semibold text-indigo-600">View all</a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($recentOrders as $order)
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-500">{{ $order->order_number }}</p>
                                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $order->items->first()?->product->title ?? 'Marketplace Order' }}</p>
                                    <p class="mt-2 text-sm text-slate-600">{{ $order->created_at->format('M d, Y') }} • {{ ucfirst($order->status) }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-slate-700">${{ number_format($order->total, 2) }}</span>
                                    <a href="{{ route('buyer.orders.show', $order) }}" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Receipt</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-ui.empty-state title="No purchases yet" message="Your first order receipt will appear here after a successful checkout." action-label="Browse Products" :action-href="route('products.index')">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </x-ui.empty-state>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] bg-gradient-to-br from-indigo-600 to-fuchsia-600 p-8 text-white shadow-lg">
                    <p class="text-sm uppercase tracking-[0.24em] text-indigo-100">Account summary</p>
                    <div class="mt-6 space-y-4">
                        <div>
                            <p class="text-sm text-indigo-100">Name</p>
                            <p class="text-xl font-semibold">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-indigo-100">Role</p>
                            <p class="text-xl font-semibold">Buyer</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">My Library</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Open your library to download completed product assets and review unique license keys.</p>
                    <a href="{{ route('buyer.library.index') }}" class="mt-6 inline-flex items-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white">Open Library</a>
                </div>
            </div>
        </div>
    </x-ui.dashboard-shell>
</x-app-layout>
