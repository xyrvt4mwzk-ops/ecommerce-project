<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->value());
        $searchTerms = collect(preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY))
            ->take(8);
        $category = $request->string('category')->value();
        $sort = $request->string('sort')->value();

        $query = Product::published()
            ->when($searchTerms->isNotEmpty(), function ($query) use ($searchTerms) {
                $searchTerms->each(function (string $term) use ($query): void {
                    $patterns = $this->searchPatterns($term);

                    $query->where(function ($query) use ($patterns) {
                        foreach ($patterns as $pattern) {
                            $query->orWhere(function ($query) use ($pattern) {
                                $query->where('title', 'like', $pattern)
                                    ->orWhere('short_description', 'like', $pattern)
                                    ->orWhere('full_description', 'like', $pattern)
                                    ->orWhere('category', 'like', $pattern)
                                    ->orWhereHas('creator', fn ($query) => $query->where('name', 'like', $pattern))
                                    ->orWhereHas('tags', fn ($query) => $query->where('name', 'like', $pattern));
                            });
                        }
                    });
                });
            })
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->with('creator');

        match ($sort) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        $products = $query
            ->paginate(12)
            ->withQueryString();

        $categories = Product::categories();

        if ($request->ajax()) {
            return view('products.partials.results', compact('products', 'categories', 'search', 'category'));
        }

        return view('products.index', compact('products', 'categories', 'search', 'category', 'sort'));
    }

    public function show(Product $product): View
    {
        $canModerate = auth()->check() && (auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('content-reviewer'));
        $ownsProduct = auth()->check() && auth()->id() === $product->user_id;
        abort_unless(
            ($product->status === Product::STATUS_PUBLISHED && $product->moderation_status !== Product::MODERATION_TAKEN_DOWN)
            || $canModerate || $ownsProduct,
            404,
        );

        $product->load(['creator', 'reviews' => fn ($query) => $query
            ->when(! $canModerate, fn ($query) => $query->where('status', 'published'))
            ->with('user:id,name')->latest()]);

        return view('products.show', compact('product'));
    }

    /**
     * Include the literal fragment plus small adjacent-letter transpositions.
     * This handles common short typos such as "dou" for "duo" without using
     * an expensive full fuzzy-search engine.
     *
     * @return array<int, string>
     */
    private function searchPatterns(string $term): array
    {
        $characters = preg_split('//u', mb_strtolower($term), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $variants = [$term];

        if (count($characters) >= 3 && count($characters) <= 16) {
            // Keep the first character anchored to avoid broad matches such as
            // "dou" becoming "odu" and matching the middle of "productivity".
            for ($index = 1; $index < count($characters) - 1; $index++) {
                $swapped = $characters;
                [$swapped[$index], $swapped[$index + 1]] = [$swapped[$index + 1], $swapped[$index]];
                $variants[] = implode('', $swapped);
            }
        }

        return collect($variants)
            ->unique()
            ->map(fn (string $variant) => '%'.addcslashes($variant, '\\%_').'%')
            ->values()
            ->all();
    }
}
