<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * RoleMiddleware
 *
 * Usage in routes:
 *   Route::get(...)->middleware('role:admin');
 *   Route::post(...)->middleware('role:admin,kepala_keluarga');
 *
 * Behavior:
 * - If user not authenticated -> redirect to login
 * - If user authenticated but role not in allowed list -> abort 403 (for AJAX/JSON) or redirect back with error
 *
 * Keep this middleware small: authorization rules more complex than simple role-check
 * should live in Policy classes or dedicated Gate logic.
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $roles  Comma-separated list of allowed roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $roles = null)
    {
        // If not authenticated, redirect to login (web guard)
        if (!Auth::check()) {
            // For AJAX/JSON requests, return 401 JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // If no roles specified, allow any authenticated user
        if (is_null($roles) || trim($roles) === '') {
            return $next($request);
        }

        $allowed = array_filter(array_map('trim', explode(',', $roles)));

        $user = Auth::user();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // If user role is in allowed list -> continue
        if (in_array($user->role, $allowed, true)) {
            return $next($request);
        }

        // Not allowed: handle gracefully
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // Redirect back with message (or to home)
        return redirect()->route('home')->withErrors(['authorization' => 'Anda tidak memiliki izin untuk mengakses halaman ini.']);
    }
}
