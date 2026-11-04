<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateReferral;
use App\Models\Product;
use App\Services\AffiliateAttributionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $referrals = $user->affiliateReferrals()->with('product')->get();
        $commissions = $user->affiliateCommissions()->with('order')->latest()->limit(10)->get();

        $summary = [
            'clicks' => $referrals->sum('clicks'),
            'commissions' => $user->affiliateCommissions()->sum('amount'),
            'converted' => $user->affiliateCommissions()->count(),
            'available' => $user->affiliateCommissions()->where('status', 'approved')->whereNull('payout_id')->sum('amount'),
            'pending' => $user->affiliateCommissions()->where('status', 'pending')->sum('amount'),
        ];

        return view('affiliate.dashboard', compact('referrals', 'commissions', 'summary'));
    }

    public function products(): View
    {
        $products = Product::query()
            ->published()
            ->where('affiliate_enabled', true)
            ->with('creator')
            ->latest()
            ->paginate(12);

        $referrals = Auth::user()
            ->affiliateReferrals()
            ->whereIn('product_id', $products->getCollection()->pluck('id'))
            ->get()
            ->keyBy('product_id');

        return view('affiliate.products.index', compact('products', 'referrals'));
    }

    public function storeReferral(Product $product): RedirectResponse
    {
        abort_unless($product->status === Product::STATUS_PUBLISHED, 404);
        abort_unless($product->affiliate_enabled && Auth::user()->affiliate_status === 'active', 403);

        AffiliateReferral::firstOrCreate(
            [
                'affiliate_user_id' => Auth::id(),
                'product_id' => $product->id,
            ],
            [
                'code' => $this->uniqueReferralCode(),
                'clicks' => 0,
            ],
        );

        return back()->with('success', 'Your referral link is ready to share.');
    }

    public function capture(Request $request, string $code, AffiliateAttributionService $attribution)
    {
        $referral = AffiliateReferral::with('affiliate', 'product')->where('code', $code)->firstOrFail();
        $attribution->capture($referral, $request);

        return redirect()->route('products.show', $referral->product->slug);
    }

    private function uniqueReferralCode(): string
    {
        do {
            $code = Str::lower(Str::random(16));
        } while (AffiliateReferral::where('code', $code)->exists());

        return $code;
    }
}
