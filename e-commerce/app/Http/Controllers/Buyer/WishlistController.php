<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $wishlists = $request->user()->wishlists()->with('product.creator')->latest()->paginate(12);

        return view('buyer.wishlist.index', compact('wishlists'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->status === Product::STATUS_PUBLISHED, 404);
        Wishlist::firstOrCreate(['user_id' => $request->user()->id, 'product_id' => $product->id]);

        return back()->with('success', 'Saved to your wishlist.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $request->user()->wishlists()->where('product_id', $product->id)->delete();

        return back()->with('success', 'Removed from your wishlist.');
    }
}
