<x-app-layout>
    @php($navItems = [
        ['label' => 'Overview', 'href' => route('admin.dashboard'), 'active' => true],
        ['label' => 'Products', 'href' => route('admin.products.index')],
        ['label' => 'Orders', 'href' => route('admin.orders.index')],
        ['label' => 'Roles', 'href' => route('admin.roles.index')],
    ])

    <x-slot name="header">
        <x-ui.page-header
            title="Admin Dashboard"
            subtitle="A distinct, sidebar-driven control center for super-admins."
        >
            <x-slot name="actions">
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Manage Products</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="w-full">
        <div class="grid gap-6 xl:grid-cols-[0.28fr,0.72fr]">
            <x-ui.sidebar title="Super Admin" subtitle="Platform control center" :items="$navItems">
                Admin-only access for product moderation, role management, and marketplace oversight.
            </x-ui.sidebar>

            <div class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-5">
                    @foreach([
                        ['label' => 'Users', 'value' => $summary['users'], 'accent' => 'indigo'],
                        ['label' => 'Products', 'value' => $summary['products'], 'accent' => 'violet'],
                        ['label' => 'Orders', 'value' => $summary['orders'], 'accent' => 'slate'],
                        ['label' => 'Revenue', 'value' => '$'.number_format($summary['revenue'], 2), 'accent' => 'indigo'],
                        ['label' => 'Affiliates', 'value' => $summary['affiliates'], 'accent' => 'violet'],
                    ] as $stat)
                        <x-ui.stat-card :label="$stat['label']" :value="$stat['value']" :accent="$stat['accent']">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </x-ui.stat-card>
                    @endforeach
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                        <h3 class="text-lg font-semibold text-slate-900">Recent users</h3>
                        <div class="mt-5 overflow-hidden rounded-3xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-slate-600">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                                        <th class="px-4 py-3 text-left font-semibold">Email</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td class="px-4 py-4 text-slate-900">{{ $user->name }}</td>
                                            <td class="px-4 py-4 text-slate-600">{{ $user->email }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                        <h3 class="text-lg font-semibold text-slate-900">Recent orders</h3>
                        <div class="mt-5 space-y-4">
                            @foreach($recentOrders as $order)
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <p class="text-sm text-slate-500">{{ $order->order_number }}</p>
                                            <p class="mt-1 font-semibold text-slate-900">{{ $order->user->name }}</p>
                                            <p class="mt-2 text-sm text-slate-600">${{ number_format($order->total, 2) }} • {{ ucfirst($order->status) }}</p>
                                        </div>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Open</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                        <h3 class="text-lg font-semibold text-slate-900">Product moderation</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Manage product status and keep the marketplace presentation-ready.</p>
                        <a href="{{ route('admin.products.index') }}" class="mt-6 inline-flex items-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white">Open moderation</a>
                    </div>
                    <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                        <h3 class="text-lg font-semibold text-slate-900">Role management</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Assign platform roles while keeping super-admin access protected.</p>
                        <a href="{{ route('admin.roles.index') }}" class="mt-6 inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Manage roles</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
