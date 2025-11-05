<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RedirectIfAuthenticated
 *
 * Jika user sudah login dan mencoba mengakses route bertanda "guest",
 * middleware ini akan mengarahkan kembali ke halaman "home".
 */
class RedirectIfAuthenticated
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$guards
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Sudah login → arahkan ke dashboard utama
                return redirect()->intended(route('home'));
            }
        }

        return $next($request);
    }
}
