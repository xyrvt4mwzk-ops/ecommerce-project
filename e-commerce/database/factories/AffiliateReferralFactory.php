<?php

namespace Database\Factories;

use App\Models\AffiliateReferral;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AffiliateReferralFactory extends Factory
{
    protected $model = AffiliateReferral::class;

    public function definition(): array
    {
        return [
            'affiliate_user_id' => User::factory(),
            'product_id' => Product::factory(),
            'code' => Str::lower(Str::random(12)),
            'clicks' => fake()->numberBetween(5, 40),
        ];
    }
}
