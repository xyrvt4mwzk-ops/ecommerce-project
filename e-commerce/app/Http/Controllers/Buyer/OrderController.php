<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Auth::user()->orders()
            ->with('payment')
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('buyer.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_unless($order->user_id === Auth::id() || Auth::user()->hasRole('super-admin'), 403);

        $order->load('items.product', 'payment', 'licenses');

        return view('buyer.orders.show', compact('order'));
    }
}
