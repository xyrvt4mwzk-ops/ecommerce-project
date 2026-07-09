<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    //display list all roles
    public function index()
    {
        $roles = Role::with('Permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    //store new created role
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
