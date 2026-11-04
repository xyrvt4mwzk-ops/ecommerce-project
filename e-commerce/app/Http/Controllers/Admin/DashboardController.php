<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $summary = [
            'users' => User::count(),
            'creators' => User::whereHas('roles', fn ($query) => $query->where('name', 'creator'))->count(),
            'affiliates' => User::whereHas('roles', fn ($query) => $query->where('name', 'affiliate'))->count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'revenue' => Order::sum('total'),
        ];

        $recentUsers = User::latest()->take(6)->get();
        $recentOrders = Order::with('user')->latest()->take(6)->get();
        $recentCommissions = AffiliateCommission::with('affiliate', 'order')->latest()->take(6)->get();
        $totalCommissions = AffiliateCommission::sum('amount');

        return view('admin.dashboard', compact('summary', 'recentUsers', 'recentOrders', 'recentCommissions', 'totalCommissions'));
    }
}
