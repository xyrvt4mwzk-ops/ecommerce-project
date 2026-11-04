<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\PaywayCardService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(CartService $cart, PaywayCardService $payway)
    {
        return view('cart.index', [
            'cart' => $cart->totals(),
            'cardPaymentsEnabled' => $payway->enabled(),
        ]);
    }

    public function add(Request $request, Product $product, CartService $cart)
    {
        abort_unless($product->status === Product::STATUS_PUBLISHED, 404);
        $cart->add($product);

        if ($request->string('redirect')->toString() === 'cart') {
            return redirect()->route('cart.index')->with('success', 'Added to cart. Review all items before payment.');
        }

        return back()->with('success', 'Added to cart.');
    }

    public function remove(Product $product, CartService $cart)
    {
        $cart->remove($product);

        return redirect()->route('cart.index')->with('success', 'Removed from cart.');
    }

    public function coupon(Request $request, CartService $cart, CouponService $coupons)
    {
        $data = $request->validate(['code' => 'required|string|max:50']);
        $code = strtoupper($data['code']);
        $products = collect($cart->items())->pluck('product');
        if ($products->isEmpty()) {
            return back()->withErrors(['code' => 'Add a product to your cart before applying a coupon.']);
        }

        $eligibility = $coupons->evaluate($code, $products);
        $coupon = $eligibility['coupon'];

        if (! $coupon) {
            return back()->withInput()->withErrors(['code' => $eligibility['message']]);
        }

        session(['cart_coupon' => $code]);

        return redirect()->route('cart.index')->with('success', 'Coupon applied successfully.');
    }
}
