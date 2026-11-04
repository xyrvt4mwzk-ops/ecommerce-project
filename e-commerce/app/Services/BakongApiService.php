<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BakongApiService
{
    public function checkTransaction(string $md5): array
    {
        $token = (string) config('bakong.api_token');
        if ($token === '') {
            return ['state' => 'error', 'message' => 'Bakong API token is not configured.'];
        }

        try {
            $response = Http::acceptJson()->withToken($token)->timeout(10)->post(
                rtrim((string) config('bakong.api_base_url'), '/').'/v1/check_transaction_by_md5',
                ['md5' => $md5],
            );
        } catch (ConnectionException $exception) {
            Log::warning('BAKONG_API_ERROR', ['message' => $exception->getMessage()]);

            return ['state' => 'error', 'message' => 'Bakong is temporarily unavailable.'];
        }

        $body = $response->json() ?: [];
        if (! $response->successful()) {
            Log::warning('BAKONG_API_ERROR', ['http_status' => $response->status(), 'response_code' => data_get($body, 'responseCode')]);

            return ['state' => $response->status() === 404 ? 'pending' : 'error', 'message' => (string) data_get($body, 'responseMessage', 'Unable to verify payment.')];
        }

        if ((int) data_get($body, 'responseCode', 1) !== 0 || ! is_array(data_get($body, 'data'))) {
            return ['state' => 'pending', 'message' => (string) data_get($body, 'responseMessage', 'Payment has not been found yet.')];
        }

        return ['state' => 'paid', 'transaction' => data_get($body, 'data'), 'response' => $body];
    }
}
