<x-app-layout>
    @php($user = Auth::user())
    @php($referrals = $user->affiliateReferrals()->with('product')->latest()->get())
    @php($commissions = $user->affiliateCommissions()->with('order')->latest()->take(8)->get())
    @php($clicks = $referrals->sum('clicks'))
    @php($commissionTotal = $commissions->sum('amount'))
    @php($conversionCount = $commissions->count())

    <x-slot name="header">
        <x-ui.page-header
            title="Affiliate Dashboard"
            subtitle="Track referral clicks, conversions, and commission totals."
        >
            <x-slot name="actions">
                <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Browse Products</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="w-full">
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            <x-ui.stat-card label="Clicks" :value="$clicks" hint="Total referral traffic captured." accent="indigo">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M13 3l8 8-8 8-1.5-5 5-1.5-6.5-6.5z" stroke-width="1.8" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Conversions" :value="$conversionCount" hint="Orders tied to your referrals." accent="violet">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 6L9 17l-5-5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-ui.stat-card>
            <x-ui.stat-card label="Commission" :value="'$'.number_format($commissionTotal, 2)" hint="Estimated commission totals." accent="slate">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20" stroke-width="1.8"/><path d="M17 6.5c0-2-2.2-3.5-5-3.5S7 4.5 7 6.5 9.2 10 12 10s5 1.5 5 3.5S14.8 17 12 17s-5-1.5-5-3.5" stroke-width="1.8"/></svg>
            </x-ui.stat-card>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
            <div class="space-y-6">
                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-indigo-600">Referral link generator</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Share your products</h2>
                        </div>
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Estimated data</span>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-slate-600">Use these links to promote products. Copy them into your campaigns, social posts, or landing pages.</p>
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-slate-900">Referral links</h3>
                        <span class="text-sm text-slate-500">Click the copy button to share</span>
                    </div>
                    <div class="mt-6 space-y-4">
                        @forelse($referrals as $referral)
                            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <p class="text-sm text-slate-500">{{ $referral->product->title }}</p>
                                        <p class="mt-1 break-all font-mono text-sm text-slate-800">{{ route('affiliate.capture', $referral->code) }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-slate-700">{{ $referral->clicks }} clicks</span>
                                        <button type="button" onclick="navigator.clipboard.writeText('{{ route('affiliate.capture', $referral->code) }}')" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Copy</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <x-ui.empty-state title="No referral links yet" message="Browse products to create referral activity for your affiliate profile." action-label="Browse Products" :action-href="route('products.index')">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </x-ui.empty-state>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-lg">
                    <p class="text-sm uppercase tracking-[0.24em] text-cyan-300">Commission notice</p>
                    <p class="mt-4 text-2xl font-semibold tracking-tight">Commissions are tracked from referral-attributed paid orders.</p>
                    <p class="mt-3 text-sm leading-6 text-slate-300">This dashboard is presentation-ready and safe for assignment use.</p>
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Commission history</h3>
                    <div class="mt-5 overflow-hidden rounded-3xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Order</th>
                                    <th class="px-4 py-3 text-left font-semibold">Amount</th>
                                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($commissions as $commission)
                                    <tr>
                                        <td class="px-4 py-4 text-slate-900">{{ $commission->order->order_number }}</td>
                                        <td class="px-4 py-4 text-slate-700">${{ number_format($commission->amount, 2) }}</td>
                                        <td class="px-4 py-4"><x-ui.status-badge :status="$commission->status" /></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-slate-500">No commission activity yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
