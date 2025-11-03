<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KepalaKeluargaController;
use App\Http\Controllers\PendudukController;

Route::get('/', function () {
    return view('home');
})->name('home');

// Resource KK (index, create, store, show, edit, update, destroy)
Route::resource('kk', KepalaKeluargaController::class);

// Additional route: ambil anggota sebagai JSON untuk AJAX (tidak conflict dengan resource)
Route::get('kk/{id}/anggotas', [KepalaKeluargaController::class, 'anggotaJson'])
    ->name('kk.anggota');

// daftar semua penduduk (KK + Anggota)
Route::get('/penduduk', [PendudukController::class, 'index'])->name('penduduk.index');

// detail untuk KK (penduduk controller)
Route::get('/penduduk/kk/{id}', [PendudukController::class, 'showKk'])->name('penduduk.kk.show');
Route::get('/penduduk/kk/{id}/export', [PendudukController::class, 'exportKkPdf'])->name('penduduk.kk.export');

// detail untuk anggota
Route::get('/penduduk/anggota/{id}', [PendudukController::class, 'showAnggota'])->name('penduduk.anggota.show');
Route::get('/penduduk/anggota/{id}/export', [PendudukController::class, 'exportAnggotaPdf'])->name('penduduk.anggota.export');
