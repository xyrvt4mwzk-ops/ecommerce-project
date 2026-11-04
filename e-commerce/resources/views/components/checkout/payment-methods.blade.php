@props(['cardPaymentsEnabled' => false])

<fieldset>
    <legend class="text-sm font-semibold text-slate-900">Choose a payment method</legend>
    <div class="mt-3 grid gap-3 sm:grid-cols-2">
        <label class="relative cursor-pointer">
            <input type="radio" name="payment_method" value="khqr" class="peer sr-only" @checked(old('payment_method', 'khqr') === 'khqr') />
            <span class="flex h-full items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-100">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-red-50 text-sm font-black text-red-600">KHQR</span>
                <span>
                    <span class="block text-sm font-bold text-slate-950">Pay with KHQR</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">Scan using ABA Mobile or another KHQR banking app.</span>
                </span>
            </span>
        </label>

        <label class="relative {{ $cardPaymentsEnabled ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}">
            <input type="radio" name="payment_method" value="card" class="peer sr-only" @checked(old('payment_method') === 'card') @disabled(! $cardPaymentsEnabled) />
            <span class="flex h-full items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-100">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-indigo-50 text-indigo-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke-width="1.8"/><path d="M3 10h18M7 15h3" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <span>
                    <span class="block text-sm font-bold text-slate-950">Credit or debit card</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">Secure ABA PayWay checkout for Visa, Mastercard, UnionPay, and JCB.</span>
                    @unless($cardPaymentsEnabled)
                        <span class="mt-1 block text-xs font-semibold text-amber-700">PayWay configuration required</span>
                    @endunless
                </span>
            </span>
        </label>
    </div>
    @error('payment_method')
        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
    @enderror
</fieldset>
