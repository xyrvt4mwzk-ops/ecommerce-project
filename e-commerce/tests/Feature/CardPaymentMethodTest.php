<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CardPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'payway.enabled' => true,
            'payway.merchant_id' => 'sandbox-merchant',
            'payway.api_key' => 'sandbox-api-key',
            'payway.purchase_url' => 'https://checkout-sandbox.payway.test/purchase',
            'payway.check_url' => 'https://checkout-sandbox.payway.test/check',
            'payway.currency' => 'USD',
        ]);
    }

    public function test_buyer_can_select_card_or_khqr_on_checkout(): void
    {
        [$buyer, $product] = $this->buyerAndProduct();

        $this->actingAs($buyer)
            ->get(route('purchase.resume', $product))
            ->assertOk()
            ->assertSeeText('Pay with KHQR')
            ->assertSeeText('Credit or debit card')
            ->assertSee('name="payment_method" value="khqr"', false)
            ->assertSee('name="payment_method" value="card"', false);
    }

    public function test_selecting_card_creates_a_payway_payment_and_signed_hosted_form(): void
    {
        [$buyer, $product] = $this->buyerAndProduct();

        $response = $this->actingAs($buyer)
            ->post(route('checkout.product.start', $product), [
                'payment_method' => 'card',
            ]);

        $payment = Payment::firstOrFail();
        $response->assertOk()
            ->assertViewIs('payments.payway-redirect')
            ->assertSee('action="https://checkout-sandbox.payway.test/purchase"', false)
            ->assertSee('name="payment_option" value="cards"', false)
            ->assertSee('name="hash"', false)
            ->assertSeeText('Card details preview')
            ->assertSeeText('Card number')
            ->assertSee('data-card-preview', false)
            ->assertDontSee('x-init="$nextTick(() => $refs.paywayForm.submit())"', false);

        $this->assertSame('card', $payment->method);
        $this->assertSame('aba-payway', $payment->provider);
        $this->assertNotEmpty($payment->provider_reference);
        $this->assertSame('pending', $payment->status);
    }

    public function test_cart_remains_available_until_payment_is_confirmed(): void
    {
        [$buyer, $firstProduct] = $this->buyerAndProduct();
        $secondProduct = Product::factory()->create(['price' => 7]);
        $cart = [
            $firstProduct->id => 1,
            $secondProduct->id => 1,
        ];

        $this->actingAs($buyer)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.cart.start'), ['payment_method' => 'card'])
            ->assertOk()
            ->assertSessionHas('cart', $cart);

        $payment = Payment::with('order')->latest()->firstOrFail();
        $payment->update(['status' => 'paid', 'paid_at' => now()]);
        $payment->order->update(['status' => 'paid']);

        $this->get(route('checkout.success', $payment->order))
            ->assertOk()
            ->assertSessionMissing('cart');
    }

    public function test_payway_callback_verifies_the_transaction_before_completing_the_order(): void
    {
        [$buyer, $product] = $this->buyerAndProduct();
        $order = Order::factory()->pending()->create([
            'user_id' => $buyer->id,
            'subtotal' => 10,
            'total' => 10,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'price' => 10,
            'quantity' => 1,
        ]);
        $payment = Payment::factory()->pending()->create([
            'order_id' => $order->id,
            'method' => 'card',
            'provider' => 'aba-payway',
            'provider_reference' => 'PW-TEST-1001',
            'amount' => 10,
            'currency' => 'USD',
        ]);

        Http::fake([
            '*' => Http::response([
                'status' => 0,
                'description' => 'approved',
                'tran_id' => 'PW-TEST-1001',
                'amount' => 10,
                'original_currency' => 'USD',
                'apv' => '123456',
            ]),
        ]);

        $this->post(route('payments.payway.callback'), ['tran_id' => 'PW-TEST-1001'])
            ->assertOk();

        $this->assertSame('paid', $payment->fresh()->status);
        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame(1, License::where('order_id', $order->id)->count());
    }

    private function buyerAndProduct(): array
    {
        $role = Role::firstOrCreate(['name' => 'buyer'], ['display_name' => 'Buyer']);
        $buyer = User::factory()->create();
        $buyer->roles()->attach($role);
        $product = Product::factory()->create(['price' => 10]);

        return [$buyer, $product];
    }
}
