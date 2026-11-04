<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\Payout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        $available = $request->user()->affiliateCommissions()
            ->where('status', 'approved')->whereNull('payout_id')->sum('amount');
        $payouts = $request->user()->payouts()->where('kind', 'affiliate')->latest()->paginate(15);
        $minimum = (float) config('marketplace.affiliate.minimum_payout', 10);

        return view('affiliate.payouts.index', compact('available', 'payouts', 'minimum'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->affiliate_status === 'active', 403, 'This affiliate account cannot request payouts.');
        $minimum = (float) config('marketplace.affiliate.minimum_payout', 10);

        $payout = DB::transaction(function () use ($request, $minimum) {
            $commissions = AffiliateCommission::query()
                ->where('affiliate_user_id', $request->user()->id)
                ->where('status', 'approved')
                ->whereNull('payout_id')
                ->lockForUpdate()
                ->get();
            $amount = (float) $commissions->sum('amount');
            abort_if($amount < $minimum, 422, 'The minimum affiliate payout is $'.number_format($minimum, 2).'.');

            $payout = Payout::create([
                'user_id' => $request->user()->id,
                'kind' => 'affiliate',
                'amount' => $amount,
                'currency' => 'USD',
                'status' => 'requested',
            ]);
            AffiliateCommission::whereKey($commissions->pluck('id'))->update(['payout_id' => $payout->id]);

            return $payout;
        });

        return redirect()->route('affiliate.payouts.index')->with('success', 'Payout request #'.$payout->id.' was submitted.');
    }
}
