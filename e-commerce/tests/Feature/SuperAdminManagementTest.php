<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_role_specific_product_management(): void
    {
        $admin = $this->userWithRole('super-admin');
        $product = Product::factory()->create();

        $this->actingAs($admin)->get(route('creator.products.create'))->assertOk();
        $this->actingAs($admin)->get(route('creator.products.edit', $product))->assertOk();
    }

    public function test_super_admin_can_create_update_and_delete_a_coupon(): void
    {
        $admin = $this->userWithRole('super-admin');

        $this->actingAs($admin)->get(route('admin.coupons.index'))
            ->assertOk()
            ->assertSeeText('Coupons');

        $this->actingAs($admin)->post(route('admin.coupons.store'), [
            'code' => 'welcome20',
            'type' => 'percent',
            'value' => 20,
            'usage_limit' => 100,
            'is_active' => 1,
        ])->assertRedirect(route('admin.coupons.index'));

        $coupon = Coupon::firstOrFail();
        $this->assertSame('WELCOME20', $coupon->code);

        $this->actingAs($admin)->put(route('admin.coupons.update', $coupon), [
            'code' => 'welcome25',
            'type' => 'percent',
            'value' => 25,
            'is_active' => 1,
        ])->assertRedirect(route('admin.coupons.index'));

        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'code' => 'WELCOME25']);

        $this->actingAs($admin)->delete(route('admin.coupons.destroy', $coupon))
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    public function test_non_admin_cannot_manage_coupons(): void
    {
        $buyer = $this->userWithRole('buyer');

        $this->actingAs($buyer)->get(route('admin.coupons.index'))->assertForbidden();
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
