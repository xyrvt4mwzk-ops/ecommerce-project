<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(6),
            'short_description' => fake()->sentence(12),
            'full_description' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(array_keys(Product::categories())),
            'price' => fake()->randomFloat(2, 9, 99),
            'cover_image_path' => null,
            'file_path' => null,
            'status' => Product::STATUS_PUBLISHED,
            'affiliate_enabled' => true,
            'affiliate_commission_rate' => 10,
            'moderation_status' => Product::MODERATION_CLEAR,
        ];
    }
}
