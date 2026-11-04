<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('creator') || $user->hasRole('super-admin');
    }

    public function view(User $user, Product $product): bool
    {
        return $product->status === Product::STATUS_PUBLISHED
            || $user->hasRole('super-admin')
            || $product->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('creator') || $user->hasRole('super-admin');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasRole('super-admin') || $product->user_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('super-admin') || $product->user_id === $user->id;
    }
}
