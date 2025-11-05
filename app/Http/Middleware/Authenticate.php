<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            // Jika akses menuju area admin → arahkan ke login admin
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            // Selain itu (home, kk/, dll) → arahkan ke login Kepala Keluarga
            return route('kk.login');
        }

        return null;
    }
}
