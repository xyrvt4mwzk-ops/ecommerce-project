<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Complete your payment"
            subtitle="Review your product, apply a coupon, then choose card or KHQR payment."
        >
            <x-slot name="actions">
                <a href="{{ route('products.show', $product) }}" class="btn-secondary">Back to product</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="page-wrap">
        <div class="grid items-start gap-7 lg:grid-cols-[minmax(0,1fr)_23rem]">
            <section class="panel">
                <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                    <div>
                        <p class="eyebrow">Selected product</p>
                        <h2 class="mt-1 text-xl font-bold tracking-tight text-slate-950">Ready for checkout</h2>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Instant delivery</span>
                </div>

                <article class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-center">
                    <a href="{{ route('products.show', $product) }}" class="aspect-[16/10] w-full shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 sm:w-52">
                        <x-ui.product-cover :product="$product" class="h-full w-full" />
                    </a>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $product->categoryRecord?->name ?? ucfirst($product->category) }}</p>
                        <a href="{{ route('products.show', $product) }}" class="mt-1 block text-lg font-bold tracking-tight text-slate-950 hover:text-indigo-700">{{ $product->title }}</a>
                        <p class="mt-2 text-sm text-slate-500">By {{ $product->creator->name }}</p>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $product->short_description }}</p>
                        <p class="mt-4 text-lg font-bold text-slate-950">${{ number_format($product->price, 2) }}</p>
                    </div>
                </article>
            </section>

            <aside class="space-y-5 lg:sticky lg:top-24">
                <section class="panel">
                    <p class="eyebrow">Coupon code</p>
                    <p class="mt-1 text-sm text-slate-600">Enter a valid creator coupon before generating the QR.</p>

                    <form method="POST" action="{{ route('purchase.coupon', $product) }}" class="mt-4">
                        @csrf
                        <label for="purchase-coupon" class="text-sm font-semibold text-slate-800">Discount code</label>
                        <div class="mt-2 flex gap-2">
                            <input
                                id="purchase-coupon"
                                name="code"
                                value="{{ old('code', $totals['coupon']?->code) }}"
                                placeholder="Enter coupon code"
                                maxlength="50"
                                autocomplete="off"
                                required
                                class="field-control mt-0 min-w-0 uppercase"
                            />
                            <button type="submit" class="btn-secondary shrink-0">Apply</button>
                        </div>
                        @error('code')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </form>

                    @if($totals['coupon'])
                        <div class="mt-3 flex items-center gap-2 rounded-xl bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700">
                            <span aria-hidden="true">✓</span>
                            <span>{{ $totals['coupon']->code }} applied</span>
                        </div>
                    @endif
                </section>

                <form method="POST" action="{{ route('checkout.product.start', $product) }}" class="space-y-5">
                    @csrf
                    <section class="panel">
                        <p class="eyebrow">Payment method</p>
                        <div class="mt-3"><x-checkout.payment-methods :card-payments-enabled="$cardPaymentsEnabled" /></div>
                    </section>

                    <section class="overflow-hidden rounded-3xl bg-slate-950 p-6 text-white shadow-xl shadow-slate-300/50">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Order summary</p>
                        <div class="mt-5 space-y-3 border-b border-white/10 pb-5 text-sm">
                            <div class="flex justify-between text-slate-300"><span>Subtotal</span><span>${{ number_format($totals['subtotal'], 2) }}</span></div>
                            @if($totals['discount'] > 0)<div class="flex justify-between text-emerald-300"><span>Coupon discount</span><span>−${{ number_format($totals['discount'], 2) }}</span></div>@endif
                        </div>
                        <div class="mt-5 flex items-baseline justify-between"><span class="font-medium text-slate-300">Total</span><span class="text-3xl font-bold tracking-tight">${{ number_format($totals['total'], 2) }}</span></div>
                        <p class="mt-3 text-xs leading-5 text-slate-400">Your order is delivered only after the selected provider verifies payment.</p>
                        <button type="submit" class="btn-primary mt-6 w-full">Continue to payment</button>
                    </section>
                </form>
            </aside>
        </div>
    </div>
</x-app-layout>
