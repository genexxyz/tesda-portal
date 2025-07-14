<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user status is active
        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account is not active. Please contact the administrator.'
            ]);
        }

        // Check role permissions
        if (!$user->role || !in_array($user->role->name, $roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}