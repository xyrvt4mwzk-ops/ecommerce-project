<?php

namespace App\Http\Controllers;

use App\Models\ContentReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): RedirectResponse|View
    {
        $user = Auth::user();

        if (! $user->roles()->exists()) {
            return redirect()->route('role.select');
        }

        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('buyer')) {
            return redirect()->route('buyer.dashboard');
        }

        if ($user->hasRole('creator')) {
            return redirect()->route('creator.dashboard');
        }

        if ($user->hasRole('affiliate')) {
            return redirect()->route('affiliate.dashboard');
        }

        if ($user->hasRole('content-reviewer')) {
            return redirect()->route('reviewer.dashboard');
        }

        return view('dashboard');
    }

    public function buyer(): View
    {
        return view('dashboard.buyer');
    }

    public function creator(): View
    {
        return view('dashboard.creator');
    }

    public function affiliate(): View
    {
        return view('dashboard.affiliate');
    }

    public function admin(): View
    {
        return view('dashboard.admin');
    }

    public function reviewer(): View
    {
        $summary = [
            'open' => ContentReport::where('status', 'open')->count(),
            'taken_down' => ContentReport::where('status', 'taken_down')->count(),
            'countered' => ContentReport::where('status', 'countered')->count(),
        ];
        $reports = ContentReport::with('product', 'reporter')->latest()->take(6)->get();

        return view('dashboard.reviewer', compact('summary', 'reports'));
    }
}
