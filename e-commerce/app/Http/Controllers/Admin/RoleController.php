<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // display list all roles
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('display_name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    // store new created role
    public function store(Request $request)
    {
        $vallidated = $request->validate([
            'name' => 'required|unique:roles,name',
            'display_name' => 'required',
            'description' => 'nullable',
        ]);

        Role::create($vallidated);

        return redirect()->back()->with('success', 'Role created successfully.');
    }
}
