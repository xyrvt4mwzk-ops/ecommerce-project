<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <x-ui.page-header title="Secure checkout" subtitle="Scan the dynamic KHQR before the timer runs out." />
            <a href="{{ route('products.show', $payment->order->items()->firstOrFail()->product) }}" class="btn-secondary shrink-0">Back to product</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8" data-status-url="{{ route('payments.status', $payment) }}" data-success-url="{{ route('checkout.success', $payment->order) }}" data-expires-at="{{ $payment->expires_at?->getTimestamp() }}">
        <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/50 sm:p-9">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-600 via-violet-500 to-cyan-400"></div>
                <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.14em] text-emerald-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> KHQR payment
                        </div>
                        <h2 class="mt-4 text-2xl font-bold tracking-tight text-slate-950">Pay {{ $payment->currency }} {{ number_format($payment->amount, 2) }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Securely payable to {{ config('bakong.merchant_name') }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-left sm:text-right">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Time remaining</p>
                        <p id="payment-countdown" class="mt-1 font-mono text-2xl font-bold tracking-tight text-slate-950">01:00</p>
                    </div>
                </div>

                <div class="mt-8 grid items-center gap-8 lg:grid-cols-[minmax(0,1fr)_18rem]">
                    <div class="order-2 space-y-5 lg:order-1">
                        <div class="flex gap-4">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">1</span>
                            <p class="pt-0.5 text-sm leading-6 text-slate-600">Open <span class="font-semibold text-slate-900">ABA Mobile</span> or another KHQR-enabled banking app.</p>
                        </div>
                        <div class="flex gap-4">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">2</span>
                            <p class="pt-0.5 text-sm leading-6 text-slate-600">Scan this QR and confirm the amount before sending payment.</p>
                        </div>
                        <div class="flex gap-4">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">3</span>
                            <p class="pt-0.5 text-sm leading-6 text-slate-600">Keep this page open. We’ll confirm your payment automatically.</p>
                        </div>
                        <div id="payment-state" class="flex items-center gap-2 rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-amber-500"></span><span>Waiting for payment confirmation…</span>
                        </div>
                    </div>
                    <div class="order-1 mx-auto w-full max-w-[18rem] rounded-3xl border border-slate-200 bg-white p-3 shadow-lg shadow-slate-200/70 lg:order-2">
                        <img src="{{ data_get($payment->metadata, 'qr_image') }}" alt="KHQR code for order {{ $payment->order->order_number }}" class="aspect-square w-full rounded-2xl" />
                    </div>
                </div>

                <form method="POST" action="{{ route('payments.cancel', $payment) }}" class="mt-8 border-t border-slate-100 pt-5">
                    @csrf
                    <button class="btn-ghost px-0 text-slate-500 underline decoration-slate-300 underline-offset-4 hover:bg-transparent">Cancel this payment</button>
                </form>
            </section>

            <aside class="space-y-4 lg:sticky lg:top-6">
                <section class="rounded-3xl bg-slate-950 p-6 text-white shadow-xl shadow-slate-300/50">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Order summary</p>
                    <p class="mt-3 font-mono text-sm text-slate-300">{{ $payment->order->order_number }}</p>
                    <div class="mt-6 space-y-4 border-y border-white/10 py-5">
                        @foreach($payment->order->items as $item)
                            <div class="flex items-start justify-between gap-4 text-sm">
                                <span class="line-clamp-2 leading-5 text-slate-300">{{ $item->product->title }}</span>
                                <span class="shrink-0 font-medium text-white">{{ $payment->currency }} {{ number_format($item->price * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5 flex items-baseline justify-between"><span class="text-sm font-medium text-slate-300">Total</span><span class="text-2xl font-bold">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span></div>
                </section>

                <section class="panel-muted rounded-3xl">
                    <div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm">✓</span><div><p class="text-sm font-semibold text-slate-900">Protected checkout</p><p class="text-xs text-slate-500">Status updates automatically</p></div></div>
                </section>
            </aside>
        </div>
    </div>

    <script>
        (() => {
            const root = document.querySelector('[data-status-url]');
            const state = document.getElementById('payment-state');
            const countdown = document.getElementById('payment-countdown');
            const expiresAt = Number(root.dataset.expiresAt) * 1000;
            let busy = false;
            let stopped = false;
            const tones = {
                amber: 'flex items-center gap-2 rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800',
                emerald: 'flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800',
                rose: 'flex items-center gap-2 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800',
            };
            const dots = { amber: 'bg-amber-500', emerald: 'bg-emerald-500', rose: 'bg-rose-500' };
            const update = (text, tone = 'amber') => { state.className = tones[tone]; state.innerHTML = `<span class="h-2 w-2 rounded-full ${dots[tone]}"></span><span>${text}</span>`; };
            const tick = () => {
                const seconds = Math.max(0, Math.ceil((expiresAt - Date.now()) / 1000));
                countdown.textContent = `${String(Math.floor(seconds / 60)).padStart(2, '0')}:${String(seconds % 60).padStart(2, '0')}`;
                if (seconds === 0) { stopped = true; update('Payment expired. Please start a new checkout.', 'rose'); return; }
                window.setTimeout(tick, 500);
            };
            const check = async () => {
                if (busy || stopped) return;
                busy = true;
                try {
                    const response = await fetch(root.dataset.statusUrl, { headers: { Accept: 'application/json' } });
                    const data = await response.json();
                    if (data.status === 'paid') { update('Payment successful. Preparing your purchase…', 'emerald'); window.location.assign(data.redirect_url || root.dataset.successUrl); return; }
                    if (data.status === 'expired') { stopped = true; update('Payment expired. Please start a new checkout.', 'rose'); return; }
                    if (data.message) update(data.message);
                    window.setTimeout(check, 3500);
                } catch (_) { update('Checking payment status…'); window.setTimeout(check, 5000); }
                finally { busy = false; }
            };
            tick(); check();
        })();
    </script>
</x-app-layout>
