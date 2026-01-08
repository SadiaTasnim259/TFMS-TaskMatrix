<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is logged in and needs to change password (first login or reset)
        if ($user && ($user->is_first_login || $user->must_change_password)) {

            // List of allowed routes to prevent redirect loop
            // 'logout' is crucial to allow them to exit
            // 'change-password' is where they need to be
            $allowedRoutes = [
                'change-password',
                'change-password.post',
                'logout',
            ];

            if ($request->route() && !in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('change-password')
                    ->with('error', 'You must change your password before proceeding.');
            }
        }

        return $next($request);
    }
}
