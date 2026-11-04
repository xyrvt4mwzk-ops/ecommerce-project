<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\BakongKhqrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class BuyerOrderPaymentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_sees_payment_actions_for_pending_and_cancelled_orders(): void
    {
        $buyer = $this->buyer();
        $activePayment = $this->orderWithPayment($buyer, 'pending', 'pending', true);
        $cancelledPayment = $this->orderWithPayment($buyer, 'cancelled', 'cancelled');

        $this->actingAs($buyer)
            ->get(route('buyer.orders.index'))
            ->assertOk()
            ->assertSee('href="'.route('payments.khqr.show', $activePayment).'"', false)
            ->assertSeeText('Pay now')
            ->assertSee('action="'.route('payments.retry', $cancelledPayment).'"', false)
            ->assertSeeText('Pay again');
    }

    public function test_buyer_can_generate_a_new_khqr_for_a_cancelled_payment(): void
    {
        $buyer = $this->buyer();
        $payment = $this->orderWithPayment($buyer, 'cancelled', 'cancelled');

        $this->mock(BakongKhqrService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('create')->once()->andReturn([
                'payload' => 'replacement-khqr-payload',
                'md5' => str_repeat('b', 32),
                'qr_image' => 'data:image/png;base64,dGVzdA==',
                'expires_at' => now()->addMinute(),
                'currency' => 'USD',
            ]);
        });

        $this->actingAs($buyer)
            ->post(route('payments.retry', $payment))
            ->assertRedirect(route('payments.khqr.show', $payment));

        $payment->refresh();
        $this->assertSame('pending', $payment->status);
        $this->assertSame('pending', $payment->order->status);
        $this->assertSame('replacement-khqr-payload', $payment->khqr_payload);
        $this->assertTrue($payment->expires_at->isFuture());
    }

    public function test_buyer_cannot_retry_another_buyers_payment(): void
    {
        $owner = $this->buyer();
        $otherBuyer = $this->buyer();
        $payment = $this->orderWithPayment($owner, 'cancelled', 'cancelled');

        $this->actingAs($otherBuyer)
            ->post(route('payments.retry', $payment))
            ->assertForbidden();
    }

    private function buyer(): User
    {
        $role = Role::firstOrCreate(
            ['name' => 'buyer'],
            ['display_name' => 'Buyer'],
        );
        $buyer = User::factory()->create();
        $buyer->roles()->attach($role);

        return $buyer;
    }

    private function orderWithPayment(
        User $buyer,
        string $orderStatus,
        string $paymentStatus,
        bool $activeKhqr = false,
    ): Payment {
        $product = Product::factory()->create(['price' => 10]);
        $order = Order::factory()->create([
            'user_id' => $buyer->id,
            'subtotal' => 10,
            'total' => 10,
            'status' => $orderStatus,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'price' => 10,
            'quantity' => 1,
        ]);

        return Payment::factory()->create([
            'order_id' => $order->id,
            'method' => 'khqr',
            'provider' => 'bakong',
            'status' => $paymentStatus,
            'amount' => 10,
            'currency' => 'USD',
            'khqr_payload' => $activeKhqr ? 'active-khqr-payload' : null,
            'khqr_md5' => $activeKhqr ? str_repeat('a', 32) : null,
            'expires_at' => $activeKhqr ? now()->addMinute() : now()->subMinute(),
        ]);
    }
}
