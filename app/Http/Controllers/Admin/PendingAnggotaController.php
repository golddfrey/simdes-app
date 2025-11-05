<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendingAnggota;
use App\Models\KepalaKeluarga;
use App\Models\Anggota;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PendingAnggotaController extends Controller
{
    /**
     * Menampilkan daftar seluruh pengajuan anggota keluarga.
     * Hanya menampilkan yang berstatus 'pending'.
     */
    public function index()
    {
        $pengajuan = PendingAnggota::with('kepalaKeluarga')
            ->where('status', 'pending')           // <- penting: hanya pending
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.pengajuan.index', compact('pengajuan'));
    }

    /**
     * Approve sebuah pengajuan.
     * Route: POST admin/anggota/pengajuan/{id}/approve
     */
    public function approve(Request $request, $id)
    {
        $adminId = Auth::id();

        DB::beginTransaction();
        try {
            $pending = PendingAnggota::findOrFail($id);

            if ($pending->status !== 'pending') {
                return redirect()->back()->with('warning', 'Pengajuan ini sudah diproses sebelumnya.');
            }

            $data = $pending->data_json ?? [
                'nik' => $pending->nik,
                'nama' => $pending->nama,
                'jenis_kelamin' => $pending->jenis_kelamin,
                'status_keluarga' => $pending->status_keluarga,
                'tempat_lahir' => $pending->tempat_lahir,
                'tanggal_lahir' => $pending->tanggal_lahir,
            ];

            if (empty($data['nik'])) {
                return redirect()->back()->with('error', 'NIK tidak ditemukan di data pengajuan.');
            }

            if (empty($pending->kepala_keluarga_id)) {
                return redirect()->back()->with('error', 'Tidak ada kepala keluarga terkait pada pengajuan ini.');
            }

            $kkExists = \DB::table('kepala_keluargas')->where('id', $pending->kepala_keluarga_id)->exists();
            if (! $kkExists) {
                return redirect()->back()->with('error', 'Kepala keluarga terkait tidak ditemukan (FK invalid).');
            }

            $exists = Anggota::where('nik', $data['nik'])->exists();
            if ($exists) {
                $pending->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                    'reviewed_by' => $adminId,
                    'alasan' => 'NIK sudah terdaftar'
                ]);
                DB::commit();
                return redirect()->route('admin.anggota.pending.index')->with('error', 'Gagal approve: NIK sudah terdaftar.');
            }

            if (!empty($data['tanggal_lahir'])) {
                try {
                    $parsed = \Carbon\Carbon::parse($data['tanggal_lahir']);
                    $data['tanggal_lahir'] = $parsed->format('Y-m-d');
                } catch (\Throwable $e) {
                    \Log::warning('Parse tanggal_lahir gagal untuk pending id '.$pending->id.': '.$e->getMessage());
                    $data['tanggal_lahir'] = null;
                }
            }

            $anggotaData = [
                'nik' => $data['nik'] ?? null,
                'nama' => $data['nama'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'status_keluarga' => $data['status_keluarga'] ?? null,
                'tempat_lahir' => $data['tempat_lahir'] ?? null,
                'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                'kepala_keluarga_id' => $pending->kepala_keluarga_id,
            ];

            $anggota = Anggota::create($anggotaData);

            $pending->update([
                'status' => 'approved',
                'approved_at' => now(),
                'reviewed_by' => $adminId,
            ]);

            DB::commit();

            return redirect()->route('admin.anggota.pending.index')->with('success', 'Pengajuan disetujui dan anggota berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Approve pending anggota error: '.$e->getMessage(), ['id'=>$id, 'trace'=>$e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pengajuan.');
        }
    }

    /**
     * Reject sebuah pengajuan.
     * Route: POST admin/anggota/pengajuan/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        $adminId = Auth::id();

        $pending = PendingAnggota::findOrFail($id);

        if ($pending->status !== 'pending') {
            return redirect()->back()->with('warning', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $pending->status = 'rejected';
        $pending->rejected_at = Carbon::now();
        $pending->reviewed_by = $adminId;
        $pending->alasan = $request->input('alasan', 'Ditolak oleh admin');
        $pending->save();

        return redirect()->route('admin.anggota.pending.index')->with('success', 'Pengajuan ditolak.');
    }
}
