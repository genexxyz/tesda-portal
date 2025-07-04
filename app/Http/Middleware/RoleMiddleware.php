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

        if (!Auth::user()->role || !in_array(Auth::user()->role->name, $roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}