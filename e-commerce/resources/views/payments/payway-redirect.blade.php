<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Secure card payment"
            subtitle="You are being redirected to ABA PayWay's hosted card checkout."
        />
    </x-slot>

    <div class="mx-auto max-w-2xl py-8">
        <section class="panel p-6 sm:p-8" x-data>
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-indigo-50 text-indigo-700">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke-width="1.8"/><path d="M3 10h18M7 15h3" stroke-width="1.8" stroke-linecap="round"/></svg>
            </span>
            <div class="text-center">
                <h2 class="mt-5 text-xl font-bold tracking-tight text-slate-950">Card payment</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Order total: {{ $payment->currency }} {{ number_format($payment->amount, 2) }}</p>
            </div>

            <div data-card-preview class="mt-7 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-950">Card details preview</p>
                        <p class="mt-1 text-xs leading-5 text-slate-500">This presentation interface does not transmit or store these fields.</p>
                    </div>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">Visa · Mastercard</span>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="cardholder-preview" class="text-sm font-semibold text-slate-800">Name on card</label>
                        <input id="cardholder-preview" type="text" autocomplete="off" placeholder="CARDHOLDER NAME" class="field-control mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <label for="card-number-preview" class="text-sm font-semibold text-slate-800">Card number</label>
                        <input id="card-number-preview" type="text" inputmode="numeric" autocomplete="off" maxlength="19" placeholder="0000 0000 0000 0000" class="field-control mt-2" />
                    </div>
                    <div>
                        <label for="card-expiry-preview" class="text-sm font-semibold text-slate-800">Expiry date</label>
                        <input id="card-expiry-preview" type="text" inputmode="numeric" autocomplete="off" maxlength="5" placeholder="MM/YY" class="field-control mt-2" />
                    </div>
                    <div>
                        <label for="card-cvv-preview" class="text-sm font-semibold text-slate-800">Security code</label>
                        <input id="card-cvv-preview" type="password" inputmode="numeric" autocomplete="off" maxlength="4" placeholder="CVV" class="field-control mt-2" />
                    </div>
                </div>

                <div class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-800">
                    For security, enter real or sandbox card details only on the ABA PayWay page that opens next.
                </div>
            </div>

            <form x-ref="paywayForm" method="POST" action="{{ $action }}" class="mt-6">
                @foreach($fields as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}" />
                @endforeach
                <button type="submit" class="btn-primary w-full">Continue to ABA PayWay</button>
            </form>
            <a href="{{ route('buyer.orders.show', $payment->order) }}" class="btn-ghost mt-3 w-full">Return to order</a>
        </section>
    </div>
</x-app-layout>
