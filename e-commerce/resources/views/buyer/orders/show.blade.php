<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Order details"
            subtitle="Review your products, payment status, and available purchase actions."
        >
            <x-slot name="actions">
                <a href="{{ route('buyer.orders.index') }}" class="btn-secondary">Back to orders</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <div class="grid gap-6 md:grid-cols-3">
            <x-ui.stat-card label="Order number" :value="$order->order_number" hint="Unique receipt identifier." accent="indigo">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 3h12v18H6z" stroke-width="1.8"/><path d="M9 7h6M9 11h6M9 15h4" stroke-width="1.8" stroke-linecap="round"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Total" :value="'$'.number_format($order->total, 2)" hint="Paid through your selected checkout method." accent="violet">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20" stroke-width="1.8"/><path d="M17 6.5c0-2-2.2-3.5-5-3.5S7 4.5 7 6.5 9.2 10 12 10s5 1.5 5 3.5S14.8 17 12 17s-5-1.5-5-3.5" stroke-width="1.8"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Status" :value="ucfirst($order->status)" hint="Order progress at a glance." accent="slate">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 6L9 17l-5-5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr,0.8fr]">
            <div class="space-y-6">
                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Items</h3>
                    <div class="mt-6 space-y-4">
                        @foreach($order->items as $item)
                            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="h-16 w-16 overflow-hidden rounded-2xl ring-1 ring-slate-200/70">
                                            <x-ui.product-cover :product="$item->product" class="h-16 w-16" />
                                        </div>
                                        <div>
                                        <p class="font-semibold text-slate-900">{{ $item->product->title }}</p>
                                        <p class="mt-2 text-sm text-slate-600">${{ number_format($item->price, 2) }} × {{ $item->quantity }}</p>
                                        <p class="mt-2 text-sm text-slate-500">License key: <span class="font-mono text-slate-900">{{ $order->licenses->where('order_item_id', $item->id)->first()?->license_key ?? 'N/A' }}</span></p>
                                        </div>
                                    </div>
                                    <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-slate-700">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Payment summary</h3>
                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Gateway</p>
                            <p class="mt-2 font-semibold text-slate-900">{{ $order->payment?->provider ?? 'bakong' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Status</p>
                            <p class="mt-2 font-semibold text-slate-900">{{ ucfirst($order->payment?->status ?? 'pending') }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Purchased</p>
                            <p class="mt-2 font-semibold text-slate-900">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-lg">
                    <p class="text-sm uppercase tracking-[0.24em] text-indigo-200">Receipt status</p>
                    <p class="mt-4 text-3xl font-semibold tracking-tight">{{ ucfirst($order->status) }}</p>
                    @if($order->payment?->hasActiveKhqr())
                        <p class="mt-3 text-sm leading-6 text-slate-300">Your KHQR is ready. Complete payment before it expires to receive your license and download.</p>
                        <a href="{{ route('payments.khqr.show', $order->payment) }}" class="btn-primary mt-6 w-full">Pay now</a>
                    @elseif($order->payment?->canRetry())
                        <p class="mt-3 text-sm leading-6 text-slate-300">The previous payment is no longer active. Generate a new KHQR to complete this order.</p>
                        <form method="POST" action="{{ route('payments.retry', $order->payment) }}" class="mt-6">
                            @csrf
                            <button type="submit" class="btn-primary w-full">Pay again</button>
                        </form>
                    @elseif($order->status === 'paid')
                        <p class="mt-3 text-sm leading-6 text-slate-300">Payment is confirmed. Your licenses and secure downloads are available.</p>
                        <a href="{{ route('buyer.library.index') }}" class="btn-primary mt-6 w-full">Open library</a>
                    @else
                        <p class="mt-3 text-sm leading-6 text-slate-300">This order does not currently require a payment action.</p>
                    @endif
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Secure licenses</h3>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        @foreach($order->licenses as $license)
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="font-semibold text-slate-900">{{ $license->product->title }}</p>
                                <p class="mt-1">License key: <span class="font-mono text-slate-900">{{ $license->license_key }}</span></p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
