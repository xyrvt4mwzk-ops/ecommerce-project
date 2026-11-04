<?php

namespace Database\Factories;

use App\Models\AffiliateCommission;
use App\Models\AffiliateReferral;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AffiliateCommissionFactory extends Factory
{
    protected $model = AffiliateCommission::class;

    public function definition(): array
    {
        return [
            'affiliate_referral_id' => AffiliateReferral::factory(),
            'affiliate_user_id' => User::factory(),
            'order_id' => Order::factory(),
            'amount' => fake()->randomFloat(2, 2, 15),
            'rate' => fake()->randomFloat(2, 5, 15),
            'status' => 'paid',
        ];
    }
}
