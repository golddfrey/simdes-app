<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Desa;
use App\Models\KepalaKeluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KepalaKeluargaController extends Controller
{
    /**
     * Listing dengan cursor pagination
     */
public function index(Request $request)
{
    $q = $request->query('q');

    $selectCols = ['id','nik','nama','desa_id','rt','rw','created_at'];

    $query = KepalaKeluarga::select($selectCols)
        ->with(['anggotas:id,kepala_keluarga_id,nama,jenis_kelamin,tanggal_lahir,status_keluarga,nik'])
        ->orderBy('id', 'desc');

    if ($q) {
        $query->where(function($sub) use ($q) {
            $sub->where('nama', 'like', '%'.$q.'%')
                ->orWhere('nik', 'like', '%'.$q.'%');
        });
    }

    // Gunakan paginate sederhana yang kompatibel untuk pagination link (atau cursorPaginate jika sudah pakai)
    $kks = $query->paginate(15)->withQueryString();

    // Jika request AJAX (XHR) -> kembalikan partial HTML sebagai JSON.html
    // Kita deteksi beberapa cara: ajax() or wantsJson() or header X-Requested-With
    $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

    if ($isAjax) {
        $html = view('kk._list', compact('kks'))->render();
        return response()->json(['html' => $html]);
    }

    // normal page
    return view('kk.index', compact('kks','q'));
}


    /**
     * Show create form
     */
    public function create()
    {
        return view('kk.create');
    }

    /**
     * Store new Kepala Keluarga
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:32|unique:kepala_keluargas,nik',
            'nama' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'desa_id' => 'nullable|exists:desas,id',
            'rt' => 'nullable|string|max:10',   // <-- ADDED
            'rw' => 'nullable|string|max:10',   // <-- ADDED
            'anggota' => 'nullable|array',
            'anggota.*.nama' => 'required_with:anggota|string|max:191',
            'anggota.*.nik' => 'nullable|string|max:32',
            'anggota.*.jenis_kelamin' => 'nullable|in:L,P',
            'anggota.*.status_keluarga' => 'nullable|string|max:100',
            'anggota.*.tempat_lahir' => 'nullable|string|max:191',
            'anggota.*.tanggal_lahir' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $anggotaList = $validated['anggota'] ?? [];
            unset($validated['anggota']);

            $kk = KepalaKeluarga::create($validated);

            foreach ($anggotaList as $a) {
                $a['kepala_keluarga_id'] = $kk->id;
                Anggota::create($a);
            }

            if (!empty($kk->desa_id)) {
                $jumlahBaru = 1 + count($anggotaList);
                Desa::where('id', $kk->desa_id)->increment('jumlah_penduduk_cached', $jumlahBaru);
            }

            DB::commit();

            return redirect()->route('kk.create')->with('success', 'Pendaftaran berhasil — data tersimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('KK store error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['internal' => 'Terjadi masalah saat menyimpan data. Silakan coba lagi.']);
        }
    }

    /**
     * Show single KK
     */
    public function show($id)
    {
        $kk = KepalaKeluarga::with('anggotas')->findOrFail($id);

        return view('kk.show', compact('kk'));
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $kk = KepalaKeluarga::with('anggotas')->findOrFail($id);

        $oldAnggota = $kk->anggotas->map(function ($a) {
            return $a->only(['nik', 'nama', 'jenis_kelamin', 'status_keluarga', 'tempat_lahir', 'tanggal_lahir']);
        })->values()->toArray();

        return view('kk.edit', compact('kk', 'oldAnggota'));
    }

    /**
     * Update KK dan anggota (simple replace strategy)
     */
    public function update(Request $request, $id)
    {
        $kk = KepalaKeluarga::with('anggotas')->findOrFail($id);

        $validated = $request->validate([
            'nik' => 'required|string|max:32|unique:kepala_keluargas,nik,' . $kk->id,
            'nama' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'desa_id' => 'nullable|exists:desas,id',
            'rt' => 'nullable|string|max:10',   // <-- ADDED
            'rw' => 'nullable|string|max:10',   // <-- ADDED
            'anggota' => 'nullable|array',
            'anggota.*.nama' => 'required_with:anggota|string|max:191',
            'anggota.*.nik' => 'nullable|string|max:32',
            'anggota.*.jenis_kelamin' => 'nullable|in:L,P',
            'anggota.*.status_keluarga' => 'nullable|string|max:100',
            'anggota.*.tempat_lahir' => 'nullable|string|max:191',
            'anggota.*.tanggal_lahir' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $oldDesaId = $kk->desa_id;
            $oldCountTotal = 1 + $kk->anggotas->count();

            $anggotaList = $validated['anggota'] ?? [];
            unset($validated['anggota']);

            $kk->update($validated);

            Anggota::where('kepala_keluarga_id', $kk->id)->delete();

            foreach ($anggotaList as $a) {
                $a['kepala_keluarga_id'] = $kk->id;
                Anggota::create($a);
            }

            $newDesaId = $kk->desa_id;
            $newCountTotal = 1 + count($anggotaList);

            if ($oldDesaId && $oldDesaId !== $newDesaId) {
                Desa::where('id', $oldDesaId)->decrement('jumlah_penduduk_cached', $oldCountTotal);
            }

            if ($newDesaId) {
                Desa::where('id', $newDesaId)->increment('jumlah_penduduk_cached', $newCountTotal);
            }

            DB::commit();

            return redirect()->route('kk.show', $kk->id)->with('success', 'Data berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('KK update error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['internal' => 'Gagal menyimpan perubahan.']);
        }
    }

    /**
     * Delete KK (optional)
     */
    public function destroy($id)
    {
        $kk = KepalaKeluarga::with('anggotas')->findOrFail($id);

        DB::transaction(function () use ($kk) {
            $total = 1 + $kk->anggotas->count();
            $desaId = $kk->desa_id;

            Anggota::where('kepala_keluarga_id', $kk->id)->delete();
            $kk->delete();

            if ($desaId) {
                Desa::where('id', $desaId)->decrement('jumlah_penduduk_cached', $total);
            }
        });

        return redirect()->route('kk.index')->with('success', 'Data Kepala Keluarga dihapus.');
    }
}
