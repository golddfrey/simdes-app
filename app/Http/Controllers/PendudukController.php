<?php

namespace App\Http\Controllers;

use App\Models\KepalaKeluarga;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class PendudukController extends Controller
{
    /**
     * Tampilkan semua penduduk (KK + Anggota) sebagai daftar card.
     * Pencarian berdasarkan q (nik atau nama).
     */
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));

        // 1) ambil semua kepala keluarga (hanya field penting)
        $heads = KepalaKeluarga::select('id','nik','nama','jenis_kelamin','rt','rw','alamat','created_at')
            ->get()
            ->map(function($h){
                return (object)[
                    'source' => 'kk',
                    'id' => $h->id,
                    'nik' => $h->nik,
                    'nama' => $h->nama,
                    'jenis_kelamin' => $h->jenis_kelamin,
                    'rt' => $h->rt,
                    'rw' => $h->rw,
                    'alamat' => $h->alamat,
                    'created_at' => $h->created_at,
                    'kepala_nama' => null, // tidak relevan untuk KK
                ];
            });

        // 2) ambil anggota beserta nama kepala keluarganya
        $members = Anggota::with('kepalaKeluarga:id,nama')
            ->select('id','nik','nama','jenis_kelamin','tempat_lahir','tanggal_lahir','status_keluarga','kepala_keluarga_id','created_at')
            ->get()
            ->map(function($a){
                return (object)[
                    'source' => 'anggota',
                    'id' => $a->id,
                    'nik' => $a->nik,
                    'nama' => $a->nama,
                    'jenis_kelamin' => $a->jenis_kelamin,
                    'tempat_lahir' => $a->tempat_lahir,
                    'tanggal_lahir' => $a->tanggal_lahir,
                    'status_keluarga' => $a->status_keluarga,
                    'kepala_keluarga_id' => $a->kepala_keluarga_id,
                    'kepala_nama' => optional($a->kepalaKeluarga)->nama,
                    'created_at' => $a->created_at,
                ];
            });

        // 3) gabungkan
        $all = $heads->concat($members);

        // 4) jika ada query, filter (nik atau nama)
        if ($q !== '') {
            $qLower = mb_strtolower($q);
            $all = $all->filter(function($r) use ($qLower) {
                return (mb_stripos((string)$r->nik, $qLower) !== false) ||
                       (mb_stripos((string)$r->nama, $qLower) !== false) ||
                       (isset($r->alamat) && mb_stripos((string)$r->alamat, $qLower) !== false) ||
                       (isset($r->kepala_nama) && mb_stripos((string)$r->kepala_nama, $qLower) !== false);
            })->values();
        }

        // 5) urutkan — misal berdasarkan nama asc (ubah sesuai kebutuhan)
        $all = $all->sortBy('nama')->values();

        // 6) pagination manual
        $perPage = 12;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $itemsForCurrentPage = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $itemsForCurrentPage,
            $all->count(),
            $perPage,
            $page,
            [
                'path' => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('penduduk.index', [
            'residents' => $paginator,
            'q' => $q,
        ]);
    }

    /**
     * Detail Kepala Keluarga (KK)
     */
    public function showKk($id)
    {
        $kk = KepalaKeluarga::with('anggotas')->findOrFail($id);
        return view('penduduk.kk_show', compact('kk'));
    }

    /**
     * Export biodata KK ke PDF
     */
    public function exportKkPdf($id)
    {
        $kk = KepalaKeluarga::with('anggotas')->findOrFail($id);
        $pdf = PDF::loadView('penduduk.kk_pdf', compact('kk'));
        return $pdf->download('biodata_kk_'.$kk->nik.'.pdf');
    }

    /**
     * Detail Anggota
     */
    public function showAnggota($id)
    {
        $a = Anggota::with('kepalaKeluarga')->findOrFail($id);
        return view('penduduk.anggota_show', compact('a'));
    }

    /**
     * Export biodata anggota ke PDF
     */
    public function exportAnggotaPdf($id)
    {
        $a = Anggota::with('kepalaKeluarga')->findOrFail($id);
        $pdf = PDF::loadView('penduduk.anggota_pdf', compact('a'));
        return $pdf->download('biodata_anggota_'.$a->id.'.pdf');
    }
}
