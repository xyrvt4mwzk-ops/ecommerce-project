<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_role_is_redirected_to_its_own_dashboard(): void
    {
        $destinations = [
            'super-admin' => 'admin.dashboard',
            'creator' => 'creator.dashboard',
            'buyer' => 'buyer.dashboard',
            'affiliate' => 'affiliate.dashboard',
            'content-reviewer' => 'reviewer.dashboard',
        ];

        foreach ($destinations as $roleName => $routeName) {
            $role = Role::create(['name' => $roleName, 'display_name' => str($roleName)->replace('-', ' ')->title()]);
            $user = User::factory()->create();
            $user->roles()->attach($role);

            $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route($routeName));
        }
    }

    public function test_a_buyer_cannot_access_an_administrator_route(): void
    {
        $role = Role::create(['name' => 'buyer', 'display_name' => 'Buyer']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_each_role_receives_its_own_navigation_without_privileged_links(): void
    {
        $expectations = [
            'super-admin' => ['admin.dashboard', 'Roles & permissions', 'Add product'],
            'creator' => ['creator.dashboard', 'Add product', 'Roles & permissions'],
            'buyer' => ['buyer.dashboard', 'My library', 'Roles & permissions'],
            'affiliate' => ['affiliate.dashboard', 'Products to promote', 'Roles & permissions'],
            'content-reviewer' => ['reviewer.dashboard', 'Content Reviewer', 'Roles & permissions'],
        ];

        foreach ($expectations as $roleName => [$routeName, $visibleText, $hiddenText]) {
            $role = Role::create(['name' => $roleName, 'display_name' => str($roleName)->replace('-', ' ')->title()]);
            $user = User::factory()->create();
            $user->roles()->attach($role);

            $response = $this->actingAs($user)->get(route($routeName));

            $response->assertOk()->assertSeeText($visibleText);
            if ($roleName !== 'super-admin') {
                $response->assertDontSeeText($hiddenText);
            }
        }
    }

    public function test_non_admin_roles_cannot_open_privileged_role_routes(): void
    {
        foreach (['creator', 'affiliate', 'content-reviewer'] as $roleName) {
            $role = Role::create(['name' => $roleName, 'display_name' => str($roleName)->replace('-', ' ')->title()]);
            $user = User::factory()->create();
            $user->roles()->attach($role);

            $this->actingAs($user)->get(route('admin.roles.index'))->assertForbidden();
        }
    }
}
