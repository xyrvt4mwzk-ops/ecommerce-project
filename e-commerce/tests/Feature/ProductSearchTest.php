<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_matches_partial_product_words(): void
    {
        Product::factory()->create([
            'title' => 'Duolingo Premium',
            'slug' => 'duolingo-premium',
            'short_description' => 'Language learning access.',
        ]);
        Product::factory()->create([
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365-search-test',
            'short_description' => 'Office productivity access.',
        ]);

        $this->get(route('products.index', ['search' => 'dou']))
            ->assertOk()
            ->assertSeeText('Duolingo Premium')
            ->assertDontSeeText('Microsoft 365');
    }

    public function test_each_search_fragment_can_match_a_different_product_field(): void
    {
        Product::factory()->create([
            'title' => 'Canva Pro',
            'slug' => 'canva-pro-search-test',
            'short_description' => 'Professional design toolkit.',
            'full_description' => 'Complete creative design library.',
        ]);
        Product::factory()->create([
            'title' => 'Canva Templates',
            'slug' => 'canva-templates-search-test',
            'short_description' => 'Social media layouts.',
            'full_description' => 'Reusable layouts for social publishing.',
        ]);

        $this->get(route('products.index', ['search' => 'can des']))
            ->assertOk()
            ->assertSeeText('Canva Pro')
            ->assertDontSeeText('Canva Templates');
    }

    public function test_search_matches_creator_names_and_tags(): void
    {
        $creator = User::factory()->create(['name' => 'Sok Learning Studio']);
        $product = Product::factory()->create([
            'user_id' => $creator->id,
            'title' => 'Premium Study Library',
            'slug' => 'premium-study-library-search-test',
        ]);
        $product->tags()->attach(Tag::create(['name' => 'Language Tools', 'slug' => 'language-tools']));

        $this->get(route('products.index', ['search' => 'sok']))
            ->assertOk()
            ->assertSeeText($product->title);

        $this->get(route('products.index', ['search' => 'lang']))
            ->assertOk()
            ->assertSeeText($product->title);
    }

    public function test_live_search_request_returns_only_the_results_fragment(): void
    {
        $product = Product::factory()->create([
            'title' => 'Duolingo Premium',
            'slug' => 'duolingo-live-search-test',
        ]);

        $this->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get(route('products.index', ['search' => 'duo']))
            ->assertOk()
            ->assertSeeText($product->title)
            ->assertDontSeeText('Guest marketplace');
    }
}
