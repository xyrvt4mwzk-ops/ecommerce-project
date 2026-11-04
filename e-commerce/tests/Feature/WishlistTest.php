<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_buyer_can_save_and_remove_a_published_product(): void
    {
        $buyer = User::factory()->create();
        $buyerRole = Role::create(['name' => 'buyer', 'display_name' => 'Buyer']);
        $buyer->roles()->attach($buyerRole);
        $creator = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $creator->id, 'status' => Product::STATUS_PUBLISHED]);

        $this->actingAs($buyer)->post(route('buyer.wishlist.store', $product))->assertSessionHas('success');
        $this->assertDatabaseHas('wishlists', ['user_id' => $buyer->id, 'product_id' => $product->id]);

        $this->actingAs($buyer)->delete(route('buyer.wishlist.destroy', $product))->assertSessionHas('success');
        $this->assertDatabaseMissing('wishlists', ['user_id' => $buyer->id, 'product_id' => $product->id]);
    }
}
