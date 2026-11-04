<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Collection;

class CouponService
{
    public function findForProduct(?string $code, Product $product): ?Coupon
    {
        return $this->evaluate($code, collect([$product]))['coupon'];
    }

    public function evaluate(?string $code, Collection $products): array
    {
        $normalizedCode = strtoupper(trim((string) $code));
        if ($normalizedCode === '') {
            return ['coupon' => null, 'message' => 'Enter a coupon code.'];
        }

        $coupon = Coupon::query()->where('code', $normalizedCode)->first();
        if (! $coupon) {
            return ['coupon' => null, 'message' => 'This coupon code does not exist.'];
        }
        if (! $coupon->is_active) {
            return ['coupon' => null, 'message' => 'This coupon is currently disabled.'];
        }
        if ($coupon->starts_at?->isFuture()) {
            return [
                'coupon' => null,
                'message' => 'This coupon starts on '.$coupon->starts_at->format('M j, Y g:i A').'.',
            ];
        }
        if ($coupon->ends_at?->isPast()) {
            return ['coupon' => null, 'message' => 'This coupon has expired.'];
        }
        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            return ['coupon' => null, 'message' => 'This coupon has reached its usage limit.'];
        }

        $eligible = $products->contains(function (Product $product) use ($coupon): bool {
            if ($coupon->product_id && (int) $coupon->product_id !== (int) $product->id) {
                return false;
            }

            return ! $coupon->creator_id || (int) $coupon->creator_id === (int) $product->user_id;
        });

        if (! $eligible) {
            return ['coupon' => null, 'message' => 'This coupon does not apply to the selected product.'];
        }

        return ['coupon' => $coupon, 'message' => null];
    }

    public function productTotals(Product $product, ?string $code): array
    {
        $subtotal = round((float) $product->price, 2);
        $coupon = $this->findForProduct($code, $product);
        $discount = 0.0;

        if ($coupon) {
            $discount = $coupon->type === 'percent'
                ? $subtotal * (float) $coupon->value / 100
                : min($subtotal, (float) $coupon->value);
        }

        $discount = round(min($subtotal, $discount), 2);

        return [
            'subtotal' => $subtotal,
            'coupon' => $coupon,
            'discount' => $discount,
            'total' => round(max(0, $subtotal - $discount), 2),
        ];
    }
}
