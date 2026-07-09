<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserRoleController;

Route::get('/', function () {
    return view('welcome');
});

    Route::middleware(['auth', 'role:super-admin'])->prefix('admin')->group(function () {

    // Role management routes
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');

    // User Role assignment routes
    Route::post('/users/{user}/assign-role', [UserRoleController::class, 'assignRole'])->name('admin.users.assign-role');
    Route::post('/users/{user}/revoke-role', [UserRoleController::class, 'revokeRole'])->name('admin.users.revoke-role');
});
