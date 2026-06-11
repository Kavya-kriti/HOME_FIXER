<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:  ->middleware('role:admin')
     *                   ->middleware('role:customer,provider')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        $userRole = auth()->user()->role;

        if (!in_array($userRole, $roles)) {
            // Log the unauthorised access attempt
            \Log::warning('Unauthorised role access attempt', [
                'user_id'       => auth()->id(),
                'user_role'     => $userRole,
                'required_roles' => $roles,
                'url'           => $request->fullUrl(),
            ]);

            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
