<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\Payout;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AffiliateManagementController extends Controller
{
    public function index(): View
    {
        $affiliates = User::whereHas('roles', fn ($query) => $query->where('name', 'affiliate'))
            ->withCount('affiliateReferrals')->withSum('affiliateCommissions', 'amount')->latest()->get();
        $commissions = AffiliateCommission::with(['affiliate', 'order', 'referral.product'])->latest()->take(25)->get();
        $payouts = Payout::with('user')->where('kind', 'affiliate')->latest()->take(25)->get();
        $products = Product::published()->with('creator')->orderBy('title')->get();

        return view('admin.affiliates.index', compact('affiliates', 'commissions', 'payouts', 'products'));
    }

    public function updateRate(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'affiliate_enabled' => ['required', 'boolean'],
            'affiliate_commission_rate' => ['required', 'numeric', 'min:0', 'max:50'],
        ]);
        $product->update($data);

        return back()->with('success', 'Affiliate settings updated for '.$product->title.'.');
    }

    public function updateAffiliate(Request $request, User $affiliate): RedirectResponse
    {
        abort_unless($affiliate->hasRole('affiliate'), 404);
        $data = $request->validate([
            'affiliate_status' => ['required', 'in:active,suspended'],
            'affiliate_fraud_reason' => ['nullable', 'string', 'max:2000'],
        ]);
        $flagged = filled($data['affiliate_fraud_reason'] ?? null);
        $affiliate->update($data + ['affiliate_fraud_flagged_at' => $flagged ? now() : null]);
        $affiliate->affiliateReferrals()->update(['is_active' => $data['affiliate_status'] === 'active']);

        return back()->with('success', 'Affiliate status updated.');
    }

    public function destroy(User $affiliate): RedirectResponse
    {
        abort_unless($affiliate->hasRole('affiliate'), 404);
        $affiliate->affiliateReferrals()->update(['is_active' => false]);
        $affiliate->roles()->detach($affiliate->roles()->where('name', 'affiliate')->value('roles.id'));
        $affiliate->update(['affiliate_status' => 'removed']);

        return back()->with('success', 'The user was removed from the affiliate program. Historical records were preserved.');
    }

    public function updateCommission(Request $request, AffiliateCommission $commission): RedirectResponse
    {
        abort_if($commission->status === 'paid', 422, 'A paid commission cannot be changed.');
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected,flagged'],
            'fraud_reason' => ['nullable', 'required_if:status,flagged', 'string', 'max:2000'],
        ]);
        abort_if($commission->payout_id && $data['status'] !== 'approved', 422, 'A commission in a payout request cannot be changed.');
        $commission->update([
            'status' => $data['status'],
            'approved_at' => $data['status'] === 'approved' ? now() : null,
            'fraud_flagged_at' => $data['status'] === 'flagged' ? now() : null,
            'fraud_reason' => $data['fraud_reason'] ?? null,
        ]);

        return back()->with('success', 'Commission status updated.');
    }

    public function updatePayout(Request $request, Payout $payout): RedirectResponse
    {
        abort_unless($payout->kind === 'affiliate', 404);
        abort_if($payout->status === 'paid', 422, 'A paid payout cannot be changed.');
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected,paid'],
            'reference' => ['nullable', 'required_if:status,paid', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $payout->update($data + ['processed_at' => in_array($data['status'], ['rejected', 'paid'], true) ? now() : null]);

        if ($data['status'] === 'paid') {
            $payout->commissions()->update(['status' => 'paid']);
        } elseif ($data['status'] === 'rejected') {
            $payout->commissions()->update(['status' => 'approved', 'payout_id' => null]);
        }

        return back()->with('success', 'Payout status updated.');
    }
}
