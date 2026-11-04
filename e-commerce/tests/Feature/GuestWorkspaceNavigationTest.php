<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestWorkspaceNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_public_pages_use_the_shared_workspace_shell(): void
    {
        $creatorRole = Role::create(['name' => 'creator', 'display_name' => 'Creator']);
        $creator = User::factory()->create(['name' => 'Public Creator']);
        $creator->roles()->attach($creatorRole);
        $product = Product::factory()->create([
            'user_id' => $creator->id,
            'status' => Product::STATUS_PUBLISHED,
            'category' => 'graphics',
        ]);

        foreach ([
            route('home'),
            route('products.index'),
            route('products.show', $product),
            route('categories.index'),
            route('categories.show', 'graphics'),
            route('creators.index'),
            route('creators.show', $creator),
            route('about'),
            route('help'),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSeeText('DigitalMarket')
                ->assertSeeText('Guest marketplace')
                ->assertSeeText('Sign In')
                ->assertSeeText('Register')
                ->assertSee('aria-label="Guest profile — sign in"', false)
                ->assertDontSee('placeholder="Search marketplace"', false)
                ->assertDontSee('<span class="block text-xs text-slate-400">Continue with Google</span>', false)
                ->assertDontSee('href="'.route('buyer.library.index').'"', false)
                ->assertDontSee('href="'.route('admin.roles.index').'"', false);
        }
    }

    public function test_guest_product_page_exposes_public_content_but_not_private_actions(): void
    {
        $creator = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $creator->id,
            'status' => Product::STATUS_PUBLISHED,
        ]);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSeeText($product->title)
            ->assertSeeText('Sign in to purchase')
            ->assertSeeText('Sign in to write a review')
            ->assertDontSeeText('Buy now')
            ->assertSee('href="'.route('login', ['purchase' => $product->slug]).'"', false)
            ->assertSee('href="'.route('login').'"', false)
            ->assertDontSee('href="'.route('google.redirect', ['intent' => 'login']).'"', false);
    }

    public function test_guest_protected_routes_redirect_to_google_sign_in(): void
    {
        foreach ([
            route('cart.index'),
            route('buyer.library.index'),
            route('buyer.orders.index'),
            route('admin.users.index'),
        ] as $url) {
            $this->get($url)->assertRedirect(route('google.redirect', ['intent' => 'login']));
        }
    }

    public function test_buyer_keeps_the_same_shell_with_buyer_navigation(): void
    {
        $role = Role::create(['name' => 'buyer', 'display_name' => 'Buyer']);
        $buyer = User::factory()->create();
        $buyer->roles()->attach($role);

        $this->actingAs($buyer)->get(route('products.index'))
            ->assertOk()
            ->assertSeeText('Buyer workspace')
            ->assertSeeText('My library')
            ->assertSeeText('Wishlist')
            ->assertSeeText('Cart')
            ->assertDontSeeText('Guest marketplace');
    }

    public function test_configured_permissions_filter_navigation_and_routes(): void
    {
        $role = Role::create(['name' => 'buyer', 'display_name' => 'Buyer']);
        $role->permissions()->attach(Permission::create([
            'name' => 'buyer.dashboard',
            'display_name' => 'Buyer Dashboard',
        ]));
        $buyer = User::factory()->create();
        $buyer->roles()->attach($role);

        $this->actingAs($buyer)->get(route('buyer.dashboard'))
            ->assertOk()
            ->assertDontSee('href="'.route('buyer.wishlist.index').'"', false)
            ->assertDontSee('href="'.route('cart.index').'"', false);

        $this->actingAs($buyer)->get(route('buyer.wishlist.index'))->assertForbidden();
    }

    public function test_role_and_ownership_boundaries_are_still_enforced(): void
    {
        $buyerRole = Role::create(['name' => 'buyer', 'display_name' => 'Buyer']);
        $buyer = User::factory()->create();
        $buyer->roles()->attach($buyerRole);
        $this->actingAs($buyer)->get(route('admin.users.index'))->assertForbidden();

        $creatorRole = Role::create(['name' => 'creator', 'display_name' => 'Creator']);
        $owner = User::factory()->create();
        $otherCreator = User::factory()->create();
        $owner->roles()->attach($creatorRole);
        $otherCreator->roles()->attach($creatorRole);
        $product = Product::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherCreator)->get(route('creator.products.edit', $product))->assertForbidden();
    }
}
