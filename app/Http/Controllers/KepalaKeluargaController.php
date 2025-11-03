<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Desa;
use App\Models\KepalaKeluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class KepalaKeluargaController extends Controller
{
    /**
     * Listing dengan pagination (dengan support AJAX partial)
     *
     * - Mengambil relasi anggotas dengan kolom tertentu agar ringan.
     * - Jika AJAX (XHR) maka kembalikan partial HTML sebagai JSON.html
     * - Pastikan field tanggal menjadi Carbon agar view ->format() aman.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');

        // kolom yang ingin kita ambil (ringkas untuk performa)
        $selectCols = ['id','nik','nama','desa_id','rt','rw','created_at'];

        $query = KepalaKeluarga::select($selectCols)
            // eager load anggota tapi pilih kolom penting supaya ringan
            ->with(['anggotas:id,kepala_keluarga_id,nama,jenis_kelamin,tanggal_lahir,status_keluarga,nik'])
            ->orderBy('id', 'desc');

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('nama', 'like', '%'.$q.'%')
                    ->orWhere('nik', 'like', '%'.$q.'%');
            });
        }

        // paginate biasa (15 per halaman)
        $kks = $query->paginate(15)->withQueryString();

        // --- PENTING: pastikan atribut tanggal pada model menjadi Carbon
        // Karena kadang attribute bisa berupa string (tergantung driver / select),
        // kita force-parse agar view aman saat memakai ->format()
        $kks->getCollection()->transform(function ($kk) {
            // created_at
            if (isset($kk->created_at) && ! ($kk->created_at instanceof Carbon)) {
                try {
                    $kk->created_at = Carbon::parse($kk->created_at);
                } catch (\Throwable $e) {
                    // kalau parsing gagal, biarkan stringnya agar tidak crash
                    Log::warning("Gagal parse created_at untuk KK id {$kk->id}: ".$e->getMessage());
                }
            }

            // anggota: parse tanggal_lahir masing-masing anggota (jika ada)
            if ($kk->relationLoaded('anggotas')) {
                $kk->anggotas->transform(function ($a) {
                    if (isset($a->tanggal_lahir) && $a->tanggal_lahir !== null && ! ($a->tanggal_lahir instanceof Carbon)) {
                        try {
                            $a->tanggal_lahir = Carbon::parse($a->tanggal_lahir);
                        } catch (\Throwable $e) {
                            Log::warning("Gagal parse tanggal_lahir anggota id {$a->id}: ".$e->getMessage());
                        }
                    }
                    return $a;
                });
            }

            return $kk;
        });

        // cek apakah request AJAX (XHR) — kita tangani beberapa kemungkinan
        $isAjax = $request->ajax() ||
                  $request->wantsJson() ||
                  strtolower($request->header('X-Requested-With') ?? '') === 'xmlhttprequest';

        if ($isAjax) {
            // render partial list view (pastikan ada file resources/views/kk/_list.blade.php)
            $html = view('kk._list', compact('kks'))->render();
            return response()->json(['html' => $html]);
        }

        // normal page render
        return view('kk.index', compact('kks','q'));
    }


    /**
     * Tampilkan form create
     */
    public function create()
    {
        return view('kk.create');
    }

    /**
     * Simpan KK baru + anggota
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:32|unique:kepala_keluargas,nik',
            'nama' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'desa_id' => 'nullable|exists:desas,id',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
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
     * Tampilkan detail KK
     */
    public function show($id)
    {
        $kk = KepalaKeluarga::with('anggotas')->findOrFail($id);

        // Pastikan tanggal anggota juga Carbon di view detail
        $kk->anggotas->transform(function ($a) {
            if (isset($a->tanggal_lahir) && ! ($a->tanggal_lahir instanceof Carbon)) {
                try {
                    $a->tanggal_lahir = Carbon::parse($a->tanggal_lahir);
                } catch (\Throwable $e) {
                    Log::warning("Gagal parse tanggal_lahir anggota id {$a->id}: ".$e->getMessage());
                }
            }
            return $a;
        });

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
     * Update KK (replace anggota)
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
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
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
     * Hapus KK (opsional)
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
