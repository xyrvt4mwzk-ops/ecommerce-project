<?php

namespace Tests\Feature;

use App\Models\AffiliateReferral;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_affiliate_can_open_products_to_promote_and_create_one_referral_link(): void
    {
        $affiliate = $this->userWithRole('affiliate');
        $product = Product::factory()->create(['status' => Product::STATUS_PUBLISHED]);

        $this->actingAs($affiliate)
            ->get(route('affiliate.products.index'))
            ->assertOk()
            ->assertSeeText('Products to promote')
            ->assertSeeText($product->title);

        $this->actingAs($affiliate)
            ->post(route('affiliate.products.referral.store', $product))
            ->assertRedirect();

        $this->actingAs($affiliate)
            ->post(route('affiliate.products.referral.store', $product))
            ->assertRedirect();

        $this->assertDatabaseCount('affiliate_referrals', 1);
        $this->assertDatabaseHas('affiliate_referrals', [
            'affiliate_user_id' => $affiliate->id,
            'product_id' => $product->id,
            'clicks' => 0,
        ]);
    }

    public function test_a_non_affiliate_cannot_open_affiliate_products(): void
    {
        $buyer = $this->userWithRole('buyer');

        $this->actingAs($buyer)
            ->get(route('affiliate.products.index'))
            ->assertForbidden();
    }

    public function test_an_affiliate_cannot_create_a_referral_for_an_unpublished_product(): void
    {
        $affiliate = $this->userWithRole('affiliate');
        $product = Product::factory()->create(['status' => Product::STATUS_DRAFT]);

        $this->actingAs($affiliate)
            ->post(route('affiliate.products.referral.store', $product))
            ->assertNotFound();

        $this->assertDatabaseCount('affiliate_referrals', 0);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::create([
            'name' => $roleName,
            'display_name' => str($roleName)->replace('-', ' ')->title(),
        ]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }
}
