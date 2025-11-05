<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KepalaKeluargaController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\Admin\PendingAnggotaController;

// ----------------------
// Guest (login pages)
// ----------------------
Route::middleware('guest')->group(function () {
    Route::get('admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');

    Route::get('kk/login', [AuthController::class, 'showKkLogin'])->name('kk.login');
    Route::post('kk/login', [AuthController::class, 'kkLogin'])->name('kk.login.post');
});

// ----------------------
// Auth common
// ----------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// ----------------------
// Admin area
// ----------------------
Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
    Route::get('/', [HomeController::class, 'adminIndex'])->name('dashboard');

    // 1) PENGAJUAN (LETakkan DI ATAS RESOURCE)
    Route::get('anggota/pengajuan', [\App\Http\Controllers\Admin\PendingAnggotaController::class, 'index'])
        ->name('anggota.pending.index');
    Route::post('anggota/pengajuan/{id}/approve', [\App\Http\Controllers\Admin\PendingAnggotaController::class, 'approve'])
        ->name('anggota.pending.approve');
    Route::post('anggota/pengajuan/{id}/reject', [\App\Http\Controllers\Admin\PendingAnggotaController::class, 'reject'])
        ->name('anggota.pending.reject');

    // 2) RESOURCE ANGGOTA – letakkan SETELAH pengajuan
    Route::resource('anggota', \App\Http\Controllers\AnggotaController::class)
        ->names('anggota')
        // paksa nama parameter jadi 'anggota' (hindari 'anggotum')
        ->parameters(['anggota' => 'anggota']);

    // 3) Resource KK / dll
    Route::resource('kepala-keluarga', \App\Http\Controllers\KepalaKeluargaController::class)
        ->names('kepala_keluarga');

    // Penduduk
    Route::get('penduduk', [PendudukController::class, 'index'])->name('penduduk.index');
    Route::get('penduduk/{penduduk}', [PendudukController::class, 'show'])->name('penduduk.show');
});


// ----------------------
// Kepala Keluarga area
// ----------------------
Route::prefix('kk')->name('kk.')->middleware(['auth','role:kepala_keluarga'])->group(function () {
    Route::get('/', [KepalaKeluargaController::class, 'dashboard'])->name('dashboard');

    Route::get('anggota', [AnggotaController::class, 'indexForKepala'])->name('anggota.index');
    Route::get('anggota/create', [AnggotaController::class, 'createForKepala'])->name('anggota.create');
    Route::post('anggota', [AnggotaController::class, 'storeForKepala'])->name('anggota.store');

    Route::get('anggota/{anggota}/edit', [AnggotaController::class, 'editForKepala'])->name('anggota.edit');
    Route::put('anggota/{anggota}', [AnggotaController::class, 'updateForKepala'])->name('anggota.update');

    Route::get('anggota/pengajuan', [AnggotaController::class, 'listPendingForKepala'])->name('anggota.pending');
});
