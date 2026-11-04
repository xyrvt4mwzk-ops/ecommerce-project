<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoleSelectionController extends Controller
{
    public function create(): View
    {
        $user = Auth::user();

        if ($user->roles()->exists()) {
            return redirect()->route('dashboard');
        }

        $roles = Role::whereIn('name', ['buyer', 'creator', 'affiliate'])->orderBy('display_name')->get();

        return view('auth.role-selection', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'in:buyer,creator,affiliate'],
        ]);

        $user = Auth::user();
        $user->assignPrimaryRole($request->input('role'));

        if ($request->input('role') !== 'buyer') {
            $request->session()->forget('url.intended');
        }

        return match ($request->input('role')) {
            'buyer' => redirect()->intended(route('buyer.dashboard')),
            'creator' => redirect()->route('creator.dashboard'),
            'affiliate' => redirect()->route('affiliate.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}
