<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(20);
        $roles = Role::orderBy('display_name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    // assign role to user
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,id',
        ]);

        $user->roles()->sync([$request->role]);

        return redirect()->back()->with('success', 'Role assigned successfully.');
    }
}
