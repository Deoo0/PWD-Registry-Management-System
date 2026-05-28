<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage in routes:
     *   Route::middleware('role:admin')   — admin only
     *   Route::middleware('role:staff')   — staff only
     *   Route::middleware('role:admin,staff') — either role
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        // Support both a direct FK column and a relationship
        $userRoleId = (string) ($user->usertype_id ?? $user->usertype?->id);

        if (! in_array($userRoleId, $roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}