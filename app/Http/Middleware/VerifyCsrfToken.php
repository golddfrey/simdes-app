<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * VerifyCsrfToken
 *
 * Middleware untuk melindungi aplikasi dari serangan CSRF pada request web.
 * Secara default, semua POST/PUT/PATCH/DELETE pada group 'web' akan divalidasi tokennya.
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * Daftar URI yang dikecualikan dari verifikasi CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Contoh:
        // 'webhook/*',
        // 'payment/notify',
    ];
}
