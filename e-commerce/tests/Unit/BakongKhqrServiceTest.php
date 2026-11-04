<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\BakongApiService;
use App\Services\BakongKhqrService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BakongKhqrServiceTest extends TestCase
{
    public function test_it_generates_a_unique_dynamic_khqr_payload_and_md5_for_an_order(): void
    {
        config(['bakong.account_id' => 'merchant@abaa', 'bakong.merchant_name' => 'Piseth Sok', 'bakong.merchant_city' => 'Phnom Penh']);
        $order = new Order(['order_number' => 'ORD-10052', 'total' => 5]);

        $payment = app(BakongKhqrService::class)->create($order);

        $this->assertStringStartsWith('000201010212', $payment['payload']);
        $this->assertStringContainsString('0013merchant@abaa', $payment['payload']);
        $this->assertStringContainsString('9934', $payment['payload']);
        $this->assertStringEndsWith('6304'.substr($payment['payload'], -4), $payment['payload']);
        $this->assertSame(md5($payment['payload']), $payment['md5']);
        $this->assertStringStartsWith('data:image/png;base64,', $payment['qr_image']);
    }

    public function test_a_pending_bakong_response_does_not_report_paid(): void
    {
        config(['bakong.api_token' => 'test-token']);
        Http::fake(['*' => Http::response(['responseCode' => 1, 'responseMessage' => 'Transaction could not be found.'], 200)]);

        $result = app(BakongApiService::class)->checkTransaction('a'.str_repeat('0', 31));

        $this->assertSame('pending', $result['state']);
    }

    public function test_a_successful_bakong_response_returns_the_verified_transaction(): void
    {
        config(['bakong.api_token' => 'test-token']);
        Http::fake(['*' => Http::response(['responseCode' => 0, 'data' => ['hash' => 'transaction-hash', 'amount' => 5, 'currency' => 'USD', 'toAccountId' => 'merchant@aba']], 200)]);

        $result = app(BakongApiService::class)->checkTransaction('a'.str_repeat('0', 31));

        $this->assertSame('paid', $result['state']);
        $this->assertSame('transaction-hash', $result['transaction']['hash']);
    }
}
