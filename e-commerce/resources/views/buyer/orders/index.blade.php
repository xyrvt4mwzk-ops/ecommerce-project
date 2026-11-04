<x-app-layout>
    <x-slot name="header"><x-ui.page-header title="Orders" subtitle="Review your marketplace purchases and payment status." /></x-slot>

    <div class="w-full">
        @if($orders->isNotEmpty())
            <div class="table-shell">
                <table class="data-table">
                    <thead><tr><th>Order</th><th>Items</th><th>Total</th><th>Status</th><th>Placed</th><th></th></tr></thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $order->order_number }}</td>
                                <td>{{ $order->items_count }}</td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td><x-ui.status-badge :status="$order->status" /></td>
                                <td>{{ $order->created_at->format('M j, Y') }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                                        @if($order->payment?->hasActiveKhqr())
                                            <a href="{{ route('payments.khqr.show', $order->payment) }}" class="btn-primary px-3 py-2 text-xs">Pay now</a>
                                        @elseif($order->payment?->canRetry())
                                            <form method="POST" action="{{ route('payments.retry', $order->payment) }}">
                                                @csrf
                                                <button type="submit" class="btn-primary px-3 py-2 text-xs">Pay again</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('buyer.orders.show', $order) }}" class="font-semibold text-indigo-700">View order</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-8">{{ $orders->links() }}</div>
        @else
            <x-ui.empty-state title="No orders yet" message="Your completed and pending purchases will appear here." action-label="Browse products" :action-href="route('products.index')" />
        @endif
    </div>
</x-app-layout>
