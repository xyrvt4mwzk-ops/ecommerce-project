<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\BakongKhqrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class DirectPurchaseCouponTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_apply_a_coupon_before_generating_khqr(): void
    {
        [$buyer, $product] = $this->buyerAndProduct();
        $coupon = Coupon::create([
            'product_id' => $product->id,
            'code' => 'SAVE20',
            'type' => 'percent',
            'value' => 20,
            'is_active' => true,
        ]);

        $this->actingAs($buyer)
            ->post(route('purchase.coupon', $product), ['code' => 'save20'])
            ->assertRedirect(route('purchase.resume', $product))
            ->assertSessionHas('purchase_coupons.'.$product->id, $coupon->code);

        $this->actingAs($buyer)
            ->withSession(['purchase_coupons.'.$product->id => $coupon->code])
            ->get(route('purchase.resume', $product))
            ->assertOk()
            ->assertSeeText('SAVE20 applied')
            ->assertSeeText('$8.00');
    }

    public function test_buyer_cannot_apply_a_coupon_for_another_product(): void
    {
        [$buyer, $product] = $this->buyerAndProduct();
        $otherProduct = Product::factory()->create();
        Coupon::create([
            'product_id' => $otherProduct->id,
            'code' => 'OTHER20',
            'type' => 'percent',
            'value' => 20,
            'is_active' => true,
        ]);

        $this->actingAs($buyer)
            ->from(route('purchase.resume', $product))
            ->post(route('purchase.coupon', $product), ['code' => 'OTHER20'])
            ->assertRedirect(route('purchase.resume', $product))
            ->assertSessionHasErrors('code')
            ->assertSessionMissing('purchase_coupons.'.$product->id);
    }

    public function test_scheduled_coupon_explains_when_it_becomes_available(): void
    {
        [$buyer, $product] = $this->buyerAndProduct();
        Coupon::create([
            'product_id' => $product->id,
            'code' => 'LATER20',
            'type' => 'percent',
            'value' => 20,
            'starts_at' => now()->addHour(),
            'is_active' => true,
        ]);

        $this->actingAs($buyer)
            ->from(route('purchase.resume', $product))
            ->post(route('purchase.coupon', $product), ['code' => 'LATER20'])
            ->assertRedirect(route('purchase.resume', $product))
            ->assertSessionHasErrors([
                'code' => 'This coupon starts on '.now()->addHour()->format('M j, Y g:i A').'.',
            ]);
    }

    public function test_direct_checkout_uses_the_discounted_total_for_order_and_payment(): void
    {
        [$buyer, $product] = $this->buyerAndProduct();
        $coupon = Coupon::create([
            'product_id' => $product->id,
            'code' => 'SAVE20',
            'type' => 'percent',
            'value' => 20,
            'is_active' => true,
        ]);

        $this->mock(BakongKhqrService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('create')->once()->andReturn([
                'payload' => 'test-khqr-payload',
                'md5' => str_repeat('a', 32),
                'qr_image' => 'data:image/png;base64,dGVzdA==',
                'expires_at' => now()->addMinute(),
                'currency' => 'USD',
            ]);
        });

        $response = $this->actingAs($buyer)
            ->withSession(['purchase_coupons.'.$product->id => $coupon->code])
            ->post(route('checkout.khqr', $product));

        $order = Order::firstOrFail();
        $payment = Payment::firstOrFail();

        $response->assertRedirect(route('payments.khqr.show', $payment));
        $this->assertSame(10.0, (float) $order->subtotal);
        $this->assertSame(8.0, (float) $order->total);
        $this->assertSame(8.0, (float) $payment->amount);
        $this->assertSame('SAVE20', $payment->metadata['coupon']);
        $this->assertSame(2.0, (float) $payment->metadata['discount']);
    }

    private function buyerAndProduct(): array
    {
        $buyerRole = Role::create(['name' => 'buyer', 'display_name' => 'Buyer']);
        $buyer = User::factory()->create();
        $buyer->roles()->attach($buyerRole);
        $product = Product::factory()->create(['price' => 10]);

        return [$buyer, $product];
    }
}
