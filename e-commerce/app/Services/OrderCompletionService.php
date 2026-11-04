<?php

namespace App\Services;

use App\Models\AffiliateCommission;
use App\Models\License;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderCompletionService
{
    public function completeBakongPayment(Payment $payment, array $transaction, array $verificationResponse): Payment
    {
        $transactionId = (string) data_get($transaction, 'hash');
        abort_if($transactionId === '', 422, 'Bakong transaction reference is missing.');

        return $this->completeVerifiedPayment($payment, $transactionId, $verificationResponse);
    }

    public function completeVerifiedPayment(Payment $payment, string $transactionId, array $verificationResponse): Payment
    {
        return DB::transaction(function () use ($payment, $transactionId, $verificationResponse) {
            $payment = Payment::query()->lockForUpdate()->with('order.items.product', 'order.referral.affiliate', 'order.affiliateClick')->findOrFail($payment->id);
            if ($payment->status === 'paid') {
                return $payment;
            }

            abort_if($transactionId === '', 422, 'Verified transaction reference is missing.');
            $used = Payment::query()->where('transaction_id', $transactionId)->whereKeyNot($payment->id)->exists();
            abort_if($used, 422, 'This transaction has already been used.');

            $payment->update([
                'status' => 'paid',
                'transaction_id' => $transactionId,
                'provider_reference' => $payment->provider_reference ?: $transactionId,
                'paid_at' => now(),
                'verified_at' => now(),
                'verification_response' => $verificationResponse,
            ]);

            $order = $payment->order;
            $order->update(['status' => 'paid']);
            foreach ($order->items as $item) {
                License::firstOrCreate([
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'user_id' => $order->user_id,
                ], ['license_key' => implode('-', [Str::upper(Str::random(4)), Str::upper(Str::random(4)), Str::upper(Str::random(4)), Str::upper(Str::random(4))])]);
            }
            $referredItem = $order->referral
                ? $order->items->firstWhere('product_id', $order->referral->product_id)
                : null;
            $eligibleReferral = $order->referral
                && $order->affiliateClick
                && ! $order->affiliateClick->is_suspicious
                && $order->referral->is_active
                && $order->referral->affiliate->affiliate_status === 'active'
                && $order->user_id !== $order->referral->affiliate_user_id
                && $referredItem?->product?->affiliate_enabled;

            if ($eligibleReferral && ! AffiliateCommission::where('order_id', $order->id)->exists()) {
                $rate = (float) $referredItem->product->affiliate_commission_rate;
                AffiliateCommission::create([
                    'affiliate_referral_id' => $order->referral->id,
                    'affiliate_click_id' => $order->affiliateClick->id,
                    'affiliate_user_id' => $order->referral->affiliate_user_id,
                    'order_id' => $order->id,
                    'amount' => round((float) $referredItem->price * $referredItem->quantity * $rate / 100, 2),
                    'rate' => $rate,
                    'status' => 'pending',
                ]);
                $order->affiliateClick->update(['order_id' => $order->id, 'converted_at' => now()]);
            }

            return $payment->fresh();
        });
    }
}
