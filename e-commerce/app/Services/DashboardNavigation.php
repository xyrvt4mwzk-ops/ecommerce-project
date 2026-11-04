<?php

namespace App\Services;

use App\Models\User;

class DashboardNavigation
{
    /**
     * Build the shared workspace navigation for guests and authenticated roles.
     * Route middleware, policies, and ownership checks remain the security layer.
     */
    public function for(?User $user): array
    {
        if (! $user) {
            return [
                'role' => 'Guest',
                'context' => 'Marketplace',
                'identity' => 'Guest marketplace',
                'home' => route('home'),
                'groups' => [
                    ['label' => 'Overview', 'items' => [
                        $this->item('Home', 'home', 'home'),
                    ]],
                    ['label' => 'Marketplace', 'items' => [
                        $this->item('Browse Products', 'products.index', 'package', ['products.index', 'products.show']),
                        $this->item('Categories', 'categories.index', 'categories', ['categories.*']),
                        $this->item('Creators', 'creators.index', 'users', ['creators.*']),
                        $this->item('Search', 'search', 'search'),
                    ]],
                    ['label' => 'Information', 'items' => [
                        $this->item('About', 'about', 'info'),
                        $this->item('Help / FAQ', 'help', 'help'),
                    ]],
                    ['label' => 'Account', 'items' => [
                        $this->item('Sign In', 'login', 'login'),
                        $this->item('Register', 'register', 'plus'),
                    ]],
                ],
            ];
        }

        $role = $this->roleFor($user);
        $navigation = match ($role) {
            'super-admin' => [
                'role' => 'Super Admin',
                'home' => route('admin.dashboard'),
                'groups' => [
                    ['label' => 'Overview', 'items' => [
                        $this->item('Dashboard', 'admin.dashboard', 'grid', null, [], 'admin.dashboard'),
                    ]],
                    ['label' => 'Marketplace', 'items' => [
                        $this->item('Products', 'admin.products.index', 'package', ['admin.products.*', 'creator.products.create', 'creator.products.edit'], [], 'admin.products.manage'),
                        $this->item('Categories', 'categories.index', 'categories', ['categories.*']),
                        $this->item('Orders', 'admin.orders.index', 'bag', ['admin.orders.*'], [], 'admin.orders.view'),
                    ]],
                    ['label' => 'Marketing', 'items' => [
                        $this->item('Coupons', 'admin.coupons.index', 'ticket', ['admin.coupons.*'], [], 'admin.coupons.manage'),
                        $this->item('Affiliates & payouts', 'admin.affiliates.index', 'link', ['admin.affiliates.*', 'admin.affiliate-*'], [], 'admin.affiliates.manage'),
                    ]],
                    ['label' => 'Moderation', 'items' => [
                        $this->item('DMCA & reports', 'reviewer.moderation.index', 'shield', ['reviewer.moderation.*', 'reviewer.reviews.*'], [], 'reviewer.moderation.manage'),
                    ]],
                    ['label' => 'Users & creators', 'items' => [
                        $this->item('Users', 'admin.users.index', 'users', ['admin.users.*'], [], 'admin.access.manage'),
                        $this->item('Roles & permissions', 'admin.roles.index', 'shield', ['admin.roles.*'], [], 'admin.access.manage'),
                    ]],
                    ['label' => 'Account', 'items' => [
                        $this->item('Profile', 'profile.edit', 'user', ['profile.*'], [], 'profile.manage'),
                    ]],
                ],
            ],
            'creator' => [
                'role' => 'Creator',
                'home' => route('creator.dashboard'),
                'groups' => [
                    ['label' => 'Overview', 'items' => [
                        $this->item('Dashboard', 'creator.dashboard', 'grid', null, [], 'creator.dashboard'),
                    ]],
                    ['label' => 'My store', 'items' => [
                        $this->item('Products', 'creator.products.index', 'package', ['creator.products.index', 'creator.products.edit'], [], 'creator.products.manage'),
                        $this->item('Add product', 'creator.products.create', 'plus', null, [], 'creator.products.manage'),
                        $this->item('Copyright cases', 'creator.content-reports.index', 'shield', ['creator.content-reports.*'], [], 'creator.moderation.respond'),
                    ]],
                    ['label' => 'Marketplace', 'items' => [
                        $this->item('Browse Products', 'products.index', 'package', ['products.*', 'search']),
                    ]],
                    ['label' => 'Account', 'items' => [
                        $this->item('Profile', 'profile.edit', 'user', ['profile.*'], [], 'profile.manage'),
                    ]],
                ],
            ],
            'affiliate' => [
                'role' => 'Affiliate',
                'home' => route('affiliate.dashboard'),
                'groups' => [
                    ['label' => 'Overview', 'items' => [
                        $this->item('Dashboard', 'affiliate.dashboard', 'grid', null, [], 'affiliate.dashboard'),
                    ]],
                    ['label' => 'Marketplace', 'items' => [
                        $this->item('Browse Products', 'products.index', 'package', ['products.*', 'search']),
                    ]],
                    ['label' => 'Referrals', 'items' => [
                        $this->item('Products to promote', 'affiliate.products.index', 'link', ['affiliate.products.*', 'affiliate.capture'], [], 'affiliate.dashboard'),
                        $this->item('Payouts', 'affiliate.payouts.index', 'wallet', ['affiliate.payouts.*'], [], 'affiliate.payouts.manage'),
                    ]],
                    ['label' => 'Account', 'items' => [
                        $this->item('Profile', 'profile.edit', 'user', ['profile.*'], [], 'profile.manage'),
                    ]],
                ],
            ],
            'content-reviewer' => [
                'role' => 'Content Reviewer',
                'home' => route('reviewer.dashboard'),
                'groups' => [
                    ['label' => 'Overview', 'items' => [
                        $this->item('Dashboard', 'reviewer.dashboard', 'grid', null, [], 'reviewer.dashboard'),
                    ]],
                    ['label' => 'Marketplace', 'items' => [
                        $this->item('Browse Products', 'products.index', 'package', ['products.*', 'search']),
                    ]],
                    ['label' => 'Moderation', 'items' => [
                        $this->item('DMCA & reports', 'reviewer.moderation.index', 'shield', ['reviewer.moderation.*', 'reviewer.reviews.*'], [], 'reviewer.moderation.manage'),
                    ]],
                    ['label' => 'Account', 'items' => [
                        $this->item('Profile', 'profile.edit', 'user', ['profile.*'], [], 'profile.manage'),
                    ]],
                ],
            ],
            default => [
                'role' => 'Buyer',
                'home' => route('buyer.dashboard'),
                'groups' => [
                    ['label' => 'Overview', 'items' => [
                        $this->item('Dashboard', 'buyer.dashboard', 'grid', null, [], 'buyer.dashboard'),
                    ]],
                    ['label' => 'Purchases', 'items' => [
                        $this->item('My library', 'buyer.library.index', 'download', ['buyer.library.*'], [], 'buyer.library.view'),
                        $this->item('Orders', 'buyer.orders.index', 'receipt', ['buyer.orders.*'], [], 'buyer.orders.view'),
                    ]],
                    ['label' => 'Marketplace', 'items' => [
                        $this->item('Browse Products', 'products.index', 'package', ['products.*', 'search']),
                        $this->item('Wishlist', 'buyer.wishlist.index', 'heart', ['buyer.wishlist.*'], [], 'buyer.wishlist.manage'),
                        $this->item('Cart', 'cart.index', 'bag', ['cart.*', 'checkout.*', 'payments.*'], [], 'buyer.cart.manage'),
                    ]],
                    ['label' => 'Account', 'items' => [
                        $this->item('Profile', 'profile.edit', 'user', ['profile.*'], [], 'profile.manage'),
                    ]],
                ],
            ],
        };

        $navigation['context'] = 'Workspace';
        $navigation['identity'] = $navigation['role'].' workspace';
        $navigation['groups'] = collect($navigation['groups'])
            ->map(function (array $group) use ($user): array {
                $group['items'] = array_values(array_filter(
                    $group['items'],
                    fn (array $item): bool => ! $item['permission'] || $user->hasPermission($item['permission'])
                ));

                return $group;
            })
            ->filter(fn (array $group): bool => $group['items'] !== [])
            ->values()
            ->all();

        return $navigation;
    }

    private function item(
        string $label,
        string $route,
        string $icon,
        ?array $activeRoutes = null,
        array $parameters = [],
        ?string $permission = null,
    ): array {
        $activeRoutes ??= [$route];

        return [
            'label' => $label,
            'route' => $route,
            'href' => route($route, $parameters),
            'icon' => $icon,
            'permission' => $permission,
            'active' => request()->routeIs(...$activeRoutes),
        ];
    }

    private function roleFor(User $user): string
    {
        foreach (['super-admin', 'creator', 'buyer', 'affiliate', 'content-reviewer'] as $role) {
            if ($user->hasRole($role)) {
                return $role;
            }
        }

        return 'buyer';
    }
}
