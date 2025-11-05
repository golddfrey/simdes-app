<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

/**
 * PreventRequestsDuringMaintenance
 *
 * Middleware ini memblokir request publik saat aplikasi dalam mode maintenance.
 * Jalankan "php artisan down" untuk mengaktifkan mode maintenance, dan
 * "php artisan up" untuk menonaktifkan.
 */
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Routes yang diizinkan diakses saat maintenance.
     *
     * @var array<int, string>
     */
    protected $except = [
        // contoh: 'health', 'status'
    ];
}
