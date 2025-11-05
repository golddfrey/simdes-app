<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Route di sini otomatis diprefix dengan /api (lihat RouteServiceProvider).
| Simpel aja dulu supaya file-nya ada dan artisan nggak error.
|
*/

Route::get('/ping', function () {
    return response()->json([
        'ok' => true,
        'app' => config('app.name'),
        'env' => config('app.env'),
        'time' => now()->toISOString(),
    ]);
});
