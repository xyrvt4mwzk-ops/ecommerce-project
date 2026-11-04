<?php

namespace Database\Factories;

use App\Models\License;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LicenseFactory extends Factory
{
    protected $model = License::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'order_item_id' => OrderItem::factory(),
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'license_key' => $this->generateLicenseKey(),
        ];
    }

    private function generateLicenseKey(): string
    {
        return implode('-', [
            Str::upper(Str::random(4)),
            Str::upper(Str::random(4)),
            Str::upper(Str::random(4)),
            Str::upper(Str::random(4)),
        ]);
    }
}
