<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="All Orders"
            subtitle="Review marketplace orders and payment activity."
        />
    </x-slot>

    <div class="w-full">
        <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Order #</th>
                            <th class="px-4 py-3 text-left font-semibold">Buyer</th>
                            <th class="px-4 py-3 text-left font-semibold">Total</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Payment</th>
                            <th class="px-4 py-3 text-left font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($orders as $order)
                            <tr>
                                <td class="px-4 py-4 font-medium text-slate-900">{{ $order->order_number }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $order->user->name }}</td>
                                <td class="px-4 py-4 text-slate-600">${{ number_format($order->total, 2) }}</td>
                                <td class="px-4 py-4"><x-ui.status-badge :status="$order->status" /></td>
                                <td class="px-4 py-4 text-slate-600">{{ ucfirst($order->payment?->status ?? 'none') }}</td>
                                <td class="px-4 py-4">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-xs font-semibold text-white">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
</x-app-layout>
