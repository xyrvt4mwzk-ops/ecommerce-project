<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserRoleController extends Controller
{
    //assign role to user
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,id'
        ]);

        $user->roles()->attach($request->role);
        return redirect()->back()->with('success', 'Role assigned successfully.');
    }
}
