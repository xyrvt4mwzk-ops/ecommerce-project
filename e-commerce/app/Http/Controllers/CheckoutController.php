<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\AffiliateAttributionService;
use App\Services\BakongKhqrService;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\PaywayCardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function startProductCheckout(
        Request $request,
        Product $product,
        BakongKhqrService $khqrService,
        CouponService $coupons,
        PaywayCardService $payway,
        AffiliateAttributionService $attribution,
    ): View|RedirectResponse {
        $method = $request->validate([
            'payment_method' => ['required', 'in:khqr,card'],
        ])['payment_method'];

        if ($method === 'khqr') {
            return $this->startKhqrCheckout($product, $khqrService, $coupons, $attribution);
        }

        return $this->startProductCardCheckout($product, $coupons, $payway, $attribution);
    }

    public function startCartCheckout(
        Request $request,
        CartService $cart,
        BakongKhqrService $khqrService,
        PaywayCardService $payway,
        AffiliateAttributionService $attribution,
    ): View|RedirectResponse {
        $method = $request->validate([
            'payment_method' => ['required', 'in:khqr,card'],
        ])['payment_method'];

        if ($method === 'khqr') {
            return $this->startCartKhqrCheckout($cart, $khqrService, $attribution);
        }

        return $this->startCartCardCheckout($cart, $payway, $attribution);
    }

    public function startCartKhqrCheckout(CartService $cart, BakongKhqrService $khqrService, AffiliateAttributionService $attribution)
    {
        abort_unless(Auth::check() && Auth::user()->hasRole('buyer'), 403);
        $data = $cart->totals();
        abort_if(empty($data['items']), 422, 'Your cart is empty.');
        $affiliate = $attribution->resolveForProducts(Auth::user(), collect($data['items'])->pluck('product.id'));
        $order = DB::transaction(function () use ($data, $affiliate) {
            $order = Order::create(['order_number' => $this->generateOrderNumber(), 'user_id' => Auth::id(), 'affiliate_referral_id' => $affiliate['referral']->id ?? null, 'affiliate_click_id' => $affiliate['click']->id ?? null, 'subtotal' => $data['subtotal'], 'total' => $data['total'], 'status' => 'pending']);
            foreach ($data['items'] as $item) {
                OrderItem::create(['order_id' => $order->id, 'product_id' => $item['product']->id, 'price' => $item['product']->price, 'quantity' => $item['quantity']]);
            }
            Payment::create(['order_id' => $order->id, 'method' => 'khqr', 'status' => 'pending', 'amount' => $data['total'], 'provider' => 'bakong', 'metadata' => ['coupon' => $data['coupon']?->code, 'discount' => $data['discount']]]);
            if ($data['coupon']) {
                $data['coupon']->increment('usage_count');
            }

            return $order->load('payment');
        });
        $khqr = $khqrService->create($order);
        $order->payment->update(['currency' => $khqr['currency'], 'khqr_md5' => $khqr['md5'], 'khqr_payload' => $khqr['payload'], 'expires_at' => $khqr['expires_at'], 'metadata' => array_merge($order->payment->metadata ?? [], ['qr_image' => $khqr['qr_image']])]);

        return redirect()->route('payments.khqr.show', $order->payment);
    }

    public function startKhqrCheckout(Product $product, BakongKhqrService $khqrService, CouponService $coupons, AffiliateAttributionService $attribution)
    {
        abort_unless(Auth::check() && Auth::user()->hasRole('buyer'), 403);

        $couponSessionKey = 'purchase_coupons.'.$product->id;
        $totals = $coupons->productTotals($product, Session::get($couponSessionKey));
        if ($totals['total'] <= 0) {
            return redirect()->route('purchase.resume', $product)
                ->with('error', 'This coupon covers the full price. Free-order fulfillment is not available yet.');
        }

        $affiliate = $attribution->resolveForProducts(Auth::user(), [$product->id]);
        $referral = $affiliate['referral'] ?? null;
        $click = $affiliate['click'] ?? null;

        $order = DB::transaction(function () use ($product, $referral, $click, $totals, $couponSessionKey) {
            $existing = Order::query()
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
                ->whereHas('payment', fn ($query) => $query->where('provider', 'bakong')->where('status', 'pending')->where('amount', $totals['total'])->where('expires_at', '>', now()))
                ->with('payment')
                ->latest()
                ->first();
            if ($existing) {
                return $existing;
            }

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => Auth::id(),
                'affiliate_referral_id' => $referral?->id,
                'affiliate_click_id' => $click?->id,
                'subtotal' => $totals['subtotal'],
                'total' => $totals['total'],
                'status' => 'pending',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => 1,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'method' => 'khqr',
                'status' => 'pending',
                'amount' => $totals['total'],
                'provider' => 'bakong',
                'provider_reference' => null,
                'metadata' => [
                    'product_id' => $product->id,
                    'product_slug' => $product->slug,
                    'affiliate_referral_id' => $referral?->id,
                    'coupon' => $totals['coupon']?->code,
                    'discount' => $totals['discount'],
                ],
            ]);

            if ($totals['coupon']) {
                $totals['coupon']->increment('usage_count');
            }
            Session::forget($couponSessionKey);

            return $order->load('payment');
        });

        if (! $order->payment->khqr_payload) {
            try {
                $khqr = $khqrService->create($order);
            } catch (\RuntimeException $exception) {
                return redirect()->route('purchase.resume', $product)->with('error', $exception->getMessage());
            }
            $order->payment->update([
                'currency' => $khqr['currency'],
                'khqr_md5' => $khqr['md5'],
                'khqr_payload' => $khqr['payload'],
                'expires_at' => $khqr['expires_at'],
                'metadata' => array_merge($order->payment->metadata ?? [], ['qr_image' => $khqr['qr_image']]),
            ]);
            logger()->info('KHQR_CREATED', ['order_id' => $order->id, 'payment_id' => $order->payment->id]);
        }

        return redirect()->route('payments.khqr.show', $order->payment);
    }

    public function success(Order $order, CartService $cart): View
    {
        abort_unless(Auth::id() === $order->user_id || Auth::user()->hasRole('super-admin'), 403);

        $order->load('items.product', 'payment', 'licenses');

        if (Auth::id() === $order->user_id && ($order->status === 'paid' || $order->payment?->status === 'paid')) {
            $cart->removeProducts($order->items->pluck('product_id'));
        }

        return view('checkout.success', compact('order'));
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
    }

    private function startProductCardCheckout(
        Product $product,
        CouponService $coupons,
        PaywayCardService $payway,
        AffiliateAttributionService $attribution,
    ): View|RedirectResponse {
        abort_unless(Auth::check() && Auth::user()->hasRole('buyer'), 403);

        if (! $payway->enabled()) {
            return redirect()->route('purchase.resume', $product)
                ->with('error', 'Card payment is not configured yet. Please choose KHQR or configure ABA PayWay.');
        }

        $couponSessionKey = 'purchase_coupons.'.$product->id;
        $totals = $coupons->productTotals($product, Session::get($couponSessionKey));
        if ($totals['total'] <= 0) {
            return redirect()->route('purchase.resume', $product)
                ->with('error', 'This coupon covers the full price. Free-order fulfillment is not available yet.');
        }

        $affiliate = $attribution->resolveForProducts(Auth::user(), [$product->id]);
        $referral = $affiliate['referral'] ?? null;
        $click = $affiliate['click'] ?? null;

        $payment = DB::transaction(function () use ($product, $totals, $referral, $click, $couponSessionKey) {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => Auth::id(),
                'affiliate_referral_id' => $referral?->id,
                'affiliate_click_id' => $click?->id,
                'subtotal' => $totals['subtotal'],
                'total' => $totals['total'],
                'status' => 'pending',
            ]);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => 1,
            ]);
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => 'card',
                'status' => 'pending',
                'amount' => $totals['total'],
                'currency' => strtoupper((string) config('payway.currency', 'USD')),
                'provider' => 'aba-payway',
                'metadata' => [
                    'product_id' => $product->id,
                    'product_slug' => $product->slug,
                    'affiliate_referral_id' => $referral?->id,
                    'coupon' => $totals['coupon']?->code,
                    'discount' => $totals['discount'],
                ],
            ]);
            if ($totals['coupon']) {
                $totals['coupon']->increment('usage_count');
            }
            Session::forget($couponSessionKey);

            return $payment;
        });

        return $this->paywayRedirect($payment, $payway, route('purchase.resume', $product));
    }

    private function startCartCardCheckout(CartService $cart, PaywayCardService $payway, AffiliateAttributionService $attribution): View|RedirectResponse
    {
        abort_unless(Auth::check() && Auth::user()->hasRole('buyer'), 403);

        if (! $payway->enabled()) {
            return redirect()->route('cart.index')
                ->with('error', 'Card payment is not configured yet. Please choose KHQR or configure ABA PayWay.');
        }

        $data = $cart->totals();
        abort_if(empty($data['items']), 422, 'Your cart is empty.');
        $affiliate = $attribution->resolveForProducts(Auth::user(), collect($data['items'])->pluck('product.id'));

        $payment = DB::transaction(function () use ($data, $affiliate) {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => Auth::id(),
                'affiliate_referral_id' => $affiliate['referral']->id ?? null,
                'affiliate_click_id' => $affiliate['click']->id ?? null,
                'subtotal' => $data['subtotal'],
                'total' => $data['total'],
                'status' => 'pending',
            ]);
            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'price' => $item['product']->price,
                    'quantity' => $item['quantity'],
                ]);
            }
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => 'card',
                'status' => 'pending',
                'amount' => $data['total'],
                'currency' => strtoupper((string) config('payway.currency', 'USD')),
                'provider' => 'aba-payway',
                'metadata' => ['coupon' => $data['coupon']?->code, 'discount' => $data['discount']],
            ]);
            if ($data['coupon']) {
                $data['coupon']->increment('usage_count');
            }

            return $payment;
        });

        return $this->paywayRedirect($payment, $payway, route('cart.index'));
    }

    private function paywayRedirect(Payment $payment, PaywayCardService $payway, string $failureUrl): View|RedirectResponse
    {
        try {
            $checkout = $payway->hostedCheckout($payment);
        } catch (\RuntimeException $exception) {
            $payment->update(['status' => 'failed']);
            $payment->order->update(['status' => 'failed']);

            return redirect()->to($failureUrl)->with('error', $exception->getMessage());
        }

        return view('payments.payway-redirect', [
            'payment' => $payment,
            'action' => $checkout['action'],
            'fields' => $checkout['fields'],
        ]);
    }
}
