<?php

namespace App\Services;

use App\Models\AffiliateClick;
use App\Models\AffiliateReferral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AffiliateAttributionService
{
    public function capture(AffiliateReferral $referral, Request $request): AffiliateClick
    {
        $ipHash = $this->hash((string) $request->ip());
        $sessionHash = $this->hash($request->session()->getId());
        $reason = null;

        if (! $referral->is_active || $referral->affiliate->affiliate_status !== 'active') {
            $reason = 'inactive_affiliate';
        } elseif ($request->user()?->id === $referral->affiliate_user_id) {
            $reason = 'self_referral';
        } elseif ($request->user()?->id === $referral->product->user_id) {
            $reason = 'creator_self_referral';
        } elseif (AffiliateClick::query()
            ->where('affiliate_referral_id', $referral->id)
            ->where(fn ($query) => $query->where('session_hash', $sessionHash)->orWhere('ip_hash', $ipHash))
            ->where('created_at', '>=', now()->subMinutes(config('marketplace.affiliate.duplicate_click_minutes', 10)))
            ->exists()) {
            $reason = 'repeated_click';
        }

        $click = AffiliateClick::create([
            'affiliate_referral_id' => $referral->id,
            'user_id' => $request->user()?->id,
            'ip_hash' => $ipHash,
            'session_hash' => $sessionHash,
            'user_agent' => str($request->userAgent())->limit(500)->toString(),
            'is_suspicious' => $reason !== null,
            'suspicion_reason' => $reason,
        ]);

        if ($reason === null) {
            $referral->increment('clicks');
            Session::put([
                'affiliate_referral_id' => $referral->id,
                'affiliate_click_id' => $click->id,
                'affiliate_attributed_at' => now()->timestamp,
            ]);
        }

        return $click;
    }

    public function resolveForProducts(User $buyer, iterable $productIds): ?array
    {
        $attributedAt = (int) Session::get('affiliate_attributed_at', 0);
        $oldest = now()->subDays(config('marketplace.affiliate.attribution_days', 30))->timestamp;
        if ($attributedAt < $oldest) {
            $this->forget();

            return null;
        }

        $click = AffiliateClick::query()
            ->with('referral.product', 'referral.affiliate')
            ->whereKey(Session::get('affiliate_click_id'))
            ->where('affiliate_referral_id', Session::get('affiliate_referral_id'))
            ->where('is_suspicious', false)
            ->whereNull('converted_at')
            ->first();

        $ids = collect($productIds)->map(fn ($id) => (int) $id);
        if (! $click
            || ! $click->referral->is_active
            || $click->referral->affiliate->affiliate_status !== 'active'
            || ! $ids->contains($click->referral->product_id)
            || $buyer->id === $click->referral->affiliate_user_id
            || $buyer->id === $click->referral->product->user_id) {
            $this->forget();

            return null;
        }

        return ['referral' => $click->referral, 'click' => $click];
    }

    public function forget(): void
    {
        Session::forget(['affiliate_referral_id', 'affiliate_click_id', 'affiliate_attributed_at']);
    }

    private function hash(string $value): string
    {
        return hash_hmac('sha256', $value, (string) config('app.key'));
    }
}
