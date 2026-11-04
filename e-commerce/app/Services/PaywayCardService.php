<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PaywayCardService
{
    private const HASH_ORDER = [
        'req_time', 'merchant_id', 'tran_id', 'amount', 'items', 'shipping',
        'ctid', 'pwt', 'firstname', 'lastname', 'email', 'phone', 'type',
        'payment_option', 'return_url', 'cancel_url', 'continue_success_url',
        'return_deeplink', 'currency', 'custom_fields', 'return_params',
    ];

    public function enabled(): bool
    {
        return (bool) config('payway.enabled')
            && filled(config('payway.merchant_id'))
            && filled(config('payway.api_key'))
            && filled(config('payway.purchase_url'));
    }

    public function hostedCheckout(Payment $payment): array
    {
        $this->ensureConfigured();
        $payment->loadMissing('order.items.product', 'order.user');

        $transactionId = $this->transactionId($payment);
        $name = $this->customerName((string) $payment->order->user->name);
        $items = [[
            'name' => Str::limit('Digital products '.$payment->order->order_number, 80, ''),
            'quantity' => '1',
            'price' => number_format((float) $payment->amount, 2, '.', ''),
        ]];

        $fields = [
            'req_time' => now('UTC')->format('YmdHis'),
            'merchant_id' => (string) config('payway.merchant_id'),
            'tran_id' => $transactionId,
            'firstname' => $name['first'],
            'lastname' => $name['last'],
            'email' => Str::limit((string) $payment->order->user->email, 50, ''),
            'type' => 'purchase',
            'payment_option' => 'cards',
            'items' => base64_encode((string) json_encode($items, JSON_UNESCAPED_SLASHES)),
            'amount' => number_format((float) $payment->amount, 2, '.', ''),
            'currency' => strtoupper((string) config('payway.currency', 'USD')),
            'return_url' => base64_encode(route('payments.payway.callback')),
            'cancel_url' => route('payments.payway.cancel-return', $payment),
            'continue_success_url' => route('payments.payway.complete', $payment),
            'return_params' => $payment->order->order_number,
        ];
        $fields['hash'] = $this->hash($fields);

        $payment->update([
            'provider_reference' => $transactionId,
            'currency' => $fields['currency'],
            'metadata' => array_merge($payment->metadata ?? [], ['payway_request_time' => $fields['req_time']]),
        ]);

        return [
            'action' => (string) config('payway.purchase_url'),
            'fields' => $fields,
        ];
    }

    public function checkTransaction(Payment $payment): array
    {
        $this->ensureConfigured();
        abort_if(blank($payment->provider_reference), 422, 'PayWay transaction reference is missing.');

        $fields = [
            'req_time' => now('UTC')->format('YmdHis'),
            'merchant_id' => (string) config('payway.merchant_id'),
            'tran_id' => (string) $payment->provider_reference,
        ];
        $fields['hash'] = base64_encode(hash_hmac(
            'sha512',
            implode('', $fields),
            (string) config('payway.api_key'),
            true,
        ));

        try {
            $response = Http::asMultipart()
                ->withHeaders(['language' => 'en'])
                ->timeout(15)
                ->post((string) config('payway.check_url'), $fields);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Unable to contact ABA PayWay. Please try again shortly.', previous: $exception);
        }

        if (! $response->successful() || ! is_array($response->json())) {
            throw new RuntimeException('ABA PayWay returned an invalid verification response.');
        }

        return $response->json();
    }

    public function isApproved(Payment $payment, array $response): bool
    {
        if ((int) data_get($response, 'status', -1) !== 0) {
            return false;
        }

        $transactionId = (string) data_get($response, 'tran_id', $payment->provider_reference);
        $amount = (float) data_get($response, 'amount', data_get($response, 'totalAmount', -1));
        $currency = strtoupper((string) data_get($response, 'original_currency', $payment->currency));

        return hash_equals((string) $payment->provider_reference, $transactionId)
            && abs($amount - (float) $payment->amount) < 0.01
            && $currency === strtoupper((string) $payment->currency);
    }

    public function verifiedReference(Payment $payment, array $response): string
    {
        return 'PAYWAY:'.(string) $payment->provider_reference.':'.(string) data_get($response, 'apv', 'APPROVED');
    }

    private function hash(array $fields): string
    {
        $payload = collect(self::HASH_ORDER)
            ->filter(fn (string $field) => array_key_exists($field, $fields))
            ->map(fn (string $field) => (string) $fields[$field])
            ->implode('');

        return base64_encode(hash_hmac('sha512', $payload, (string) config('payway.api_key'), true));
    }

    private function transactionId(Payment $payment): string
    {
        return Str::upper(Str::limit('PW'.base_convert((string) $payment->id, 10, 36).now('UTC')->format('ymdHis').Str::random(2), 20, ''));
    }

    private function customerName(string $name): array
    {
        $parts = preg_split('/\s+/', trim(preg_replace('/[^A-Za-z0-9 ]/', '', $name) ?: 'Customer')) ?: [];
        $first = Str::limit(array_shift($parts) ?: 'Customer', 20, '');
        $last = Str::limit(implode(' ', $parts) ?: 'Buyer', 20, '');

        return compact('first', 'last');
    }

    private function ensureConfigured(): void
    {
        if (! $this->enabled()) {
            throw new RuntimeException('Card payment is not configured. Add your ABA PayWay merchant ID and API key.');
        }
    }
}
