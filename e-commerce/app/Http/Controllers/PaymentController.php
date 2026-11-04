<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\BakongApiService;
use App\Services\BakongKhqrService;
use App\Services\OrderCompletionService;
use App\Services\PaywayCardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function khqr(Payment $payment): View|RedirectResponse
    {
        $this->authorizePayment($payment);
        if ($payment->status === 'paid') {
            return redirect()->route('checkout.success', $payment->order);
        }

        if (! $payment->hasActiveKhqr()) {
            if ($payment->status === 'pending' && $payment->expires_at?->isPast()) {
                $payment->update(['status' => 'expired']);
            }

            return redirect()
                ->to($this->orderDetailsUrl($payment))
                ->with('error', 'This payment is no longer active. Select Pay again to generate a new KHQR.');
        }

        return view('payments.khqr', ['payment' => $payment->load('order.items.product')]);
    }

    public function retry(Payment $payment, BakongKhqrService $khqrService, PaywayCardService $payway): View|RedirectResponse
    {
        $this->authorizePayment($payment);

        if ($payment->status === 'paid') {
            return redirect()->route('checkout.success', $payment->order);
        }

        if ($payment->hasActiveKhqr()) {
            return redirect()->route('payments.khqr.show', $payment);
        }

        abort_unless($payment->canRetry(), 422, 'This payment cannot be retried.');

        if ($payment->provider === 'aba-payway') {
            try {
                $checkout = $payway->hostedCheckout($payment);
            } catch (\RuntimeException $exception) {
                return redirect()->to($this->orderDetailsUrl($payment))->with('error', $exception->getMessage());
            }
            $payment->update(['status' => 'pending']);
            $payment->order->update(['status' => 'pending']);

            return view('payments.payway-redirect', [
                'payment' => $payment,
                'action' => $checkout['action'],
                'fields' => $checkout['fields'],
            ]);
        }

        try {
            $khqr = $khqrService->create($payment->order);
        } catch (\RuntimeException $exception) {
            return redirect()->to($this->orderDetailsUrl($payment))->with('error', $exception->getMessage());
        }

        $payment->update([
            'status' => 'pending',
            'currency' => $khqr['currency'],
            'khqr_md5' => $khqr['md5'],
            'khqr_payload' => $khqr['payload'],
            'transaction_id' => null,
            'paid_at' => null,
            'verified_at' => null,
            'expires_at' => $khqr['expires_at'],
            'verification_response' => null,
            'metadata' => array_merge($payment->metadata ?? [], ['qr_image' => $khqr['qr_image']]),
        ]);
        $payment->order->update(['status' => 'pending']);

        Log::info('KHQR_PAYMENT_RETRIED', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('payments.khqr.show', $payment);
    }

    public function status(Payment $payment, BakongApiService $bakong, OrderCompletionService $completion): JsonResponse
    {
        $this->authorizePayment($payment);
        if ($payment->status === 'paid') {
            return response()->json(['status' => 'paid', 'redirect_url' => route('checkout.success', $payment->order)]);
        }
        if ($payment->status !== 'pending') {
            return response()->json(['status' => $payment->status]);
        }
        if ($payment->expires_at?->isPast()) {
            $payment->update(['status' => 'expired']);
            Log::info('PAYMENT_EXPIRED', ['payment_id' => $payment->id]);

            return response()->json(['status' => 'expired']);
        }

        Log::info('BAKONG_PAYMENT_CHECK', ['payment_id' => $payment->id]);
        $result = $bakong->checkTransaction((string) $payment->khqr_md5);
        if ($result['state'] !== 'paid') {
            return response()->json(['status' => 'pending', 'message' => $result['message'] ?? null]);
        }

        $transaction = $result['transaction'];
        if (! $this->matchesPayment($payment, $transaction)) {
            Log::warning('BAKONG_PAYMENT_MISMATCH', ['payment_id' => $payment->id]);

            return response()->json(['status' => 'pending', 'message' => 'Payment details do not match this order.']);
        }

        $completion->completeBakongPayment($payment, $transaction, $result['response']);
        Log::info('BAKONG_PAYMENT_CONFIRMED', ['payment_id' => $payment->id]);

        return response()->json(['status' => 'paid', 'redirect_url' => route('checkout.success', $payment->order)]);
    }

    public function cancel(Payment $payment): RedirectResponse
    {
        $this->authorizePayment($payment);
        if ($payment->status === 'pending') {
            $payment->update(['status' => 'cancelled']);
            $payment->order->update(['status' => 'cancelled']);
        }

        return redirect()->route('products.show', $payment->order->items()->firstOrFail()->product);
    }

    private function authorizePayment(Payment $payment): void
    {
        abort_unless($payment->order->user_id === Auth::id() || Auth::user()->hasRole('super-admin'), 403);
    }

    private function orderDetailsUrl(Payment $payment): string
    {
        return Auth::user()->hasRole('super-admin')
            ? route('admin.orders.show', $payment->order)
            : route('buyer.orders.show', $payment->order);
    }

    private function matchesPayment(Payment $payment, array $transaction): bool
    {
        return strtolower((string) data_get($transaction, 'toAccountId')) === strtolower((string) config('bakong.account_id'))
            && strtoupper((string) data_get($transaction, 'currency')) === strtoupper((string) $payment->currency)
            && abs((float) data_get($transaction, 'amount') - (float) $payment->amount) < 0.01;
    }
}
