<?php
namespace App\Http\Controllers;

use App\Models\PendingAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Anggota;

class AnggotaController extends Controller
{
    // Menampilkan daftar anggota untuk Kepala Keluarga
    public function indexForKepala(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->kepala_keluarga_id, 403, 'Anda bukan kepala keluarga.');

        $anggota = Anggota::where('kepala_keluarga_id', $user->kepala_keluarga_id)
            ->orderBy('status_keluarga')
            ->orderBy('tanggal_lahir')
            ->paginate(15);

        return view('anggota.index', compact('anggota'));
    }

    // Form tambah anggota baru (KK)
    public function createForKepala()
    {
        return view('anggota.create');
    }

    // Simpan pengajuan anggota
    public function storeForKepala(Request $request)
    {
        $kkId = Auth::user()->kepala_keluarga_id;

        // Validasi input data anggota
        $data = $request->validate([
            'nik'             => ['required','digits:16','unique:pending_anggotas,nik','unique:anggotas,nik'],
            'nama'            => ['required','string','max:255'],
            'jenis_kelamin'   => ['required','in:L,P'],
            'status_keluarga' => ['nullable','in:ISTRI,ANAK,KELUARGA LAIN'],
            'tempat_lahir'    => ['required','string','max:100'],
            'tanggal_lahir'   => ['required','date'],
        ]);

        // Menambahkan status_keluarga jika status ada
        $data['status_keluarga'] = $data['status_keluarga'] ?? null;

        // Membuat data JSON untuk kolom data_json (array, model akan cast ke JSON)
        $data_json = [
            'nik'             => $data['nik'],
            'nama'            => $data['nama'],
            'jenis_kelamin'   => $data['jenis_kelamin'],
            'status_keluarga' => $data['status_keluarga'],
            'tempat_lahir'    => $data['tempat_lahir'],
            'tanggal_lahir'   => $data['tanggal_lahir'],
        ];

        // Debugging: pastikan data JSON yang akan disimpan ada dan benar
        \Log::debug('Data JSON: ', $data_json);

        // Simpan pengajuan anggota ke dalam tabel pending_anggotas
        PendingAnggota::create([
            'nik'               => $data['nik'],
            'nama'              => $data['nama'],
            'jenis_kelamin'     => $data['jenis_kelamin'],
            'status_keluarga'   => $data['status_keluarga'],
            'tempat_lahir'      => $data['tempat_lahir'],
            'tanggal_lahir'     => $data['tanggal_lahir'],
            'kepala_keluarga_id'=> $kkId,
            'status'            => 'pending',  // Status pengajuan
            'submitted_by'      => Auth::id(), 
            'data_json'         => $data_json,  // simpan array; model akan serialize
        ]);

        return redirect()->route('kk.anggota.pending')
            ->with('success', 'Pengajuan anggota dikirim. Menunggu persetujuan admin.');
    }

    // Daftar pengajuan yang diajukan oleh KK
    public function listPendingForKepala()
    {
        $kkId = Auth::user()->kepala_keluarga_id;

        $list = PendingAnggota::where('kepala_keluarga_id', $kkId)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('anggota.pending', compact('list'));
    }

    // (Jika ada method edit/updateForKepala, jangan lupa disesuaikan juga)
}
