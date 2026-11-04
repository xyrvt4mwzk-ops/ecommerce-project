<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();
        abort_unless($product->status === Product::STATUS_PUBLISHED, 404);
        abort_unless($user->licenses()->where('product_id', $product->id)->where('status', 'active')->exists(), 403);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['nullable', 'string', 'max:2000'],
        ]);

        Review::updateOrCreate(
            ['product_id' => $product->id, 'user_id' => $user->id],
            $data,
        );

        return back()->with('success', 'Your review has been saved.');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        abort_unless($request->user()->id === $review->user_id || $request->user()->hasRole('super-admin'), 403);
        $review->delete();

        return back()->with('success', 'Your review has been deleted.');
    }

    public function respond(Request $request, Review $review): RedirectResponse
    {
        abort_unless(
            $request->user()->hasRole('super-admin') || $review->product()->where('user_id', $request->user()->id)->exists(),
            403,
        );

        $data = $request->validate(['creator_response' => ['required', 'string', 'max:2000']]);
        $review->update($data + ['creator_responded_at' => now()]);

        return back()->with('success', 'Your response has been published.');
    }
}
