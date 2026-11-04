<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\OrderCompletionService;
use App\Services\PaywayCardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PaywayController extends Controller
{
    public function callback(
        Request $request,
        PaywayCardService $payway,
        OrderCompletionService $completion,
    ): JsonResponse {
        $transactionId = (string) $request->input('tran_id');
        $payment = Payment::query()
            ->where('provider', 'aba-payway')
            ->where('provider_reference', $transactionId)
            ->first();

        if (! $payment) {
            Log::warning('PAYWAY_CALLBACK_UNKNOWN_TRANSACTION', ['tran_id' => $transactionId]);

            return response()->json(['message' => 'Transaction not found.'], 404);
        }

        try {
            $this->reconcile($payment, $payway, $completion);
        } catch (RuntimeException $exception) {
            Log::warning('PAYWAY_CALLBACK_VERIFICATION_FAILED', [
                'payment_id' => $payment->id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'Verification pending.'], 202);
        }

        return response()->json(['message' => 'Payment status received.']);
    }

    public function complete(
        Payment $payment,
        PaywayCardService $payway,
        OrderCompletionService $completion,
    ): RedirectResponse {
        $this->authorizePayment($payment);

        if ($payment->status !== 'paid') {
            try {
                $this->reconcile($payment, $payway, $completion);
            } catch (RuntimeException $exception) {
                return redirect()->route('buyer.orders.show', $payment->order)
                    ->with('error', $exception->getMessage());
            }
        }

        if ($payment->fresh()->status === 'paid') {
            return redirect()->route('checkout.success', $payment->order);
        }

        return redirect()->route('buyer.orders.show', $payment->order)
            ->with('info', 'Your card payment is still being verified. Refresh the order shortly.');
    }

    public function cancelReturn(Payment $payment): RedirectResponse
    {
        $this->authorizePayment($payment);

        if ($payment->status === 'pending') {
            $payment->update(['status' => 'cancelled']);
            $payment->order->update(['status' => 'cancelled']);
        }

        return redirect()->route('buyer.orders.show', $payment->order)
            ->with('error', 'Card payment was cancelled. You can choose Pay again whenever you are ready.');
    }

    private function reconcile(
        Payment $payment,
        PaywayCardService $payway,
        OrderCompletionService $completion,
    ): void {
        if ($payment->status === 'paid') {
            return;
        }

        $response = $payway->checkTransaction($payment);
        if ($payway->isApproved($payment, $response)) {
            $completion->completeVerifiedPayment(
                $payment,
                $payway->verifiedReference($payment, $response),
                $response,
            );
            Log::info('PAYWAY_CARD_PAYMENT_CONFIRMED', ['payment_id' => $payment->id]);

            return;
        }

        $status = (int) data_get($response, 'status', -1);
        if ($status === 3) {
            $payment->update(['status' => 'failed', 'verification_response' => $response]);
            $payment->order->update(['status' => 'failed']);
        }
    }

    private function authorizePayment(Payment $payment): void
    {
        abort_unless($payment->order->user_id === Auth::id() || Auth::user()->hasRole('super-admin'), 403);
        abort_unless($payment->provider === 'aba-payway', 404);
    }
}
