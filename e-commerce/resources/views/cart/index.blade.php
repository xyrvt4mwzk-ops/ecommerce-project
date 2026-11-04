<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div><div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-50 text-indigo-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 1.9-1.5L19 8H7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="20" r="1.2" fill="currentColor"/><circle cx="17" cy="20" r="1.2" fill="currentColor"/></svg></span><div><p class="eyebrow">Checkout</p><h1 class="text-2xl font-bold tracking-tight text-slate-950">Your cart</h1></div></div><p class="mt-3 text-sm text-slate-600">Review your selections, apply a discount, then choose card or KHQR payment.</p></div>
            <a href="{{ route('products.index') }}" class="btn-secondary shrink-0">Continue browsing</a>
        </div>
    </x-slot>

    <div class="page-wrap">
        @if($cart['items'])
            <div class="grid items-start gap-7 lg:grid-cols-[minmax(0,1fr)_23rem]">
                <section class="panel">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                        <div>
                            <p class="eyebrow">Your selection</p>
                            <h2 class="mt-1 text-xl font-bold tracking-tight text-slate-950">{{ count($cart['items']) }} {{ \Illuminate\Support\Str::plural('product', count($cart['items'])) }} ready to check out</h2>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Instant delivery</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($cart['items'] as $item)
                            <article class="flex gap-4 py-5 sm:items-center">
                                <a href="{{ route('products.show', $item['product']) }}" class="h-20 w-28 shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 sm:h-24 sm:w-36">
                                    <x-ui.product-cover :product="$item['product']" class="h-full w-full" />
                                </a>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Digital product</p>
                                    <a href="{{ route('products.show', $item['product']) }}" class="mt-1 block truncate font-semibold text-slate-950 hover:text-indigo-700">{{ $item['product']->title }}</a>
                                    <p class="mt-1 text-sm text-slate-500">By {{ $item['product']->creator->name }}</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900">${{ number_format($item['product']->price, 2) }}</p>
                                </div>
                                <form method="POST" action="{{ route('cart.remove', $item['product']) }}" class="self-start sm:self-center">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-ghost px-2.5 text-rose-600 hover:bg-rose-50 hover:text-rose-700" aria-label="Remove {{ $item['product']->title }}">Remove</button>
                                </form>
                            </article>
                        @endforeach
                    </div>
                    <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-slate-500">Your cart stays saved while you browse and until payment is confirmed.</p>
                        <a href="{{ route('products.index') }}" class="btn-secondary shrink-0">
                            <span aria-hidden="true">+</span> Add more products
                        </a>
                    </div>
                </section>

                <aside class="space-y-5 lg:sticky lg:top-24">
                    <section class="panel">
                        <p class="eyebrow">Discount code</p>
                        <p class="mt-1 text-sm text-slate-600">Have a creator coupon? Apply it before payment.</p>
                        <form method="POST" action="{{ route('cart.coupon') }}" class="mt-4 flex gap-2">
                            @csrf
                            <label class="sr-only" for="coupon-code">Coupon code</label>
                            <input id="coupon-code" name="code" value="{{ old('code', $cart['coupon']?->code) }}" placeholder="Enter code" class="field-control mt-0 min-w-0 uppercase" />
                            <button class="btn-secondary shrink-0">Apply</button>
                        </form>
                        @error('code')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                        @if($cart['coupon'])
                            <div class="mt-3 flex items-center gap-2 rounded-xl bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700"><span>✓</span> {{ $cart['coupon']->code }} applied</div>
                        @endif
                    </section>

                    @auth
                        @if(Auth::user()->hasRole('buyer'))
                            <form method="POST" action="{{ route('checkout.cart.start') }}" class="space-y-5">
                                @csrf
                                <section class="panel">
                                    <p class="eyebrow">Payment method</p>
                                    <div class="mt-3"><x-checkout.payment-methods :card-payments-enabled="$cardPaymentsEnabled" /></div>
                                </section>
                    @endif
                    @endauth

                    <section class="overflow-hidden rounded-3xl bg-slate-950 p-6 text-white shadow-xl shadow-slate-300/50">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Order summary</p>
                        <div class="mt-5 space-y-3 border-b border-white/10 pb-5 text-sm">
                            <div class="flex justify-between text-slate-300"><span>Subtotal</span><span>${{ number_format($cart['subtotal'], 2) }}</span></div>
                            @if($cart['discount'] > 0)<div class="flex justify-between text-emerald-300"><span>Discount</span><span>−${{ number_format($cart['discount'], 2) }}</span></div>@endif
                        </div>
                        <div class="mt-5 flex items-baseline justify-between"><span class="font-medium text-slate-300">Total</span><span class="text-3xl font-bold tracking-tight">${{ number_format($cart['total'], 2) }}</span></div>
                        <p class="mt-3 text-xs leading-5 text-slate-400">One secure payment. Your downloads and license records are released after confirmation.</p>
                        @auth
                            @if(Auth::user()->hasRole('buyer'))
                                <button type="submit" class="btn-primary mt-6 w-full">Continue to payment</button>
                            @else
                                <p class="mt-6 rounded-xl bg-amber-400/10 px-4 py-3 text-sm text-amber-200">Choose the Buyer role before checkout.</p>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-primary mt-6 w-full">Log in to continue</a>
                        @endauth
                    </section>
                    @auth
                        @if(Auth::user()->hasRole('buyer'))
                            </form>
                        @endif
                    @endauth
                </aside>
            </div>
        @else
            <section class="soft-grid overflow-hidden rounded-[2rem] border border-slate-200 bg-white px-6 py-16 text-center shadow-sm sm:px-10">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-indigo-50 text-2xl text-indigo-600">🛒</div>
                <h1 class="mt-6 text-2xl font-bold tracking-tight text-slate-950">Your cart is waiting for something great.</h1>
                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-600">Discover digital products from independent creators and add the ones you want to purchase together.</p>
                <a href="{{ route('products.index') }}" class="btn-primary mt-7">Browse the marketplace</a>
            </section>
        @endif
    </div>
</x-app-layout>
