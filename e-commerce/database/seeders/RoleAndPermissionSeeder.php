<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define your 5 Core Roles
        $roles = [
            [
                'name' => 'super-admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full access to platform control, payouts, and system overrides.',
            ],
            [
                'name' => 'creator',
                'display_name' => 'Content Creator',
                'description' => 'Can upload digital goods, manage store fonts, and view earnings analytics.',
            ],
            [
                'name' => 'buyer',
                'display_name' => 'Customer / Buyer',
                'description' => 'Can purchase products, download instant secure links, and leave reviews.',
            ],
            [
                'name' => 'affiliate',
                'display_name' => 'Affiliate Marketer',
                'description' => 'Can generate tracking links and track click-to-conversion referral rewards.',
            ],
            [
                'name' => 'content-reviewer',
                'display_name' => 'Content Reviewer / Moderator',
                'description' => 'Can audit products, handle DMCA copyright complaints, and clear takedown flags.',
            ],
        ];

        // 2. Loop through and insert each role into your XAMPP MySQL database
        foreach ($roles as $roleData) {
            Role::firstOrCreate(['name' => $roleData['name']], $roleData);
        }

        $rolePermissions = [
            'buyer' => [
                'buyer.dashboard', 'buyer.library.view', 'buyer.orders.view',
                'buyer.wishlist.manage', 'buyer.cart.manage', 'buyer.checkout.create',
                'buyer.reviews.manage', 'profile.manage',
            ],
            'creator' => [
                'creator.dashboard', 'creator.products.manage', 'creator.reviews.respond',
                'creator.moderation.respond', 'profile.manage',
            ],
            'affiliate' => [
                'affiliate.dashboard', 'affiliate.payouts.manage', 'profile.manage',
            ],
            'content-reviewer' => [
                'reviewer.dashboard', 'reviewer.moderation.manage', 'profile.manage',
            ],
            'super-admin' => [
                'admin.dashboard', 'admin.products.manage', 'admin.orders.view',
                'admin.access.manage', 'admin.coupons.manage', 'admin.affiliates.manage',
                'reviewer.moderation.manage', 'profile.manage',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $permissionIds = collect($permissionNames)->map(function (string $name): int {
                return Permission::firstOrCreate(
                    ['name' => $name],
                    ['display_name' => str($name)->replace('.', ' ')->title()],
                )->id;
            });

            Role::where('name', $roleName)->firstOrFail()->permissions()->syncWithoutDetaching($permissionIds);
        }
    }
}
