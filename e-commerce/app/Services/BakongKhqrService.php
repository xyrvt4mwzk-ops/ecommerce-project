<?php

namespace App\Services;

use App\Models\Order;
use Carbon\CarbonImmutable;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;
use RuntimeException;

class BakongKhqrService
{
    public function create(Order $order): array
    {
        $accountId = trim((string) config('bakong.account_id'));
        $merchantName = $this->sanitize((string) config('bakong.merchant_name'), 25);
        $merchantCity = $this->sanitize((string) config('bakong.merchant_city'), 15);
        $accountInformation = trim((string) config('bakong.account_information'));
        $acquiringBank = $this->sanitize((string) config('bakong.acquiring_bank'), 32);
        $currency = strtoupper((string) config('bakong.currency', 'USD'));

        if ($accountId === '' || $merchantName === '' || $merchantCity === '') {
            throw new RuntimeException('Bakong KHQR is not configured. Set BAKONG_ACCOUNT_ID, BAKONG_MERCHANT_NAME, and BAKONG_MERCHANT_CITY.');
        }
        if (! preg_match('/^[A-Za-z0-9._-]{1,27}@[A-Za-z0-9._-]{1,32}$/', $accountId)) {
            throw new RuntimeException('BAKONG_ACCOUNT_ID must be your real Bakong receiver ID, for example your-name@abaa. Do not use an ABA account number.');
        }
        if (! isset(['USD' => '840', 'KHR' => '116'][$currency])) {
            throw new RuntimeException('Bakong currency must be USD or KHR.');
        }

        $createdAt = CarbonImmutable::now();
        $expiresAt = $createdAt->addMinutes((int) config('bakong.lifetime_minutes', 10));
        $response = BakongKHQR::generateIndividual(new IndividualInfo(
            bakongAccountID: $accountId,
            merchantName: $merchantName,
            merchantCity: $merchantCity,
            acquiringBank: $acquiringBank !== '' ? $acquiringBank : null,
            accountInformation: $accountInformation !== '' ? $accountInformation : null,
            currency: $currency === 'USD' ? KHQRData::CURRENCY_USD : KHQRData::CURRENCY_KHR,
            amount: (float) $order->total,
            billNumber: Str::limit($order->order_number, 25, ''),
            expirationTimestamp: (string) $expiresAt->valueOf(),
            merchantCategoryCode: '5999',
        ));

        if (($response->status['code'] ?? 1) !== 0 || ! is_array($response->data) || empty($response->data['qr'])) {
            throw new RuntimeException((string) ($response->status['message'] ?? 'Unable to generate a KHQR payment QR.'));
        }

        $payload = (string) $response->data['qr'];
        if (! BakongKHQR::verify($payload)->isValid) {
            throw new RuntimeException('The KHQR SDK generated an invalid payment payload.');
        }

        $result = (new PngWriter)->write(new QrCode(data: $payload, size: 320, margin: 12));

        return [
            'payload' => $payload,
            'md5' => (string) $response->data['md5'],
            'qr_image' => $result->getDataUri(),
            'expires_at' => $expiresAt,
            'currency' => $currency,
        ];
    }

    private function sanitize(string $value, int $maximumLength): string
    {
        return Str::upper(Str::limit(trim(preg_replace('/[^A-Za-z0-9 .,&-]/', '', $value) ?? ''), $maximumLength, ''));
    }
}
