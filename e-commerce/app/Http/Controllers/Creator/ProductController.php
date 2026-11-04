<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Auth::user()->products()->latest()->paginate(12);

        return view('creator.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Product::categories();

        return view('creator.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            'full_description' => 'required|string',
            'category' => 'required|string|in:'.implode(',', array_keys(Product::categories())),
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:draft,published,archived',
            'affiliate_enabled' => 'required|boolean',
            'affiliate_commission_rate' => 'required|numeric|min:0|max:50',
            'cover_image' => 'nullable|image|max:2048',
            'digital_file' => 'nullable|file|max:10240|mimes:zip,pdf,mp3,mp4,svg,png,jpg,jpeg',
        ]);

        $data['user_id'] = Auth::id();
        $data['slug'] = $this->buildSlug($data['title']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('products/covers');
        }

        if ($request->hasFile('digital_file')) {
            $data['file_path'] = $request->file('digital_file')->store('products/files');
        }

        Product::create($data);

        return redirect()->route($this->indexRoute())->with('success', 'Product saved successfully.');
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        $categories = Product::categories();

        return view('creator.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            'full_description' => 'required|string',
            'category' => 'required|string|in:'.implode(',', array_keys(Product::categories())),
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:draft,published,archived',
            'affiliate_enabled' => 'required|boolean',
            'affiliate_commission_rate' => 'required|numeric|min:0|max:50',
            'cover_image' => 'nullable|image|max:2048',
            'digital_file' => 'nullable|file|max:10240|mimes:zip,pdf,mp3,mp4,svg,png,jpg,jpeg',
        ]);

        $data['slug'] = $this->buildSlug($data['title'], $product->id);

        if ($request->hasFile('cover_image')) {
            if ($product->cover_image_path) {
                Storage::delete($product->cover_image_path);
            }
            $data['cover_image_path'] = $request->file('cover_image')->store('products/covers');
        }

        if ($request->hasFile('digital_file')) {
            if ($product->file_path) {
                Storage::delete($product->file_path);
            }
            $data['file_path'] = $request->file('digital_file')->store('products/files');
        }

        $product->update($data);

        return redirect()->route($this->indexRoute())->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        if ($product->cover_image_path) {
            Storage::delete($product->cover_image_path);
        }

        if ($product->file_path) {
            Storage::delete($product->file_path);
        }

        $product->delete();

        return redirect()->route($this->indexRoute())->with('success', 'Product deleted successfully.');
    }

    private function buildSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $baseSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$baseSlug}-".($count++);
        }

        return $slug;
    }

    private function indexRoute(): string
    {
        return Auth::user()->hasRole('super-admin')
            ? 'admin.products.index'
            : 'creator.products.index';
    }
}
