<x-app-layout>
    @php($workspace = [
        ['label' => 'Overview', 'href' => route('admin.dashboard'), 'active' => true],
        ['label' => 'Products', 'href' => route('admin.products.index')],
        ['label' => 'Orders', 'href' => route('admin.orders.index')],
        ['label' => 'Roles', 'href' => route('admin.roles.index')],
        ['label' => 'Profile settings', 'href' => route('profile.edit')],
    ])
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-slate-900">{{ __('Admin Dashboard') }}</h2>
                <p class="mt-1 text-sm text-slate-600">Overview of users, products, orders, and revenue.</p>
            </div>
        </div>
    </x-slot>

    <x-ui.dashboard-shell title="Admin console" subtitle="Marketplace operations" :items="$workspace">
        <div class="space-y-6">
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    <p class="text-sm text-slate-500">Users</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary['users'] }}</p>
                </div>
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    <p class="text-sm text-slate-500">Creators</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary['creators'] }}</p>
                </div>
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    <p class="text-sm text-slate-500">Affiliates</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary['affiliates'] }}</p>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    <p class="text-sm text-slate-500">Products</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary['products'] }}</p>
                </div>
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    <p class="text-sm text-slate-500">Orders</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $summary['orders'] }}</p>
                </div>
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    <p class="text-sm text-slate-500">Revenue</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">${{ number_format($summary['revenue'], 2) }}</p>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <p class="text-sm text-slate-500">Affiliate commissions</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">${{ number_format($totalCommissions, 2) }}</p>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Recent users</h3>
                    <div class="mt-4 space-y-4 text-sm text-slate-600">
                        @foreach($recentUsers as $user)
                            <div class="rounded-3xl border border-gray-200 bg-slate-50 p-4">
                                <p class="font-medium text-slate-900">{{ $user->name }}</p>
                                <p>{{ $user->email }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Recent orders</h3>
                    <div class="mt-4 space-y-4 text-sm text-slate-600">
                        @foreach($recentOrders as $order)
                            <div class="rounded-3xl border border-gray-200 bg-slate-50 p-4">
                                <p class="font-medium text-slate-900">{{ $order->order_number }}</p>
                                <p>Buyer: {{ $order->user->name }}</p>
                                <p>${{ number_format($order->total, 2) }} • {{ ucfirst($order->status) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Recent commissions</h3>
                    <div class="mt-4 space-y-4 text-sm text-slate-600">
                        @foreach($recentCommissions as $commission)
                            <div class="rounded-3xl border border-gray-200 bg-slate-50 p-4">
                                <p class="font-medium text-slate-900">{{ $commission->affiliate->name }}</p>
                                <p>Order: {{ $commission->order->order_number }}</p>
                                <p>${{ number_format($commission->amount, 2) }} • {{ ucfirst($commission->status) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </x-ui.dashboard-shell>
</x-app-layout>
