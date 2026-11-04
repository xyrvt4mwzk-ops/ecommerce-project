<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('creator')->latest()->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function updateStatus(Request $request, Product $product)
    {
        $request->validate([
            'status' => 'required|in:draft,published,archived',
        ]);

        $product->update(['status' => $request->status]);

        return redirect()->route('admin.products.index')->with('success', 'Product status updated.');
    }
}
