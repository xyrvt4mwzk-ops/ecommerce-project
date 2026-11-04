<?php

namespace Tests\Feature\Auth;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertDontSee('href="'.route('products.index').'"', false);
        $response->assertDontSee('href="'.route('cart.index').'"', false);
    }

    public function test_guest_marketplace_sign_in_links_open_the_login_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('href="'.route('login').'"', false);

        $navigation = app(\App\Services\DashboardNavigation::class)->for(null);
        $signIn = collect($navigation['groups'])
            ->flatMap(fn (array $group) => $group['items'])
            ->firstWhere('label', 'Sign In');

        $this->assertSame(route('login'), $signIn['href']);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('role.select', absolute: false));
    }

    public function test_buyer_login_preserves_purchase_intent_and_continues_to_checkout(): void
    {
        $buyerRole = Role::create(['name' => 'buyer', 'display_name' => 'Buyer']);
        $buyer = User::factory()->create();
        $buyer->roles()->attach($buyerRole);
        $product = Product::factory()->create();

        $this->get(route('login', ['purchase' => $product->slug]))
            ->assertOk()
            ->assertSeeText('Continue purchasing '.$product->title)
            ->assertSessionHas('url.intended', route('purchase.resume', $product));

        $this->post(route('login'), [
            'email' => $buyer->email,
            'password' => 'password',
        ])->assertRedirect(route('purchase.resume', $product));

        $this->get(route('purchase.resume', $product))
            ->assertOk()
            ->assertSeeText('Complete your payment')
            ->assertSeeText('Coupon code')
            ->assertSee('action="'.route('purchase.coupon', $product).'"', false)
            ->assertSee('action="'.route('checkout.product.start', $product).'"', false)
            ->assertSeeText('Pay with KHQR')
            ->assertSeeText('Credit or debit card');
    }

    public function test_new_user_purchase_intent_continues_after_selecting_buyer_role(): void
    {
        Role::create(['name' => 'buyer', 'display_name' => 'Buyer']);
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->get(route('login', ['purchase' => $product->slug]));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('role.select'));

        $this->post(route('role.store'), ['role' => 'buyer'])
            ->assertRedirect(route('purchase.resume', $product));
    }

    public function test_non_buyer_purchase_intent_returns_to_product_with_clear_message(): void
    {
        $creatorRole = Role::create(['name' => 'creator', 'display_name' => 'Creator']);
        $creator = User::factory()->create();
        $creator->roles()->attach($creatorRole);
        $product = Product::factory()->create();

        $this->actingAs($creator)
            ->get(route('purchase.resume', $product))
            ->assertRedirect(route('products.show', $product))
            ->assertSessionHas('error', 'Switch to a Buyer account to continue to payment.');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
