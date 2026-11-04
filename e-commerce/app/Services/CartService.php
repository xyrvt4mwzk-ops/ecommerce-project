<?php

namespace App\Services;

use App\Models\Product;

class CartService
{
    public function __construct(private readonly CouponService $coupons) {}

    public function items(): array
    {
        $rows = session('cart', []);

        return Product::published()
            ->with('creator')
            ->whereIn('id', array_keys($rows))
            ->get()
            ->map(fn ($product) => [
                'product' => $product,
                'quantity' => (int) $rows[$product->id],
            ])
            ->all();
    }

    public function add(Product $product): void
    {
        $rows = session('cart', []);
        $rows[$product->getKey()] = 1;

        session()->put('cart', $rows);
    }

    public function remove(Product $product): void
    {
        $rows = session('cart', []);
        unset($rows[$product->getKey()]);

        session()->put('cart', $rows);

        if ($rows === []) {
            session()->forget('cart_coupon');
        }
    }

    public function removeProducts(iterable $productIds): void
    {
        $rows = session('cart', []);

        foreach ($productIds as $productId) {
            unset($rows[(int) $productId], $rows[(string) $productId]);
        }

        if ($rows === []) {
            session()->forget(['cart', 'cart_coupon']);

            return;
        }

        session()->put('cart', $rows);
        session()->forget('cart_coupon');
    }

    public function totals(): array
    {
        $items = $this->items();
        $subtotal = collect($items)->sum(fn ($item) => $item['product']->price * $item['quantity']);
        $products = collect($items)->pluck('product');
        $coupon = $this->coupons->evaluate((string) session('cart_coupon'), $products)['coupon'];
        $discount = 0;

        if ($coupon && (! $coupon->usage_limit || $coupon->usage_count < $coupon->usage_limit)) {
            $eligibleSubtotal = collect($items)
                ->filter(function ($item) use ($coupon): bool {
                    if ($coupon->product_id) {
                        return $item['product']->id === $coupon->product_id;
                    }

                    if ($coupon->creator_id) {
                        return $item['product']->user_id === $coupon->creator_id;
                    }

                    return true;
                })
                ->sum(fn ($item) => $item['product']->price * $item['quantity']);

            if ($eligibleSubtotal <= 0) {
                $coupon = null;
            } else {
                $discount = $coupon->type === 'percent'
                    ? $eligibleSubtotal * $coupon->value / 100
                    : min($eligibleSubtotal, $coupon->value);
            }
        }

        return compact('items', 'subtotal', 'coupon', 'discount') + [
            'total' => max(0, $subtotal - $discount),
        ];
    }
}
