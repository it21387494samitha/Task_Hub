<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureUserIsAdmin — Restricts access to admin-only routes.
 *
 * Allows both Admin and Team Leader roles through to the admin panel.
 * Pure developers are redirected back to the dashboard.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isDeveloper()) {
            abort(403, 'Access denied. Admin or Team Leader role required.');
        }

        return $next($request);
    }
}
