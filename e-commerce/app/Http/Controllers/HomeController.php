<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $featuredProducts = Product::published()
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        $categories = Product::categories();

        $recommended = Product::published()
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('home', compact('featuredProducts', 'categories', 'recommended'));
    }
}
