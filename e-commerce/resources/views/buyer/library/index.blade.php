<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="My Library"
            subtitle="Download completed products and review your purchased licenses."
        >
            <x-slot name="actions">
                <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Browse More</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="w-full">
        <div class="grid gap-6 md:grid-cols-3">
            <x-ui.stat-card label="Purchased items" :value="$licenses->total()" hint="Completed licenses in your library." accent="indigo">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 4h2l2.5 12h10l2-8H7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="License keys" :value="$licenses->count()" hint="Unique licenses visible below." accent="violet">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 12h16" stroke-width="1.8"/><path d="M9 12v4h4" stroke-width="1.8"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Downloads" :value="$licenses->count()" hint="Secure downloads ready to open." accent="slate">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v10m0 0l-4-4m4 4l4-4M4 21h16" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>
        </div>

        <div class="mt-8 space-y-6">
            @forelse($licenses as $license)
                <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200/70">
                    <div class="grid gap-6 p-6 lg:grid-cols-[1.1fr,0.9fr] lg:items-center">
                        <div class="flex items-start gap-4">
                            <div class="h-16 w-16 overflow-hidden rounded-2xl ring-1 ring-slate-200/70">
                                <x-ui.product-cover :product="$license->product" class="h-16 w-16" />
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">{{ $license->product->title }}</h3>
                                <p class="mt-2 text-sm text-slate-600">License key: <span class="font-mono text-slate-900">{{ $license->license_key }}</span></p>
                                <p class="mt-2 text-sm text-slate-500">Purchased on {{ $license->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <a href="{{ route('download.license', $license) }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Download</a>
                            <a href="{{ route('buyer.orders.show', $license->order) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">View Receipt</a>
                        </div>
                    </div>
                </div>
            @empty
                <x-ui.empty-state title="No library items yet" message="Your completed purchases will appear here after successful payment." action-label="Browse Products" :action-href="route('products.index')">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="1.8" stroke-linecap="round"/></svg>
                </x-ui.empty-state>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $licenses->links() }}
        </div>
    </div>
</x-app-layout>
