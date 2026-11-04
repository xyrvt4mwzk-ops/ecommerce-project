<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CouponService;
use App\Services\PaywayCardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseIntentController extends Controller
{
    public function show(Request $request, Product $product, CouponService $coupons, PaywayCardService $payway): View|RedirectResponse
    {
        abort_unless($product->status === Product::STATUS_PUBLISHED, 404);

        if (! $request->user()->hasRole('buyer')) {
            return redirect()
                ->route('products.show', $product)
                ->with('error', 'Switch to a Buyer account to continue to payment.');
        }

        $totals = $coupons->productTotals($product, $request->session()->get($this->couponSessionKey($product)));
        $cardPaymentsEnabled = $payway->enabled();

        return view('checkout.resume', compact('product', 'totals', 'cardPaymentsEnabled'));
    }

    public function applyCoupon(Request $request, Product $product, CouponService $coupons): RedirectResponse
    {
        abort_unless($product->status === Product::STATUS_PUBLISHED, 404);
        abort_unless($request->user()->hasRole('buyer'), 403);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);
        $eligibility = $coupons->evaluate($data['code'], collect([$product]));
        $coupon = $eligibility['coupon'];

        if (! $coupon) {
            return back()
                ->withInput()
                ->withErrors(['code' => $eligibility['message']]);
        }

        $request->session()->put($this->couponSessionKey($product), $coupon->code);

        return redirect()->route('purchase.resume', $product)->with('success', 'Coupon applied successfully.');
    }

    private function couponSessionKey(Product $product): string
    {
        return 'purchase_coupons.'.$product->id;
    }
}
