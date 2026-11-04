<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Order {{ $order->order_number }}"
            subtitle="Detailed order view for super-admin review."
        >
            <x-slot name="actions">
                <x-ui.status-badge :status="$order->status" />
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <div class="grid gap-6 md:grid-cols-3">
            <x-ui.stat-card label="Buyer" :value="$order->user->name" hint="{{ $order->user->email }}" accent="indigo">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke-width="1.8"/><path d="M4 20a8 8 0 0 1 16 0" stroke-width="1.8"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Payment" :value="'$'.number_format($order->payment?->amount ?? $order->total, 2)" hint="{{ ucfirst($order->payment?->status ?? 'pending') }}" accent="violet">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h18v10H3z" stroke-width="1.8"/><path d="M7 11h4" stroke-width="1.8"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Gateway" value="{{ $order->payment?->provider ?? 'bakong' }}" hint="Checkout provider used for this order." accent="slate">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20" stroke-width="1.8"/><path d="M17 6.5c0-2-2.2-3.5-5-3.5S7 4.5 7 6.5 9.2 10 12 10s5 1.5 5 3.5S14.8 17 12 17s-5-1.5-5-3.5" stroke-width="1.8"/></svg>
            </x-ui.stat-card>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                <h3 class="text-lg font-semibold text-slate-900">Items</h3>
                <div class="mt-6 space-y-4">
                    @foreach($order->items as $item)
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $item->product->title }}</p>
                                    <p class="mt-2 text-sm text-slate-600">${{ number_format($item->price, 2) }} × {{ $item->quantity }}</p>
                                </div>
                                <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-slate-700">${{ number_format($item->price * $item->quantity, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Licenses</h3>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        @foreach($order->licenses as $license)
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="font-semibold text-slate-900">{{ $license->product->title }}</p>
                                <p class="mt-1">License key: <span class="font-mono text-slate-900">{{ $license->license_key }}</span></p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-lg">
                    <p class="text-sm uppercase tracking-[0.24em] text-indigo-200">Order status</p>
                    <p class="mt-4 text-3xl font-semibold tracking-tight">{{ ucfirst($order->status) }}</p>
                    <p class="mt-3 text-sm leading-6 text-slate-300">This page is styled for easy product, payment, and license review.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
