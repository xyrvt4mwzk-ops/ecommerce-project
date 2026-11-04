<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Coupons" subtitle="Create and manage marketplace, creator, and product-specific discounts.">
            <x-slot name="actions">
                <a href="{{ route('admin.coupons.create') }}" class="btn-primary">Create coupon</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="space-y-5">
        @if(session('success'))
            <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif

        <form method="GET" class="panel flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
            <label class="sr-only" for="coupon-search">Search coupon codes</label>
            <input id="coupon-search" name="search" value="{{ request('search') }}" placeholder="Search coupon code" class="field-control mt-0 min-w-0 flex-1" />
            <label class="sr-only" for="coupon-status">Coupon status</label>
            <select id="coupon-status" name="status" class="field-control mt-0 sm:w-44">
                <option value="">All statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
            <button class="btn-primary" type="submit">Filter</button>
        </form>

        @if($coupons->count())
            <div class="panel overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold">Code</th>
                                <th class="px-5 py-3 text-left font-semibold">Discount</th>
                                <th class="px-5 py-3 text-left font-semibold">Scope</th>
                                <th class="px-5 py-3 text-left font-semibold">Usage</th>
                                <th class="px-5 py-3 text-left font-semibold">Status</th>
                                <th class="px-5 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($coupons as $coupon)
                                <tr>
                                    <td class="px-5 py-4"><span class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold text-slate-800">{{ $coupon->code }}</span></td>
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $coupon->type === 'percent' ? rtrim(rtrim(number_format($coupon->value, 2), '0'), '.').'%' : '$'.number_format($coupon->value, 2) }}</td>
                                    <td class="px-5 py-4 text-slate-600">
                                        @if($coupon->product){{ $coupon->product->title }}
                                        @elseif($coupon->creator){{ $coupon->creator->name }} store
                                        @else Marketplace-wide
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ number_format($coupon->usage_count) }} / {{ $coupon->usage_limit ? number_format($coupon->usage_limit) : 'Unlimited' }}</td>
                                    <td class="px-5 py-4"><x-ui.status-badge :status="$coupon->is_active ? 'active' : 'disabled'" /></td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-200 hover:text-indigo-700">Edit</a>
                                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete this coupon?')">
                                                @csrf @method('DELETE')
                                                <button class="rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 p-5">{{ $coupons->links() }}</div>
            </div>
        @else
            <x-ui.empty-state title="No coupons yet" message="Create a marketplace-wide or product-specific coupon for buyers to apply in their cart." action-label="Create coupon" :action-href="route('admin.coupons.create')" />
        @endif
    </div>
</x-app-layout>
