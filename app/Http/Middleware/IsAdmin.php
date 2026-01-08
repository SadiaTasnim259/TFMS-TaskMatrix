<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(401, 'Please login first');
        }

        // Check if user has isAdmin method and is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to admin panel. Only admins allowed.');
        }

        return $next($request);
    }
}
