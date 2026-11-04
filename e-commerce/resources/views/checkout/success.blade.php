<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Payment Success"
            subtitle="Your order has been completed successfully."
        >
            <x-slot name="actions">
                <x-ui.status-badge status="paid" />
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 space-y-6">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
            <div class="grid gap-6 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Order number</p>
                    <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ $order->order_number }}</p>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Keep this order number for your receipt and support reference.</p>
                </div>
                <div class="rounded-3xl bg-slate-50 p-6">
                    <p class="text-sm text-slate-500">Total paid</p>
                    <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">${{ number_format($order->total, 2) }}</p>
                    <p class="mt-3 text-sm text-slate-600">{{ ucfirst($order->payment?->status ?? 'paid') }} via {{ strtoupper(str_replace('_', ' ', $order->payment?->method ?? 'khqr')) }}.</p>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
            <h3 class="text-lg font-semibold text-slate-900">Purchased items</h3>
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
                                <p class="mt-2 text-sm text-slate-600">License key: <span class="font-mono text-slate-900">{{ $order->licenses->where('order_item_id', $item->id)->first()?->license_key ?? 'N/A' }}</span></p>
                                </div>
                            </div>
                            @php($license = $order->licenses->where('order_item_id', $item->id)->first())
                            @if($license)
                                <a href="{{ route('download.license', $license) }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Download</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('buyer.library.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white">Go to My Library</a>
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Continue Browsing</a>
        </div>
    </div>
</x-app-layout>
