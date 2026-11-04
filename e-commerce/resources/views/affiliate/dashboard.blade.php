<x-app-layout>
    @php($workspace = [
        ['label' => 'Overview', 'href' => route('affiliate.dashboard'), 'active' => true],
        ['label' => 'Marketplace', 'href' => route('products.index')],
        ['label' => 'Profile settings', 'href' => route('profile.edit')],
    ])
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Affiliate Dashboard') }}</h2>
                <p class="mt-1 text-sm text-gray-600">Manage referrals, view commissions, and grow your clicks.</p>
            </div>
            <span class="inline-flex items-center rounded-2xl bg-indigo-100 px-4 py-2 text-sm font-semibold text-indigo-700">Referral analytics</span>
        </div>
    </x-slot>

    <x-ui.dashboard-shell title="Affiliate hub" subtitle="Links and earnings" :items="$workspace">
        <div class="space-y-6">
            <div class="grid gap-6 xl:grid-cols-[0.9fr,0.6fr]">
                <div class="rounded-3xl bg-white p-6 shadow-sm">
                    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm text-slate-500">Total clicks</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary['clicks'] }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm text-slate-500">Commissions</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">${{ number_format($summary['commissions'], 2) }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm text-slate-500">Conversions</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary['converted'] }}</p>
                        </div>
                    </div>
                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-emerald-50 p-4"><div><p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Available for payout</p><p class="mt-1 text-xl font-bold text-emerald-950">${{ number_format($summary['available'], 2) }}</p></div><a href="{{ route('affiliate.payouts.index') }}" class="btn-secondary">Manage payouts</a></div>
                </div>
                <div class="rounded-3xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Referral link</h3>
                    <p class="mt-3 text-sm text-slate-600">Copy your affiliate links and share them across channels. Visitor clicks and paid orders are tracked for commission reporting.</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-3xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Referral links</h3>
                    <div class="mt-4 space-y-4">
                        @forelse($referrals as $referral)
                            <div class="rounded-3xl border border-gray-200 bg-slate-50 p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-slate-500">{{ $referral->product->title }}</p>
                                        <p class="mt-2 text-sm text-slate-700">{{ route('affiliate.capture', $referral->code) }}</p>
                                    </div>
                                    <div class="text-right text-sm text-slate-500">
                                        <p>Clicks: {{ $referral->clicks }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-slate-600">No referral links yet. Browse products to create links after you select your affiliate role.</div>
                        @endforelse
                    </div>
                </div>
                <div class="rounded-3xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Recent commissions</h3>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        @forelse($commissions as $commission)
                            <div class="rounded-3xl border border-gray-200 bg-slate-50 p-4">
                                <p>{{ $commission->order->order_number }} - ${{ number_format($commission->amount, 2) }}</p>
                                <p class="mt-1 text-xs text-slate-500">Rate {{ number_format($commission->rate, 2) }}% • {{ ucfirst($commission->status) }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-600">No commission activity yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </x-ui.dashboard-shell>
</x-app-layout>
