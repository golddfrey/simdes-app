<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/**
 * TrimStrings
 *
 * Middleware ini akan otomatis menghapus spasi di awal/akhir dari setiap
 * input string pada request yang masuk. Tujuannya untuk menjaga konsistensi data.
 */
class TrimStrings extends Middleware
{
    /**
     * Daftar input yang tidak akan di-trim.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
