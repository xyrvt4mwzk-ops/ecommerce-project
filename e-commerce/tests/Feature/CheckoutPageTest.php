<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_checkout_button_adds_the_product_to_the_existing_cart(): void
    {
        [$buyer, $product] = $this->buyerAndProduct();

        $this->actingAs($buyer)
            ->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('action="'.route('cart.add', $product).'"', false)
            ->assertSee('name="redirect" value="cart"', false)
            ->assertSeeText('Checkout')
            ->assertDontSeeText('Buy now');

        $this->actingAs($buyer)
            ->post(route('cart.add', $product), ['redirect' => 'cart'])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('cart.'.$product->id, 1);

        $this->actingAs($buyer)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSeeText('Choose a payment method')
            ->assertSeeText('Pay with KHQR')
            ->assertSeeText('Credit or debit card');
    }

    public function test_cart_keeps_multiple_products_while_the_buyer_continues_browsing(): void
    {
        [$buyer, $firstProduct] = $this->buyerAndProduct();
        $secondProduct = Product::factory()->create(['price' => 7]);

        $this->actingAs($buyer)
            ->post(route('cart.add', $firstProduct))
            ->assertSessionHas('cart.'.$firstProduct->id, 1);

        $this->actingAs($buyer)
            ->get(route('products.index'))
            ->assertOk();

        $this->actingAs($buyer)
            ->post(route('cart.add', $secondProduct), ['redirect' => 'cart'])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('cart.'.$firstProduct->id, 1)
            ->assertSessionHas('cart.'.$secondProduct->id, 1);

        $this->actingAs($buyer)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSeeText($firstProduct->title)
            ->assertSeeText($secondProduct->title)
            ->assertSeeText('$17.00')
            ->assertSeeText('Add more products');
    }

    public function test_buyer_can_apply_a_marketplace_coupon_to_cart(): void
    {
        [$buyer, $product] = $this->buyerAndProduct();
        Coupon::create([
            'code' => 'DISC2',
            'type' => 'fixed',
            'value' => 2,
            'starts_at' => now()->subMinute(),
            'ends_at' => now()->addDay(),
            'is_active' => true,
        ]);

        $this->actingAs($buyer)
            ->withSession(['cart' => [$product->id => 1]])
            ->post(route('cart.coupon'), ['code' => 'disc2'])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('cart_coupon', 'DISC2')
            ->assertSessionHas('success', 'Coupon applied successfully.');

        $this->actingAs($buyer)
            ->withSession(['cart' => [$product->id => 1], 'cart_coupon' => 'DISC2'])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSeeText('DISC2 applied')
            ->assertSeeText('$8.00');
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
