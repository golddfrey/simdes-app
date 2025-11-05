<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\KepalaKeluarga;
use App\Models\Anggota;
use App\Models\Desa;
use App\Models\Penduduk;

/**
 * HomeController
 *
 * - index(): dashboard umum (redirect admin ke /admin)
 * - adminIndex(): dashboard admin + ringkasan
 */
class HomeController extends Controller
{
    /**
     * Show the general dashboard.
     * Admin akan diarahkan ke dashboard admin.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $stats = [
            'total_desa' => Desa::count(),
        ];

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user && $user->isKepalaKeluarga()) {
            $kkId = $user->kepala_keluarga_id;
            $anggotaCount = $kkId ? Anggota::where('kepala_keluarga_id', $kkId)->count() : 0;

            return view('home', [
                'user'  => $user,
                'stats' => array_merge($stats, [
                    'anggota_count' => $anggotaCount,
                ]),
            ]);
        }

        return view('home', [
            'user'  => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Admin dashboard — protected by role:admin middleware.
     */
    public function adminIndex(Request $request)
    {
        // Hitung penduduk dengan aman (cek apakah tabel penduduks ada).
        $pendudukCount = 0;

        try {
            // Pastikan class ada & tabelnya tersedia sebelum melakukan count
            if (class_exists(Penduduk::class)) {
                $table = (new Penduduk)->getTable(); // default Eloquent: 'penduduks'
                if (Schema::hasTable($table)) {
                    $pendudukCount = Penduduk::count();
                } else {
                    // fallback: asumsi penduduk = kepala keluarga + anggota
                    $pendudukCount = KepalaKeluarga::count() + Anggota::count();
                }
            } else {
                $pendudukCount = KepalaKeluarga::count() + Anggota::count();
            }
        } catch (\Throwable $e) {
            // Jika terjadi error (mis. koneksi/migrasi), fallback aman
            $pendudukCount = KepalaKeluarga::count() + Anggota::count();
        }

        $counts = [
            'kepala_keluarga' => KepalaKeluarga::count(),
            'anggota'         => Anggota::count(),
            'penduduk'        => $pendudukCount,
            'desa'            => Desa::count(),
        ];

        // Latest entries (dibatasi kecil agar ringan)
        $latestKk = KepalaKeluarga::orderBy('created_at', 'desc')->limit(10)->get();
        $latestAnggota = Anggota::orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.dashboard', [
            'counts'        => $counts,
            'latestKk'      => $latestKk,
            'latestAnggota' => $latestAnggota,
        ]);
    }
}
