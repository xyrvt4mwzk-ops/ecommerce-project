<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function categories(): View
    {
        $categories = collect(Product::categories())->map(function (string $name, string $slug): array {
            return [
                'slug' => $slug,
                'name' => $name,
                'products_count' => Product::published()->where('category', $slug)->count(),
            ];
        });

        return view('marketplace.categories', compact('categories'));
    }

    public function category(string $category): View
    {
        $categories = Product::categories();
        abort_unless(array_key_exists($category, $categories), 404);

        $products = Product::published()
            ->where('category', $category)
            ->with('creator')
            ->latest()
            ->paginate(12);

        return view('marketplace.category', [
            'category' => $category,
            'categoryName' => $categories[$category],
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function creators(): View
    {
        $creators = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'creator'))
            ->whereHas('products', fn ($query) => $query->published())
            ->withCount(['products as published_products_count' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->paginate(18);

        return view('marketplace.creators', compact('creators'));
    }

    public function creator(User $creator): View
    {
        abort_unless($creator->hasRole('creator'), 404);

        $products = $creator->products()
            ->published()
            ->with('creator')
            ->latest()
            ->paginate(12);

        return view('marketplace.creator', compact('creator', 'products'));
    }

    public function about(): View
    {
        return view('marketplace.about');
    }

    public function help(): View
    {
        return view('marketplace.help');
    }
}
