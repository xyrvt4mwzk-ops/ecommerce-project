<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * 
     */
    public function handle(Request $request, Closure $next, string $role = ''): Response
    {
        // 1. Check if the user is logged in
        if (!Auth::check()) {
            abort(401, 'Unauthorized access.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Super administrators are the platform override role. Fine-grained
        // permissions and policies still protect the action being performed.
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // 2. Check if the logged-in user has the required role
        if (!$user->roles()->where('name', $role)->exists()) {
            abort(403, 'You do not have the required role to access this resource.');
        }

        return $next($request);
    }
}
