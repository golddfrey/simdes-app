<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * EncryptCookies
 *
 * Mengenkripsi/dekripsi cookie agar aman.
 */
class EncryptCookies extends Middleware
{
    /**
     * Daftar nama cookie yang TIDAK akan dienkripsi.
     *
     * @var array<int, string>
     */
    protected $except = [
        // contoh: 'cookie_tanpa_enkripsi'
    ];
}
